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
 * @package    local_notification
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function local_notification_before_footer() {
    global $DB, $USER;

//    $selectUnreadNotificationSql = "SELECT ln.id AS id, ln.notificationtext AS text, ln.notificationtype AS type
//                    FROM {local_notification} ln
//                    LEFT JOIN {local_notification_read} lnr ON ln.id = lnr.notificationid
//                    WHERE  lnr.userid <> :userid";
//
//    $params = [
//        'userid' => $USER->id,
//    ];
//
//    $notificationlist = $DB->get_record_sql($selectUnreadNotificationSql, $params);
    $notificationlist = $DB->get_records('local_notification');

//    foreach ($notificationlist as $notification) {
//        \core\notification::add($notification->notificationtext, $notification->notificationtype);
//
//        $readrecord = new stdClass();
//        $readrecord->notificationid = $notification->id;
//        $readrecord->userid = $USER->id;
//        $readrecord->timeread = time();
//
//        $DB->insert_record('local_notification_read', $readrecord);
//    }
}