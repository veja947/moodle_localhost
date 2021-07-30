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
        $this->title = get_string('pluginname', 'block_custom_dashboard');
    }

    function has_config() {
        return true;
    }

    public function hide_header()
    {
        return true;
    }

    function get_content() {

//        $renderable = new \block_custom_dashboard\output\customdashboard($this->config);
//        $renderer = $this->page->get_renderer('main');

        $this->content = new stdClass();
//        $this->content->text = $renderer->render($renderable);

        $students = \block_custom_dashboard\manager::get_students();
        $modulesinprogress = \block_custom_dashboard\manager::get_modules_in_progress();
        $modulesinprogress = \block_custom_dashboard\manager::get_modules_completed();
        $allmodules = \block_custom_dashboard\manager::get_all_modules();
        $this->content->total_students = count($students);

        $this->content->text = 'hello dashboard';
        return $this->content;
    }

    public function instance_allow_multiple()
    {
        return false;
    }
}