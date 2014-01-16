<?php

class Iugu_InvoiceTest extends Iugu_TestCase
{

  public function setUp()
  {
    $this->customer = self::createTestCustomer();
    $this->invoice = Iugu_Invoice::create(
      Array(
        "email"=>"patrick@iugu.com",
        "due_date"=>"30/12/" . date("Y"),
        "customer_id"=>$this->customer,
        "items"=>
        Array(
          Array(
            "description"=>"Item Teste",
            "quantity"=>"1",
            "price_cents"=>"1000"
          )
        )
      )
    );

  }

  public function testCreateInvoice()
  {
    $this->assertNotNull($this->invoice);
    $this->assertTrue( count($this->invoice["errors"]) == 0 );
  }

  public function testCreateEmptyInvoice()
  {
    $invoice = Iugu_Invoice::create(); 
    $this->assertNotNull( $invoice );
    $this->assertTrue( count($invoice["errors"]) > 0 );
  }

  public function testRefreshInvoice()
  {
    $this->assertTrue( $this->invoice->refresh() );
  }

  public function testFetchInvoice()
  {
    $this->expectException("IuguException");
    $new_invoice = Iugu_Invoice::fetch( "NO VALID INVOICE" );

    $fetched_invoice = Iugu_Invoice::fetch( $this->invoice->id );
    $this->assertNotNull( $fetched_invoice );
    $this->assertNotNull( $fetched_invoice["id"] );
  }

  public function testSearchPaymentMethod()
  {
    $searchResults = Iugu_Invoice::search(Array()); 
    $this->assertTrue( $searchResults->total() > 0 );
  }

  public function testCustomerWithValidInvoices()
  {
    $this->assertTrue( $this->customer->invoices()->search()->total() > 0);
  }

  public function tearDown()
  {
    $this->assertTrue( $this->invoice->delete() );
    $this->assertTrue( $this->customer->delete() );
  }

}

?>
