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
 * The program_created event class.
 *
 * @package    local_program
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_program\event;


use core\event\base;
use local_program\program;

defined('MOODLE_INTERNAL') || die();

class program_created extends base
{

    protected function init()
    {
        $this->data['objecttable'] = program::TABLE;
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Creates an instance of the event from an object
     * @param program $program
     * @return program_created
     */
    public static function create_from_object(program $program): program_created
    {
        $event = static::create([
            'context' => \context_system::instance(),
            'objectid' => $program->get('id'),
        ]);
        $event->add_record_snapshot(program::TABLE, $program->to_record());
        return $event;
    }
}