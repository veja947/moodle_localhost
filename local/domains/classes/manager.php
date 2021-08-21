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

use local_domains\domain;
use local_domains\subdomain;

defined('MOODLE_INTERNAL') || die();
class manager
{
    const DOMAIN_ACTION_VERIFY = 'verify';
    const DOMAIN_ACTION_DELETE = 'delete';
    const DOMAIN_ACTION_PRIMARY_DOMAIN = 'primary';

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

    public function get_active_subdomains(): array
    {
        global $DB;
        $active_subdomains = $DB->get_records(subdomain::TABLE);
        $subdomains = [];
        foreach ($active_subdomains as $record) {
            $subdomains[$record->id] = new subdomain(0, $record);
        }
        return $subdomains ?? [];
    }

    public function get_domains_or_subdomains_display_array(array $domains): array
    {
        $result = [];
        foreach ($domains as $domain) {
            $result[$domain->get('id')] = $domain->get_properties_display();
        }
        return $result;
    }

    private function get_domain_by_id(int $id, \moodle_url $exceptionlink = null, bool $showexception = true): ?domain
    {
        $domains = $this->get_active_domains();
        if (array_key_exists($id, $domains)) {
            return $domains[$id];
        }
        if ($showexception) {
            throw new \moodle_exception('domainnotfound', domain::TABLE,
                $exceptionlink ?: self::get_base_url());
        }
        return null;
    }

    private function get_subdomain_by_id(int $id, \moodle_url $exceptionlink = null, bool $showexception = true): ?subdomain
    {
        $subdomains = $this->get_active_subdomains();
        if (array_key_exists($id, $subdomains)) {
            return $subdomains[$id];
        }
        if ($showexception) {
            throw new \moodle_exception('subdomainnotfound', subdomain::TABLE,
                $exceptionlink ?: self::get_base_url());
        }
        return null;
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

    public function create_subdomain(\stdClass $data): subdomain
    {
        $subdomain = new subdomain(0, $data);
        $subdomain->create();
        return $subdomain;
    }

    public function primary_domain(int $id): ?domain
    {
        global $DB;
        if (!$DB->get_record(domain::TABLE, ['id' => $id, 'status' => 1])) {
            return null;
        }

        $domain = $this->get_domain_by_id($id);

        // revert current primary domain to non-primary
        if ($previous_domain_obj = $DB->get_record(domain::TABLE, ['primarydomain' => 1], 'id')) {
            $previous_domain_id = $previous_domain_obj->id;
            $previous_domain = $this->get_domain_by_id($previous_domain_id);
            $this->update_domain($previous_domain, (object)[
                'primarydomain' => 0,
            ]);
        }

        // update new primary domain
        return $this->update_domain($domain, (object)[
            'primarydomain' => 1,
        ]);
    }

    /**
     * Delete a Domain
     *
     * @param int $id
     * @throws \moodle_exception
     */
    public function delete_domain(int $id): ?domain
    {
        global $DB;
        if (!$DB->get_record(domain::TABLE, ['id' => $id])) {
            return null;
        }

        $domain = $this->get_domain_by_id($id);

        // delete domain record
        $domain->delete();

        return $domain;
    }

    /**
     * Delete a SubDomain
     *
     * @param int $id
     * @throws \moodle_exception
     */
    public function delete_subdomain(int $id): ?subdomain
    {
        global $DB;
        if (!$DB->get_record(subdomain::TABLE, ['id' => $id])) {
            return null;
        }

        $subdomain = $this->get_subdomain_by_id($id);

        // delete subdomain record
        $subdomain->delete();

        return $subdomain;
    }

    private function update_domain(domain $domain, \stdClass $newData): ?domain
    {
        foreach ($newData as $key => $value) {
            if (domain::has_property($key) && $key !== 'id') {
                $domain->set($key, $value);
            }
        }
        $domain->save();
        return $domain;
    }

    /**
     * verify a Domain
     *
     * @param int $id
     * @throws \moodle_exception
     */
    public function verify_domain(int $id): ?domain
    {
        global $DB;
        if (!$DB->get_record(domain::TABLE, ['id' => $id])) {
            return null;
        }

        $domain = $this->get_domain_by_id($id);

        // verify token
        $name = $domain->get('name');
        $token = $domain->get('token');
        $status = $this->verify_token($name, $token);

        // update domain
        return $this->update_domain($domain, (object)[
            'status' => (int)$status,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function generate_token(): string
    {
        $token = random_bytes(15);
        return bin2hex($token);
    }

    private function verify_token(string $domain, string $token): bool
    {
        $records = dns_get_record($domain, DNS_TXT);
        foreach ($records as $record) {
            if ($record['txt'] === $token) {
                return true;
            }
        }
        return false;
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