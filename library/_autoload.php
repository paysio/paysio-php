<?php

defined('PAYIO_LIBRARY_PATH') or define('PAYIO_LIBRARY_PATH', __DIR__);

spl_autoload_register(function ($className) {
    $classFile = PAYIO_LIBRARY_PATH . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
    if (is_file($classFile)) {
        require_once $classFile;
    }
});