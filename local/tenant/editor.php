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
 * @package    local_tenant
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->libdir.'/gdlib.php');
require_once($CFG->dirroot.'/local/acccount/classes/form/tenant_edit_form.php');

global $DB;
$pagetitle = 'Edit Tenant Form';

$PAGE->set_url(\local_tenant\manager::get_editor_url());
$PAGE->set_context(\context_system::instance());
$PAGE->set_title($pagetitle);
//$PAGE->set_pagelayout('admin');
//$PAGE->set_pagetype('admin-local-tenant-edit');

$mform = new \local_tenant\tenant_edit_form();

echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle);
// TODO: fix $PAGE navbar missing
$mform->display();
echo $OUTPUT->footer();

