<?php
spl_autoload_register(function ($class) {
    if (file_exists($APP_PATH['sys'] . $class . '.php')) {
        require $APP_PATH['sys'] . $class . '.php';
    }
});
