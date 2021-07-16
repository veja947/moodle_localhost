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
 * Class manager and methods for managing the list of programs
 *
 * @package    local_program
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_program;

use local_program\event\program_created;

defined('MOODLE_INTERNAL') || die();
class manager
{
    const PROGRAM_ACTION_ARCHIVE = 'archive';
    const PROGRAM_ACTION_RESTORE = 'restore';
    const PROGRAM_ACTION_DELETE = 'delete';

    /**
     * Returns list of active programs in the system
     *
     * @return program[]
     */
    public function get_active_programs(): array {
        global $DB;
        $cache = \cache::make('local_program', 'programs');
        if (!($programs = $cache->get('active'))) {
            $activeRecords = $DB->get_records(program::TABLE, ['archived' => 0]);
            $programs = [];
            foreach ($activeRecords as $record) {
                $programs[$record->id] = new program(0, $record);
            }
            $cache->set('active', $programs);
        }
        return $programs ?? [];
    }

    public function get_active_program_by_id(int $id, \moodle_url $exceptionlink = null, bool $showexception = true): ?program {
        $programs = $this->get_active_programs();
        if (array_key_exists($id, $programs)) {
            return $programs[$id];
        }
        if ($showexception) {
            throw new \moodle_exception('programnotfound', 'local_program',
                $exceptionlink ?: self::get_base_url());
        }
        return null;
    }

    /**
     * Returns list of archived programs in the system
     *
     * @return program[]
     */
    public function get_archived_programs(): array {
        global $DB;
        $cache = \cache::make('local_program', 'programs');
        if (($archievedPrograms = $cache->get('archived')) == false) {
            $records = $DB->get_records(program::TABLE, ['archived' => 1], 'timearchived DESC');
            $archievedPrograms = [];
            foreach ($records as $record) {
                $archievedPrograms[$record->id] = new program(0, $record);
            }
            $cache->set('archived', $archievedPrograms);
        }
        return $archievedPrograms ?? [];
    }

    public function get_programs_display_array(array $programs): array {
        $result = [];
        foreach ($programs as $program) {
            $result[$program->get('id')] = $program->get_properties_display();
        }
        return $result;
    }

    /**
     * Creates a new program
     *
     * @param \stdClass $data
     */
    public function create_program(\stdClass $data): program {
        global $DB;
        $program = new program(0, $data);
        $program->create();
        program_created::create_from_object($program)->trigger();
        $this->reset_programs_cache();
        return $program;
    }


    /**
     * Base URL to view programs list
     * @return \moodle_url
     */
    public static function get_base_url() : \moodle_url {
        return new \moodle_url('/local/program/manage.php');
    }

    /**
     * Editor URL to view program form
     * @return \moodle_url
     */
    public static function get_editor_url() : \moodle_url {
        return new \moodle_url('/local/program/edit.php');
    }

    /**
     * Resets programs list cache
     */
    protected function reset_programs_cache() {
        \cache_helper::purge_by_event('programsmodified');
        \cache::make('local_program', 'myprogram')->purge();
        \cache::make('local_program', 'programs')->purge();
    }
}