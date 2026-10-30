import moodle_docs_theme

project = "moodle-customfield_json"
copyright = "2023 onwards, Kelson da Costa Medeiros"
author = "Kelson da Costa Medeiros"
release = "1.1.0"

extensions = [
    "sphinx.ext.githubpages",
    "moodle_docs_theme",
]

templates_path = []
exclude_patterns = ["_build", "Thumbs.db", ".DS_Store"]

language = "en"

html_theme = "moodle_docs_theme"
html_theme_path = [moodle_docs_theme.get_html_theme_path()]

html_theme_options = {
    "project_name": "moodle-customfield_json",
    "tagline": "Custom field that stores a JSON document, with a visual editor",
    "github_url": "https://github.com/moodle-by-kelsoncm/customfield_json",
    "github_repo": "moodle-by-kelsoncm/customfield_json",
    "github_version": "main",
    "doc_path": "docs/en/",
    "show_edit_on_github": True,
    "enable_dark_mode": True,
    "enable_language_selector": True,
    "navigation_links": "Home|index, Installation|installation, Configuration|configuration, Usage|usage",
}

html_static_path = []
