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
 * @package    local_acccount
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$action = optional_param('action', null, PARAM_ALPHA) ?? 'no action';
$id = optional_param('id', null, PARAM_INT) ?? null;

require_login(0, false);
//require_capability('tool/tenant:manage', context_system::instance());
$PAGE->set_context(context_system::instance());
$PAGE->set_url(\local_tenant\manager::get_base_url());

$PAGE->set_heading('Manage Tenants');
echo $OUTPUT->header();

echo $OUTPUT->footer();