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
require_once($CFG->dirroot . '/local/domains/classes/form/domain_edit_form.php');
require_once($CFG->dirroot . '/local/domains/classes/form/subdomain_edit_form.php');
$PAGE->requires->css('/local/domains/css/styles.css');
//admin_externalpage_setup('domainsindex');
global $DB;

$PAGE->set_url(new moodle_url('/local/domains/index.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Manage Domains');

$manager = new \local_domains\manager();

$domainform = new domain_edit_form();
$domainformhtml = $domainform->render();

$subdomainform = new subdomain_edit_form();
$subdomainformhtml = $subdomainform->render();

if ($domainform->is_cancelled()) {
    // go back to index.php page
    redirect($CFG->wwwroot . '/local/domains/index.php');
} else if ($fromform = $domainform->get_data()) {
    // create new domain
    $new_domain = $manager->create_domain((object)[
        'name' => $fromform->name,
        'token' => $manager->generate_token(),
        'status' => 0,
        'primarydomain' => 0,
        'tenantid' => 99, // TODO: instead real tenantid
        'timecreated' => time(),
        'provider' => null, // TODO: get provider name
    ]);

    // go back to index.php page
    redirect($CFG->wwwroot . '/local/domains/index.php',
        'You created a new Domain: ' . $fromform->name);
}

if ($subdomainform->is_cancelled()) {
    // go back to index.php page
    redirect($CFG->wwwroot . '/local/domains/index.php');
} else if ($subfromform = $subdomainform->get_data()) {
    // create new domain
    $new_subdomain = $manager->create_subdomain((object)[
        'name' => $subfromform->name,
        'status' => 0,
        'primarydomain' => 0,
        'tenantid' => 99, // TODO: instead real tenantid
        'timecreated' => time(),
    ]);

    // go back to index.php page
    redirect($CFG->wwwroot . '/local/domains/index.php',
        'You created a new Sub Domain: ' . $subfromform->name);
}







$domainid = $_GET['domainid'] ?? null;
$subdomainid = $_GET['subdomainid'] ?? null;
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
$activesubdomains = $manager->get_active_subdomains();
$activedomainsdisplay = $manager->get_domains_display_array($activedomains);
$activesubdomainsdisplay = $manager->get_domains_display_array($activesubdomains);

$templateContext = (object)[
    'active_domains_list' => array_values($activedomainsdisplay),
    'active_subdomains_list' => array_values($activesubdomainsdisplay),
    'edit_url' => \local_domains\manager::get_editor_url(),
    'action_url' => \local_domains\manager::get_base_url(),
    'domainform' => $domainformhtml,
    'subdomainform' => $subdomainformhtml,
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_domains/index', $templateContext);
echo $OUTPUT->footer();