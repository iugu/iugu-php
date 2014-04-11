<?php

$baseDirectory = dirname(__FILE__);

require( $baseDirectory . "/Iugu/Backward_Compatibility.php" );

require( $baseDirectory . "/Iugu/Base.php" );
require( $baseDirectory . "/Iugu/SearchResult.php" );
require( $baseDirectory . "/Iugu/Object.php" );
require( $baseDirectory . "/Iugu/Utilities.php" );

require( $baseDirectory . "/Iugu/APIRequest.php" );
require( $baseDirectory . "/Iugu/APIResource.php" );
require( $baseDirectory . "/Iugu/APIChildResource.php" );

require( $baseDirectory . "/Iugu/Customer.php" );
require( $baseDirectory . "/Iugu/PaymentMethod.php" );
require( $baseDirectory . "/Iugu/PaymentToken.php" );
require( $baseDirectory . "/Iugu/Charge.php" );
require( $baseDirectory . "/Iugu/Invoice.php" );
require( $baseDirectory . "/Iugu/Subscription.php" );
require( $baseDirectory . "/Iugu/Plan.php" );

require( $baseDirectory . "/Iugu/Factory.php" );
