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

    /**
     * Função para pecorrer o array e remover pontos, traços, parenteses.
     * @param $array
     * @return $newArray Formatado.
     */
    public function formatDataArray($array){
        $newArray = array();
        for ($i=0; $i < count($array); $i++) { 
            $newArray[ $array[$i]['name'] ] = $this->removePoints($array[$i]['value']);
        }
        return $newArray;
    }

    /**
     * Função para remover pontos, virgulas, barras, traços, parenteses e acentuação.
     * @param $string
     * @return $string formadata
     */
    public function removePoints($string){
        $remove = array (".",",","/","(",")","-");
        $res = str_replace($remove, "", $string);
        $removeSC = preg_replace( '/[`^~\'"]/', null, iconv( 'UTF-8', 'ASCII//TRANSLIT', $res ) );
        return $removeSC; //Retorna os dados com UPPERCASE
    }



    
    
}