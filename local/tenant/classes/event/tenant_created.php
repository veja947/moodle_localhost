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
 * Class tenant
 *
 * @package    local_tenant
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_tenant\event;

use core\event\base;
use local_tenant\tenant;
use local_tenant\manager;

defined('MOODLE_INTERNAL') || die();

class tenant_created extends base
{
    /**
     * Init method.
     *
     * @return void
     */
    protected function init()
    {
        $this->data['objecttable'] = 'local_tenant';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Creates an instance of the event from an object
     * @param tenant $tenant
     * @return tenant_created
     */
    public static function create_from_object(tenant $tenant): tenant_created
    {
        $event = static::create([
            'context' => \context_system::instance(),
            'objectid' => $tenant->get('id')
        ]);
        $event->add_record_snapshot(tenant::TABLE, $tenant->to_record());
        return $event;
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name()
    {
        return get_string('eventtenantcreated', 'local_tenant');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description()
    {
        return "The user with id '$this->userid' created the tenant with id '$this->objectid'";
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url()
    {
        return new \moodle_url(manager::get_view_url($this->objectid));
    }

    /**
     * This is used when restoring course logs where it is required that we
     * map the objectid to it's new value in the new course.
     *
     * @return int|string
     */
    public static function get_objectid_mapping()
    {
        return base::NOT_MAPPED;
    }

    /**
     * This is used when restoring course logs where it is required that we
     * map the information in 'other' to it's new value in the new course.
     *
     * @return array|bool
     */
    public static function get_other_mapping()
    {
        return false;
    }
}