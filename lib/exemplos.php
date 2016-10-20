<?php
    // exemplo de uso da classe BankVerification
    Iugu::setApiKey($user_token);   // Chave do user
    $updateBank = Iugu_BankVerification::create(Array(
            "agency"                    => '9999-9',
            "account"                   => '999999-9',
            "account_type"              => 'cp',   		// cp, cc
            "bank"                      => '001',       // "001" para Banco do Brasil, "033" para Santander, "104" para Caixa Econ�mica, "237" para Bradesco, "341" para Ita� e "399" para HSBC
            "automatic_validation"      => false,
    ));


    //Market Place
    //
    // criar sub-conta
    $mktplace = Iugu_Marketplace::create(Array(
        "action"                    => "create_account", // criei esse atributo action pra ser enviado na api para determinar qual a��o. Achei mais f�cil do que criar novas fun��es
        "name"                      => 'nome',
        "reply_to"                  => 'email@email.com',
    ));
    
    // verifica��o de subconta
    $validate = Iugu_Account::create(Array(
        "id"                        => '49196DF60BC64B6EB42DEC9C5D81C2CC ',
        "action"                    => "request_verification",
        "automatic_validation"      => false,
        "files"                     => false,
        "data" => array(
            "price_range"           => "Mais que R$ 500,00",
            "physical_products"     => false,
            "business_type"         => "Faminto - Almo�o Gr�tis",
            "person_type"           => "Pessoa F�sica",
            "automatic_transfer"    => false,
            "cpf"                   => '999.999.999-99',
            "name"                  => "nome",
            "address"               => "endere�o",
            "cep"                   => "cep",
            "city"                  => "cidade",
            "state"                 => "estado",
            "telephone"             => "27 33333333",
            "bank"                  => 'Banco do Brasil',       //'Banco do Brasil', 'Ita�', 'Bradesco', 'Caixa Econ�mica', 'Banco do Brasil', 'Santander'
            "bank_ag"               => '9999-9',
            "account_type"          => 'Corrente',       // Poupan�a, Corrente
            "bank_cc"               => '999999-9',
        )
    ));
    
    // retornando os dados do mktplace
    Iugu::setApiKey('1015cd6f9ddc96a357403f994f907cfd');   // user token como parametro
    $account = Iugu_Account::fetch("49196DF60BC64B6EB42DEC9C5D81C2CC"); // id da conta como parametro
    
    
    
    // transferendo valor para subconta
    $transfer = Iugu_Transfer::create(Array(
        "receiver_id"               => "49196DF60BC64B6EB42DEC9C5D81C2CC", // id da conta recebedora
        "amount_cents"              => '1000' // valor em centavos = R$ 10,00
    ));
    
    
    // retirando valor da subconta
    Iugu::setApiKey('1015cd6f9ddc96a357403f994f907cfd');   // Chave do user
    $transfer = Iugu_Transfer::create(Array(
        "receiver_id"               => "49196DF60BC64B6EB42DEC9C5D81C2CC", // chave da conta mestre ou da conta recebedora
        "amount_cents"              => $numValorCentavos
    ));
    
    
    // pedido de retirada de dinheiro
    Iugu::setApiKey('1015cd6f9ddc96a357403f994f907cfd');   // Chave do user
    $withdraw = Iugu_Account::create(Array(
        "action"                    => 'request_withdraw',
        "account_id"                => '49196DF60BC64B6EB42DEC9C5D81C2CC', // id da conta
        "amount"                    => '10.00' // reais com ponto separador decimal R$ 10,00
    ));
