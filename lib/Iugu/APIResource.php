<?php

class APIResource extends Iugu_Object 
{
  private static $_apiRequester = null;

  public static function convertClassToObjectType() {
    $object_type = str_replace("Iugu_", "", get_called_class());
    return mb_strtolower($object_type, "UTF-8");
  }

  public static function objectBaseURI() {
    $object_type = self::convertClassToObjectType();
    switch($object_type) {
      // Add Exceptions as needed
      default:
       return $object_type . 's'; 
    } 
  }

  public static function getAPI() {
    if (APIResource::$_apiRequester == null) APIResource::$_apiRequester = new Iugu_APIRequest();  
    return APIResource::$_apiRequester;
  }

  private static function request( $data=Array(), $type="GET", $addend="" ) {
    return self::getAPI()->request(
      $type,
      Iugu::getBaseURI() . self::objectBaseURI() . $addend,
      $data
    );
  }

  public static function url($object=NULL) {
    return true;
  }

  public static function create($attributes=Array()) {
    return Iugu_Factory::createFromResponse(
      self::convertClassToObjectType(),
      self::request( $attributes, "POST" )
    );
  }

  public static function find($id) {
    try {
      $response = self::getAPI()->request(
        "GET",
        Iugu::getBaseURI() . self::objectBaseURI() . "/" . $id
      );
    } catch (IuguObjectNotFound $e) {
     throw new IuguObjectNotFound(self::convertClassToObjectType(get_called_class()) . ":" . $id . " not found"); 
    }

    print_r($response);

    // echo "OK\r\n";
    // echo self::convertClassToObjectType( get_called_class() ) . "\r\n";
    // echo self::objectBaseURI() . "\r\n";
    return null; 
  }

  public function test() {
    echo self::objectBaseURI() . "\r\n"; 
  }

  public function delete() {
    if ($this["id"] == null) return false;

    try {
      $response = self::request( Array(), "DELETE", "/" . $this["id"] );
      if (isset($response->errors)) throw IuguException();
    } catch (Exception $e) {
      return false;
    }

    return true;
  }

}

?>
