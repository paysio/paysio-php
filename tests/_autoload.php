<?php

defined('PAYIO_TESTS_PATH') or define('PAYIO_TESTS_PATH', __DIR__);

defined('PAYIO_LIBRARY_PATH') or define('PAYIO_LIBRARY_PATH', realpath(__DIR__ . '/../library/'));

require_once PAYIO_LIBRARY_PATH . '/_autoload.php';

spl_autoload_register(function ($className) {
    $classFile = PAYIO_TESTS_PATH . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
    if (is_file($classFile)) {
        require_once $classFile;
    }
});