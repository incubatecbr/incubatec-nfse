<?php

abstract class IncubaMain {

    public function __construct() {
        // $authentication = new ConnectDB();
        // $this->conn = $authentication->token();
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

    public function formatDataArray($array){
        $newArray = array();
        for ($i=0; $i < count($array); $i++) { 
            $newArray[ $array[$i]['name'] ] = $this->removePoints($array[$i]['value']);
        }
        return $newArray;
    }

    public function removePoints($string){
        $remove = array (".",",","/","(",")","-");
        $res = str_replace($remove, "", $string);
        return $res;
    }



    
    
}