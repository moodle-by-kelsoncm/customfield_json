Instalação
==========

Requisitos
----------

* Moodle 4.5 LTS ou 5.3 LTS.
* PHP suportado pela sua versão do Moodle.

A partir de um ZIP
------------------

1. Baixe ``customfield_json-<versão>.zip`` das `releases <https://github.com/moodle-by-kelsoncm/customfield_json/releases>`_.
2. Acesse *Administração do site > Plugins > Instalar plugins*, envie o ZIP e siga os passos.
3. Conclua a atualização em *Administração do site > Notificações*.

A partir do código-fonte
------------------------

Copie o repositório para ``customfield/field/json`` (``public/customfield/field/json`` no Moodle 5.1 ou superior) e acesse *Administração do site > Notificações*:

.. code-block:: bash

   git clone https://github.com/moodle-by-kelsoncm/customfield_json.git public/customfield/field/json

Idiomas
-------

O pacote traz só as strings em inglês. Os demais idiomas, inclusive o português do Brasil, vêm da ferramenta de tradução do Moodle (AMOS), pelos pacotes de idioma.

Código de terceiros
-------------------

O editor visual é o `JSON Editor <https://github.com/josdejong/jsoneditor>`_ 10.4.3, de Jos de Jong (Licença Apache 2.0), no build completo (cerca de 1 MB, carregado só nos formulários com campo JSON), que inclui o editor de código Ace. Ele está listado no ``thirdpartylibs.xml``.
