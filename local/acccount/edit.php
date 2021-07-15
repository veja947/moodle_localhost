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
require_once($CFG->libdir . '/adminlib.php');

global $DB;

$pagetitle = 'Edit Acccount';


$PAGE->set_url(\local_acccount\manager::get_editor_url());
$PAGE->set_context(\context_system::instance());
$PAGE->set_title($pagetitle);
$PAGE->set_pagelayout('admin');
$PAGE->set_pagetype('admin-local-acccount-edit');


$mform = new edit();
$manager = new \local_acccount\manager();
$acccountId = $_GET['acccountid'] ?? null;

if ($acccountId) {
    // set data to form
    $acccount = $manager->get_active_acccount_by_id($acccountId);
    $mform->set_data($acccount->get_properties_display());
}




if ($mform->is_cancelled()) {
    // go back to manage.php page
    redirect(
        $CFG->wwwroot . '/local/acccount/manage.php',
        'You cancelled the acccount edit form',
    );
} else if ($fromform = $mform->get_data()) {
    if ($acccoundid = $fromform->id) {
        // update current acccount
        $acccountEntity = $manager->get_active_acccount_by_id($acccoundid);
        $manager->update_acccount($acccountEntity, $fromform);
        // go back to manage.php page
        redirect($CFG->wwwroot . '/local/acccount/manage.php',
            'You updated the Acccount: ' . $fromform->name,
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } else {
        // create new acccount
        $newAcccount = $manager->create_acccount((object)[
            'name' => $fromform->name,
            'sitename' => $fromform->sitename,
            'siteshortname' => $fromform->siteshortname,
            'idnumber' => $fromform->idnumber,
        ]);
        $acccounts[$newAcccount->get('id')] = $newAcccount;
        // go back to manage.php page
        redirect($CFG->wwwroot . '/local/acccount/manage.php', 'You created a new Acccount: ' . $fromform->name);
    }

}


echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle);
// TODO: fix $PAGE navbar missing
$mform->display();
echo $OUTPUT->footer();

