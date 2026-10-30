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

namespace customfield_json\form;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/form/textarea.php');

/**
 * Textarea that starts the visual JSON editor when it is rendered.
 *
 * Custom field forms are often dynamic forms (modals), which collect the JavaScript of the page
 * only while the form is rendered, after its definition. Requiring the editor here, instead of in
 * the form definition, makes it start in normal and in dynamic forms.
 *
 * @package    customfield_json
 * @copyright  2026 onwards Kelson da Costa Medeiros
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class json_textarea extends \MoodleQuickForm_textarea {
    /** @var string Element type name used in addElement(). */
    public const TYPE = 'customfield_json_textarea';

    /**
     * Register the element type in the forms library.
     */
    public static function register(): void {
        \MoodleQuickForm::registerElementType(self::TYPE, __FILE__, self::class);
    }

    /**
     * Context for the textarea template, requiring the editor for this element.
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(\renderer_base $output) {
        $this->require_editor();
        return parent::export_for_template($output);
    }

    /**
     * HTML of the textarea, used by renderers that do not use templates.
     *
     * @return string
     */
    public function toHtml() { // phpcs:ignore moodle.NamingConventions.ValidFunctionName.LowercaseMethod
        $this->require_editor();
        return parent::toHtml();
    }

    /**
     * Start the visual editor on this textarea, unless the element is frozen (locked field).
     */
    protected function require_editor(): void {
        global $PAGE;

        if ($this->isFrozen()) {
            return;
        }
        $PAGE->requires->js_call_amd('customfield_json/editor', 'init', ['#' . $this->getAttribute('id')]);
    }
}
