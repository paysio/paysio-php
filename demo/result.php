<?php

require_once 'head.php';

$body = file_get_contents('php://input');
$event = new \Paysio\Event(json_decode($body));

//file_put_contents('/tmp/wh.log', var_export($event, true));