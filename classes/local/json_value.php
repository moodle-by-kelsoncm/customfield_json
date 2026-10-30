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
 * Validation and canonical storage of JSON values.
 *
 * Custom field data is free text, so it is read as raw text (like the core textarea field) and
 * then accepted only when it is valid JSON within the size and depth limits. It is stored
 * re-encoded by PHP, which also escapes HTML special characters inside strings.
 *
 * @package    customfield_json
 * @copyright  2026 onwards Kelson da Costa Medeiros
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class json_value {
    /** @var int Default maximum size of a value, in kilobytes. */
    public const DEFAULT_MAX_KB = 64;

    /** @var int Largest maximum size an administrator can set, in kilobytes. */
    public const LIMIT_MAX_KB = 1024;

    /** @var int Maximum nesting depth. */
    public const MAX_DEPTH = 64;

    /** @var int Flags used to store a value. */
    private const ENCODE_FLAGS = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        | JSON_PRESERVE_ZERO_FRACTION | JSON_HEX_TAG | JSON_HEX_AMP | JSON_THROW_ON_ERROR;

    /**
     * Check a value typed by the user.
     *
     * @param string|null $value Raw value; empty means "no value" and is valid.
     * @param int $maxkb Maximum size in kilobytes.
     * @return string|null Error message, or null when the value is valid.
     */
    public static function validate(?string $value, int $maxkb = self::DEFAULT_MAX_KB): ?string {
        $value = trim((string)$value);
        if ($value === '') {
            return null;
        }
        if (strlen($value) > $maxkb * 1024) {
            return get_string('errortoolarge', 'customfield_json', $maxkb);
        }
        try {
            json_decode($value, false, self::MAX_DEPTH, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return get_string('errorinvalidjson', 'customfield_json', $e->getMessage());
        }
        return null;
    }

    /**
     * Canonical form of a valid value: pretty-printed, with HTML special characters escaped.
     *
     * @param string|null $value Value already accepted by validate().
     * @return string Canonical JSON, or an empty string for an empty value.
     */
    public static function normalise(?string $value): string {
        $value = trim((string)$value);
        if ($value === '') {
            return '';
        }
        $decoded = json_decode($value, false, self::MAX_DEPTH, JSON_THROW_ON_ERROR);
        return json_encode($decoded, self::ENCODE_FLAGS, self::MAX_DEPTH);
    }

    /**
     * Readable form of a stored value, without the escaping of HTML characters.
     *
     * The result is plain text: it must be escaped (as the display template does) before output.
     *
     * @param string|null $value Stored value.
     * @return string Pretty-printed JSON, or the value as stored if it cannot be decoded.
     */
    public static function readable(?string $value): string {
        $value = trim((string)$value);
        try {
            $decoded = json_decode($value, false, self::MAX_DEPTH, JSON_THROW_ON_ERROR);
            return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR, self::MAX_DEPTH);
        } catch (\JsonException $e) {
            return $value;
        }
    }

    /**
     * HTML of a stored value with syntax highlighting.
     *
     * Every token is escaped and wrapped in a span with a class for its kind (key, string, number,
     * boolean, null, punctuation); whitespace is escaped as it is. The result is safe to print.
     *
     * @param string|null $value Stored value.
     * @return string HTML, without the surrounding pre element.
     */
    public static function highlight(?string $value): string {
        $text = self::readable($value);
        $pattern = '/("(?:\\\\.|[^"\\\\])*")(\s*:)?|(-?\d+(?:\.\d+)?(?:[eE][+-]?\d+)?)|\b(true|false)\b|\b(null)\b|([{}\[\],])/u';
        $html = '';
        $offset = 0;
        preg_match_all($pattern, $text, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        foreach ($matches as $match) {
            [$token, $start] = $match[0];
            $html .= s(substr($text, $offset, $start - $offset));
            if (($match[1][1] ?? -1) >= 0 && ($match[2][1] ?? -1) >= 0) {
                $html .= self::span('key', $match[1][0]) . self::span('punct', $match[2][0]);
            } else if (($match[1][1] ?? -1) >= 0) {
                $html .= self::span('string', $token);
            } else if (($match[3][1] ?? -1) >= 0) {
                $html .= self::span('number', $token);
            } else if (($match[4][1] ?? -1) >= 0) {
                $html .= self::span('boolean', $token);
            } else if (($match[5][1] ?? -1) >= 0) {
                $html .= self::span('null', $token);
            } else {
                $html .= self::span('punct', $token);
            }
            $offset = $start + strlen($token);
        }
        return $html . s(substr($text, $offset));
    }

    /**
     * Escaped token wrapped in a span.
     *
     * @param string $kind Token kind, used in the class name.
     * @param string $text Token text.
     * @return string HTML
     */
    private static function span(string $kind, string $text): string {
        return '<span class="customfield-json-' . $kind . '">' . s($text) . '</span>';
    }

    /**
     * Maximum size configured for a field.
     *
     * @param mixed $setting The maxsize setting of the field.
     * @return int Size in kilobytes.
     */
    public static function max_kb($setting): int {
        $maxkb = (int)$setting;
        if ($maxkb < 1) {
            return self::DEFAULT_MAX_KB;
        }
        return min($maxkb, self::LIMIT_MAX_KB);
    }
}
