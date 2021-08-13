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
 * Class setting for helping admin to setup this plugin
 *
 * @package    block_custom_dashboard
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_custom_dashboard\customize;

require_once("{$CFG->libdir}/externallib.php");
require_once("{$CFG->libdir}/completionlib.php");
defined('MOODLE_INTERNAL') || die();

class setting
{

    const PROGRAM_TABLE_NAME = 'local_program';
    const PROGRAM_COURSES_TABLE_NAME = 'local_program_course';

    const COMPLETION_STATUS_NOT_STARTED = 'not_started';
    const COMPLETION_STATUS_IN_PROGRESS = 'in_progress';
    const COMPLETION_STATUS_COMPLETED = 'completed';

    const PROGRESS_BAR_COLOR_COMPLETED = '#1890FF';
    const PROGRESS_BAR_COLOR_IN_PROGRESS = '#48D597';
    const PROGRESS_BAR_COLOR_NOT_STARTED = '#DA291C';
}