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

/**
 * Visual JSON editor on top of the textarea of a JSON custom field.
 *
 * The textarea stays in the form and keeps the value that is submitted; the editor writes
 * every change back to it. Without JavaScript, or if the editor fails, the textarea is used.
 *
 * @module     customfield_json/editor
 * @copyright  2026 onwards Kelson da Costa Medeiros
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Config from 'core/config';
import {getString} from 'core/str';

/** @type {string} Bundled JSON Editor build (third-party, see thirdpartylibs.xml). */
const LIBRARY = '/customfield/field/json/thirdparty/jsoneditor/jsoneditor.min.js';

/** @type {string} Stylesheet of the bundled JSON Editor build. */
const STYLESHEET = '/customfield/field/json/thirdparty/jsoneditor/jsoneditor.min.css';

/**
 * Add the JSON Editor stylesheet to the page once.
 *
 * Custom field forms are often loaded inside modals, after the page head was printed, so the
 * stylesheet is added here instead of through the page requirements.
 */
const loadStylesheet = () => {
    const href = Config.wwwroot + STYLESHEET;
    if (document.querySelector(`link[href="${href}"]`)) {
        return;
    }
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = href;
    document.head.append(link);
};

/**
 * Load the JSON Editor library through RequireJS (it is a UMD module).
 *
 * @returns {Promise<Function>} The JSONEditor constructor.
 */
const loadLibrary = () => new Promise((resolve, reject) => {
    window.require([Config.wwwroot + LIBRARY], resolve, reject);
});

/** @type {string[]} Languages bundled with JSON Editor. */
const EDITOR_LANGUAGES = ['en', 'es', 'zh-CN', 'pt-BR', 'tr', 'ja', 'fr-FR', 'de', 'ru', 'sv'];

/**
 * JSON Editor language matching the Moodle page language (the editor would follow the browser).
 *
 * @returns {string}
 */
const editorLanguage = () => {
    const lang = (document.documentElement.lang || 'en').toLowerCase();
    const exact = EDITOR_LANGUAGES.find((code) => code.toLowerCase() === lang);
    const base = EDITOR_LANGUAGES.find((code) => code.toLowerCase().split('-')[0] === lang.split('-')[0]);
    return exact || base || 'en';
};

/**
 * Whether a text parses as JSON.
 *
 * @param {string} text Text to check.
 * @returns {string|null} Parse error message, or null when valid.
 */
const parseError = (text) => {
    if (text.trim() === '') {
        return null;
    }
    try {
        JSON.parse(text);
        return null;
    } catch (e) {
        return e.message;
    }
};

/**
 * Show or clear the validation message under the editor.
 *
 * @param {HTMLElement} feedback Feedback element.
 * @param {string|null} error Parse error, or null.
 * @returns {Promise<void>}
 */
const showError = async(feedback, error) => {
    // Typing fires many checks; only the latest one may update the message.
    const check = String(Number(feedback.dataset.check || 0) + 1);
    feedback.dataset.check = check;
    if (error === null) {
        feedback.textContent = '';
        feedback.classList.remove('d-block');
        return;
    }
    const message = await getString('editorinvalid', 'customfield_json', error);
    if (feedback.dataset.check !== check) {
        return;
    }
    feedback.textContent = message;
    feedback.classList.add('d-block');
};

/**
 * Start the visual editor for a textarea.
 *
 * @param {HTMLTextAreaElement} textarea Textarea of the custom field.
 * @returns {Promise<void>}
 */
const setup = async(textarea) => {
    if (textarea.dataset.customfieldJson) {
        return;
    }
    textarea.dataset.customfieldJson = 'ready';

    const container = document.createElement('div');
    container.className = 'customfield-json-editor';
    const feedback = document.createElement('div');
    feedback.className = 'invalid-feedback';

    const initial = textarea.value;
    const initialError = parseError(initial);
    try {
        loadStylesheet();
        const JSONEditor = await loadLibrary();
        textarea.after(container, feedback);
        // "code" is the text mode with syntax highlighting (Ace, bundled in the full build).
        const editor = new JSONEditor(container, {
            modes: ['tree', 'code', 'form', 'view'],
            mode: initialError === null ? 'tree' : 'code',
            language: editorLanguage(),
            onChangeText: (text) => {
                textarea.value = text;
                showError(feedback, parseError(text));
            },
        });
        if (initial.trim() === '') {
            editor.set({});
        } else {
            editor.setText(initial);
            showError(feedback, initialError);
        }
    } catch (e) {
        // Keep the plain textarea when the library cannot load or the editor cannot start.
        container.remove();
        feedback.remove();
        return;
    }
    // The editor is the visible control; the textarea only carries the value. An empty field
    // shows an empty object in the tree but submits nothing until it is edited.
    textarea.setAttribute('aria-hidden', 'true');
    textarea.setAttribute('tabindex', '-1');
};

/**
 * Start the visual editor for the textareas matching a selector.
 *
 * Forms loaded in modals add a random suffix to the element ids, so the textareas are found by name.
 *
 * @param {string} selector CSS selector of the textareas.
 */
export const init = (selector) => {
    document.querySelectorAll(selector).forEach((textarea) => setup(textarea));
};
