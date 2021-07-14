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


use local_tenant\event\tenant_created;

defined('MOODLE_INTERNAL') || die();

class manager
{
    /**
     * Returns list of tenants in the system
     *
     * @return tenant[]
     */
    public function get_tenants() : array {
        global $DB;
        $cache = \cache::make('local_tenant', 'tenants');
        if (!($tenants = $cache->get('active'))) {
            $tenantrecords = $DB->get_records(tenant::TABLE, ['archived' => 0],
                'isdefault DESC, sortorder, id');
            $tenants = [];
            foreach ($tenantrecords as $tenantrecord) {
                $tenants[$tenantrecord->id] = new tenant(0, $tenantrecord);
            }
            if (empty($tenants)) {
                // Create default tenant.
                $tenant = $this->create_tenant((object)[
                    'name' => 'Default tenant',
                    'isdefault' => 1]);
                $tenants[$tenant->get('id')] = $tenant;
            }
            $cache->set('active', $tenants);
        }
        return $tenants;
    }

    /**
     * Creates a new tenant
     *
     * @param \stdClass $data
     */
    public function create_tenant(\stdClass $data) : tenant {
        global $DB;
        $sortorder = (int)$DB->get_field_sql('
            SELECT max(sortorder) 
            FROM {local_tenant} 
            WHERE archived = 0', []);
        $data->sortorder = $sortorder + 1;
        $tenant = new tenant(0, $data);
        $tenant->create();
        if (!$tenant->get('isdefault')) {
            // Do not trigger event when default tenant is created, it is done automatically on the first request
            // and may affect core unittests.
            tenant_created::create_from_object($tenant)->trigger();
        }
        $this->reset_tenants_cache();
        return $tenant;
    }

    /**
     * Resets tenants list cache
     */
    protected function reset_tenants_cache() {
        \cache_helper::purge_by_event('tenantsmodified');
        \cache::make('local_tenant', 'mytenant')->purge();
        \cache::make('local_tenant', 'tenants')->purge();
    }

    /**
     * Base URL to view tenants list
     * @return \moodle_url
     */
    public static function get_base_url() : \moodle_url {
        return new \moodle_url('/local/tenant/index.php');
    }

    public static function get_editor_url(): \moodle_url {
        return new \moodle_url('/local/tenant/editor.php');
    }
}