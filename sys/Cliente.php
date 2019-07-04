<?php

class Cliente extends IncubaMain
{

    /**
     * Construtor para instanciar a classe banco de dados
     */
    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->_connect();
    }

    /**
     * Função responsavel por buscar nome da cidade, uf e codigo municipio ibge.
     * A busca é realizada na API ViaCep(https://viacep.com.br/).
     * Informe o cep sem pontuação e receba o JSON. 
     * @param $cep
     * @return $request - Dados da requisição ou [] (vazio).
     */
    public function getAddressData(){
        $cep = $this->removePoints($_POST['data']);
        $request = file_get_contents("https://viacep.com.br/ws/{$cep}/json/", 0, null);
        $err = strpos($request, "erro");//Verifica se o retorno da API foi um erro.
        if($err === false){//Se for false não encontrou 'erro' no retorno da API.
            $json = json_decode($request);
        }else{//Encontrou 'error' no retorno.
            $json = false;
        }
        return $json;
    }

}
