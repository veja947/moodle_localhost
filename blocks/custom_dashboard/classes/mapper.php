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
 * Class mapper for getting proper data from DB
 *
 * @package    block_custom_dashboard
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_custom_dashboard;

use block_custom_dashboard\customize\setting;

defined('MOODLE_INTERNAL') || die();

require_once("{$CFG->libdir}/externallib.php");
require_once("{$CFG->libdir}/completionlib.php");

class mapper
{
    function __construct()
    {
    }

    public function get_campaign_dashboard_statistics(int $program_id = null, int $course_id = null): array
    {
        global $DB;
        $results = $table_records = $selector_records = $campaign_modules_record = array();

        $sql = "SELECT DISTINCT ccom.id AS 'record_id', 
                    u.id AS 'user_id',
                    ccat.id AS 'category_id', 
                    c.id AS 'course_id',
                    c.fullname AS 'course_name',
                    lpc.programid AS 'program_id',
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
                      JOIN {" . setting::PROGRAM_COURSES_TABLE_NAME . "} AS lpc ON c.id = lpc.courseid
                      JOIN {" . setting::PROGRAM_TABLE_NAME ."} AS lp ON lp.id = lpc.programid 
                ";

        if ($program_id) {
            $sql .= " WHERE lpc.programid=:programid ";
        }

        if ($course_id) {
            $sql .= " AND c.id = :courseid ";
        }

        $params = [
            'programid' => $program_id,
            'courseid' => $course_id,
        ];

        $rs = $DB->get_recordset_sql($sql, $params);

        if (!$rs->valid()) {
            return $results;
        }

        foreach ($rs as $record) {
            $program_id = (int)$record->program_id;
            $course_id = (int)$record->course_id;
            if (!isset($selector_records[$program_id])) {
                $selector_records[$program_id] = $record->program_name;
            }

            if (!isset($table_records[$program_id])) {
                $table_records[$program_id] = [
                    'key' => $program_id,
                    'campaign' => $record->program_name,
                    'students' => [$record->user_id],
                    'not_started_number' => $record->completion_status === setting::COMPLETION_STATUS_NOT_STARTED ? 1: 0,
                    'in_progress_number' => $record->completion_status === setting::COMPLETION_STATUS_IN_PROGRESS ? 1: 0,
                    'completed_number' => $record->completion_status === setting::COMPLETION_STATUS_COMPLETED ? 1: 0,
                ];
            } else {
                array_push($table_records[$program_id]['students'], $record->user_id);
                $table_records[$program_id][$record->completion_status . '_number']++;
            }

            if (!isset($campaign_modules_record[$program_id][$course_id])) {
                $campaign_modules_record[$program_id][$course_id] = [
                    'key' => $program_id,
                    'campaign' => $record->program_name,
                    'module_name' => $record->course_name,
                    'module_id' => $record->course_id,
                    'students' => [$record->user_id],
                    'not_started_number' => $record->completion_status === setting::COMPLETION_STATUS_NOT_STARTED ? 1: 0,
                    'in_progress_number' => $record->completion_status === setting::COMPLETION_STATUS_IN_PROGRESS ? 1: 0,
                    'completed_number' => $record->completion_status === setting::COMPLETION_STATUS_COMPLETED ? 1: 0,
                ];
            } else {
                array_push($campaign_modules_record[$program_id][$course_id]['students'], $record->user_id);
                $campaign_modules_record[$program_id][$course_id][$record->completion_status . '_number']++;
            }
        }
        $rs->close();
        return [
            'table_records' => $table_records,
            'selector_records' => $selector_records,
            'module_records' => $campaign_modules_record,
        ];
    }

    private function check_table_exist(string $name): bool
    {
        global $DB;
        return $DB->get_manager()->table_exists($name);
    }
}