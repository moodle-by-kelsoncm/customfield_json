Uso
===

Editar um documento
-------------------

No formulário de edição de um curso, coorte, grupo ou agrupamento, o campo JSON abre o editor visual:

* **Árvore:** expanda objetos e listas, acrescente, duplique, mova ou remova itens e mude o tipo de um valor pelo menu de cada item.
* **Formulário:** edite só os valores, mantendo a estrutura.
* **Código:** edite o JSON como texto, com realce de sintaxe, numeração de linhas e botões para formatar ou compactar.
* **Visualização:** árvore só de leitura.

Cada alteração é gravada no campo do formulário. Se o texto não for JSON válido, uma mensagem abaixo do editor mostra o erro do parser, e o formulário só salva depois da correção.

Um documento que não é JSON válido abre no modo código. Sem JavaScript, ou se o editor não iniciar, o campo é um textarea simples em fonte monoespaçada, e as mesmas checagens do servidor valem. Um campo bloqueado aparece como texto para quem não pode alterá-lo.

Exibição
--------

Onde o Moodle mostra os campos personalizados (por exemplo, na lista de cursos, quando o campo é visível), o documento aparece formatado num bloco de código com rolagem, com chaves, strings, números, booleanos e ``null`` em cores diferentes. O conteúdo sempre é escapado: HTML dentro das strings aparece como texto. Um campo sem valor mostra o valor padrão.

.. figure:: img/display.png
   :alt: Um curso na lista de cursos, mostrando o campo personalizado JSON como um bloco de código formatado.
   :width: 100%

   O campo JSON na lista de cursos.

Upload e web services
---------------------

O upload de cursos e os web services podem gravar valores de campos personalizados sem passar pela validação do formulário. O campo confere esses valores também: um valor válido é gravado na forma canônica, e um inválido é ignorado, mantendo o valor já gravado.
