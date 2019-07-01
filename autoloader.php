<?php
spl_autoload_register(function ($class) {
    if (file_exists('sys/' . $class . '.php')) {
        require 'sys/' . $class . '.php';
    }
});
