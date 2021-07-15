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

namespace local_acccount\event;

use core\event\base;
use local_acccount\acccount_user;

defined('MOODLE_INTERNAL') || die();

class user_acccount_updated extends base {

    protected function init()
    {
        $this->data['objecttable'] = 'local_acccount_user';
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public static function create_from_object(acccount_user $acccountUser, \stdClass $oldRecord): user_acccount_updated {
        $params = [
            'context' => \context_system::instance(),
            'objectid' => $acccountUser->get('id'),
            'relateduserid' => $acccountUser->get('userid'),
            'other' => ['acccountid' => $acccountUser->get('acccountid'), 'oldacccountid' => $oldRecord->acccountid]
        ];

        $event = static::create($params);
        $event->add_record_snapshot('local_acccount_user', $acccountUser->to_record());
        return $event;
    }
}