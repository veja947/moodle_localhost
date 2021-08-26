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
require_once($CFG->libdir.'/csvlib.class.php');
require_once($CFG->dirroot . '/user/externallib.php');
require_once($CFG->dirroot . '/local/users/classes/form/upload_users_form.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/uploaduser/locallib.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/uploaduser/user_form.php');
require_once($CFG->libdir . '/adminlib.php');

$pagetitle = 'Upload Users via Text File';
$manager = new \local_users\manager();

$PAGE->set_url(new moodle_url('/local/users/uploadusers.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title($pagetitle);



$iid         = optional_param('iid', '', PARAM_INT);
$previewrows = optional_param('previewrows', 10, PARAM_INT);
core_php_time_limit::raise(60 * 60); // 1 hour should be enough.
raise_memory_limit(MEMORY_HUGE);




$mform = new upload_users_form();

if ($mform->is_cancelled()) {
    // go back to index.php page
    redirect($CFG->wwwroot . '/local/users/index.php');
} else if ($fromform = $mform->get_data()) {
    global $DB;
    // TODO: validate users
    $contentstring = $mform->get_file_content('usersfile') ?? '';
    $contentarray = preg_split('/[\ \n\,]+/', $contentstring);
    if ($uploadfinished = $manager->check_users_emails_in_file($contentarray)) {




        $user1 = array(
            'username' => 'usernametest6',
            'firstname' => 'First Name User Test 6',
            'lastname' => 'Last Name User Test 6',
            'email' => 'usertest6@example.com',
            'password' => 'Moodle2012!'
        );
        $createdusers = core_user_external::create_users(array($user1));
        $noreplyuser = \core_user::get_noreply_user();
        foreach ($createdusers as $singleuser) {
            email_to_user($DB->get_record('user', ['id'=> $singleuser['id']]), $noreplyuser, 'email subject', 'email message', '<h1>email html</h1>');
        }




        // go back to index.php page
        redirect($CFG->wwwroot . '/local/users/index.php',
            'Users upload is successful.');
    } else {
        \core\notification::error('Upload failed, please check your file.');
    }

}


echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle);
$mform->display();
echo $OUTPUT->footer();