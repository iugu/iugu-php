<?php

class Iugu_PaymentTokenTest extends Iugu_TestCase
{
  public function setUp()
  {
    $this->payment_token = Iugu_PaymentToken::create(
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
  }

  public function tearDown()
  {
  }

  public function testCreatePaymentToken()
  {
    $this->assertNotNull($this->payment_token);
    $this->assertTrue( count($this->payment_token["errors"]) == 0 );
 
  }

  public function testCreateEmptyPaymentToken()
  {
    $payment_token = Iugu_PaymentToken::create(); 
    $this->assertNotNull( $payment_token );
    $this->assertTrue( count($payment_token["errors"]) > 0 );
  }

}

?>
