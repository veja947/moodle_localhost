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

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/program/classes/form/edit.php');
require_once($CFG->libdir . '/adminlib.php');
admin_externalpage_setup('programedit');

$pagetitle = 'Edit Program';
$manager = new \local_program\manager();

$PAGE->set_url(new moodle_url('/local/program/edit.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title($pagetitle);

$mform = new edit();

$programId = $_GET['programid'] ?? null;

if ($programId) {
    // set data to form
    $program = $manager->get_active_program_by_id($programId);
    $mform->set_data($program->get_properties_display());

    // set data to courses
    $courses = $manager->getCourseIDsForProgram($programId);
    $mform->set_data([
        'courses' => $courses,
    ]);
}


if ($mform->is_cancelled()) {
    // go back to manage.php page
    redirect($CFG->wwwroot . '/local/program/manage.php', 'You cancelled the program edit form');
} else if ($fromform = $mform->get_data()) {

    if ($pid = $fromform->id) {
        // update current program
        $programEntity = $manager->get_active_program_by_id($pid);
        $manager->update_program($programEntity, $fromform);
        $manager->update_program_courses($pid, $fromform->courses);

        // go back to manage.php page
        redirect($CFG->wwwroot . '/local/program/manage.php',
            'You updated the Program: ' . $fromform->name,
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } else {

        // create new program
        $newProgram = $manager->create_program((object)[
            'name' => $fromform->name,
            'idnumber' => $fromform->idnumber,
            'description' => $fromform->description,
            'acccountid' => $fromform->acccountid ?? null,

        ]);
        $manager->update_program_courses($newProgram->get('id'), $fromform->courses);
    }

    // go back to manage.php page
    redirect($CFG->wwwroot . '/local/program/manage.php',
        'You created a new Program: ' . $fromform->name);
}


echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle);
$mform->display();
echo $OUTPUT->footer();

