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

  public static function API() {
    if (APIResource::$_apiRequester == null) APIResource::$_apiRequester = new Iugu_APIRequest();  
    return APIResource::$_apiRequester;
  }

  public static function url($object=NULL) {
    $append = "";

    if (is_string($object)) $append = "/" . $object; 
    else if (is_object($object) && (isset($object["id"])) ) $append = "/" . $object["id"];

    return Iugu::getBaseURI() . self::objectBaseURI() . $append;
  }

  protected static function createFromResponse($response, &$totalRecords=null) {
    return Iugu_Factory::createFromResponse(
      self::convertClassToObjectType(),
      $response,
      $totalRecords
    );
  }

  protected static function createAPI($attributes=Array()) {
    return self::createFromResponse(
      self::API()->request(
        "POST",
        self::url(),
        $attributes
      )
    );
  }

  protected function deleteAPI() {
    if ($this["id"] == null) return false;

    try {
      $response = self::API()->request(
        "DELETE",
        self::url($this)
      );

      if (isset($response->errors)) throw IuguException();
    } catch (Exception $e) {
      return false;
    }

    return true;
  }

  protected static function searchAPI($options=Array()) {
    try {
      $response = self::API()->request(
        "GET",
        self::url(),
        $options
      );

      return self::createFromResponse($response);

    } catch (Exception $e) {}

    return Array();
  }



  protected static function fetchAPI($id) {
    try {
      $response = self::API()->request(
        "GET",
        self::url($id)
      );

      return self::createFromResponse($response);
    } catch (IuguObjectNotFound $e) {
      throw new IuguObjectNotFound(self::convertClassToObjectType(get_called_class()) . ":" . $id . " not found"); 
    }
  }

  protected function refreshAPI() {
    if ($this->is_new()) return false;

    try {
      $response = self::API()->request(
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

  protected function saveAPI() {
    try {
      $response = self::API()->request(
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
