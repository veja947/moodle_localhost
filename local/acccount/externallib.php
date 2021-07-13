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
 *
 * @package    local_acccount
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

class local_acccount_external extends external_api {


    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function test_parameters() {
        return new external_function_parameters(
            array(
                'acccountids' => new external_multiple_structure(
                    new external_value(
                        PARAM_INT,
                        'Acccount ID'),
                    'List of group id. A group id is an integer.'),
            )
        );
    }

    /**
     * Get acccounts definition specified by ids
     *
     * @param array $acccountids arrays of acccount ids
     * @return array of acccount objects
     * @since Moodle 2.2
     */
    public static function test(array $acccountids) {
        $params = self::validate_parameters(self::test_parameters(), array('acccountids'=>$acccountids));
        $transaction = $DB->start_delegated_transaction(); //If an exception is thrown in the below code, all DB queries in this code will be rollback.
//        $acccounts = [];

//        foreach ($params['acccountids'] as $acccountid) {
//            // validate params
//            $group = groups_get_group($groupid, 'id, courseid, name, idnumber, description, descriptionformat, enrolmentkey', MUST_EXIST);
//
//            // now security checks
//            $context = context_course::instance($group->courseid, IGNORE_MISSING);
//            try {
//                self::validate_context($context);
//            } catch (Exception $e) {
//                $exceptionparam = new stdClass();
//                $exceptionparam->message = $e->getMessage();
//                $exceptionparam->courseid = $group->courseid;
//                throw new moodle_exception('errorcoursecontextnotvalid' , 'webservice', '', $exceptionparam);
//            }
//            require_capability('moodle/course:managegroups', $context);
//
//            list($group->description, $group->descriptionformat) =
//                external_format_text($group->description, $group->descriptionformat,
//                    $context->id, 'group', 'description', $group->id);
//
//            $groups[] = (array)$group;
//        }

        $acccounts = array('hello','world','!');


        return $acccounts;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.2
     */
    public static function test_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'record id'),
                    'name' => new external_value(PARAM_TEXT, 'multilang compatible name, course unique'),
                )
            )
        );
    }
}