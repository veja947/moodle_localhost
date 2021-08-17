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

require_once(__DIR__ . '/../../config.php'); // load config.php
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/webservice/lib.php');
require_once($CFG->dirroot . '/webservice/lib.php');
require_once($CFG->dirroot . '/local/domains/classes/form/edit.php');
//admin_externalpage_setup('programslist');
global $DB;

$PAGE->set_url(new moodle_url('/local/domains/index.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Manage Domains');

$manager = new \local_domains\manager();
$mform = new edit();

if ($mform->is_cancelled()) {
    // go back to index.php page
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

    // go back to index.php page
    redirect($CFG->wwwroot . '/local/domains/index.php',
        'You created a new Domain: ' . $fromform->name);
}

$domainid = $_GET['domainid'] ?? null;
$action = $_GET['action'] ?? null;
switch ($action) {
    case \local_domains\manager::DOMAIN_ACTION_VERIFY:
        $manager->verify_domain((int)$domainid);
        break;

    case \local_domains\manager::DOMAIN_ACTION_DELETE:
        $manager->delete_domain((int)$domainid);
        break;
}

$activedomains = $manager->get_active_domains();
$activedomainsdisplay = $manager->get_domains_display_array($activedomains);

$templateContext = (object)[
    'active_domains_list' => array_values($activedomainsdisplay),
    'edit_url' => \local_domains\manager::get_editor_url(),
    'action_url' => \local_domains\manager::get_base_url(),
];

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->render_from_template('local_domains/index', $templateContext);
echo $OUTPUT->footer();