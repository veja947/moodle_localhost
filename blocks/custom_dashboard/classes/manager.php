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

    public static function get_program_statics(int $programid=null): array
    {
        $students_number = self::get_students_count_in_program($programid);
        $unstarted_records_number = self::get_unstarted_records_count_in_program($programid);
        $in_progress_records_number = self::get_in_progress_records_count_in_program($programid);
        $completed_records_number = self::get_completed_records_count_in_program($programid);

        $result = [
            'total_students_number' => $students_number,
            'unstarted_number' => $unstarted_records_number,
            'in_progress_number' => $in_progress_records_number,
            'completed_number' => $completed_records_number,
        ];

        return $result;
    }

    private static function get_students_count_in_program(int $program_id=null, int $course_id=null): int
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

    private static function get_unstarted_records_count_in_program(int $program_id=null, int $course_id=null): int
    {
        global $DB;
        $count_select_sql = 'SELECT COUNT(DISTINCT ccom.id) ';
        $unstarted_condition_sql = ' AND ccom.timestarted = 0 AND ccom.timeenrolled <> 0 ';
        $sql = self::get_filter_program_records_sql($program_id, $course_id, $unstarted_condition_sql, $count_select_sql);
        $params = [
            'programid' => $program_id,
            'courseid' => $course_id,
        ];
        return $DB->count_records_sql($sql, $params);
    }

    private static function get_in_progress_records_count_in_program(int $program_id=null, int $course_id=null): int
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

    private static function get_completed_records_count_in_program(int $program_id=null, int $course_id=null): int
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
     * @throws \moodle_exception
     * @return string
     */
    private static function get_filter_program_records_sql(int $program_id=null, int $course_id=null, string $filter_condition='', string $select_data=''): string
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
                    FROM mdl_user AS u 
                      JOIN mdl_course_completions AS ccom ON u.id = ccom.userid
                      JOIN mdl_course AS c ON c.id = ccom.course
                      JOIN mdl_course_categories AS ccat ON c.category = ccat.id
                      JOIN mdl_local_program_course AS lpc ON c.id = lpc.courseid
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
        $result = 0;

        if (self::check_table_exist($table)) {
            $result = $DB->count_records_sql($sql);
        }
        return $result;
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
}