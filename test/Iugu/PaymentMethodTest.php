<?php

class Iugu_PaymentTest extends Iugu_TestCase
{
  public function testCreateAndDeletePaymentMethod()
  {
    $object = self::createTestCustomer();

    

    $this->assertTrue( $object->delete() );
  }
}

?>
