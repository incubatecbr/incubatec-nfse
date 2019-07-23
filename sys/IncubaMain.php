<?php

abstract class IncubaMain {

    public function __construct() {
        $database = new Database();
    }

    public function init() {
        //percorre $_POST
        foreach ($_POST as $key => $val) {
            $this->$key = $val;
        }
        if (isset($this->action) && method_exists($this, $this->action)) {
            $_method = $this->action;
            return $this->$_method();
        }
    }
}