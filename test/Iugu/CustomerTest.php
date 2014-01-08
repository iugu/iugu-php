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

  public function testFetchCustomer()
  {
    $object = self::createTestCustomer();
    $customer_id = $object->id;
    $customer = new Iugu_Customer();
    $customer->id = $customer_id;
    $this->assertTrue( $customer->fetch() );
    $this->assertEqual( $object->name, $customer->name );
  }
}

?>
