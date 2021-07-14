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
 * Class manager and methods for managing the list of tenants
 *
 * @package    local_acccount
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_acccount;

use local_acccount\event\acccount_created;

defined('MOODLE_INTERNAL') || die();

class manager
{
    /**
     * Returns list of acccounts in the system
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

    public function get_acccounts_id_and_name(array $acccounts): array {
        $result = [];
        foreach ($acccounts as $acccount) {
            $result[$acccount->get('id')] = [
                'id' => $acccount->get('id'),
                'name' => $acccount->get('name'),
            ];
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
     * Resets acccounts list cache
     */
    protected function reset_acccounts_cache() {
        \cache_helper::purge_by_event('acccountsmodified');
        \cache::make('local_acccount', 'mytenant')->purge();
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