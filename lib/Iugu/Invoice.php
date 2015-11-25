<?php

class Iugu_Invoice extends APIResource {
  public static function create($attributes=Array()) { return self::createAPI($attributes); }
  public static function fetch($key)                  { return self::fetchAPI($key); }
  public        function save()                      { return $this->saveAPI(); }
  public        function delete()                    { return $this->deleteAPI(); }

  public        function refresh()                   { return $this->refreshAPI(); }
  public static function search($options=Array())    { return self::searchAPI($options); }

  public function customer() {
    if (!isset($this->customer_id)) return false;
    if (!$this->customer_id) return false;
    return Iugu_Customer::fetch($this->customer_id);
  }

  public function cancel() {
    if ($this->is_new()) return false;

    try {
      $response = self::API()->request(
        "PUT",
        static::url($this) . "/cancel"
      );
      if (isset($response->errors)) throw new IuguRequestException( $response->errors );
      $new_object = self::createFromResponse( $response );
      $this->copy( $new_object );
      $this->resetStates();
    } catch (Exception $e) {
      return false;
    }

    return true;
  }

  public function refund() {
    if ($this->is_new()) return false;

    try {
      $response = self::API()->request(
        "POST",
        static::url($this) . "/refund"
      );
      if (isset($response->errors)) {
        throw new IuguRequestException( $response->errors );
      }
      $new_object = self::createFromResponse( $response );
      $this->copy( $new_object );
      $this->resetStates();
    } catch (Exception $e) {
      return false;
    }

    return true;
  }
}
