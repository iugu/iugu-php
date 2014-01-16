<?php

class Iugu_SubscriptionTest extends Iugu_TestCase
{
  public function setUp()
  {
    $this->customer = self::createTestCustomer();
    $this->subscription = Iugu_Subscription::create(
      Array(
        "plan_identifier" => "basic_plan", 
        "customer_id" => $this->customer
      )
    );
  }

  public function testCreateSubscription()
  {
    $this->assertNotNull($this->subscription);
    $this->assertTrue( count($this->subscription["errors"]) == 0 );
  }

  public function testCreateEmptySubscription()
  {
    $subscription = Iugu_Subscription::create(); 
    $this->assertNotNull( $subscription );
    $this->assertTrue( count($subscription["errors"]) > 0 );
  }

  public function testRefreshSubscription()
  {
    $this->assertTrue( $this->subscription->refresh() );
  }

  public function testFetchSubscription()
  {
    $this->expectException("IuguObjectNotFound");
    $new_subscription = Iugu_Subscription::fetch( "NOT_VALID" );

    $fetched_subscription = Iugu_Subscription::fetch( $this->subscription->id );
    $this->assertNotNull( $fetched_subscription );
    $this->assertNotNull( $fetched_subscription["id"] );
  }

  public function testSearchSubscription()
  {
    $searchResults = Iugu_Subscription::search(Array()); 
    $this->assertTrue( $searchResults->total() > 0 );
  }

  public function testSubscriptionHasCustomer()
  {
    $this->assertTrue( $this->subscription->customer() );
  }

  public function testSubscriptionChangePlan()
  {
    $this->assertFalse( $this->subscription->change_plan("basic_plan") );
    $this->assertTrue( $this->subscription->change_plan("advanced_plan") );
  }

  public function testSuspendAndActivateSubscription()
  {
    $this->assertTrue( $this->subscription->suspend() );
    $this->assertFalse( $this->subscription->suspend() );
    $this->assertTrue( $this->subscription->activate() );
    $this->assertFalse( $this->subscription->activate() );
  }

  public function tearDown()
  {
    $this->assertTrue( $this->subscription->delete() );
    $this->assertTrue( $this->customer->delete() );
  }
}

?>
