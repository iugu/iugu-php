# Iugu para PHP

## Instalação

Faça o download da biblioteca:

~~~
git clone https://github.com/iugu/iugu-php
~~~

Inclua a biblioteca em seu arquivo PHP:

~~~
require_once(".../iugu-php/lib/Iugu.php");
~~~

### Usando Composer

~~~
$ composer require iugu/iugu-php
Please provide a version constraint for the iugu/iugu-php requirement: <version>
~~~

O autoload do composer irá cuidar do resto.

## Exemplo de Uso

~~~
Iugu::setApiKey("c73d49f9-6490-46ee-ba36-dcf69f6334fd"); // Ache sua chave API no Painel
~~~

## Documentação

Acesse [iugu.com/documentacao](http://iugu.com/documentacao) para referência

## Testes

Instale as dependências. Iugu-PHP utiliza SimpleTest.

~~~
composer update --dev
~~~

Execute a comitiva de testes:
~~~
php ./test/Iugu.php
~~~
