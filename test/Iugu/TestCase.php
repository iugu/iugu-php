<?php

class Iugu_TestCase extends UnitTestCase
{
  protected static function createTestCustomer($_attributes=Array())
  {
    $attributes = Array(
      "email"=>"patricknegri@gmail.com",
      "name"=>"Patrick Negri"
    );
    return Iugu_Customer::create( array_merge($attributes,$_attributes) );
  }
}

?>
