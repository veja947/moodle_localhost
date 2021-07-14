<?php


require_once('../config.php');
require_once($CFG->libdir.'/gdlib.php');
require_once($CFG->dirroot.'/local/acccount/classes/form/tenant_edit_form.php');

global $DB;
$pagetitle = 'test';


$PAGE->set_context(\context_system::instance());
$PAGE->set_title($pagetitle);
$PAGE->set_pagelayout('admin');
$PAGE->set_url(new moodle_url('test/test.php'));

echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle);
// TODO: fix $PAGE navbar missing

echo $OUTPUT->footer();