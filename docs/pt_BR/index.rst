moodle-customfield_json
=======================

**Campo personalizado que guarda um documento JSON em cursos, coortes, grupos e outras áreas, editado num editor visual em árvore e sempre válido.**

.. figure:: img/edit.png
   :alt: Edição de um curso: o campo personalizado JSON mostra um editor em árvore com objetos, listas e valores.
   :width: 100%

   Edição de um curso: o campo personalizado JSON abre um editor em árvore.

* **Dados estruturados onde você precisa.** Guarde identificadores de integração, configurações ou qualquer dado aninhado de um curso, coorte ou grupo num só campo, em vez de vários campos de texto.
* **Editor visual.** Modos árvore, código (com realce de sintaxe), formulário e visualização do `JSON Editor <https://github.com/josdejong/jsoneditor>`_, com o textarea simples como alternativa.
* **Sempre válido.** O servidor só grava JSON válido dentro do tamanho máximo definido para o campo, e guarda o documento numa forma canônica e formatada.
* **Exibição segura.** O valor aparece com realce de sintaxe num bloco de código, sempre escapado: HTML dentro das strings nunca é interpretado.

English version: `English <../en/index.html>`_.

.. toctree::
   :maxdepth: 2
   :caption: Conteúdo

   installation
   configuration
   usage

Principais recursos
-------------------

* Novo tipo de campo personalizado **JSON**, disponível em todas as áreas que usam os campos personalizados do Moodle: cursos, coortes, grupos, agrupamentos e outras.
* Tamanho máximo por campo (64 KB por padrão, até 1024 KB) e valor padrão validado, ambos editados com o mesmo editor.
* Validação nos formulários, com o erro do parser na tela; valores inválidos gravados sem o formulário são ignorados.
* Armazenamento canônico: formatado, com ``<``, ``>`` e ``&`` escapados dentro das strings.
* Backup e restauração, exportação e exclusão de privacidade pelo subsistema de campos personalizados.
* **Moodle 4.5 LTS e 5.3 LTS**: testado numa matriz de CI com PostgreSQL e MariaDB.

Links
-----

* Código-fonte e issues: https://github.com/moodle-by-kelsoncm/customfield_json
* Perfil do usuário: o mesmo editor para campos de perfil é o plugin `profilefield_json <https://github.com/moodle-by-kelsoncm/profilefield_json>`_.
