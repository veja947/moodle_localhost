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
 * @package    local_domains
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/domains/classes/form/edit.php');
require_once($CFG->libdir . '/adminlib.php');

$pagetitle = 'Edit Domain';
$manager = new \local_domains\manager();

$PAGE->set_url(new moodle_url('/local/domains/edit.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title($pagetitle);

$mform = new edit();

if ($mform->is_cancelled()) {
    // go back to manage.php page
    redirect($CFG->wwwroot . '/local/domains/index.php');
} else if ($fromform = $mform->get_data()) {
    // create new domain
    $new_domain = $manager->create_domain((object)[
        'name' => $fromform->name,
        'token' => $manager->generate_token(),
        'status' => 0,
        'tenantid' => 99, // TODO: instead real tenantid
        'timeverified' => null
    ]);

    // go back to manage.php page
    redirect($CFG->wwwroot . '/local/domains/index.php',
        'You created a new Domain: ' . $fromform->name);
}


echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle);
$mform->display();
echo $OUTPUT->footer();