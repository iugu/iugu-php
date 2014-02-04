<?php

class Iugu_Factory
{
  public static function createFromResponse( $object_type, $response ) {
    // Should i send fetch to here?
    $object_type = str_replace(" ", "", ucwords(str_replace("_", " ", $object_type)));
    $class_name = "Iugu_" . $object_type;

    if (!class_exists($class_name)) return null;

    if ( is_object($response) && (isset($response->items)) && (isset($response->totalItems)) ) {
      $results = Array();

      foreach ($response->items as $item) {
        array_push( $results, self::createFromResponse($object_type, $item) );
      }

      return new Iugu_SearchResult( $results, $response->totalItems );
    }  else if (is_array($response)) {
      $results = Array();

      foreach ($response as $item) {
        array_push( $results, self::createFromResponse($object_type, $item) );
      }

      return new Iugu_SearchResult( $results, count($results) );
    }
    else if (is_object($response)) {
      return new $class_name( (Array) $response );
    }

    return null; 
  }
}
