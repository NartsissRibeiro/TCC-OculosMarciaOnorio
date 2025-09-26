pedido/controller e pedido/views: já estão funcionando!

assets/css/style.css coloquei todo o css utilizado em todas as paginas para poder colocar os "include: header"
sem ter que utilizar a estrutura do html:5 toda hora.
Aviso: cuidado ao retirar alguns botões como por exemplo "home-conteiner btn", 
pois ele funciona como botão padrão para a tela inicial "views/telainicial/index" 

Views: onde terá todo o visual do site nas pastas.
(a pasta usuario terá o cadastroUsuario que era para estar em controller, tecnicamente era para estar em Controller mas não quis mudar ainda para não atrasar muitas coisas.)

Controller: toda a parte do código onde poderá fazer todas as funcionalidades do CRUD: normalmente terá o form da para cadastrar algo:(new) deletar alguma informação criada:(delete) para editar alguma informação criada:(edit). O (index) serão onde vc poderá ver a consulta por exemplo de produto onde neste caso estará em "views/produto/index".

as dependencias do composer (vendor, composer.json, src) eu coloquei ao projeto agora para utilizar mais tarde,
deem "composer install" no terminal do projeto quando derem um pull em suas máquinas.

IMPORTANTE: POR FAVOR, não façam nada na branch main, sempre criem suas branchs com titulos do que vcs estão fazendo,
e avisem sempre quais pastas estão mexendo para não haja conflitos. Sempre quando atualizarmos a branch main, atualizem
suas branchs para que não ocorra outros problemas para fazermos o "Pull request".

Sempre quando vc iniciar o projeto, ele estará na Branch Main, sempre deem uma olhada para não errarem.

DICA: caso não saibam para fazer para fazerem os comandos do git e para ver quais branchs estão, é só entrar em um terminal "Git Bash".

