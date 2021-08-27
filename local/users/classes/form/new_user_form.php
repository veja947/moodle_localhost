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


class new_user_form extends \moodleform
{

    protected function definition()
    {
        global $CFG;
        $mform = $this->_form;

        // 0. user id
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // 1. email name
        $mform->addElement('text', 'email', 'User\'s email', [
            'id' => 'new_user_email_input',
            'placeholder' => 'example@email.com',
        ]);
        $mform->setType('email', PARAM_NOTAGS);
        $mform->addRule('email', get_string('required'), 'required');
        $mform->setDefault('email', '');

        // 2. first name
        $mform->addElement('text', 'firstname', 'User\'s First Name', [
            'class' => 'new-user-firstname-input',
            'placeholder' => '',
        ]);
        $mform->addRule('firstname', get_string('required'), 'required');
        $mform->setType('firstname', PARAM_NOTAGS);

        // 3. last name
        $mform->addElement('text', 'lastname', 'User\'s Last Name', [
            'class' => 'new-user-lastname-input',
            'placeholder' => '',
        ]);
        $mform->addRule('lastname', get_string('required'), 'required');
        $mform->setType('lastname', PARAM_NOTAGS);

        // add submit and cancel button
        $this->add_action_buttons(true, 'Save');
    }

    // custom validation should be added here
    function validation($data, $files)
    {
        global $DB;
        $errors = parent::validation($data, $files);
        if (empty(trim($data['email']))) {
            $errors['email'] = 'email name is required';
        }
        if (!filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Not a valid email address';
        }
        return $errors;
    }

    public function reset() {
        $this->_form->updateSubmission(null, null);
        $this->_form->_errors = [];
    }
}