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
global $DB, $USER;

$PAGE->set_url(new \moodle_url('/local/domains/index.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Manage Domains');

$manager = new \local_domains\manager();

$domainform = new domain_edit_form();
$subdomainform = new subdomain_edit_form();
[
    $domainnotificationtext,
    $domainnotificationtype
] = $manager->set_default_domain_notification();
[
    $subdomainnotificationtext,
    $subdomainnotificationtype
] = $manager->set_default_subdomain_notification();

if ($domainform->is_cancelled()) {
    $domainform->reset();
    // go back to index.php page
    redirect(\local_domains\manager::get_base_url());
} else if ($fromform = $domainform->get_data()) {
    // validation
    if ($errors = $domainform->validate_domain($fromform->name)) {
        $domainnotificationtext = $errors['name'];
        $domainnotificationtype = 'error';
    } else {
        // create new domain
        $new_domain = $manager->create_domain((object)[
            'name' => $fromform->name,
            'token' => $manager->generate_token(),
            'status' => 0,
            'tenantid' => $USER->tenantid ?? 0,
            'timecreated' => time(),
        ]);

        $domainnotificationtext = "Domain <span class='highlight-in-notification'>" . $fromform->name . "</span> successfully created.";
        $domainnotificationtype = 'success';
    }

    $domainform->reset();
} else if ($domainform->is_validated()) {
    $domainform->reset();
} else {
    if (isset($domainform->get_validation_errors()['name'])) {
        $domainnotificationtext = $domainform->get_validation_errors()['name'];
        $domainnotificationtype = 'error';
    }
    $domainform->reset();
}

if ($subdomainform->is_cancelled()) {
    // go back to index.php page
    redirect(\local_domains\manager::get_base_url());
} else if ($subfromform = $subdomainform->get_data()) {
    if ($errors = $subdomainform->validate_subdomain($subfromform->name, $subfromform->domainid)) {
        $subdomainnotificationtext = $errors['name'];
        $subdomainnotificationtype = 'error';
    } else {
        // create new subdomain
        $new_subdomain = $manager->create_subdomain((object)[
            'name' => $subfromform->name,
            'status' => 0,
            'primarydomain' => 0,
            'tenantid' => $USER->tenantid ?? 0,
            'timecreated' => time(),
            'domainid' => $subfromform->domainid ?: null,
        ]);

        $subdomainnotificationtext = "Domain <span class='highlight-in-notification'>" . $new_subdomain->get_formatted_subdomain_full_name() . "</span> successfully created.";
        $subdomainnotificationtype = 'success';
    }
    $subdomainform->reset();
} else if ($subdomainform->is_validated()) {
    $subdomainform->reset();
} else {
    if (isset($subdomainform->get_validation_errors()['name'])) {
        $subdomainnotificationtext = $subdomainform->get_validation_errors()['name'];
        $subdomainnotificationtype = 'error';
    }
    $subdomainform->reset();
}

$domainid = $_GET['domainid'] ?? null;
$subdomainid = $_GET['subdomainid'] ?? null;
$action = $_GET['action'] ?? null;

switch ($action) {
    case \local_domains\manager::DOMAIN_ACTION_DELETE:
        $domainid
            ? $deleteddomainname = $manager->delete_domain($domainid)
            : $deletedsubdomainname = $manager->delete_subdomain($subdomainid);
        if ($domainid && $deleteddomainname) {
            [$domainnotificationtext, $domainnotificationtype] =
                $manager->set_domain_deletion_notification($deleteddomainname);
        }
        if ($subdomainid && $deletedsubdomainname) {
            [$subdomainnotificationtext, $subdomainnotificationtype] =
                $manager->set_subdomain_deletion_notification($deletedsubdomainname);
        }
        break;

    case \local_domains\manager::DOMAIN_ACTION_PRIMARY_DOMAIN:
        $subdomain = $manager->primary_subdomain($subdomainid);
        [$subdomainnotificationtext, $subdomainnotificationtype] =
            $manager->set_primary_subdomain_notification($subdomain);
        break;

    case \local_domains\manager::DOMAIN_ACTION_VERIFY:
        $domainid
            ? $domain = $manager->verify_domain($domainid)
            : $subdomain = $manager->verify_subdomain($subdomainid);
        if ($domainid && $domain) {
            [$domainnotificationtext, $domainnotificationtype] =
                $manager->set_domain_verify_notification($domain);
        }
        if ($subdomainid && $subdomain) {
            [$subdomainnotificationtext, $subdomainnotificationtype] =
                $manager->set_subdomain_verify_notification($subdomain);
        }
        break;
}

$activedomains = $manager->get_active_domains();
$activesubdomains = $manager->get_active_subdomains();
$activedomainsdisplay = $manager->get_domains_or_subdomains_display_array($activedomains);
$activesubdomainsdisplay = $manager->get_domains_or_subdomains_display_array($activesubdomains);
$domainformhtml = $domainform->render();
$subdomainformhtml = $subdomainform->render();



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
