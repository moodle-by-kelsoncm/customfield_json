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

use core_customfield\category_controller;
use core_customfield_generator;
use core_customfield_test_instance_form;

/**
 * Tests for the JSON custom field, in the course and cohort areas.
 *
 * @package    customfield_json
 * @copyright  2026 onwards Kelson da Costa Medeiros
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \customfield_json\field_controller
 * @covers     \customfield_json\data_controller
 */
final class plugin_test extends \advanced_testcase {
    /**
     * Load the instance form fixture of the custom fields subsystem.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/customfield/tests/fixtures/test_instance_form.php');
        parent::setUpBeforeClass();
    }

    /**
     * Custom fields generator.
     *
     * @return core_customfield_generator
     */
    private function get_generator(): core_customfield_generator {
        return $this->getDataGenerator()->get_plugin_generator('core_customfield');
    }

    /**
     * Create a JSON field in a new category of an area.
     *
     * @param string $component
     * @param string $area
     * @param array $configdata
     * @return field_controller
     */
    private function create_field(string $component, string $area, array $configdata = []): field_controller {
        $category = $this->get_generator()->create_category(['component' => $component, 'area' => $area]);
        return $this->get_generator()->create_field([
            'categoryid' => $category->get('id'),
            'shortname' => 'integration',
            'type' => 'json',
            'configdata' => $configdata,
        ]);
    }

    /**
     * The controllers of the plugin are used for JSON fields.
     */
    public function test_initialise(): void {
        $this->resetAfterTest();
        $field = $this->create_field('core_course', 'course');
        $course = $this->getDataGenerator()->create_course();
        $data = $this->get_generator()->add_instance_data($field, $course->id, '{"a":1}');

        $this->assertInstanceOf(field_controller::class, \core_customfield\field_controller::create($field->get('id')));
        $this->assertInstanceOf(data_controller::class, \core_customfield\data_controller::create($data->get('id')));
    }

    /**
     * The default value is stored in canonical form.
     */
    public function test_save_default_value(): void {
        $this->resetAfterTest();
        $field = $this->create_field('core_course', 'course', ['defaultvalue' => '{"b":[1,2],"tag":"<i>"}']);

        $field = \core_customfield\field_controller::create($field->get('id'));
        $expected = "{\n    \"b\": [\n        1,\n        2\n    ],\n    \"tag\": \"" . chr(92) . "u003Ci" . chr(92) . "u003E\"\n}";
        $this->assertSame($expected, $field->get_configdata_property('defaultvalue'));
    }

    /**
     * The configuration form rejects an invalid default value and an invalid size.
     */
    public function test_config_form_validation(): void {
        $this->resetAfterTest();
        $field = $this->create_field('core_course', 'course');

        $errors = $field->config_form_validation(['configdata' => ['maxsize' => 64, 'defaultvalue' => '{"a":1}']]);
        $this->assertArrayNotHasKey('configdata[defaultvalue]', $errors);
        $this->assertArrayNotHasKey('configdata[maxsize]', $errors);

        $errors = $field->config_form_validation(['configdata' => ['maxsize' => 0, 'defaultvalue' => '{a:1}']]);
        $this->assertArrayHasKey('configdata[defaultvalue]', $errors);
        $this->assertArrayHasKey('configdata[maxsize]', $errors);
    }

    /**
     * The instance form of a course accepts only valid JSON and stores it in canonical form.
     */
    public function test_course_instance_form(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $field = $this->create_field('core_course', 'course', ['required' => 1]);
        $handler = $field->get_handler();
        $course = $this->getDataGenerator()->create_course();

        $submitdata = (array)$course + ['customfield_integration' => '{"code": "TADS",'];
        core_customfield_test_instance_form::mock_submit($submitdata, []);
        $form = new core_customfield_test_instance_form('POST', ['handler' => $handler, 'instance' => $course]);
        $this->assertFalse($form->is_validated());

        $submitdata['customfield_integration'] = '{"code": "TADS", "year": 2026}';
        core_customfield_test_instance_form::mock_submit($submitdata, []);
        $form = new core_customfield_test_instance_form('POST', ['handler' => $handler, 'instance' => $course]);
        $this->assertTrue($form->is_validated());
        $handler->instance_form_save($form->get_data());

        $data = $handler->get_instance_data($course->id, true)[$field->get('id')];
        $this->assertSame("{\n    \"code\": \"TADS\",\n    \"year\": 2026\n}", $data->get_value());
    }

    /**
     * Values saved without the form are checked too: invalid ones are ignored.
     */
    public function test_cohort_save_without_form(): void {
        $this->resetAfterTest();
        $field = $this->create_field('core_cohort', 'cohort', ['maxsize' => 1]);
        $cohort = $this->getDataGenerator()->create_cohort();

        $data = $this->get_generator()->add_instance_data($field, $cohort->id, '[true,null,1.0]');
        $this->assertSame("[\n    true,\n    null,\n    1.0\n]", $data->get_value());

        $this->get_generator()->add_instance_data($field, $cohort->id, 'not json');
        $this->assertDebuggingCalled('Invalid JSON ignored for the custom field integration.');
        $this->get_generator()->add_instance_data($field, $cohort->id, json_encode(str_repeat('x', 2048)));
        $this->assertDebuggingCalled('Invalid JSON ignored for the custom field integration.');

        $data = $field->get_handler()->get_instance_data($cohort->id, true)[$field->get('id')];
        $this->assertSame("[\n    true,\n    null,\n    1.0\n]", $data->get_value());
    }

    /**
     * The displayed value is highlighted and escaped; the form gets the readable value.
     */
    public function test_display_and_form_value(): void {
        $this->resetAfterTest();
        $field = $this->create_field('core_course', 'course');
        $course = $this->getDataGenerator()->create_course();
        $data = $this->get_generator()->add_instance_data($field, $course->id, '{"note":"<b>bold</b>"}');

        $html = $data->export_value();
        $this->assertStringContainsString('customfield-json-display', $html);
        $this->assertStringContainsString('&lt;b&gt;bold&lt;/b&gt;', $html);
        $this->assertStringNotContainsString('<b>', $html);

        $instance = new \stdClass();
        $data->instance_form_before_set_data($instance);
        $this->assertSame("{\n    \"note\": \"<b>bold</b>\"\n}", $instance->customfield_integration);
    }

    /**
     * Without a value, the default value is displayed, and an empty field shows nothing.
     */
    public function test_default_and_empty(): void {
        $this->resetAfterTest();
        $withdefault = $this->create_field('core_course', 'course', ['defaultvalue' => '{"level":1}']);
        $data = \core_customfield\data_controller::create(0, null, $withdefault);
        $this->assertStringContainsString('customfield-json-number', $data->export_value());

        $category = category_controller::create($withdefault->get('categoryid'));
        $empty = $this->get_generator()->create_field([
            'categoryid' => $category->get('id'),
            'shortname' => 'empty',
            'type' => 'json',
        ]);
        $this->assertNull(\core_customfield\data_controller::create(0, null, $empty)->export_value());
    }
}
