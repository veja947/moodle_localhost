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
 * Class manager and methods for managing the list of acccounts
 *
 * @package    local_acccount
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_acccount;

use local_acccount\event\acccount_created;
use local_acccount\event\acccount_updated;

defined('MOODLE_INTERNAL') || die();

class manager
{
    /**
     * Returns list of active acccounts in the system
     *
     * @return acccount[]
     */
    public function get_acccounts(): array {
        global $DB;
        $cache = \cache::make('local_acccount', 'acccounts');
        if (!($acccounts = $cache->get('active'))) {
            $acccountsRecords = $DB->get_records(acccount::TABLE, ['archived' => 0]);
            $acccounts = [];
            foreach ($acccountsRecords as $record) {
                $acccounts[$record->id] = new acccount(0, $record);
            }

            if (empty($acccounts)) {
                // create the default acccount
                $defaultAcccount = $this->create_acccount((object)[
                    'name' => 'Default Acccount',
                    'isdefault' => 1
                ]);
                $acccounts[$defaultAcccount->get('id')] = $defaultAcccount;
            }
            $cache->set('active', $acccounts);
        }
        return $acccounts ?? [];
    }

    /**
     * Returns list of archived acccounts in the system
     *
     * @return acccount[]
     */
    public function get_archived_acccounts(): array {
        global $DB;
        $cache = \cache::make('local_acccount', 'acccounts');
        if (($archievedAcccounts = $cache->get('archived')) == false) {
            $records = $DB->get_records(acccount::TABLE, ['archived' => 1], 'timearchived DESC');
            $archievedAcccounts = [];
            foreach ($records as $record) {
                $archievedAcccounts[$record->id] = new acccount(0, $record);
            }
            $cache->set('archived', $archievedAcccounts);
        }
        return $archievedAcccounts ?? [];
    }

    public function get_acccounts_display_array(array $acccounts): array {
        $result = [];
        foreach ($acccounts as $acccount) {
            $result[$acccount->get('id')] = $acccount->get_properties_display();
        }
        return $result;
    }

    /**
     * Creates a new acccount
     *
     * @param \stdClass $data
     */
    public function create_acccount(\stdClass $data): acccount {
        global $DB;
        $acccount = new acccount(0, $data);
        $acccount->create();
        if (!$acccount->get('isdefault')) {
            acccount_created::create_from_object($acccount)->trigger();
        }
        $this->reset_acccounts_cache();
        return $acccount;
    }

    /**
     * Updates an exsiting acccount
     *
     * @params acccount $acccount
     * @param \stdClass $newData
     *
     */
    public function update_acccount(acccount $acccount, \stdClass $newData): acccount {
        $oldRecord = $acccount->to_record();
        foreach ($newData as $key => $value) {
            if (acccount::has_property($key) && $key !== 'id') {
                $acccount->set($key, $value);
            }
        }
        $acccount->save();
        acccount_updated::create_from_object($acccount, $oldRecord)->trigger();
        $this->reset_acccounts_cache();
        return $acccount;
    }



    /**
     * Retrieves an active acccount by id
     *
     * @param int $id
     * @param \moodle_url $exceptionlink (optional) link to use in exception message
     * @return acccount
     * @throws \moodle_exception
     */
    public function get_acccount_by_id(int $id, \moodle_url $exceptionlink = null): acccount {
        $acccounts = $this->get_acccounts();
        if (array_key_exists($id, $acccounts)) {
            return $acccounts[$id];
        }
        throw new \moodle_exception('acccountnotfound', 'local_acccount',
            $exceptionlink ?: self::get_base_url());
    }

    /**
     * Resets acccounts list cache
     */
    protected function reset_acccounts_cache() {
        \cache_helper::purge_by_event('acccountsmodified');
        \cache::make('local_acccount', 'myacccount')->purge();
        \cache::make('local_acccount', 'acccounts')->purge();
    }

    /**
     * Base URL to view acccounts list
     * @return \moodle_url
     */
    public static function get_base_url() : \moodle_url {
        return new \moodle_url('/local/acccount/manage.php');
    }

    /**
     * Editor URL to view acccount form
     * @return \moodle_url
     */
    public static function get_editor_url() : \moodle_url {
        return new \moodle_url('/local/acccount/edit.php');
    }
}