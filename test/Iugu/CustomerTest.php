<?php

class Iugu_CustomerTest extends Iugu_TestCase
{
  public function testCreateAndDeleteCustomer()
  {
    $object = self::createTestCustomer();

    $this->assertNotNull($object);
    $this->assertNotNull($object["id"]);

    $this->assertTrue( $object->delete() );
  }

  public function testCreateWrongCustomer()
  {
    $object = self::createTestCustomer(Array("email"=>"patricknegri"));;

    $this->assertNull($object["id"]);
    $this->assertTrue(count($object["errors"]) > 0);

    $object->email = "patricknegri@gmail.com";
    $object->save();

    $this->assertNotNull($object["id"]);
    $this->assertTrue( $object->delete() );
  }

  public function testCreateCustomerFromSave()
  {
    $object = new Iugu_Customer();
    $object["email"] = "patricknegri@gmail.com";
    $object["name"] = "Patrick Ribeiro Negri";

    $this->assertTrue( $object->save() );
    $this->assertTrue( $object->delete() );
  }

  public function testModifyCustomer()
  {
    $object = self::createTestCustomer();
    $object->name = "Patrick Ribeiro Negri";
    $this->assertTrue( $object->save() );
    $this->assertTrue( $object->delete() );
  }

  public function testRefreshCustomer()
  {
    $object = self::createTestCustomer();
    $customer_id = $object->id;
    $customer = new Iugu_Customer();
    $customer->id = $customer_id;
    $this->assertTrue( $customer->refresh() );
    $this->assertEqual( $object->name, $customer->name );
    $this->assertTrue( $object->delete() );
  }

  public function testFetch()
  {
    $object = self::createTestCustomer();
    $customer = Iugu_Customer::fetch( $object->id );
    $this->assertNotNull($customer);
    $this->assertNotNull($customer["id"]);

    $this->expectException("IuguObjectNotFound");
    Iugu_Customer::fetch("D245B36FCD4B42DDB44208D868FF2C10");

    $this->assertTrue( $object->delete() );
  }

  public function testSearch()
  {
    $object = self::createTestCustomer();

    $searchResults = Iugu_Customer::search(Array()); 

    $this->assertTrue( $searchResults->total() > 0 );
    $this->assertTrue( $object->delete() );
  }

}

?>
