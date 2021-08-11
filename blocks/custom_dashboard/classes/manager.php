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
 * Class manager and methods for managing the data
 *
 * @package    block_custom_dashboard
 * @author     Joey Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_custom_dashboard;

use block_custom_dashboard\mapper;

defined('MOODLE_INTERNAL') || die();

require_once("{$CFG->libdir}/externallib.php");
require_once("{$CFG->libdir}/completionlib.php");

class manager
{
    const PROGRESS_BAR_COLOR_COMPLETED = '#1890FF';
    const PROGRESS_BAR_COLOR_IN_PROGRESS = '#48D597';
    const PROGRESS_BAR_COLOR_NOT_STARTED = '#DA291C';

    private mapper $mapper;


    function __construct()
    {
        $this->mapper = new mapper();
    }

    public function get_campaign_statistics_for_dashboard(): array
    {
        $results = $this->mapper->get_campaign_dashboard_statistics();
        $table_records = $this->reformat_campaigns_data_for_table_display($results['table_records']);
        $module_records = $this->reformat_modules_data_for_table_display($results['module_records']);

        return [
            'selector_records' => $results['selector_records'],
            'table_records' => $table_records,
            'module_records' => $module_records,
        ];
    }

    private function reformat_modules_data_for_table_display(array $records): array
    {
        $results = [];
        foreach ($records as $key => $modules) {
            $campaign_results = [];
            foreach ($modules as $course_id => $module) {
                $rate = self::convert_float_to_percentage($module['completed_number'], count($module['students']));
                array_push($campaign_results, [
                    'campaign' => $module['module_name'],
                    'key' => $course_id,
                    'rate' => $rate,
                    'students' => count(array_unique($module['students'])),
                    'progress' => [
                        [
                            'name' => 'Completed',
                            'value' => $rate,
                            'color' => self::PROGRESS_BAR_COLOR_COMPLETED,
                        ],
                        [
                            'name' => 'In progress',
                            'value' => self::convert_float_to_percentage($module['in_progress_number'], count($module['students'])),
                            'color' => self::PROGRESS_BAR_COLOR_IN_PROGRESS,
                        ],
                        [
                            'name' => 'Not started',
                            'value' => self::convert_float_to_percentage($module['not_started_number'], count($module['students'])),
                            'color' => self::PROGRESS_BAR_COLOR_NOT_STARTED,
                        ]
                    ]
                ]);
            }
            $results[$key] = $campaign_results;
        }
        return $results;
    }

    private function reformat_campaigns_data_for_table_display(array $records): array
    {
        $results = [];
        foreach ($records as $record) {
            $program_id = $record['key'];
            $rate = self::convert_float_to_percentage($record['completed_number'], count($record['students']));
            array_push($results, [
                'campaign' => $record['campaign'],
                'key' => $program_id,
                'rate' => $rate,
                'students' => count(array_unique($record['students'])),
                'progress' => [
                    [
                        'name' => 'Completed',
                        'value' => $rate,
                        'color' => self::PROGRESS_BAR_COLOR_COMPLETED,
                    ],
                    [
                        'name' => 'In progress',
                        'value' => self::convert_float_to_percentage($record['in_progress_number'], count($record['students'])),
                        'color' => self::PROGRESS_BAR_COLOR_IN_PROGRESS,
                    ],
                    [
                        'name' => 'Not started',
                        'value' => self::convert_float_to_percentage($record['not_started_number'], count($record['students'])),
                        'color' => self::PROGRESS_BAR_COLOR_NOT_STARTED,
                    ]
                ]
            ]);
        }

        return $results;
    }

    private static function convert_float_to_percentage(int $numerator, int $denominator): int
    {
        return $denominator ? (int)round(($numerator / $denominator) * 100 ) : 0;
    }
}