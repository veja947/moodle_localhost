<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Class manager and methods for managing the data
 *
 * @package    block_custom_dashboard
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_custom_dashboard;

defined('MOODLE_INTERNAL') || die();

require_once("{$CFG->libdir}/externallib.php");
require_once("{$CFG->libdir}/completionlib.php");

class manager
{
    const PROGRAM_TABLE_NAME = 'local_program';
    const PROGRAM_COURSES_TABLE_NAME = 'local_program_course';

    const COMPLETION_STATUS_NOT_STARTED = 'not_started';
    const COMPLETION_STATUS_IN_PROGRESS = 'in_progress';
    const COMPLETION_STATUS_COMPLETED = 'completed';

    const PROGRESS_BAR_COLOR_COMPLETED = '#1890FF';
    const PROGRESS_BAR_COLOR_IN_PROGRESS = '#48D597';
    const PROGRESS_BAR_COLOR_NOT_STARTED = '#DA291C';

    public static function get_program_ids_and_names(): array
    {
        global $DB;
        if (!self::check_table_exist(self::PROGRAM_TABLE_NAME)) {
            return [];
        }
        return $DB->get_records(self::PROGRAM_TABLE_NAME, null, '', 'id, fullname');
    }

    public static function get_program_statics(int $program_id = null, int $course_id = null): array
    {
        $records = self::get_records_in_program($program_id, $course_id);
        $results = self::filter_records($program_id, $records);
        $results['students'] = self::get_students_count_in_program($program_id);


        return $results;
    }

    private static function get_students_count_in_program(int $program_id = null, int $course_id = null): int
    {
        global $DB;
        $select_discinct_students = 'SELECT COUNT(DISTINCT u.id) AS student_count';
        $sql = self::get_filter_program_records_sql($program_id, $course_id, '', $select_discinct_students);
        $params = [
            'programid' => $program_id,
            'courseid' => $course_id,
        ];
        $result = $DB->get_records_sql($sql, $params);
        return array_pop($result)->student_count;
    }

    private static function get_records_in_program(int $program_id = null, int $course_id = null): array
    {
        $results = [];

        global $DB;

        $sql = "SELECT DISTINCT ccom.id AS 'record_id', 
                    u.id AS 'user_id',
                    ccat.id AS 'category_id', 
                    c.id AS 'course_id',
                    lpc.programid as 'program_id',
                    lp.fullname AS 'program_name',
                    CASE 
                        WHEN ccom.timestarted = 0 AND ccom.timeenrolled <> 0 then 'not_started'
                        WHEN ccom.timecompleted IS NULL AND ccom.timestarted <> 0 THEN 'in_progress'
                        WHEN ccom.timecompleted IS NOT NULL THEN 'completed'
                        END AS 'completion_status' 
                    FROM {user} AS u 
                      JOIN {course_completions} AS ccom ON u.id = ccom.userid
                      JOIN {course} AS c ON c.id = ccom.course
                      JOIN {course_categories} AS ccat ON c.category = ccat.id
                      JOIN {" . self::PROGRAM_COURSES_TABLE_NAME . "} AS lpc ON c.id = lpc.courseid
                      JOIN {" . self::PROGRAM_TABLE_NAME ."} AS lp ON lp.id = lpc.programid
                    WHERE lpc.programid=:programid 
                ";
        if ($course_id) {
            $sql .= " AND c.id = :courseid ";
        }

        $params = [
            'programid' => $program_id,
            'courseid' => $course_id,
        ];

        return $DB->get_records_sql($sql, $params);
    }

    private static function filter_records(int $program_id, array $records): array
    {
        $program_name = '';
        $not_started_records_number =
        $in_progress_records_number =
        $completed_records_number = 0;
        $records_length = count($records);

        foreach ($records as $record) {
            $program_id = $record->program_id;
            $program_name = $record->program_name;
            switch ($record->completion_status) {
                case self::COMPLETION_STATUS_NOT_STARTED:
                    $not_started_records_number++;
                    break;
                case self::COMPLETION_STATUS_IN_PROGRESS:
                    $in_progress_records_number++;
                    break;
                case self::COMPLETION_STATUS_COMPLETED:
                    $completed_records_number++;
                    break;
            }
        }

        if (!count($records)) {
            $rate = '0%';
            $program_name = self::get_program_name_by_id($program_id) ?? '';
        } else {
            $rate = self::convert_float_to_percentage($completed_records_number, $records_length) . '%';
        }


        return [
            'key' => (int)$program_id,
            'campaign' => $program_name,
            'rate' => $rate,
            'progress' => [
                [
                    'name' => 'Completed',
                    'value' => self::convert_float_to_percentage($completed_records_number, $records_length),
                    'color' => self::PROGRESS_BAR_COLOR_COMPLETED,
                ],
                [
                    'name' => 'In progress',
                    'value' => self::convert_float_to_percentage($in_progress_records_number, $records_length),
                    'color' => self::PROGRESS_BAR_COLOR_IN_PROGRESS,
                ],
                [
                    'name' => 'Not started',
                    'value' => self::convert_float_to_percentage($not_started_records_number, $records_length),
                    'color' => self::PROGRESS_BAR_COLOR_NOT_STARTED,
                ]
            ]
        ];
    }



