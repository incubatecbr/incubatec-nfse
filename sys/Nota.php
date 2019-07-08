<?php

class Nota extends IncubaMain{
    
    /**
     * Construtor para instanciar a classe banco de dados
     */
    public function __construct(){
        $db = new Database();
        $this->conn = $db->_connect();
    }

    

 
}


