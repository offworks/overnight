<?php
// autoload current folder with Overnight base namespace
spl_autoload_register(function ($class) {
    $class = substr($class, strlen('Overnight\\'));

    $class = str_replace('\\', '/', $class);

    $path = __DIR__ . '/' . $class . '.php';

    if (file_exists($path))
        require_once $path;
});


