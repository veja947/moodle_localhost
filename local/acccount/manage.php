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
admin_externalpage_setup('acccountslist');
global $DB;

$PAGE->set_url(\local_acccount\manager::get_base_url());
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Manage Acccounts');

$manager = new \local_acccount\manager();

$acccountId = $_GET['acccountid'] ?? null;
$action = $_GET['action'] ?? null;
switch ($action) {
    case \local_acccount\manager::ACCCOUNT_ACTION_ARCHIVE:
        $manager->archive_acccount((int)$acccountId);
        break;

    case \local_acccount\manager::ACCCOUNT_ACTION_RESTORE:
        $manager->restore_acccount((int)$acccountId);
        break;

    case \local_acccount\manager::ACCCOUNT_ACTION_DELETE:
        $manager->delete_acccount((int)$acccountId);
        break;
}

$activeAcccountsList = $manager->get_active_acccounts();
$archivedAcccountsList = $manager->get_archived_acccounts();

$activeAcccountsDisplay = $manager->get_acccounts_display_array($activeAcccountsList);
$archivedAcccountsDisplay = $manager->get_acccounts_display_array($archivedAcccountsList);

$templateContext = (object)[
    'active_acccount_list' => array_values($activeAcccountsDisplay),
    'archived_acccount_list' => array_values($archivedAcccountsDisplay),
    'edit_url' => \local_acccount\manager::get_editor_url(),
    'action_url' => \local_acccount\manager::get_base_url(),
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_acccount/manage', $templateContext);
echo $OUTPUT->footer();