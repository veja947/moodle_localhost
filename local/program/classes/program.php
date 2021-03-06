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
 * @package    local_program
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_program;

use core\persistent;

defined('MOODLE_INTERNAL') || die();

class program extends persistent
{
    /** The table name. */
    const TABLE = 'local_program';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'name' => array(
                'type' => PARAM_TEXT,
                'description' => 'The program name.',
            ),
            'idnumber' => array(
                'type' => PARAM_TEXT,
                'description' => 'An id number used for external services.',
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'description' => array(
                'type' => PARAM_TEXT,
                'description' => '',
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'acccountid' => array(
                'type' => PARAM_INT,
                'description' => 'Acccount id',
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'archived' => array(
                'type' => PARAM_INT,
                'description' => 'Is archived.',
                'default' => 0,
            ),
            'timearchived' => array(
                'type' => PARAM_INT,
                'description' => 'Time the program was archived.',
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
        ];
    }
    public function get_formatted_property($name) : string {
        return format_string($this->get($name), true,
                ['context' => \context_system::instance(), 'escape' => false]) ?? '';
    }

    public function get_properties_display(): array {
        return [
            'id' => $this->get('id'),
            'name' => $this->get_formatted_property('name'),
            'idnumber' => $this->get_formatted_property('idnumber'),
            'description' => $this->get_formatted_property('description'),
            'acccountid' => $this->get_formatted_property('acccountid'),
            'archived' => $this->get('archived') ? 'True' : 'False',
        ];
    }
}