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
 * @package    local_users
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/users/classes/form/upload_users_form.php');
require_once($CFG->libdir . '/adminlib.php');

$pagetitle = 'Upload Users via Text File';
$manager = new \local_users\manager();

$PAGE->set_url(new moodle_url('/local/users/uploadusers.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title($pagetitle);

$mform = new upload_users_form();

if ($mform->is_cancelled()) {
    // go back to index.php page
    redirect($CFG->wwwroot . '/local/users/index.php');
} else if ($fromform = $mform->get_data()) {
    // TODO: validate users

    // go back to manage.php page
    redirect($CFG->wwwroot . '/local/users/index.php');
}


echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle);
$mform->display();
echo $OUTPUT->footer();