# Changelog

## 1.0.0 - 2026-10-30

First release.

### Added
- Custom field type **JSON** for every area that uses Moodle custom fields (courses, cohorts, groups, groupings and others).
- JSON validation on the server (syntax, nesting depth and a maximum size per field, 64 KB by default) with the parser error shown on the form; a validated default value.
- Canonical storage: pretty-printed JSON, with `<`, `>` and `&` escaped inside strings.
- Visual editor: [JSON Editor](https://github.com/josdejong/jsoneditor) 10.4.3 (full build, Apache-2.0) with tree, code (syntax highlighting with Ace), form and view modes, also in the field settings; the textarea stays as a fallback.
- Display of the value pretty-printed with syntax highlighting, every token escaped (`customfield_json/display` template).
- Values saved without the form (uploads, web services) are ignored when invalid.
- Privacy API provider (export of the values as readable JSON).
- PHPUnit tests in the course and cohort areas, CI with moodle-plugin-ci (Moodle 4.5 and 5.3, PostgreSQL and MariaDB), documentation in English and Brazilian Portuguese, release by draft and verification.
