<?php

class Iugu_Charge extends APIResource {
  public static function create($attributes=Array()) { 
    $result = self::createAPI($attributes);
    if (!isset($result->success) && !isset($result->errors)) {
      $result->success = false; 
    }
    return $result; 
  }

  public function invoice() {
    if (!isset($this->invoice_id)) return false;
    if (!$this->invoice_id) return false;
    return Iugu_Invoice::fetch($this->invoice_id);
  }
}
