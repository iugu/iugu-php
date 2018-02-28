# Iugu para PHP

[![Build status](https://img.shields.io/travis/iugu/iugu-php.svg)](https://travis-ci.org/iugu/iugu-php)

## Requisitos

* PHP 5.4+

## Instalação

Faça o download da biblioteca:

```
git clone https://github.com/iugu/iugu-php
```

Inclua a biblioteca em seu arquivo PHP:

```php
require_once(".../iugu-php/lib/Iugu.php");
```

### Usando Composer

```
$ composer require iugu/iugu
Please provide a version constraint for the iugu/iugu requirement: 1.0.6
```

O autoload do composer irá cuidar do resto.

## Exemplo de Uso

```php
Iugu::setApiKey("c73d49f9-6490-46ee-ba36-dcf69f6334fd"); // Ache sua chave API no Painel

Iugu_Charge::create(
    [
        "token"=> "TOKEN QUE VEIO DO IUGU.JS OU CRIADO VIA BIBLIOTECA",
        "email"=>"your@email.test",
        "items" => [
            [
                "description"=>"Item Teste",
                "quantity"=>"1",
                "price_cents"=>"1000"
            ]
        ]
    ]
);
```

## Documentação

Acesse [iugu.com/documentacao](http://iugu.com/documentacao) para referência

## Testes

Instale as dependências:

```
composer update
```

Execute a comitiva de testes:

```
composer tests
```

