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
 * Definition of a custom field holding a JSON document.
 *
 * @package    customfield_json
 * @copyright  2026 onwards Kelson da Costa Medeiros
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class field_controller extends \core_customfield\field_controller {
    /** @var string Plugin type. */
    const TYPE = 'json';

    /**
     * Add the settings of the field to the configuration form.
     *
     * @param \MoodleQuickForm $mform
     */
    public function config_form_definition(\MoodleQuickForm $mform) {
        $mform->addElement('header', 'header_specificsettings', get_string('specificsettings', 'customfield_json'));
        $mform->setExpanded('header_specificsettings', true);

        $mform->addElement('text', 'configdata[maxsize]', get_string('maxsize', 'customfield_json'), ['size' => 6]);
        $mform->setType('configdata[maxsize]', PARAM_INT);
        $mform->setDefault('configdata[maxsize]', json_value::DEFAULT_MAX_KB);
        $mform->addHelpButton('configdata[maxsize]', 'maxsize', 'customfield_json');

        json_textarea::register();
        $mform->addElement(
            json_textarea::TYPE,
            'configdata[defaultvalue]',
            get_string('defaultvalue', 'core_customfield'),
            ['rows' => 6, 'cols' => 60, 'data-customfield-json-source' => '1', 'spellcheck' => 'false']
        );
        // Read as raw text: config_form_validation() only accepts valid JSON and save() stores it
        // re-encoded, with HTML characters escaped.
        $mform->setType('configdata[defaultvalue]', PARAM_RAW);
    }

    /**
     * Validate the settings of the field.
     *
     * @param array $data
     * @param array $files
     * @return array Errors by element name.
     */
    public function config_form_validation(array $data, $files = []): array {
        $errors = parent::config_form_validation($data, $files);

        $maxkb = (int)($data['configdata']['maxsize'] ?? json_value::DEFAULT_MAX_KB);
        if ($maxkb < 1 || $maxkb > json_value::LIMIT_MAX_KB) {
            $errors['configdata[maxsize]'] = get_string('errormaxsize', 'customfield_json', json_value::LIMIT_MAX_KB);
            $maxkb = json_value::LIMIT_MAX_KB;
        }
        $error = json_value::validate((string)($data['configdata']['defaultvalue'] ?? ''), $maxkb);
        if ($error !== null) {
            $errors['configdata[defaultvalue]'] = $error;
        }
        return $errors;
    }

    /**
     * Save the field, with the default value in canonical form.
     *
     * An invalid default value (only possible when the form validation is skipped) is cleared.
     */
    public function save() {
        $configdata = $this->get('configdata');
        $default = $configdata['defaultvalue'] ?? '';
        if (is_string($default) && json_value::validate($default, json_value::LIMIT_MAX_KB) === null) {
            $configdata['defaultvalue'] = json_value::normalise($default);
        } else {
            $configdata['defaultvalue'] = '';
        }
        $this->field->set('configdata', json_encode($configdata));
        parent::save();
    }
}
