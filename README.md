# moodle-customfield_json

[English](#english) · [Português (Brasil)](#português-brasil)

Documentation / Documentação: <https://moodle-by-kelsoncm.github.io/customfield_json/>

## English

Custom field that stores a JSON document in courses, cohorts, groups, groupings and every other area that uses Moodle custom fields. It is edited in a visual tree editor ([JSON Editor](https://github.com/josdejong/jsoneditor)), the server only saves valid JSON within the size set for the field, and the value is shown escaped, pretty-printed and highlighted. Supports Moodle 4.5 LTS and 5.3 LTS.

Install: copy this repository to `customfield/field/json` (`public/customfield/field/json` on Moodle 5.1+) and visit *Site administration > Notifications*. Then add a field of type **JSON**, for example at *Site administration > Courses > Default settings > Course custom fields*.

## Português (Brasil)

Campo personalizado que guarda um documento JSON em cursos, coortes, grupos, agrupamentos e nas demais áreas que usam os campos personalizados do Moodle. A edição é feita num editor visual em árvore ([JSON Editor](https://github.com/josdejong/jsoneditor)), o servidor só grava JSON válido dentro do tamanho definido para o campo, e o valor aparece escapado, formatado e colorido. Suporta Moodle 4.5 LTS e 5.3 LTS.

Instalação: copie este repositório para `customfield/field/json` (`public/customfield/field/json` no Moodle 5.1+) e acesse *Administração do site > Notificações*. Depois crie um campo do tipo **JSON**, por exemplo em *Administração do site > Cursos > Configurações padrão > Campos personalizados do curso*.

## Development / Desenvolvimento

- JavaScript sources are in `amd/src`; rebuild `amd/build` with `npx grunt amd --root=public/customfield/field/json` in a Moodle checkout.
- `thirdparty/jsoneditor` is the third-party JSON Editor 10.4.3 (full build with the Ace code editor, Apache-2.0), listed in `thirdpartylibs.xml` and loaded by `amd/src/editor.js` through RequireJS.
- CI (`.github/workflows/ci.yml`) runs `moodle-plugin-ci` on Moodle 4.5 and 5.3 with PostgreSQL and MariaDB; run it locally with [`act`](https://github.com/nektos/act) before pushing.

## Support and privacy / Suporte e privacidade

- Issue tracker / Rastreador de issues: <https://github.com/moodle-by-kelsoncm/customfield_json/issues>
- Privacy / Privacidade: the values are stored by the custom fields subsystem, which exports and deletes them; this plugin stores nothing on its own / os valores ficam no subsistema de campos personalizados, que os exporta e apaga; o plugin não guarda nada por conta própria.
- Translations / Traduções: only English is shipped; other languages go through AMOS / o pacote traz só inglês; outros idiomas via AMOS.

## License / Licença

GNU GPL v3 or later. See [LICENSE](LICENSE).
