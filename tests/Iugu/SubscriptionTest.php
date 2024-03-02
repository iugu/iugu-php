<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class Iugu_SubscriptionTest extends TestCase
{
  protected function setUp()
  {
    Iugu::$endpoint = "http://api.desenvolvimento";
    Iugu::setApiKey("development_api_token");
  }

  public function testShouldCreateASubscription()
  {
    \VCR\VCR::turnOn();
    \VCR\VCR::insertCassette("iugu_subscription_should_create_a_subscription");

    $subscription = Iugu_Subscription::create([
      "plan_identifier" => "silver",
      "customer_id" => "A11222C0E33445566E77A8899DD00B00"
    ]);

    $this->assertEquals("silver", $subscription->plan_identifier);
    $this->assertEquals("A11222C0E33445566E77A8899DD00B00", $subscription->customer_id);

    \VCR\VCR::eject();
    \VCR\VCR::turnOff();
  }

  public function testChangePlanSimulation()
  {
    \VCR\VCR::turnOn();
    \VCR\VCR::insertCassette("iugu_subscription_should_create_a_subscription");

    $subscription = Iugu_Subscription::create([
      "plan_identifier" => "silver",
      "customer_id" => "A11222C0E33445566E77A8899DD00B00"
    ]);

    \VCR\VCR::insertCassette("iugu_subscription_change_plan_simulation");
    $simulation = $subscription->change_plan_simulation("gold");

    $this->assertEquals("silver", $simulation->old_plan);
    $this->assertEquals("gold", $simulation->new_plan);

    \VCR\VCR::eject();
    \VCR\VCR::turnOff();
  }
}