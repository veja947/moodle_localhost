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
 * @package    local_tenant
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function local_tenant_before_footer() {
//    \core\notification::add('the test 4 tenant', \core\output\notification::NOTIFY_SUCCESS);
    global $SITE, $COURSE, $CFG;
    if (during_initial_install() || isset($CFG->upgraderunning)) {
        return;
    }

    // Prepare the current tenant.
    try {
        $tenantid = \local_tenant\tenancy::get_tenant_id();
    } catch (\Exception $e) {
        // We are probably inside the plugin installation.
        echo $e->getMessage();
        return;
    }
    if (isset($SITE)) {
        $tenants = \local_tenant\tenancy::get_tenants();
        $tenant = $tenants[$tenantid];
        $SITE->fullname = $tenant->sitename ?: $SITE->fullname;
        $SITE->shortname = $tenant->siteshortname ?: $SITE->shortname;

        if (isset($COURSE->id) && $COURSE->id == $SITE->id) {
            $COURSE->fullname = $tenant->sitename ?: $SITE->fullname;
            $COURSE->shortname = $tenant->siteshortname ?: $SITE->shortname;
        }
    }
}

function local_tenant_after_footer() {

}