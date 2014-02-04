<?php

class Iugu_PaymentMethod extends APIResource {

  public static function url($object=NULL) {
    if (!isset($object["customer_id"])) throw new IuguException("Missing Customer ID");

    $customer_id = $object["customer_id"];
    $object_id = NULL;

    if (isset($object["id"])) $object_id = $object["id"];

    return self::endpointAPI( $object_id, "/customers/" . $object["customer_id"] );
  }

  public static function create($attributes=Array()) { return self::createAPI($attributes); }
  public static function fetch($key)                  { return self::fetchAPI($key); }
  public        function save()                      { return $this->saveAPI(); }
  public        function delete()                    { return $this->deleteAPI(); }

  public        function refresh()                   { return $this->refreshAPI(); }
  public static function search($options=Array())    { return self::searchAPI($options); }

  // TODO: (FUTURE) Allow charge from here
}
