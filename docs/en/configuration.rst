Configuration
=============

Where to create a JSON field
----------------------------

The field type **JSON** is available in every area that uses Moodle custom fields. The main ones:

* **Courses:** *Site administration > Courses > Default settings > Course custom fields*.
* **Cohorts:** *Site administration > Users > Accounts > Cohort custom fields*.
* **Groups and groupings:** *Site administration > Courses > Groups > Group custom fields* and *Grouping custom fields*.

Other areas (for example the content bank and, on Moodle 5.x, the shared custom fields) list the type too.

Create a JSON field
-------------------

1. Open the custom fields page of the area and add a category, if there is none.
2. Choose *Add a new custom field* and **JSON**.
3. Fill in the common settings (name, short name, description, required, unique values, visibility, locked).
4. Set the JSON-specific settings and save.

.. figure:: img/settings.png
   :alt: Settings of a JSON custom field: maximum size and default value edited with the tree editor.
   :width: 100%

   JSON-specific settings.

JSON-specific settings
----------------------

* **Maximum size (KB):** largest document that can be saved in the field, from 1 to 1024 KB. Default: 64 KB.
* **Default value:** optional JSON document, edited with the same visual editor, used where the field has no value. It must be valid JSON and is stored in canonical form.

How values are stored
---------------------

Every value is checked on the server before it is saved:

* it must be valid JSON (objects, arrays, strings, numbers, booleans or ``null``), with at most 64 levels of nesting;
* it must fit in the maximum size of the field.

Accepted values are re-encoded: pretty-printed with four spaces, Unicode kept as text, and ``<``, ``>`` and ``&`` escaped inside strings (``\u003C``, ``\u003E``, ``\u0026``). An empty value means "no data".

Backup, restore and privacy
---------------------------

The values are stored by the custom fields subsystem, in the same table as the other custom fields. Moodle copies them in course backups and restores, and exports and deletes them with the data of the instance. The privacy export shows them as readable JSON.
