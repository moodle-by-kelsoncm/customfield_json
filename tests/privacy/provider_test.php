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

use core_privacy\local\request\writer;
use core_privacy\tests\provider_testcase;

/**
 * Tests for the privacy provider of customfield_json.
 *
 * @package    customfield_json
 * @copyright  2026 onwards Kelson da Costa Medeiros
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \customfield_json\privacy\provider
 */
final class provider_test extends provider_testcase {
    /**
     * The value is exported as readable JSON.
     */
    public function test_export_customfield_data(): void {
        $this->resetAfterTest();
        /** @var \core_customfield_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_customfield');
        $category = $generator->create_category();
        $field = $generator->create_field([
            'categoryid' => $category->get('id'),
            'shortname' => 'integration',
            'type' => 'json',
        ]);
        $course = $this->getDataGenerator()->create_course();
        $data = $generator->add_instance_data($field, $course->id, '{"note":"<b>bold</b>"}');

        $subcontext = ['Custom fields data', $data->get('id')];
        \core_customfield\privacy\provider::export_customfield_data($data, $subcontext);

        $exported = writer::with_context($data->get_context())->get_data($subcontext);
        $this->assertSame("{\n    \"note\": \"<b>bold</b>\"\n}", $exported->value);
    }

    /**
     * The plugin declares that it stores no personal data on its own.
     */
    public function test_get_reason(): void {
        $this->assertSame('privacy:metadata', provider::get_reason());
        $this->assertNotEmpty(get_string(provider::get_reason(), 'customfield_json'));
    }
}
