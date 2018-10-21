<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class Iugu_CustomerTest extends TestCase
{
  protected function setUp()
  {
    Iugu::$endpoint = "http://api.desenvolvimento";
    Iugu::setApiKey("development_api_token");
  }

  public function testShouldCreateACustomer()
  {
    \VCR\VCR::turnOn();
    \VCR\VCR::insertCassette("iugu_customer_should_create_a_customer");

    $customer = Iugu_Customer::create([
      "email" => "martin@fowler.com",
      "name"  => "Martin Fowler",
      "notes" => "Uses emacs"
    ]);

    $this->assertFalse($customer->is_new());
    $this->assertEquals("martin@fowler.com", $customer->email);
    $this->assertEquals("Martin Fowler",     $customer->name);
    $this->assertEquals("Uses emacs",        $customer->notes);

    \VCR\VCR::eject();
    \VCR\VCR::turnOff();
  }

  public function testShouldCreateACustomerWithCPF()
  {
    \VCR\VCR::turnOn();
    \VCR\VCR::insertCassette("iugu_customer_should_create_a_customer_with_cpf");

    $customer = Iugu_Customer::create([
      "email"    => "martin@fowler.com",
      "name"     => "Martin Fowler",
      "cpf_cnpj" => "648.144.103-01"
    ]);

    $this->assertFalse($customer->is_new());
    $this->assertEquals("648.144.103-01", $customer->cpf_cnpj);

    \VCR\VCR::eject();
    \VCR\VCR::turnOff();
  }

  public function testShouldCreateACustomerWithCNPJ()
  {
    \VCR\VCR::turnOn();
    \VCR\VCR::insertCassette("iugu_customer_should_create_a_customer_with_cnpj");

    $customer = Iugu_Customer::create([
      "email"    => "martin@fowler.com",
      "name"     => "Martin Fowler Inc",
      "cpf_cnpj" => "57.202.023/6256-27"
    ]);

    $this->assertFalse($customer->is_new());
    $this->assertEquals($customer->cpf_cnpj, "57.202.023/6256-27");

    \VCR\VCR::eject();
    \VCR\VCR::turnOff();
  }

  public function testShouldCreateACustomerWithFullAddress()
  {
    \VCR\VCR::turnOn();
    \VCR\VCR::insertCassette("iugu_customer_should_create_a_customer_with_full_address");

    $customer = Iugu_Customer::create([
      "email"      => "john.snow@greatwall.com",
      "name"       => "John Snow",
      "cpf_cnpj"   => "648.144.103-01",
      "cc_emails"  => "heisenberg@lospolloshermanos.com",
      "zip_code"   => "29190560",
      "number"     => "8",
      "street"     => "Rua dos Bobos",
      "city"       => "São Paulo",
      "state"      => "SP",
      "district"   => "Mooca",
      "complement" => "123C"
    ]);

    $this->assertFalse($customer->is_new());
    $this->assertEquals("648.144.103-01", $customer->cpf_cnpj);
    $this->assertEquals("heisenberg@lospolloshermanos.com", $customer->cc_emails);
    $this->assertEquals("29190560", $customer->zip_code);
    $this->assertEquals("8", $customer->number);
    $this->assertEquals("Rua dos Bobos", $customer->street);
    $this->assertEquals("Mooca", $customer->district);
    $this->assertEquals("123C", $customer->complement);

    \VCR\VCR::eject();
    \VCR\VCR::turnOff();
  }

  public function testShouldRaiseErrorWhenEmailIsEmpty()
  {
    \VCR\VCR::turnOn();
    \VCR\VCR::insertCassette("iugu_customer_should_not_create_a_customer_without_email");

    $customer = Iugu_Customer::create(["name" => "Fred Flintstone"]);

    $this->assertEquals("2", count($customer->errors["email"]));

    \VCR\VCR::eject();
    \VCR\VCR::turnOff();
  }
}
