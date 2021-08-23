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
use local_domains\subdomain;


class subdomain_edit_form extends moodleform
{

    protected function definition()
    {
        global $CFG;
        $mform = $this->_form;

        // 0. domain id
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // 1. subdomain name
        $mform->addElement('text', 'name', '', [
            'id' => 'subdomain_name_input',
            'placeholder' => 'Your company name',
        ]);
        $mform->setType('name', PARAM_NOTAGS);
        $mform->setDefault('name', '');

        // 2. verified domains list
        $mform->addElement('select', 'domainid', '', $this->get_all_verified_domains_array());

        // add submit and cancel button
        $this->add_action_buttons(false, 'Add');
    }

    // custom validation should be added here
    function validation($data, $files)
    {
        global $DB;
        $errors = parent::validation($data, $files);
        if (empty($data['name'])) {
            $errors['name'] = 'sub-domain name is required';
        }
        if ($record = $DB->get_record(
            subdomain::TABLE,
            ['name' => trim($data['name'])])) {
            $errors['name'] = 'sub-domain name is already existed.';
        }
        return $errors;
    }

    private function get_all_verified_domains_array(): array
    {
        global $DB;
        $results = [
            null => domain::DEFAULT_FTNT_INFO_DOMAIN,
        ];
        $rs = $DB->get_recordset(domain::TABLE, ['status' => 1], '', 'id, name');
        if (!$rs->valid()) {
            return $results;
        }
        foreach ($rs as $record) {
            if ($record->name === domain::DEFAULT_FTNT_INFO_DOMAIN) {
                continue;
            }
            $results[$record->id] = $record->name;
        }

        return $results ?? [];
    }
}