    private static function get_not_started_records_count_in_program(int $program_id = null, int $course_id = null): int
    {
        global $DB;
        $count_select_sql = 'SELECT COUNT(DISTINCT ccom.id) ';
        $not_started_condition_sql = ' AND ccom.timestarted = 0 AND ccom.timeenrolled <> 0 ';
        $sql = self::get_filter_program_records_sql($program_id, $course_id, $not_started_condition_sql, $count_select_sql);
        $params = [
            'programid' => $program_id,
            'courseid' => $course_id,
        ];
        return $DB->count_records_sql($sql, $params);
    }

    private static function get_in_progress_records_count_in_program(int $program_id = null, int $course_id = null): int
    {
        global $DB;
        $count_select_sql = 'SELECT COUNT(DISTINCT ccom.id) ';
        $in_progress_condition_sql = ' AND ccom.timecompleted IS NULL AND ccom.timestarted <> 0 ';
        $sql = self::get_filter_program_records_sql($program_id, $course_id, $in_progress_condition_sql, $count_select_sql);
        $params = [
            'programid' => $program_id,
            'courseid' => $course_id,
        ];
        return $DB->count_records_sql($sql, $params);
    }

    private static function get_completed_records_count_in_program(int $program_id = null, int $course_id = null): int
    {
        global $DB;
        $count_select_sql = 'SELECT COUNT(DISTINCT ccom.id) ';
        $completed_condition_sql = ' AND ccom.timecompleted IS NOT NULL ';
        $sql = self::get_filter_program_records_sql($program_id, $course_id, $completed_condition_sql, $count_select_sql);
        $params = [
            'programid' => $program_id,
            'courseid' => $course_id,
        ];
        return $DB->count_records_sql($sql, $params);
    }

    /**
     * dump filtering program records sql based on different condition
     * @param int|null $program_id
     * @param int|null $course_id
     * @param string $filter_condition
     * @param string $select_data
     * @return string
     * @throws \moodle_exception
     */
    private static function get_filter_program_records_sql(int $program_id = null, int $course_id = null, string $filter_condition = '', string $select_data = ''): string
    {
        try {
            $records_sql = $select_data ?: "
                    SELECT DISTINCT ccom.id AS 'record_id', 
                    u.id AS 'user_id',
                    ccat.id AS 'category_id', 
                    c.id AS 'course_id',
                    lpc.programid as 'program_id',
                    CASE 
                        WHEN ccom.timestarted = 0 AND ccom.timeenrolled <> 0 then 'NOT STARTED'
                        WHEN ccom.timecompleted IS NULL AND ccom.timestarted <> 0 THEN 'IN PROGRESS'
                        WHEN ccom.timecompleted IS NOT NULL THEN 'COMPLETED'
                        END AS 'completion_status' 
                ";

            $records_sql .= "
                    FROM {user} AS u 
                      JOIN {course_completions} AS ccom ON u.id = ccom.userid
                      JOIN {course} AS c ON c.id = ccom.course
                      JOIN {course_categories} AS ccat ON c.category = ccat.id
                      JOIN {" . self::PROGRAM_COURSES_TABLE_NAME . "} AS lpc ON c.id = lpc.courseid
                    WHERE lpc.programid=:programid 
                ";

            if ($course_id) {
                $records_sql .= " AND c.id = :courseid ";
            }

            if ($filter_condition) {
                $records_sql .= $filter_condition;
            }
            return $records_sql;
        } catch (\Exception $e) {
            return '';
        }
    }

    public static function count_records_by_sql(string $table, string $sql): int
    {
        global $DB;

        if (self::check_table_exist($table)) {
            $result = $DB->count_records_sql($sql);
        }
        return $result ?? 0;
    }

    private static function check_table_exist(string $name): bool
    {
        global $DB;
        return $DB->get_manager()->table_exists($name);
    }

    private static function get_course_by_id(int $id): ?\stdClass
    {
        global $DB;
        if (!self::check_table_exist('course')) {
            return null;
        }
        return $DB->get_record('course', ['id' => $id]);
    }

    private static function get_program_name_by_id(int $id): string
    {
        global $DB;
        if (!self::check_table_exist(self::PROGRAM_TABLE_NAME)) {
            return '';
        }
        return $DB->get_record(self::PROGRAM_TABLE_NAME, ['id' => $id], 'fullname')->fullname;
    }

    private static function convert_float_to_percentage(int $numerator, int $denominator): int
    {
        return $denominator ? (int)round(($numerator / $denominator) * 100 ) : 0;
    }
}