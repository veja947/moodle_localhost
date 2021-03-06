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
require_once(__DIR__ . '/../../config.php'); // load config.php

function local_program_before_footer() {
//    \core\notification::add('the test 4 program', \core\output\notification::NOTIFY_SUCCESS);
}

/**
 * Returns an array of courses
 *
 * @return array of mappings
 */
function program_get_courses() {
    global $DB;
    return $DB->get_records_menu('course', null, '', 'id,fullname') ?? [];
}

/**
 * Returns an array of acccounts
 *
 * @return array of mappings
 */
function program_get_acccounts() {
    global $DB;
    return $DB->get_records_menu('local_acccount', null, '', 'id,name') ?? [];
}