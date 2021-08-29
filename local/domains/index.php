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
$PAGE->requires->jquery();
$PAGE->requires->js(new moodle_url(
    $CFG->wwwroot . '/local/domains/js/index.js'));
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
$domainnotificationtext = "Add the token generated below as a DNS TXT record in your domain DNS configuration to prove that you own the domain. Then click 'Verify' button.";
$domainnotificationtype = 'default';
$subdomainnotificationtext = "Click 'Connect' button to connect the domain name to the learning platform.";
$subdomainnotificationtype = 'default';

if ($domainform->is_cancelled()) {
    $domainform->reset();
    // go back to index.php page
    redirect(\local_domains\manager::get_base_url());
} else if ($fromform = $domainform->get_data()) {
    // create new domain
    $new_domain = $manager->create_domain((object)[
        'name' => $fromform->name,
        'token' => $manager->generate_token(),
        'status' => 0,
        'tenantid' => 99, // TODO: instead real tenantid
        'timecreated' => time(),
    ]);

    $domainnotificationtext = 'Domain ' . $fromform->name . ' successfully verified.';
    $domainnotificationtype = 'success';
    $domainform->reset();
//    redirect(\local_domains\manager::get_base_url());
} else if (!$domainform->is_validated()) {
    if (isset($domainform->get_validation_errors()['name'])) {
        $domainnotificationtext = $domainform->get_validation_errors()['name'];
        $domainnotificationtype = 'error';
    }
    $domainform->reset();
    $test = $domainform->is_submitted();
} else {
    $test = $domainform->is_submitted();
}

if ($subdomainform->is_cancelled()) {
    // go back to index.php page
    redirect(\local_domains\manager::get_base_url());
    exit();
} else if ($subfromform = $subdomainform->get_data()) {
    // create new subdomain
    $new_subdomain = $manager->create_subdomain((object)[
        'name' => $subfromform->name,
        'status' => 0,
        'primarydomain' => 0,
        'tenantid' => 99, // TODO: instead real tenantid
        'timecreated' => time(),
        'domainid' => $subfromform->domainid ?: null,
    ]);

    // go back to index.php page
    redirect($CFG->wwwroot . '/local/domains/index.php',
        'You created a new Sub Domain: ' . $subfromform->name);
}







$domainid = $_GET['domainid'] ?? null;
$subdomainid = $_GET['subdomainid'] ?? null;
$action = $_GET['action'] ?? null;

switch ($action) {
    case \local_domains\manager::DOMAIN_ACTION_DELETE:
        $domainid
            ? $manager->delete_domain($domainid)
            : $manager->delete_subdomain($subdomainid);
        break;

    case \local_domains\manager::DOMAIN_ACTION_PRIMARY_DOMAIN:
        $subdomain = $manager->primary_subdomain($subdomainid);
        $subdomainnotificationtext = $subdomain
            ? "Your primary domain has been changed to " . $subdomain->get_formatted_subdomain_full_name()
            : 'Your primary domain changing failed.';
        $subdomainnotificationtype = $subdomain ? 'success' : 'error';
        break;

    case \local_domains\manager::DOMAIN_ACTION_VERIFY:
        $domainid
            ? $manager->verify_domain($domainid)
            : $manager->verify_subdomain($subdomainid);
        break;
}

$activedomains = $manager->get_active_domains();
$activesubdomains = $manager->get_active_subdomains();
$activedomainsdisplay = $manager->get_domains_or_subdomains_display_array($activedomains);
$activesubdomainsdisplay = $manager->get_domains_or_subdomains_display_array($activesubdomains);

$templateContext = (object)[
    'active_domains_list' => array_values($activedomainsdisplay),
    'active_subdomains_list' => array_values($activesubdomainsdisplay),
    'edit_url' => \local_domains\manager::get_editor_url(),
    'action_url' => \local_domains\manager::get_base_url(),
    'domainform' => $domainformhtml,
    'subdomainform' => $subdomainformhtml,
    'domain_notification_text' => $domainnotificationtext,
    'domain_notification_type' => $domainnotificationtype,
    'subdomain_notification_text' => $subdomainnotificationtext,
    'subdomain_notification_type' => $subdomainnotificationtype,
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_domains/index', $templateContext);
echo $OUTPUT->footer();