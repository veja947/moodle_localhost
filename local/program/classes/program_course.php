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
 * @package    local_program
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_program;


use core\persistent;

defined('MOODLE_INTERNAL') || die();

class program_course extends persistent
{
    /** The table name. */
    const TABLE = 'local_program_course';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'programid' => array(
                'type' => PARAM_INT,
                'description' => 'Program id',
            ),
            'courseid' => array(
                'type' => PARAM_INT,
                'description' => 'Course id',
            ),
        );
    }

    /**
     * Creates an instance for a given course
     *
     * @param int $courseid
     * @return program_course
     */
    public static function create_for_course(int $courseid) : self {
        global $DB;
        $record = $DB->get_record(self::TABLE, ['courseid' => $courseid]);
        if ($record) {
            return new self(0, $record);
        } else {
            return new self(0, (object)['userid' => $courseid]);
        }
    }
}