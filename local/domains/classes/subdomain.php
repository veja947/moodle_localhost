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

namespace local_domains;

use core\persistent;

defined('MOODLE_INTERNAL') || die();

class subdomain extends persistent
{
    /** The table name. */
    const TABLE = 'local_domains_subdomains';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties()
    {
        return [
            'name' => array(
                'type' => PARAM_TEXT,
                'description' => 'The subdomain name.',
            ),
            'status' => array(
                'type' => PARAM_INT,
                'description' => 'Is verified or not.',
                'default' => 0,
            ),
            'primarydomain' => array(
                'type' => PARAM_INT,
                'description' => 'Is subdomain the primary one or not.',
                'default' => 0,
            ),
            'tenantid' => array(
                'type' => PARAM_INT,
                'description' => 'Tenant id',
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'timecreated' => array(
                'type' => PARAM_INT,
                'description' => 'Time the domain was verified.',
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
            'domainid' => array(
                'type' => PARAM_INT,
                'description' => 'Domain id',
                'default' => null,
                'null' => NULL_ALLOWED,
            ),
        ];
    }

    public function get_formatted_property($name): string
    {
        return format_string($this->get($name), true,
                ['context' => \context_system::instance(), 'escape' => false]) ?? '';
    }

    public function get_formatted_subdomain_full_name(): string
    {
        return $this->get_formatted_property('name') . '.' . $this->get_domain_name();
    }

    public function get_formatted_date_property($name): string
    {
        $date = $this->get_formatted_property($name);
        return gmdate("M d, Y", (int)$date) ?? '';
    }

    public function get_domain_name(): string
    {
        global $DB;
        return $DB->get_record(domain::TABLE,
                ['id' => $this->get_formatted_property('domainid')],
                'name')->name ?? domain::DEFAULT_FTNT_INFO_DOMAIN;
    }

    public function get_subdomain_cname(): string
    {
        return 'cname' . '.' . $this->get_domain_name();
    }

    public function get_properties_display(): array
    {
        return [
            'id' => $this->get('id'),
            'name' => $this->get_formatted_subdomain_full_name(),
            'status' => $this->get('status'),
            'primarydomain' => $this->get('primarydomain') ? 1 : 0,
            'tenantid' => $this->get_formatted_property('tenantid'),
            'timecreated' => $this->get_formatted_date_property('timecreated'),
            'domainid' => $this->get_formatted_property('domainid'),
            'cname' => $this->get_subdomain_cname(),
        ];
    }
}