<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class Iugu_CustomerTest extends TestCase
{
    protected function setUp()
    {
        Iugu::$endpoint = 'http://api.desenvolvimento';
        Iugu::setApiKey('development_api_token');
    }

    public function testShouldCreateACustomer()
    {
        \VCR\VCR::turnOn();
        \VCR\VCR::insertCassette('iugu_customer_should_create_a_customer');

        $customer = Iugu_Customer::create([
            "email" => "martin@fowler.com",
            "name"  => "Martin Fowler",
            "notes" => "Uses emacs"
        ]);

        $this->assertEquals($customer->is_new(), false);
        $this->assertEquals($customer->email, 'martin@fowler.com');
        $this->assertEquals($customer->name,  'Martin Fowler');
        $this->assertEquals($customer->notes, 'Uses emacs');

        \VCR\VCR::eject();
        \VCR\VCR::turnOff();
    }

}
