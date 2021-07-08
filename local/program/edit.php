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

$PAGE->set_url(new moodle_url('/local/program/edit.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Edit Program Form');


$mform = new edit();

$programId = $_GET['programid'];
if ($programId) {
    $programWithAcccount = $DB->get_record_sql('
        SELECT * FROM mdl_local_program lp
        LEFT JOIN mdl_local_program_acccount lpc ON lp.id = lpc.programid
        WHERE lp.id = :programid
    ', ['programid' => $programId]);
    $formData = (object)array(
        'programname' => $programWithAcccount->name,
        'programshortname' => $programWithAcccount->shortname,
        'programacccount' => $programWithAcccount->acccountid,
        'programcourses' => [2,3],
    );

    $mform->set_data($formData);
}


if ($mform->is_cancelled()) {
    // go back to manage.php page
    redirect($CFG->wwwroot . '/local/program/manage.php', 'You cancelled the program edit form');
} else if ($fromform = $mform->get_data()) {

    // insert the data into the program table
    $newProgram = new stdClass();
    $newProgram->name = $fromform->programname;
    $newProgram->shortname = $fromform->programshortname;
    $newProgram->timecreated = time();
    $newProgram->timemodified = time();
    $programid = $DB->insert_record('local_program', $newProgram);

    // insert the courses into program-course table
    foreach ($fromform->programcourses as $id) {
        $object = new stdClass();
        $object->programid = $programid;
        $object->courseid = $id;
        $object->timecreated = time();
        $object->timemodified = time();
        $DB->insert_record('local_program_course', $object);
    }

    // insert the data into program-acccount table
    $newObject = new stdClass();
    $newObject->programid = $programid;
    $newObject->acccountid = $fromform->programacccount;
    $newObject->timecreated = time();
    $newObject->timemodified = time();
    $DB->insert_record('local_program_acccount', $newObject);

    // go back to manage.php page
    redirect($CFG->wwwroot . '/local/program/manage.php', 'You created a new Program: ' . $fromform->programname);
}


echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();

