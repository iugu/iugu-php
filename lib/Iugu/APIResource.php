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

  public static function url($object=NULL) {
    $append = "";

    if (is_string($object)) $append = "/" . $object; 
    else if (is_object($object) && (isset($object["id"])) ) $append = "/" . $object["id"];

    return Iugu::getBaseURI() . self::objectBaseURI() . $append;
  }

  public static function createFromResponse($response) {
    return Iugu_Factory::createFromResponse(
      self::convertClassToObjectType(),
      $response
    );
  }

  public static function create($attributes=Array()) {
    return self::createFromResponse(
      self::getAPI()->request(
        "POST",
        self::url(),
        $attributes
      )
    );
  }

  public static function find($id) {
    try {
      $response = self::getAPI()->request(
        "GET",
        self::url($id)
      );
    } catch (IuguObjectNotFound $e) {
     throw new IuguObjectNotFound(self::convertClassToObjectType(get_called_class()) . ":" . $id . " not found"); 
    }

    // echo "OK\r\n";
    // echo self::convertClassToObjectType( get_called_class() ) . "\r\n";
    // echo self::objectBaseURI() . "\r\n";
    return null; 
  }

  public function delete() {
    if ($this["id"] == null) return false;

    try {
      $response = self::getAPI()->request(
        "DELETE",
        self::url($this)
      );

      if (isset($response->errors)) throw IuguException();
    } catch (Exception $e) {
      return false;
    }

    return true;
  }

  public function is_new() {
    return !isset($this->_attributes["id"]); 
  }

  public function fetch() {
    if ($this->is_new()) return false;

    try {
      $response = self::getAPI()->request(
        "GET",
        self::url($this)
      );

      if (isset($response->errors)) throw IuguObjectNotFound();

      $new_object = self::createFromResponse( $response );
      $this->copy( $new_object );
      $this->resetStates();

    } catch (Exception $e) {
      return false;
    }

    return true;
  }

  public function save() {
    try {
      $response = self::getAPI()->request(
        $this->is_new() ? "POST" : "PUT",
        self::url($this),
        $this->modifiedAttributes()
      );

      if (isset($response->errors)) throw IuguException();

      $new_object = self::createFromResponse( $response );
      $this->copy( $new_object );
      $this->resetStates();

    } catch (Exception $e) {
      return false;
    }

    return true;
  }
}

?>
