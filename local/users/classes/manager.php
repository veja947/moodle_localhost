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
 * Class manager and methods for managing the list of users
 *
 * @package    local_users
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_users;


defined('MOODLE_INTERNAL') || die();
class manager
{
    /**
     * Returns list of users in the system
     *
     * @return array
     */
    public function get_all_users(): array
    {
        global $DB;
        $users = (array)get_users(true, '', false, null, 'firstname ASC',
            '', '', $page='1',
            '10', 'id, username, email, lastaccess');

        $userslist = (array)get_users_listing('lastaccess', 'ASC', 1, 10);
        $result = [];
        foreach ($users as $user) {
            $result[$user->id] = (array)$user;
        }
        return $result;
    }
}