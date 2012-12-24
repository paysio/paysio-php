<?php

/*
 * Set error reporting to the level to which Zend Framework code must comply.
 */
error_reporting( E_ALL | E_STRICT );

require_once __DIR__ . '/_autoload.php';

\Paysio\Api::setKey('HZClZur5OW3BYimWSydQNsArbph2L7IRo0ql8HK');

ob_start();