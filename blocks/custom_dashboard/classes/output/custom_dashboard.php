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
 * Class containing data for block_custom_dashboard block.
 *
 * @package    block_custom_dashboard
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_custom_dashboard\output;

use renderable;
use renderer_base;
use templatable;
use block_custom_dashboard\manager;

defined('MOODLE_INTERNAL') || die();

class customdashboard implements renderable, templatable
{
    /**
     * @var object An object containing the configuration information for the current instance of this block.
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param object $config An object containing the configuration information for the current instance of this block.
     */
    public function __construct($config) {
        $this->config = $config;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return \stdClass
     */
    public function export_for_template(renderer_base $output)
    {
        global $USER, $OUTPUT;

        $data = new \stdClass();

        if (!isset($this->config->total_users)) {
            $totalusers = manager::get_users_count();
            $data->totalusernumber =  count($totalusers);
        }

        return $data;
    }
}