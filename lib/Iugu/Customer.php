<?php

class Iugu_Customer extends APIResource {

  public static function create($attributes=Array()) { return self::createAPI($attributes); }
  public static function fetch($key)                  { return self::fetchAPI($key); }
  public        function save()                      { return $this->saveAPI(); }
  public        function delete()                    { return $this->deleteAPI(); }

  public        function refresh()                   { return $this->refreshAPI(); }
  public static function search($options=Array())    { return self::searchAPI($options); }

  public        function paymentMethods()            { return new APIChildResource(Array("customer_id" => $this->id), "Iugu_PaymentMethod"); }

  // TODO: get DefaultPaymentMethod and return
  // TODO: list all invoices by the client
}

?>
