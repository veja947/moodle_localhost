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


class acccount_user extends \core\persistent {

    /** The table name. */
    const TABLE = 'local_acccount_user';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'userid' => array(
                'type' => PARAM_INT,
                'description' => 'User id',
            ),
            'acccountid' => array(
                'type' => PARAM_INT,
                'description' => 'Acccount id',
            ),
        );
    }

    /**
     * Creates an instance for a given user
     *
     * @param int $userid
     * @return acccount_user
     */
    public static function create_for_user(int $userid) : self {
        global $DB;
        $record = $DB->get_record(self::TABLE, ['userid' => $userid]);
        if ($record) {
            return new self(0, $record);
        } else {
            return new self(0, (object)['userid' => $userid]);
        }
    }
}