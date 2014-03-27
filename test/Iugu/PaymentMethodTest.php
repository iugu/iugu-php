<?php

class Iugu_PaymentMethodTest extends Iugu_TestCase
{

  public function setUp()
  {
    $this->customer = self::createTestCustomer();
    $this->pm = $this->customer->payment_methods();
    $this->cc = $this->pm->create(
      Array(
        "description"=>"Cartão Teste",
        "item_type"=>"credit_card",
        "data"=> 
          Array(
            "number"=>"4111111111111111",
            "verification_value"=>"123",
            "first_name"=>"Patrick",
            "last_name"=>"Negri",
            "month"=>"12",
            "year"=>date("Y")
          )
      )
    );
    $this->customer->refresh(); // Refresh to get default payment
  }

  public function tearDown()
  {
    $this->assertTrue( $this->cc->delete() );
    $this->assertTrue( $this->customer->delete() );
  }

  public function testCreatePaymentMethods()
  {
    $this->assertNotNull($this->cc);
    $this->assertTrue( count($this->cc["errors"]) == 0 );
  }

  public function testCreateEmptyPaymentMethods()
  {
    $payment_method = Iugu_PaymentToken::create(); 
    $this->assertNotNull( $payment_method );
    $this->assertTrue( count($payment_method["errors"]) > 0 );
  }

  public function testRefreshPaymentMethod()
  {
    $this->assertTrue( $this->cc->refresh() );
  }

  public function testFetchPaymentMethod()
  {
    $this->expectException("IuguException");
    $new_cc = Iugu_PaymentMethod::fetch( $this->cc->id );

    $fetched_cc = $pm->fetch( $new_cc["id"] );
    $this->assertNotNull( $fetched_cc );
    $this->assertNotNull( $fetched_cc["id"] );
  }

  public function testSearchPaymentMethod()
  {
    $searchResults = $this->pm->search(Array()); 
    $this->assertTrue( $searchResults->total() > 0 );
  }

  public function testCustomerWithDefaultPayment()
  {
   $this->assertTrue( $this->customer->default_payment_method() ) ;
  }

  public function testCreateWrongPaymentMethod() {
    $cc = $this->pm->create(
      Array(
        "description"=>"Cartão Teste",
        "item_type"=>"credit_card",
        "data"=> Array(
          "number"=>"4111111111111115",
          "verification_value"=>"123",
          "first_name"=>"Patrick",
          "last_name"=>"Negri",
          "month"=>"12",
          "year"=>"10"
        )
      )
    );

    $this->assertNotNull($cc);
    $this->assertTrue( count($cc["errors"]) > 0 );
  }

}

?>
