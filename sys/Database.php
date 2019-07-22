<?php

class Database
{

    //ip do servidor.
    private $server = 'localhost';
    //usuário do banco de dados.
    private $user = 'root';
    //senha do usuário.
    private $password = '';
    //nome da tabela.
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
        $conn->set_charset("utf8");//set utf8
        return $conn;
        
    }

    /**
     * Função para fechar a conexao com o db
     * @param $conn variavel de connection
     */
    public function _close(){
        $this->conn->close();
    }

}
