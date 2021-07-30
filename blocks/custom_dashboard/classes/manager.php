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

class manager
{
    const MODULE_STATE_IN_PROGRESS = 'inprogress';
    const MODULE_STATE_COMPLETED = 'finished';

    public static function get_students(int $programid=null, int $courseid=null): array
    {
        $users = search_users($courseid, null, '');
        return $users;
    }

    public static function get_all_modules(): int
    {
        // moodle base course completion table
        if (self::check_table_exist('quiz')) {
            $sql = "SELECT COUNT(DISTINCT id) FROM
                    {quiz} q
                   ";
            $result = self::count_records_by_sql('quiz', $sql);
        } else {
            // TODO: moodle workplace program / course completion
        }


        return $result ?? 0;
    }

    public static function get_modules_in_progress(int $programid=null, int $courseid=null): int
    {
        // moodle base course completion table
        if (self::check_table_exist('quiz_attempts')) {
            $sql = "SELECT COUNT(DISTINCT id) FROM
                    {quiz_attempts} qa
                    WHERE qa.state ='" . self::MODULE_STATE_IN_PROGRESS . "'";
            $result = self::count_records_by_sql('quiz_attempts', $sql);
        } else {
            // TODO: moodle workplace program / course completion
        }

        return $result ?? 0;
    }

    public static function get_modules_completed(int $programid=null, int $courseid=null): int
    {
        // moodle base course completion table
        if (self::check_table_exist('quiz_attempts')) {
            $sql = "SELECT COUNT(DISTINCT id) FROM
                    {quiz_attempts} qa
                    WHERE qa.state ='" . self::MODULE_STATE_COMPLETED . "'";
            $result = self::count_records_by_sql('quiz_attempts', $sql);
        } else {
            // TODO: moodle workplace program / course completion
        }

        return $result ?? 0;
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

    private static function get_course_by_id(int $id): ?\stdClass
    {
        global $DB;
        if (!self::check_table_exist('course')) {
            return null;
        }
        return $DB->get_record('course', ['id' => $id]);
    }

    private static function check_table_exist(string $name): bool
    {
        global $DB;
        return $DB->get_manager()->table_exists($name);
    }
}