<?php

class Iugu_Factory
{
  public static function createFromResponse( $object_type, $response ) {
    $class_name = "Iugu_" . ucwords($object_type);
    echo "Requested Object: " . $class_name . "\r\n";
    if (class_exists($class_name)) {
      return new $class_name( (Array) $response );
    }
    return null; 
  }
}

?>
