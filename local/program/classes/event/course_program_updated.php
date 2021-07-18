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

namespace local_program\event;


use core\event\base;
use local_program\program_course;

defined('MOODLE_INTERNAL') || die();

class course_program_updated extends base
{

    protected function init()
    {
        $this->data['objecttable'] = program_course::TABLE;
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public static function create_from_object(program_course $programCourse, \stdClass $oldRecord): course_program_updated {
        global $USER;
        $params = [
            'context' => \context_system::instance(),
            'objectid' => $programCourse->get('id'),
            'relateduserid' => $USER->id,
            'other' => [
                'programid' => $programCourse->get('programid'),
                'courseid' => $programCourse->get('courseid'),
            ]
        ];

        $event = static::create($params);
        $event->add_record_snapshot(program_course::TABLE, $programCourse->to_record());
        return $event;
    }
}