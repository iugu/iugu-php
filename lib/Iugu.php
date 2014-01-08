<?php

$baseDirectory = dirname(__FILE__);

require( $baseDirectory . "/Iugu/Backward_Compatibility.php" );

require( $baseDirectory . "/Iugu/Base.php" );
require( $baseDirectory . "/Iugu/Object.php" );
require( $baseDirectory . "/Iugu/Utilities.php" );

require( $baseDirectory . "/Iugu/APIRequest.php" );
require( $baseDirectory . "/Iugu/APIResource.php" );

require( $baseDirectory . "/Iugu/Customer.php" );

require( $baseDirectory . "/Iugu/Factory.php" );

try {
  Iugu_Customer::find("0E76BD815751433DA27428950B447E2B");
}
catch (IuguObjectNotFound $e) {
  echo $e->getMessage() . "\r\n";
}

try {
  Iugu_Customer::find("0E76BD815751433DA27428950B447E2C");
}
catch (IuguObjectNotFound $e) {
  echo $e->getMessage() . "\r\n";
}

?>
