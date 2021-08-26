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
 * @package    local_users
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("$CFG->libdir/formslib.php");

class upload_users_form extends \moodleform
{
    protected function definition()
    {
        global $CFG;
        $mform = $this->_form;

        // 0. example button
        $mform->addElement('html',
            '<a href="' . $CFG->wwwroot .'/local/users/files/example.csv" 
                class="btn btn-outline-info" 
                download="example.csv">example.csv</a>' . "\n",
            'Example text file');

        // 1. file upload
        $filemanageroptions = array(
            'accepted_types' => array('.csv'),
            'maxbytes' => 0,
            'maxfiles' => 1,
            'subdirs' => 0
        );
        $mform->addElement('filepicker',
            'usersfile',
            'Upload text file',
            null,
            $filemanageroptions);
        $mform->addRule('usersfile', null, 'required');

        // add submit and cancel button
        $this->add_action_buttons(true, 'Upload Users');
    }

    // custom validation should be added here
    function validation($data, $files)
    {
        global $DB;
        $errors = parent::validation($data, $files);


        return $errors;
    }
}