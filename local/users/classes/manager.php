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


use local_domains\domain;

defined('MOODLE_INTERNAL') || die();
class manager
{
    const DEFAULT_USER_EMAIL = 'Moodle2012!';

    public function users_file_handler(string $file_string): ?array
    {
        $results = [];
        $file_to_arrays =  explode("\r\n", $file_string);
        $header_array = explode(",", $file_to_arrays[0]);
        foreach (array_slice($file_to_arrays, 1) as $k => $value) {
            $user_array = array_combine($header_array, explode(",", $value));
            if (!$this->check_email_domain_verified($user_array['email'])) {
                return null;
            }
            $user_array['password'] = self::DEFAULT_USER_EMAIL;
            array_push($results, $user_array);
        }
        return $results;
    }

    public function setting_to_new_user(int $user_id): \stdClass
    {
        global $DB;
        $user_obj = $DB->get_record('user', ['id'=> $user_id]);
        $noreplyuser = \core_user::get_noreply_user();
        email_to_user($user_obj, $noreplyuser,
            'email subject',
            'email message',
            '<h1>email html</h1>');
        set_user_preference('create_password', 1, $user_obj);
        return $user_obj;
    }

    public function check_email_domain_verified(string $email): bool
    {
        $varified_domains = $this->get_verified_domains();
        $domain_name = substr($email, strpos($email, '@') + 1);
        return in_array($domain_name, $varified_domains);
    }

    public function check_users_emails_in_file(array $contentarray): bool
    {
        $varifieddomains = $this->get_verified_domains();
        foreach ($contentarray as $value) {
            if (!strpos($value, '@')) {
                continue;
            }
            $domainname = substr($value, strpos($value, '@') + 1);
            if (!in_array($domainname, $varifieddomains)) {
                return false;
            }
        }
        return true;
    }


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

    /**
     * Upload users page url
     * @return \moodle_url
     */
    public static function get_upload_users_url(): \moodle_url
    {
        return new \moodle_url('/local/users/uploadusers.php');
    }

    /**
     * get verified domains array
     * @return domain[]
     * @throws \dml_exception
     */
    public function get_verified_domains(): array
    {
        global $DB;
        $results = [];
        $rs = $DB->get_recordset(domain::TABLE, ['status' => 1], '', 'id, name');
        foreach ($rs as $record) {
            array_push($results, $record->name);
        }
        $rs->close();
        return $results;
    }
}