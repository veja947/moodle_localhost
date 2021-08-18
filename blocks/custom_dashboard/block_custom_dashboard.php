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
 * Custom Dashboard Block
 *
 * @package    block_custom_dashboard
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/course/lib.php');

class block_custom_dashboard extends block_base
{
    function init() {
        global $PAGE, $CFG;
        $this->title = get_string('pluginname', 'block_custom_dashboard');
        $PAGE->requires->jquery();
        $PAGE->requires->js(new moodle_url(
            $CFG->wwwroot . '/blocks/custom_dashboard/dist/js/app.js'));
        $PAGE->requires->css('/blocks/custom_dashboard/dist/css/style.css');
    }

    function has_config() {
        return true;
    }

    public function hide_header()
    {
        return true;
    }

    function get_content() {
        global $PAGE, $CFG, $USER;
        $this->content = new stdClass();

        $manager = new \block_custom_dashboard\manager();
        $program_records = $manager->get_campaign_statistics_for_dashboard();
        $this->content->data = $program_records;


        $this->content->text = '<div id="app">custom dashboard</div>';


        // TODO: finally, use web service api to instead of sending data via script tag
        $this->content->text .= html_writer::tag(
            'script',
            json_encode(
                $program_records
            ),
            ['id' => 'dashboard_source_data', 'type' => 'application/json']
        );

        return $this->content;
    }

    public function instance_allow_multiple()
    {
        return false;
    }
}