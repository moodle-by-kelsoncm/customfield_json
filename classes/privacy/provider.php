<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace customfield_json\privacy;

use core_customfield\data_controller;
use core_customfield\privacy\customfield_provider;
use core_privacy\local\request\writer;
use customfield_json\local\json_value;

/**
 * Privacy Subsystem for customfield_json.
 *
 * The values are stored by the custom fields subsystem; this plugin only exports them.
 *
 * @package    customfield_json
 * @copyright  2026 onwards Kelson da Costa Medeiros
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\null_provider, customfield_provider {
    /**
     * Reason why this plugin stores no personal data on its own.
     *
     * @return string Language string identifier.
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }

    /**
     * Export the value of a field, as readable JSON.
     *
     * @param data_controller $data
     * @param \stdClass $exportdata Data prepared by the custom fields subsystem.
     * @param array $subcontext
     */
    public static function export_customfield_data(data_controller $data, \stdClass $exportdata, array $subcontext) {
        // The subsystem exports the displayed value (HTML); export the stored document instead.
        $value = (string)$data->get_value();
        $exportdata->value = trim($value) === '' ? '' : json_value::readable($value);
        writer::with_context($data->get_context())->export_data($subcontext, $exportdata);
    }

    /**
     * Nothing to do before data is deleted: the plugin has no files or tables.
     *
     * @param string $dataidstest
     * @param array $params
     * @param array $contextids
     */
    public static function before_delete_data(string $dataidstest, array $params, array $contextids) {
    }

    /**
     * Nothing to do before fields are deleted: the plugin has no files or tables.
     *
     * @param string $fieldidstest
     * @param array $params
     * @param array $contextids
     */
    public static function before_delete_fields(string $fieldidstest, array $params, array $contextids) {
    }
}
