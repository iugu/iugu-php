<?php

class Iugu_InvoiceTest extends Iugu_TestCase
{
    public function setUp()
    {
        $this->customer = self::createTestCustomer();
        $this->invoice = Iugu_Invoice::create(
          [
            'email'       => 'patrick@iugu.com',
            'due_date'    => '30/12/'.date('Y'),
            'customer_id' => $this->customer,
            'items'       => [
              [
                'description' => 'Item Teste',
                'quantity'    => '1',
                'price_cents' => '1000',
              ],
            ],
          ]
        );

        $payment_token = Iugu_PaymentToken::create(
            [
                'method' => 'credit_card',
                'data'   => [
                    'number'             => '4111111111111111',
                    'verification_value' => '123',
                    'first_name'         => 'Joao',
                    'last_name'          => 'Silva',
                    'month'              => '12',
                    'year'               => date('Y'),
                ],
            ]
        );

        $charge = Iugu_Charge::create(
            [
                'token' => $payment_token,
                'invoice_id' => $this->invoice->id
            ]
        );

    }

    public function testCreateInvoice()
    {
        $this->assertNotNull($this->invoice);
        $this->assertTrue(count($this->invoice['errors']) == 0);
    }

    public function testCaptureInvoice()
    {
        $this->invoice->capture();
        $this->assertTrue($this->invoice->status == "paid");
        $this->assertNotNull($this->invoice);
        $this->assertTrue(count($this->invoice['errors']) == 0);
    }

    public function testCreateEmptyInvoice()
    {
        $invoice = Iugu_Invoice::create();
        $this->assertNotNull($invoice);
        $this->assertTrue(count($invoice['errors']) > 0);
    }

    public function testRefreshInvoice()
    {
        $this->assertTrue($this->invoice->refresh());
    }

    public function testFetchInvoice()
    {
        $this->expectException('IuguException');
        $new_invoice = Iugu_Invoice::fetch('NO VALID INVOICE');

        $fetched_invoice = Iugu_Invoice::fetch($this->invoice->id);
        $this->assertNotNull($fetched_invoice);
        $this->assertNotNull($fetched_invoice['id']);
    }

    public function testSearchPaymentMethod()
    {
        $searchResults = Iugu_Invoice::search([]);
        $this->assertTrue($searchResults->total() > 0);
    }

    public function testCustomerWithValidInvoices()
    {
        $this->assertTrue($this->customer->invoices()->search()->total() > 0);
    }

    public function tearDown()
    {
        $this->assertTrue($this->invoice->delete());
        $this->assertTrue($this->customer->delete());
    }
}
