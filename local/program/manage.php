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
 * @package    local_program
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php'); // load config.php
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/webservice/lib.php');
require_once($CFG->dirroot . '/webservice/lib.php');
admin_externalpage_setup('programslist');
global $DB;

$PAGE->set_url(new moodle_url('/local/program/manage.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Manage Programs');

$manager = new \local_program\manager();

$programId = $_GET['programid'] ?? null;
$action = $_GET['action'] ?? null;
switch ($action) {
    case \local_program\manager::PROGRAM_ACTION_ARCHIVE:
        $manager->archive_program((int)$programId);
        break;

    case \local_program\manager::PROGRAM_ACTION_RESTORE:
        $manager->restore_program((int)$programId);
        break;

    case \local_program\manager::PROGRAM_ACTION_DELETE:
        $manager->delete_program((int)$programId);
        break;
}

$activePrograms = $manager->get_active_programs();
$archivedPrograms = $manager->get_archived_programs();

$activeProgramsDisplay = $manager->get_programs_display_array($activePrograms);
$archivedProgramsDisplay = $manager->get_programs_display_array($archivedPrograms);

$templateContext = (object)[
    'active_program_list' => array_values($activeProgramsDisplay),
    'archived_program_list' => array_values($archivedProgramsDisplay),
    'edit_url' => \local_program\manager::get_editor_url(),
    'action_url' => \local_program\manager::get_base_url(),
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_program/manage', $templateContext);
echo $OUTPUT->footer();