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
 * @package    local_acccount
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php'); // load config.php
require_once($CFG->libdir.'/adminlib.php');
admin_externalpage_setup('acccountroles');
global $DB;

$manager = new \local_acccount\manager();

$PAGE->set_url(\local_acccount\manager::get_roles_url());
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Roles');

// These are needed early because of tabs.php.
$context = $PAGE->context;
list($assignableroles, $assigncounts, $nameswithcounts) = get_assignable_roles($context, ROLENAME_BOTH, true);
$overridableroles = get_overridable_roles($context, ROLENAME_BOTH);

$list = [];
foreach ($assignableroles as $key => $role) {
    array_push($list, [
        'roleid' => $key,
        'name' => $role,
        'count' => $assigncounts[$key],
    ]);
}

$templateContext = (object)[
    'acccount_roles_list' => $list,
    'assign_url' => \local_acccount\manager::get_assign_roles_url(['contextid' => $context->id]),
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_acccount/role', $templateContext);
echo $OUTPUT->footer();

