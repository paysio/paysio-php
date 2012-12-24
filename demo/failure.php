<?php

require_once 'head.php';

$charge = \Paysio\Charge::retrieve($_GET['charge_id']);

echo $charge->description . ' - Error ' . \Paysio\Currency::decimalAmount($charge->amount) . ' RUB';