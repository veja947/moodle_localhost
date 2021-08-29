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

require_once(__DIR__ . '/../../config.php'); // load config.php
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/webservice/lib.php');
require_once($CFG->dirroot . '/webservice/lib.php');
require_once($CFG->dirroot . '/local/users/classes/form/new_user_form.php');
require_once($CFG->dirroot . '/user/externallib.php');
$PAGE->requires->css('/local/users/css/index.css');
global $DB;
$manager = new \local_users\manager();

$PAGE->set_url(new moodle_url('/local/users/index.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_heading('Users', false);
$PAGE->set_title('All Users');

$newuserform = new new_user_form();
$newuserformhtml = $newuserform->render();

$page = $_GET['page'] ?? 1;
$totalpage = $manager->get_users_table_pages_number();

if ($newuserform->is_cancelled()) {
    // go back to index.php page
    $newuserform->reset();
//    redirect($CFG->wwwroot . '/local/users/index.php');
} else if ($fromform = $newuserform->get_data()) {

    $email = $fromform->email;
    $userinfo = array(
        'username' => $fromform->email,
        'firstname' => $fromform->firstname,
        'lastname' => $fromform->lastname,
        'email' => $fromform->email,
        'password' => \local_users\manager::DEFAULT_USER_PASSWORD,
    );


    $createduser = core_user_external::create_users([$userinfo]);
    $manager->setting_to_new_user($createduser[0]['id']);
} else {
    $test = $newuserform->is_validated();
}

$templateContext = (object)[
    'all_users_list' => array_values($manager->get_all_confirmed_users($page)),
    'users_list_page_number' => $totalpage,
    'current_page' => $page,
    'next_page' => $page + 1 < $totalpage ? $page + 1 : $totalpage,
    'no_next_page' => $page + 1 > $totalpage,
    'previous_page' => $page <= 1 ? 1 : $page - 1,
    'no_previous_page' => $page <= 1,
    'new_user_form' => $newuserformhtml,
    'upload_users_url' => \local_users\manager::get_upload_users_url(),
    'users_index_url' => \local_users\manager::get_users_base_url(),
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_users/index', $templateContext);
echo $OUTPUT->footer();