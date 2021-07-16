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
}

//if ($programId) {
//    // set program info and acccount info
//    $programWithAcccount = $DB->get_record_sql('
//        SELECT * FROM {local_program} lp
//        LEFT JOIN {local_program_acccount} lpc ON lp.id = lpc.programid
//        WHERE lp.id = :programid
//    ', ['programid' => $programId]);
//
//    // set courses info
//    $programWithCourses = $DB->get_records_sql('
//        SELECT courseid FROM {local_program} lp
//        LEFT JOIN {local_program_course} lpc ON lp.id = lpc.programid
//        WHERE lp.id = :programid
//    ', ['programid' => $programId]);
//    $courseIdArray = [];
//    foreach ($programWithCourses as $course) {
//        array_push($courseIdArray, $course->courseid);
//    }
//
//    $formData = (object)array(
//        'id' => $programId,
//        'name' => $programWithAcccount->name,
//        'idnumber' => $programWithAcccount->idnumber,
//        'description' => $programWithAcccount->description,
//        'acccountid' => $programWithAcccount->acccountid,
//        'courses' => $courseIdArray,
//    );
//
//    $mform->set_data($formData);
//}


if ($mform->is_cancelled()) {
    // go back to manage.php page
    redirect($CFG->wwwroot . '/local/program/manage.php', 'You cancelled the program edit form');
} else if ($fromform = $mform->get_data()) {

    if ($pid = $fromform->id) {
        // TODO: update current program
    } else {

        // create new program
        $newProgram = $manager->create_program((object)[
            'name' => $fromform->name,
            'idnumber' => $fromform->idnumber,
            'description' => $fromform->description,
            'acccountid' => $fromform->acccountid,

        ]);

//        // insert the data into the program table
//        $newProgram = new stdClass();
//        $newProgram->name = $fromform->programname;
//        $newProgram->idnumber = $fromform->programidnumber;
//        $newProgram->description = $fromform->programdescription;
//        $newProgram->timecreated = time();
//        $newProgram->timemodified = time();
//        $programid = $DB->insert_record('local_program', $newProgram);
//
//        // insert the courses into program-course table
//        foreach ($fromform->programcourses as $id) {
//            $object = new stdClass();
//            $object->programid = $programid;
//            $object->courseid = $id;
//            $object->timecreated = time();
//            $object->timemodified = time();
//            $DB->insert_record('local_program_course', $object);
//        }
//
//        // insert the data into program-acccount table
//        $newObject = new stdClass();
//        $newObject->programid = $programid;
//        $newObject->acccountid = $fromform->programacccount;
//        $newObject->timecreated = time();
//        $newObject->timemodified = time();
//        $DB->insert_record('local_program_acccount', $newObject);
    }

    // go back to manage.php page
    redirect($CFG->wwwroot . '/local/program/manage.php',
        'You created a new Program: ' . $fromform->name);
}


echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle);
$mform->display();
echo $OUTPUT->footer();

