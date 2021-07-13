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
 * @package    local_acccount
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_acccount;
defined('MOODLE_INTERNAL') || die();



class acccount extends \core\persistent {

    /** The table name. */
    const TABLE = 'local_acccount';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'name' => array(
                'type' => PARAM_TEXT,
                'description' => 'The acccount name.',
            ),
            'sitename' => array(
                'type' => PARAM_TEXT,
                'description' => 'The acccount site name.',
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'siteshortname' => array(
                'type' => PARAM_TEXT,
                'description' => 'The acccount site short name.',
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'idnumber' => array(
                'type' => PARAM_RAW,
                'description' => 'An id number used for external services.',
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'loginurl' => array(
                'type' => PARAM_TEXT,
                'description' => 'The alternative acccount login url.',
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
                'description' => 'Time the acccount was archived.',
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'isdefault' => array(
                'type' => PARAM_INT,
                'description' => 'Is default acccount',
                'default' => 0,
            ),
            'categoryid' => array(
                'type' => PARAM_INT,
                'description' => 'Category ID this acccount is linked to',
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'cssconfig' => array(
                'type' => PARAM_RAW,
                'description' => 'The CSS config for this acccount.',
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
        ];
    }

    /**
     * Tenant name ready for display
     * @return string
     */
    public function get_formatted_name() : string {
        return format_string($this->get('name'), true,
            ['context' => \context_system::instance(), 'escape' => false]);
    }
}