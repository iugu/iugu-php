<?php

include_once(dirname(__FILE__)."/../vendor/simpletest/simpletest/autorun.php");

error_reporting( E_ALL | E_STRICT );

echo "Running iugu PHP Test Suite\r\n";

$apiKey = getenv('IUGU_API_KEY');
if (!$apiKey) {
  echo "MISSING IUGU_API_KEY in Environment. $ export IUGU_API_KEY=<your_api_key>.";
  exit(1);
}

include_once(dirname(__FILE__)."/../lib/Iugu.php");

class Iugu_CustomerTest extends UnitTestCase
{
  public function testCreateAndDeleteCustomer()
  {
    $object = Iugu_Customer::create(
      Array(
        "email" => "patricknegri@gmail.com",
        "name" => "Patrick Negri"
      )
    );

    $this->assertNotNull($object);
    $this->assertNotNull($object["id"]);

    $this->assertTrue( $object->delete() );
  }
}

?>
