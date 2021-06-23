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
 * Version details
 *
 * @package    local_notification
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/notification/classes/form/editform.php');

global $DB;

$PAGE->set_url(new moodle_url('/local/notification/edit.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Edit Notification Form');


// display the edit form
$mform = new editform();


if ($mform->is_cancelled()) {
    // go back to manage.php page
    redirect($CFG->wwwroot . '/local/notification/manage.php', 'You cancelled the notification form');
} else if ($fromform = $mform->get_data()) {

    // insert the data into the db table
    $newnotification = new stdClass();
    $newnotification->notificationtext = $fromform->notificationtext;
    $newnotification->notificationtype = $fromform->notificationtype;

    $DB->insert_record('local_notification', $newnotification);

    // go back to manage.php page
    redirect($CFG->wwwroot . '/local/notification/manage.php', 'You created a new notification: ' . $fromform->notificationtext);
}

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();