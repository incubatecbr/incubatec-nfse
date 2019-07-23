<?php

class Cliente extends IncubaMain
{

    
    /**
     * Construtor para instanciar a classe banco de dados
     */
    public function __construct(){
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
        $cep = Util::replaceString($_POST['data']);
        $request = file_get_contents("https://viacep.com.br/ws/{$cep}/json/", 0, null);
        $err = strpos($request, "erro");//Verifica se o retorno da API foi um erro.
        if($err === false){//Se for false não encontrou 'erro' no retorno da API.
            $json = json_decode($request);
        }else{//Encontrou 'error' no retorno.
            $json = false;
        }
        return $json;
    }

 
    
    /**
     * Função responsavel por inserir novo usuario no db.
     * @return boolean
     */
    public function newClient(){
        //razao social, cpf_cnpj, inscricao_estadual, logradouro, numero, complemento, cep, bairro, uf, municipio, cod_mun_ibge, telefone.
        $data = Util::formatArray($_POST['data']);
        $client = $this->getClientByCpfCnpj( $data["cpf_cnpj"] );
        if($client){//existe cliente cadastro com esse cpf/cnpj.
            return "EXISTE!";
        }else{
            $razao_social = mb_strtoupper($data["razao_social"]);//Razão social em UPPERCASE
            $ie = mb_strtoupper($data["insc_estadual"]);//Inscrição estadual em UPPERCASE
            $sql = "INSERT INTO clientes(`razao_social`,`cpf_cnpj`, `inscricao_estadual`, `logradouro`, `numero`, `complemento`, `bairro`, `municipio`, `cod_muni_ibge`, `uf`, `cep`, `telefone`) VALUES( '{$razao_social}', '{$data["cpf_cnpj"]}', '{$ie}', '{$data["endereco"]}', '{$data["numero"]}', '{$data["complemento"]}', '{$data["bairro"]}', '{$data["municipio"]}', '{$data["cod_municipio_ibge"]}', '{$data["uf"]}', '{$data["cep"]}', '{$data["tel_contato"]}')";
            if($this->conn->query($sql) === TRUE) {
                return true;
            }else{
                return "Error {$this->conn->error}";
            }
            $this->conn->close();//fecha conexao com db.
        }
    }

    /**
     * Função responsavel por verificar se existe cliente com base no cpf/cnpj.
     * @param $cpf_cnpj
     * @return bool
     */
    public function getClientByCpfCnpj($cpf_cnpj){
        $sql = "SELECT id, razao_social, cpf_cnpj, inscricao_estadual FROM clientes WHERE cpf_cnpj = {$cpf_cnpj}";
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
            $client = $result->fetch_assoc();
            return $client;
        }else{
            return false;
        }
        $this->conn->close();//fecha conexao com db.
    }


    /**
     * Função responsavel por retornar todos os dados do cliente com base no seu ID.
     * @param $id user
     */
    public function getAllDataCliente($id){
        $sql = "SELECT * FROM clientes WHERE id = {$id}";
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
            $client = $result->fetch_assoc();
            return $client;
        }else{
            return false;
        }
        $this->conn->close();//fecha conexao com db.
    }

}
