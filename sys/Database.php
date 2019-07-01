<?php

class Database
{

    private $server = 'localhost';
    private $user = 'root';
    private $password = '';
    private $db = 'incubatec_nf';

    /**
     * Função para connectar ao bd
     * @return void
     */
    public function _connect(){
        $conn = new mysqli($this->server, $this->user, $this->password, $this->db);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }

    /**
     * Função para fechar a conexao com o db
     * @param $conn variavel de connection
     */
    public function _close($conn){
        $conn->close();
    }

}
