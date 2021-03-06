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

require_once(__DIR__ . '/../../config.php'); // load config.php
require_once($CFG->libdir.'/adminlib.php');
admin_externalpage_setup('acccountlearners');
global $DB;

$manager = new \local_acccount\manager();

$PAGE->set_url(\local_acccount\manager::get_learners_url());
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Manage Learners');

$templateContext = (object)[
    'acccount_learners_list' => $manager->getLearnersDisplayArray(),
    'roles_url' => \local_acccount\manager::get_roles_url(),
];

$test = $manager->getLearnersDisplayArray();

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_acccount/learner', $templateContext);
echo $OUTPUT->footer();