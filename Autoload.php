<?php

require_once '/usr/local/lib/Hoa/Core/Core.php';

spl_autoload_register(function ( $class ) {

    $path = __DIR__ . DIRECTORY_SEPARATOR .
            str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

    if(file_exists($path))
        require_once $path;
});
