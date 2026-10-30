moodle-customfield_json
=======================

**A custom field that stores a JSON document in courses, cohorts, groups and other areas, edited with a visual tree editor and always valid.**

.. figure:: img/edit.png
   :alt: Editing a course: the JSON custom field shows a tree editor with objects, arrays and values.
   :width: 100%

   Editing a course: the JSON custom field opens a tree editor.

* **Structured data where you need it.** Keep integration identifiers, settings or any nested data of a course, cohort or group in one field instead of many text fields.
* **Visual editor.** Tree, code (with syntax highlighting), form and view modes from `JSON Editor <https://github.com/josdejong/jsoneditor>`_, with the plain textarea as a fallback.
* **Always valid.** The server only saves valid JSON within the size limit set for the field, and stores it in a canonical, pretty-printed form.
* **Safe to display.** The value is shown with syntax highlighting in a code block, always escaped: HTML inside strings is never rendered.

Versão em português: `Português (Brasil) <../pt-br/index.html>`_.

.. toctree::
   :maxdepth: 2
   :caption: Contents

   installation
   configuration
   usage

Main features
-------------

* New custom field type **JSON**, available in every area that uses Moodle custom fields: courses, cohorts, groups, groupings and others.
* Maximum size per field (64 KB by default, up to 1024 KB) and a validated default value, both edited with the same editor.
* Validation on the forms, with the parser error shown; invalid values saved without the form are ignored.
* Canonical storage: pretty-printed, with ``<``, ``>`` and ``&`` escaped inside strings.
* Backup and restore, privacy export and deletion through the custom fields subsystem.
* **Moodle 4.5 LTS and 5.3 LTS**: tested on a CI matrix with PostgreSQL and MariaDB.

Links
-----

* Source code and issues: https://github.com/moodle-by-kelsoncm/customfield_json
* User profiles: the same editor for user profile fields is the `profilefield_json <https://github.com/moodle-by-kelsoncm/profilefield_json>`_ plugin.
