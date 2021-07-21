<?php
// ensure this page is only allowed in the moodle application
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('block_custom_dashboard',
        get_string('adminpageheading', 'block_custom_dashboard'));
//    $ADMIN->add('analytics', $settings);

    $settings->add(
        new admin_setting_configcheckbox(
            'block_custom_dashboard/test',
            'Test',
            'Test description',
            0
        )
    );
}
