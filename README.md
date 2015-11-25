# Iugu para PHP

## Requisitos

* PHP 5.3+

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
$ composer require iugu/iugu
Please provide a version constraint for the iugu/iugu requirement: 1.0.6
~~~

O autoload do composer irá cuidar do resto.

## Exemplo de Uso

~~~
Iugu::setApiKey("c73d49f9-6490-46ee-ba36-dcf69f6334fd"); // Ache sua chave API no Painel

Iugu_Charge::create(
    Array(
      "token"=> "TOKEN QUE VEIO DO IUGU.JS OU CRIADO VIA BIBLIOTECA",
      "email"=>"your@email.test",
      "items" => 
      Array(
        Array(
          "description"=>"Item Teste",
          "quantity"=>"1",
          "price_cents"=>"1000"
          )
        )
      )
    );
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
