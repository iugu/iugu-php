<?php

class Iugu_PlanTest extends Iugu_TestCase
{

  public function setUp()
  {
    //var_dump(Iugu_Plan::search()->results());
    //die();
    $this->plan = Iugu_Plan::create(
      Array(
        "name" => "name",
        "identifier" => "identifier",
        "interval" => 1,
        "interval_type" => "months", //months or weeks
        "prices.value_cents" => 1,
      )
    );
  }

  public function testCreatePlan()
  {
    $this->assertNotNull($this->plan);
    $this->assertTrue( count($this->plan["errors"]) == 0 );
  }

  public function testCreateEmptyPlan()
  {
    $plan = Iugu_Plan::create();
    $this->assertNotNull( $plan );
    $this->assertTrue( count($plan["errors"]) > 0 );
  }

  public function testRefreshPlan()
  {
    $this->assertTrue( $this->plan->refresh() );
  }

  public function testFetchPlan()
  {
    $this->expectException("IuguObjectNotFound");
    $new_plan = Iugu_Plan::fetch( "NOT_VALID" );

    $fetched_plan = Iugu_Plan::fetch( $this->plan->id );
    $this->assertNotNull( $fetched_plan );
    $this->assertNotNull( $fetched_plan["id"] );
  }

  public function testSearchPlan()
  {
    $searchResults = Iugu_Plan::search(Array());
    $this->assertTrue( $searchResults->total() > 0 );
  }
  public function tearDown()
  {
    $this->assertTrue( $this->plan->delete() );
  }
}

