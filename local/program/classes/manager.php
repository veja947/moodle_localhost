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
use local_program\event\program_deleted;
use local_program\event\program_updated;

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
    public function get_active_programs(): array
    {
        global $DB;
        $cache = \cache::make(program::TABLE, 'programs');
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

    public function get_active_program_by_id(int $id, \moodle_url $exceptionlink = null, bool $showexception = true): ?program
    {
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
    public function get_archived_programs(): array
    {
        global $DB;
        $cache = \cache::make(program::TABLE, 'programs');
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

    public function get_archived_program_by_id(int $id, \moodle_url $exceptionlink = null, bool $showexception = true): ?program
    {
        $programs = $this->get_archived_programs();
        if (array_key_exists($id, $programs)) {
            return $programs[$id];
        }
        if ($showexception) {
            throw new \moodle_exception('programnotfound', program::TABLE,
                $exceptionlink ?: self::get_base_url());
        }
        return null;
    }

    public function get_programs_display_array(array $programs): array
    {
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
    public function create_program(\stdClass $data): program
    {
        global $DB;
        $program = new program(0, $data);
        $program->create();
        program_created::create_from_object($program)->trigger();
        $this->reset_programs_cache();
        return $program;
    }

    public function update_program(program $program, \stdClass $newData): program
    {
        $oldRecord = $program->to_record();
        foreach ($newData as $key => $value) {
            if (program::has_property($key) && $key !== 'id') {
                $program->set($key, $value);
            }
        }
        $program->save();
        program_updated::create_from_object($program, $oldRecord)->trigger();
        $this->reset_programs_cache();
        return $program;
    }

    /**
     * Archives an program
     *
     * @param int $id
     * @return program
     */
    public function archive_program(int $id): program
    {
        if ($program = $this->get_archived_program_by_id($id, null, false)) {
            return $program;
        }
        $program = $this->get_active_program_by_id($id);
        return $this->update_program($program, (object)[
            'archived' => 1,
            'timearchived' => time(),
        ]);
    }

    /**
     * Restore an program
     *
     * @param int $id
     * @return program
     */
    public function restore_program(int $id): program
    {
        if ($program = $this->get_active_program_by_id($id, null, false)) {
            return $program;
        }
        $program = $this->get_archived_program_by_id($id);
        return $this->update_program($program, (object)[
            'archived' => 0,
            'timearchived' => null,
        ]);
    }

    public function delete_program(int $id): ?program
    {
        global $DB;
        if (!$DB->get_record(program::TABLE, ['id' => $id])) {
            return null;
        }

        $program = $this->get_archived_program_by_id($id);

        // delete program_courses relationship
        $DB->delete_records(program_course::TABLE, ['programid' => $id]);

        // delete program record, trigger event
        $event = program_deleted::create_from_object($program);
        $program->delete();
        $event->trigger();

        $this->reset_programs_cache();
        return $program;
    }


    /**
     * Base URL to view programs list
     * @return \moodle_url
     */
    public static function get_base_url(): \moodle_url
    {
        return new \moodle_url('/local/program/manage.php');
    }

    /**
     * Editor URL to view program form
     * @return \moodle_url
     */
    public static function get_editor_url(): \moodle_url
    {
        return new \moodle_url('/local/program/edit.php');
    }

    /**
     * Resets programs list cache
     */
    protected function reset_programs_cache()
    {
        \cache_helper::purge_by_event('programsmodified');
        \cache::make(program::TABLE, 'myprogram')->purge();
        \cache::make(program::TABLE, 'programs')->purge();
    }
}