<?php

class Iugu_Customer extends APIResource {

  public static function create($attributes=Array()) { return self::createAPI($attributes); }
  public static function fetch($key)                  { return self::fetchAPI($key); }
  public        function save()                      { return $this->saveAPI(); }
  public        function delete()                    { return $this->deleteAPI(); }

  public        function refresh()                   { return $this->refreshAPI(); }
  public static function search($options=Array())    { return self::searchAPI($options); }

  public        function payment_methods()            { return new APIChildResource(Array("customer_id" => $this->id), "Iugu_PaymentMethod"); }
  public        function invoices()                  { return new APIChildResource(Array("customer_id" => $this->id), "Iugu_Invoice"); }

  // TODO: (WAITING BUGFIX) get DefaultPaymentMethod and return
  public function default_payment_method() {
    if (!isset($this->id)) return false;
    if (!isset($this->default_payment_method_id)) return false;
    if (!$this->default_payment_method_id) return false;
    return Iugu_PaymentMethod::fetch(
      Array(
        "customer_id" => $this->id,
        "id" => $this->default_payment_method_id
      )
    );
  }

}
