Installation
============

Requirements
------------

* Moodle 4.5 LTS or 5.3 LTS.
* PHP supported by your Moodle version.

From a ZIP file
---------------

1. Download ``customfield_json-<version>.zip`` from the `releases <https://github.com/moodle-by-kelsoncm/customfield_json/releases>`_.
2. Go to *Site administration > Plugins > Install plugins*, upload the ZIP and follow the steps.
3. Finish the upgrade at *Site administration > Notifications*.

From source
-----------

Copy the repository to ``customfield/field/json`` (``public/customfield/field/json`` on Moodle 5.1 or later) and visit *Site administration > Notifications*:

.. code-block:: bash

   git clone https://github.com/moodle-by-kelsoncm/customfield_json.git public/customfield/field/json

Languages
---------

The package ships only the English strings. Other languages, including Brazilian Portuguese, come from the Moodle translation tool (AMOS), through the language packs.

Third-party code
----------------

The visual editor is `JSON Editor <https://github.com/josdejong/jsoneditor>`_ 10.4.3, by Jos de Jong (Apache License 2.0), in the full build (about 1 MB, loaded only on forms with a JSON field), which includes the Ace code editor. It is listed in ``thirdpartylibs.xml``.
