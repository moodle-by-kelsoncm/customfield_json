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

namespace customfield_json;

use customfield_json\form\json_textarea;
use customfield_json\local\json_value;

/**
 * Value of a JSON custom field for one instance (course, cohort, group...).
 *
 * @package    customfield_json
 * @copyright  2026 onwards Kelson da Costa Medeiros
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_controller extends \core_customfield\data_controller {
    /**
     * Column of the customfield_data table that holds the value.
     *
     * @return string
     */
    public function datafield(): string {
        return 'value';
    }

    /**
     * Default value of the field, in canonical form.
     *
     * @return string
     */
    public function get_default_value() {
        return (string)$this->get_field()->get_configdata_property('defaultvalue');
    }

    /**
     * Add the field to the instance form, with the visual editor on top of a textarea.
     *
     * @param \MoodleQuickForm $mform
     */
    public function instance_form_definition(\MoodleQuickForm $mform) {
        $elementname = $this->get_form_element_name();
        json_textarea::register();
        $mform->addElement(
            json_textarea::TYPE,
            $elementname,
            $this->get_field()->get_formatted_name(),
            ['rows' => 10, 'cols' => 60, 'data-customfield-json-source' => '1', 'spellcheck' => 'false']
        );
        // Read as raw text, like the core textarea field: instance_form_validation() only accepts
        // valid JSON and instance_form_save() stores it re-encoded, with HTML characters escaped.
        $mform->setType($elementname, PARAM_RAW);
        if ($this->get_field()->get_configdata_property('required')) {
            $mform->addRule($elementname, null, 'required', null, 'client');
        }
    }

    /**
     * Validate the value typed by the user.
     *
     * @param array $data
     * @param array $files
     * @return array Errors by element name.
     */
    public function instance_form_validation(array $data, array $files): array {
        $errors = parent::instance_form_validation($data, $files);
        $elementname = $this->get_form_element_name();
        if (isset($data[$elementname]) && !isset($errors[$elementname])) {
            $error = json_value::validate((string)$data[$elementname], $this->get_max_kb());
            if ($error !== null) {
                $errors[$elementname] = $error;
            }
        }
        return $errors;
    }

    /**
     * Save the value in canonical form, unless it is not valid JSON.
     *
     * Uploads and web services may save custom field data without running the form validation;
     * an invalid value coming from them is ignored and the stored value is kept.
     *
     * @param \stdClass $datanew
     */
    public function instance_form_save(\stdClass $datanew) {
        $elementname = $this->get_form_element_name();
        if (!property_exists($datanew, $elementname)) {
            return;
        }
        $value = $datanew->{$elementname};
        if (!is_string($value) || json_value::validate($value, $this->get_max_kb()) !== null) {
            $shortname = $this->get_field()->get('shortname');
            debugging("Invalid JSON ignored for the custom field {$shortname}.", DEBUG_DEVELOPER);
            return;
        }
        $this->data->set('valueformat', FORMAT_PLAIN);
        $datanew = clone $datanew;
        $datanew->{$elementname} = json_value::normalise($value);
        parent::instance_form_save($datanew);
    }

    /**
     * Put the readable form of the value in the instance form.
     *
     * @param \stdClass $instance
     */
    public function instance_form_before_set_data(\stdClass $instance) {
        $value = (string)$this->get_value();
        $instance->{$this->get_form_element_name()} = trim($value) === '' ? '' : json_value::readable($value);
    }

    /**
     * Display the document, escaped, pretty-printed and highlighted.
     *
     * @return string|null HTML, or null when there is no value.
     */
    public function export_value() {
        global $OUTPUT;

        $value = $this->get_value();
        if ($this->is_empty($value)) {
            return null;
        }
        return $OUTPUT->render_from_template('customfield_json/display', ['html' => json_value::highlight($value)]);
    }

    /**
     * Maximum size of a value, in kilobytes.
     *
     * @return int
     */
    protected function get_max_kb(): int {
        return json_value::max_kb($this->get_field()->get_configdata_property('maxsize'));
    }
}
