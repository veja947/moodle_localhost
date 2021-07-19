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
use local_acccount\event\acccount_deleted;
use local_acccount\event\acccount_updated;
use local_acccount\event\user_acccount_created;
use local_acccount\event\user_acccount_updated;

defined('MOODLE_INTERNAL') || die();

class manager
{
    const ACCCOUNT_ACTION_ARCHIVE = 'archive';
    const ACCCOUNT_ACTION_RESTORE = 'restore';
    const ACCCOUNT_ACTION_DELETE = 'delete';

    /**
     * Returns list of active acccounts in the system
     *
     * @return acccount[]
     */
    public function get_active_acccounts(): array
    {
        global $DB;
        $cache = \cache::make(acccount::TABLE, 'acccounts');
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
    public function get_archived_acccounts(): array
    {
        global $DB;
        $cache = \cache::make(acccount::TABLE, 'acccounts');
        if (($archievedAcccounts = $cache->get('archived')) == false) {
            $records = $DB->get_records(acccount::TABLE,
                ['archived' => 1],
                'timearchived DESC');
            $archievedAcccounts = [];
            foreach ($records as $record) {
                $archievedAcccounts[$record->id] = new acccount(0, $record);
            }
            $cache->set('archived', $archievedAcccounts);
        }
        return $archievedAcccounts ?? [];
    }

    public function get_acccounts_display_array(array $acccounts): array
    {
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
    public function create_acccount(\stdClass $data): acccount
    {
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
    public function update_acccount(acccount $acccount, \stdClass $newData): acccount
    {
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
     * Archives an acccount
     *
     * @param int $id
     * @return acccount
     */
    public function archive_acccount(int $id): acccount
    {
        if ($acccount = $this->get_archived_acccount_by_id($id, null, false)) {
            return $acccount;
        }
        $acccount = $this->get_active_acccount_by_id($id);
        return $this->update_acccount($acccount, (object)[
            'archived' => 1,
            'timearchived' => time(),
        ]);
    }

    /**
     * Restore an acccount
     *
     * @param int $id
     * @return acccount
     */
    public function restore_acccount(int $id): acccount
    {
        if ($acccount = $this->get_active_acccount_by_id($id, null, false)) {
            return $acccount;
        }
        $acccount = $this->get_archived_acccount_by_id($id);
        return $this->update_acccount($acccount, (object)[
            'archived' => 0,
            'timearchived' => null,
        ]);
    }

    /**
     * Deletes archived acccount
     *
     * @param int $id
     * @return acccount
     * @throws \moodle_exception
     */
    public function delete_acccount(int $id): ?acccount
    {
        global $DB;
        if (!$DB->get_record(acccount::TABLE, ['id' => $id])) {
            return null;
        }

        $acccount = $this->get_archived_acccount_by_id($id);

        // delete acccount users relationship
        $DB->delete_records(acccount_user::TABLE, ['acccountid' => $id]);

        // delete acccount record, trigger event
        $event = acccount_deleted::create_from_object($acccount);
        $acccount->delete();
        $event->trigger();

        $this->reset_acccounts_cache();
        return $acccount;
    }

    /**
     * Update user's acccount value
     *
     * @param int $userid
     * @param int $acccountid
     */
    public function update_user_acccount(int $userid, int $acccountid): ?acccount_user
    {
        if (isguestuser($userid)) {
            return null;
        }

        $userAcccount = acccount_user::create_for_user($userid);
        $oldRecord = $userAcccount->to_record();

        $userAcccount->set('acccountid', $acccountid);
        $userAcccount->save();
        $oldRecord->id ?
            user_acccount_updated::create_from_object($userAcccount, $oldRecord)->trigger()
            : user_acccount_created::create_from_object($userAcccount)->trigger();

        // TODO: user roles update

        $cache = \cache::make(acccount::TABLE, 'myacccount');
        $cacheIdx = 'acccountid-' . $userid;
        $cache->delete($cacheIdx);

        return $userAcccount;
    }

    /**
     * Retrieves an active acccount by id
     *
     * @param int $id
     * @param \moodle_url $exceptionlink (optional) link to use in exception message
     * @return acccount
     * @throws \moodle_exception
     */
    public function get_active_acccount_by_id(int $id, \moodle_url $exceptionlink = null, bool $showexception = true): ?acccount
    {
        $acccounts = $this->get_active_acccounts();
        if (array_key_exists($id, $acccounts)) {
            return $acccounts[$id];
        }
        if ($showexception) {
            throw new \moodle_exception('acccountnotfound', acccount::TABLE,
                $exceptionlink ?: self::get_base_url());
        }
        return null;
    }

    /**
     * Retrieves an archived acccount by id
     *
     * @param int $id
     * @param \moodle_url $exceptionlink (optional) link to use in exception message
     * @return acccount
     * @throws \moodle_exception
     */
    public function get_archived_acccount_by_id(int $id, \moodle_url $exceptionlink = null, bool $showexception = true): ?acccount
    {
        $acccounts = $this->get_archived_acccounts();
        if (array_key_exists($id, $acccounts)) {
            return $acccounts[$id];
        }
        if ($showexception) {
            throw new \moodle_exception('acccountnotfound', acccount::TABLE,
                $exceptionlink ?: self::get_base_url());
        }
        return null;
    }

    public function getLearnersDisplayArray(int $acccountid=null): array
    {
        global $DB;

        $results = $DB->get_records_sql('
            SELECT u.email, u.firstname, u.lastname, lau.id as acccountid FROM {user} u
            LEFT JOIN {local_acccount_user} lau ON lau.userid = u.id
            WHERE :addcondition
        ', [
            'addcondition' => isset($acccountid) ? 'lau.id=' . $acccountid : true,
        ]);
        return array_values($results);
    }

    /**
     * Resets acccounts list cache
     */
    protected function reset_acccounts_cache()
    {
        \cache_helper::purge_by_event('acccountsmodified');
        \cache::make(acccount::TABLE, 'myacccount')->purge();
        \cache::make(acccount::TABLE, 'acccounts')->purge();
    }

    /**
     * Acccount URL to view acccounts list
     * @return \moodle_url
     */
    public static function get_base_url(): \moodle_url
    {
        return new \moodle_url('/local/acccount/manage.php');
    }

    /**
     * Acccount URL to edit acccount form
     * @return \moodle_url
     */
    public static function get_editor_url(): \moodle_url
    {
        return new \moodle_url('/local/acccount/edit.php');
    }

    /**
     * Acccount URL to view learners list
     * @return \moodle_url
     */
    public static function get_learners_url(): \moodle_url
    {
        return new \moodle_url('/local/acccount/learners.php');
    }

    /**
     * Acccount URL to view learners roles
     * @return \moodle_url
     */
    public static function get_roles_url(): \moodle_url
    {
        return new \moodle_url('/local/acccount/roles.php');
    }

    /**
     * Acccount URL to assign learners roles
     * @return \moodle_url
     */
    public static function get_assign_roles_url(): \moodle_url
    {
        return new \moodle_url('/local/acccount/assign.php');
    }
}