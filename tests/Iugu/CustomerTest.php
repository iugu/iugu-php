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

        $customer = Iugu_Customer::create([
            "notes" => "Uses emacs",
            "name"  => "Martin Fowler",
            "email" => "martin@fowler.com"
        ]);

        $this->assertEquals($customer->is_new(), false);
        $this->assertEquals($customer->notes, 'Uses emacs');
        $this->assertEquals($customer->name,  'Martin Fowler');
        $this->assertEquals($customer->email, 'martin@fowler.com');
    }

}
