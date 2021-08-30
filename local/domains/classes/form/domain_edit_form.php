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
require_once("$CFG->libdir/formslib.php");

use local_domains\domain;

class domain_edit_form extends moodleform
{

//    public function __construct() {
//        $this->isSubmit = $this->no_submit_button_pressed() ?? true;
//    }

    protected function definition()
    {
        global $CFG;
        $mform = $this->_form;

        // 0. domain id
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // 1. domain name
        $mform->addElement('text', 'name', '', [
            'id' => 'domain_name_input',
            'placeholder' => 'example.com',
        ]);
        $mform->setType('name', PARAM_NOTAGS);
        $mform->setDefault('name', '');

        // add submit and cancel button
        $this->add_action_buttons(false, 'Add');
    }

    // custom validation should be added here
    function validation($data, $files)
    {
        return parent::validation($data, $files);
    }

    public function validate_domain(string $name): ?array
    {
        global $DB;
        $domain = trim($name);
        if (empty($domain)) {
            return [
                'name' => 'Domain name is required.'
            ];
        }
        if ($DB->get_record(
            domain::TABLE,
            ['name' => $domain])) {
            return [
                'name' => 'Domain ' . $domain . ' is already existed.'
            ];
        }
        return null;
    }

    /**
     * Returns validation errors (used in CLI)
     *
     * @return array
     */
    public function get_validation_errors(): array {
        return $this->_form->_errors;
    }

    public function reset() {
        $this->_form->setDefault('name', '');
        $this->_form->updateSubmission(null, null);
    }
}