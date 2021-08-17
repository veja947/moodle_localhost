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
 * Class manager and methods for managing the list of domains
 *
 * @package    local_domains
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_domains;

defined('MOODLE_INTERNAL') || die();
class manager
{
    const DOMAIN_ACTION_VERIFY = 'verify';
    const DOMAIN_ACTION_DELETE = 'delete';

    /**
     * Returns list of active domains in the system
     *
     * @return domain[]
     */
    public function get_active_domains(): array
    {
        global $DB;
        $active_domains = $DB->get_records(domain::TABLE);
        $domains = [];
        foreach ($active_domains as $record) {
            $domains[$record->id] = new domain(0, $record);
        }
        return $domains ?? [];
    }

    public function get_domains_display_array(array $domains): array
    {
        $result = [];
        foreach ($domains as $domain) {
            $result[$domain->get('id')] = $domain->get_properties_display();
        }
        return $result;
    }

    /**
     * Create a new Domain
     *
     * @param \stdClass $data
     */
    public function create_domain(\stdClass $data): domain
    {
        global $DB;
        $domain = new domain(0, $data);
        $domain->create();
        return $domain;
    }

    /**
     * Base URL to view domains list
     * @return \moodle_url
     */
    public static function get_base_url(): \moodle_url
    {
        return new \moodle_url('/local/domains/index.php');
    }

    /**
     * Editor URL to view domains form
     * @return \moodle_url
     */
    public static function get_editor_url(): \moodle_url
    {
        return new \moodle_url('/local/domains/edit.php');
    }
}