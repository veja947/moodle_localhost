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
 * @package    local_tenant
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tenant;


use local_tenant\manager;

defined('MOODLE_INTERNAL') || die();

class tenancy
{
    /**
     * Returns list of tenants in the system
     *
     * @return tenant[]
     */
    public static function get_tenants() : array {
        global $DB;
        $cache = \cache::make('local_tenant', 'tenants');

        if (!($tenants = $cache->get('list'))) {
            $tenants = $DB->get_records('local_tenant', ['archived' => 0],
                'isdefault DESC, sortorder, id', 'id, name, idnumber, isdefault, sitename, categoryid, '.
                'siteshortname, useloginurlid, useloginurlidnumber, timemodified');
            $first = reset($tenants);
            if (!$tenants || !$first->isdefault) {
                // Create default tenant.
                $tenant = (new manager())->create_tenant((object)[
                    'name' => 'Default tenant',
                    'isdefault' => 1]);
                $tenants = [$tenant->get('id') => $tenant->to_record()] + $tenants;
            }
            $cache->set('list', $tenants);
        }
        return $tenants;
    }

    /**
     * Id of the tenant user belongs to
     *
     * @param int $userid userid, if omitted current user
     * @return int
     */
    public static function get_tenant_id(?int $userid = null) : int {
        global $USER;

        if (!self::is_site_multi_tenant()) {
            return self::get_default_tenant_id();
        }

        // User is logged in.
        $userid = $userid ?: ($USER ? $USER->id : 0);
        $cache = \cache::make('local_tenant', 'mytenant');

        // First make sure that the tenant for the current user is in the cache.
        $cacheidx = 'tenantid-' . $USER->id;
        if (!($mytenantid = $cache->get($cacheidx))) {
            $mytenantid = self::get_tenant_id_int($USER->id);
            $cache->set($cacheidx, $mytenantid);
        }

        // Requesting tenant for the current user.
        if ($userid == $USER->id) {
            return $mytenantid;
        }

        // Requesting tenant for another user.
        $otherusers = $cache->get('otherusers-'.$mytenantid);
        $otherusers = is_array($otherusers) ? $otherusers : [];
        if (in_array($userid, $otherusers)) {
            return $mytenantid;
        }

        $usertenantid = self::get_tenant_id_int($userid);
        if ($usertenantid == $mytenantid) {
            $otherusers[] = $userid;
            $cache->set('otherusers-'.$mytenantid, $otherusers);
        }
        return $usertenantid;
    }


    /**
     * Check if site is configured to have multiple tenants
     *
     * @return bool
     */
    public static function is_site_multi_tenant() : bool {
        $tenants = self::get_tenants();
        return count($tenants) > 1;
    }

    /**
     * Returns the default tenant in the system, all unallocated users belong to this tenant
     *
     * @return int
     */
    public static function get_default_tenant_id() : int {
        $tenants = self::get_tenants();
        $tenantid = key($tenants);
        return $tenantid;
    }

    /**
     * Does an SQL query to retrieve tenant id for the given user
     *
     * @param int $userid
     * @return int
     */
    protected static function get_tenant_id_int(int $userid) : int {
        global $DB;
        $tenantid = $DB->get_field_sql("
            SELECT t.id
            FROM {local_tenant_user} tu
            JOIN {local_tenant} t ON tu.tenantid = t.id AND t.archived = 0
            WHERE tu.userid = ?", [$userid]);

        return $tenantid ?? self::get_default_tenant_id();
    }
}