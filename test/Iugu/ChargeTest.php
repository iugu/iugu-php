<?php

class Iugu_ChargeTest extends Iugu_TestCase
{

  public function testChargeToken()
  {
    $payment_token = Iugu_PaymentToken::create(
      Array(
        "method"=>"credit_card",
        "data"=>Array(
          "number"=>"4111111111111111",
          "verification_value"=>"123",
          "first_name"=>"Joao",
          "last_name"=>"Silva",
          "month"=>"12",
          "year"=>date("Y")
        )
      )
    );

    $this->assertNotNull($payment_token);
    $this->assertTrue( count($payment_token["errors"]) == 0 );

    $charge = Iugu_Charge::create(
      Array(
        "token"=> $payment_token,
        "email"=>"patricknegri@gmail.com",
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

    $this->assertNotNull($charge);
    $this->assertTrue( count($charge["errors"]) == 0 );
    $this->assertTrue( $charge->invoice()->items_total_cents == 1000 );

    $this->assertNotNull($charge->invoice());
  }

  public function testCreateEmptyCharge()
  {
    $charge = Iugu_Charge::create(); 
    $this->assertNotNull( $charge );
    $this->assertTrue( count($charge["errors"]) > 0 );
  }

}

?>
