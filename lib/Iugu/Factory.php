<?php

class Iugu_Factory
{
  public static function createFromResponse( $object_type, $response ) {
    // Should i send fetch to here?
    $class_name = "Iugu_" . ucwords($object_type);
    if (!class_exists($class_name)) return null;

    if ( is_object($response) && (isset($response->items)) ) {
      # echo "Requested Object: " . $class_name . "\r\n";
      $results = Array();

      foreach ($response->items as $item) {
        array_push( $results, self::createFromResponse($object_type, $item) );
      }

      return new Iugu_SearchResult( $results, $response->totalItems );
    } else if (is_object($response)) {
      return new $class_name( (Array) $response );
    }

    return null; 
  }
}

?>
