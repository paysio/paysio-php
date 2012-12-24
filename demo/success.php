<?php

require_once 'head.php';

$charge = \Paysio\Charge::retrieve($_GET['charge_id']);

echo $charge->description . ' - Ok ' . \Paysio\Currency::decimalAmount($charge->amount) . ' RUB';