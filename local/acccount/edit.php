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

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/acccount/classes/form/edit.php');

global $DB;

$PAGE->set_url(new moodle_url('/local/acccount/edit.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Edit Acccount Form');

// display the edit form
$mform = new edit();

$acccountId = $_GET['acccountid'] ?? null;

if ($acccountId) {
    $acccount = $DB->get_record('local_acccount',['id' => $acccountId]);
    $formData = (object)array(
        'acccoundid' => $acccountId,
        'acccountname' => $acccount->name,
        'acccountsitename' => $acccount->sitename,
        'acccountsiteshortname' => $acccount->siteshortname,
    );

    $mform->set_data($formData);
}




if ($mform->is_cancelled()) {
    // go back to manage.php page
    redirect($CFG->wwwroot . '/local/acccount/manage.php', 'You cancelled the acccount edit form');
} else if ($fromform = $mform->get_data()) {

    if ($acccoundid = $fromform->acccoundid) {
        // update current acccount
        $acccount = $DB->get_record('local_acccount', ['id' => $acccoundid]);
        $acccount->name = $fromform->acccountname;
        $acccount->sitename = $fromform->acccountsitename;
        $acccount->siteshortname = $fromform->acccountsiteshortname;
        $acccount->timemodified = time();
        $DB->update_record('local_acccount', $acccount);
    } else {
        // insert the data into the db table
        $newAcccount = new stdClass();
        $newAcccount->name = $fromform->acccountname;
        $newAcccount->sitename = $fromform->acccountsitename;
        $newAcccount->siteshortname = $fromform->acccountsiteshortname;
        $newAcccount->timecreated = time();
        $newAcccount->timemodified = time();
        $DB->insert_record('local_acccount', $newAcccount);
    }
    // go back to manage.php page
    redirect($CFG->wwwroot . '/local/acccount/manage.php', 'You created a new Acccount: ' . $fromform->acccountname);
}



echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();

