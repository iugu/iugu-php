<?php
    // exemplo de uso da classe BankVerification
    Iugu::setApiKey($user_token);   // Chave do user
    $updateBank = Iugu_BankVerification::create(Array(
            "agency"                    => '9999-9',
            "account"                   => '999999-9',
            "account_type"              => 'cp',   		// cp, cc
            "bank"                      => '001',       // "001" para Banco do Brasil, "033" para Santander, "104" para Caixa Econômica, "237" para Bradesco, "341" para Itaú e "399" para HSBC
            "automatic_validation"      => false,
    ));


    //Market Place
    //
    // criar sub-conta
    $mktplace = Iugu_Marketplace::create(Array(
        "action"                    => "create_account", // criei esse atributo action pra ser enviado na api para determinar qual ação. Achei mais fácil do que criar novas funções
        "name"                      => 'nome',
        "reply_to"                  => 'email@email.com',
    ));

    // verificação de subconta
    $validate = Iugu_Account::create(Array(
        "id"                        => 'CLIENT_ID ',
        "action"                    => "request_verification",
        "automatic_validation"      => false,
        "files"                     => false,
        "data" => array(
            "price_range"           => "Mais que R$ 500,00",
            "physical_products"     => false,
            "business_type"         => "TIPO DO CLIENTE",
            "person_type"           => "Pessoa Física",
            "automatic_transfer"    => false,
            "cpf"                   => '999.999.999-99',
            "name"                  => "nome",
            "address"               => "endereço",
            "cep"                   => "cep",
            "city"                  => "cidade",
            "state"                 => "estado",
            "telephone"             => "27 33333333",
            "bank"                  => 'Banco do Brasil',       //'Banco do Brasil', 'Itaú', 'Bradesco', 'Caixa Econômica', 'Banco do Brasil', 'Santander'
            "bank_ag"               => '9999-9',
            "account_type"          => 'Corrente',       // Poupançaa, Corrente
            "bank_cc"               => '999999-9',
        )
    ));

    // retornando os dados do mktplace
    Iugu::setApiKey('TOKEN_IUGU');   // user token como parametro
    $account = Iugu_Account::fetch("CLIENT_ID"); // id da conta como parametro



    // transferendo valor para subconta
    $transfer = Iugu_Transfer::create(Array(
        "receiver_id"               => "CLIENT_ID", // id da conta recebedora
        "amount_cents"              => '1000' // valor em centavos = R$ 10,00
    ));


    // retirando valor da subconta
    Iugu::setApiKey('TOKEN_IUGU');   // Chave do user
    $transfer = Iugu_Transfer::create(Array(
        "receiver_id"               => "CLIENT_ID", // chave da conta mestre ou da conta recebedora
        "amount_cents"              => $numValorCentavos
    ));


    // pedido de retirada de dinheiro
    Iugu::setApiKey('TOKEN_IUGU');   // Chave do user
    $withdraw = Iugu_Account::create(Array(
        "action"                    => 'request_withdraw',
        "account_id"                => 'CLIENT_ID', // id da conta
        "amount"                    => '10.00' // reais com ponto separador decimal R$ 10,00
    ));
