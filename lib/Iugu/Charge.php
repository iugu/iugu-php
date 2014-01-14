<?php

class Iugu_Charge extends APIResource {
  public static function create($attributes=Array()) { 
    $result = self::createAPI($attributes);
    if (!isset($result->success)) {
      $result->success = false; 
    }
   return $result; 
  }

  public function invoice() {
    // TODO: Return Invoice based on Invoice ID or Throw Error 
  }
}

?>
