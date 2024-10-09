Após clonado o repositório tem alguns passos a ser considerados caso necessário:
Ao executar algum comando, se der erro relacionado ao vendor/autoload.php, pode ser necessário instalar o composer:
"composer install".
Em seguida duplicar o .env.example e renomea-lo para .env, e deve ser necessário rodar "php artisan key:generate".

O projeto está sendo rodado com o composer (php artisan serve), Laravel e MySQL;
Para o início de sua execução, deve-se criar o banco de dados 'meu_campeonato', e após isso rodar o migrate.
Após o migrate, rodar os scripts da view_0001 e da proc_view_0001, conforme arquivo do MySQL em anexo.
A partir disso, deve-se apenas criar os times e dar inicio ao campeonato.
Toda documentação necessária da API no Collection do Postman.
