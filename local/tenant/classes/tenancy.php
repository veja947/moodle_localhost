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
    public function get_tenants() : array {
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
                    'name' => get_string('defaultname', 'local_tenant'),
                    'isdefault' => 1]);
                $tenants = [$tenant->get('id') => $tenant->to_record()] + $tenants;
            }
            $cache->set('list', $tenants);
        }
        return $tenants;
    }
}