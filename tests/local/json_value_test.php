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

namespace customfield_json\local;

/**
 * Tests for the validation and canonical form of JSON values.
 *
 * @package    customfield_json
 * @category   test
 * @copyright  2026 onwards Kelson da Costa Medeiros
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \customfield_json\local\json_value
 */
final class json_value_test extends \advanced_testcase {
    /**
     * Valid and invalid values.
     *
     * @return array
     */
    public static function validate_provider(): array {
        return [
            'empty' => ['', true],
            'spaces' => ["  \n", true],
            'object' => ['{"a": 1}', true],
            'array' => ['[1, "two", null, true]', true],
            'scalar' => ['"text"', true],
            'trailing comma' => ['{"a": 1,}', false],
            'single quotes' => ["{'a': 1}", false],
            'not json' => ['hello', false],
            'too deep' => [str_repeat('[', json_value::MAX_DEPTH + 1) . str_repeat(']', json_value::MAX_DEPTH + 1), false],
        ];
    }

    /**
     * validate() accepts only valid JSON.
     *
     * @dataProvider validate_provider
     * @param string $value Value to check.
     * @param bool $valid Whether it is valid.
     */
    public function test_validate(string $value, bool $valid): void {
        $this->assertSame($valid, json_value::validate($value) === null);
    }

    /**
     * validate() refuses values larger than the limit.
     */
    public function test_validate_size(): void {
        $value = json_encode(str_repeat('x', 2048));
        $this->assertNotNull(json_value::validate($value, 1));
        $this->assertNull(json_value::validate($value, 3));
    }

    /**
     * normalise() pretty-prints and escapes HTML special characters.
     */
    public function test_normalise(): void {
        $this->assertSame('', json_value::normalise('  '));
        $this->assertSame(
            "{\n    \"a\": 1.0,\n    \"b\": \"\\u003Cb\\u003E \\u0026 ação\"\n}",
            json_value::normalise('{"a":1.0,"b":"<b> & ação"}')
        );
        $this->assertSame('{}', json_value::normalise('{}'));
        $this->assertSame('[]', json_value::normalise('[]'));
    }

    /**
     * readable() shows the characters that normalise() escaped.
     */
    public function test_readable(): void {
        $stored = json_value::normalise('{"a":"<b> & ação","b":[1,2]}');
        $this->assertSame(
            "{\n    \"a\": \"<b> & ação\",\n    \"b\": [\n        1,\n        2\n    ]\n}",
            json_value::readable($stored)
        );
        $this->assertSame('not json', json_value::readable('not json'));
    }

    /**
     * highlight() wraps each token in a span and escapes everything.
     */
    public function test_highlight(): void {
        $stored = json_value::normalise('{"<k>":"<v> \"q\"","n":-1.5e3,"t":true,"z":null,"a":[]}');
        $html = json_value::highlight($stored);

        $this->assertStringContainsString('<span class="customfield-json-key">&quot;&lt;k&gt;&quot;</span>', $html);
        $this->assertStringContainsString('<span class="customfield-json-string">&quot;&lt;v&gt;', $html);
        $this->assertStringContainsString('<span class="customfield-json-number">-1500.0</span>', $html);
        $this->assertStringContainsString('<span class="customfield-json-boolean">true</span>', $html);
        $this->assertStringContainsString('<span class="customfield-json-null">null</span>', $html);
        $this->assertStringContainsString('<span class="customfield-json-punct">[</span>', $html);
        // Only the spans added by highlight() are tags.
        $this->assertSame(0, preg_match('/<(?!\/?span)/', $html));
        // The text is the readable JSON, nothing lost.
        $this->assertSame(json_value::readable($stored), html_entity_decode(strip_tags($html), ENT_QUOTES));
    }

    /**
     * max_kb() falls back to the default and caps the limit.
     */
    public function test_max_kb(): void {
        $this->assertSame(json_value::DEFAULT_MAX_KB, json_value::max_kb(null));
        $this->assertSame(json_value::DEFAULT_MAX_KB, json_value::max_kb('0'));
        $this->assertSame(10, json_value::max_kb('10'));
        $this->assertSame(json_value::LIMIT_MAX_KB, json_value::max_kb(json_value::LIMIT_MAX_KB * 2));
    }
}
