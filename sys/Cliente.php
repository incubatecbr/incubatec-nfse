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

 
    
    /**
     * Função responsavel por inserir novo usuario no db.
     * @return boolean
     */
    public function newClient(){
        //razao social, cpf_cnpj, inscricao_estadual, logradouro, numero, complemento, cep, bairro, uf, municipio, cod_mun_ibge, telefone.
        //$rules = [35, 14, 14, 45, 5, 15, 8, 15, 2, 30, 7, 12];
        $data = Util::formatArray($_POST['data']);
   
        $razao_social = Util::addSpacesOrNumber($data["razao_social"], 35, 'X');
        $cpf_cnpj = Util::addSpacesOrNumber($data["cpf_cnpj"], 14, 'N');
        $ie = Util::addSpacesOrNumber($data["insc_estadual"], 14, 'X');
        $logradouro = Util::addSpacesOrNumber($data["endereco"], 45, 'X');
        $bairro = Util::addSpacesOrNumber($data["bairro"], 15, 'X');
        $num = Util::addSpacesOrNumber($data["numero"], 5, 'N');
        $comp = Util::addSpacesOrNumber($data["complemento"], 15, 'X');
        $municipio = Util::addSpacesOrNumber($data["municipio"], 30, 'X');
        $telefone = Util::addSpacesOrNumber($data["tel_contato"], 12, 'X');
     
        $client = $this->getClient( $cpf_cnpj );

        if($client){//existe cliente cadastro com esse cpf/cnpj.
            return "EXISTE!";
        }else{//não existe.
            //$numero = intval($data['numero']);
            $sql = "INSERT INTO clientes(`razao_social`,`cpf_cnpj`, `inscricao_estadual`, `logradouro`, `numero`, `complemento`, `bairro`, `municipio`, `cod_muni_ibge`, `uf`, `cep`, `telefone`) VALUES( '{$razao_social}', '{$cpf_cnpj}', '{$ie}', '{$logradouro}', '{$num}', '{$comp}', '{$bairro}', '{$municipio}', '{$data["cod_municipio_ibge"]}', '{$data["uf"]}', '{$data["cep"]}', '{$telefone}')";
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
    public function getClient($cpf_cnpj){
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
     * Função que recebe a requisição e verifica se existe cliente cadastrado com esse CPF/CNPJ.
     * @return object $client
     */
    public function getClientByCpfCnpj(){
        $cpf_cnpj = $_POST['data'];
        $cpf_cnpj_formated = $this->_remove($_POST['data']);
        $client = $this->getClient($cpf_cnpj_formated);
        if( !$client ){
            return false;
        }
        return $client;
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
    
    /**
     * Função auxiliar para formatar data.
     * @param [array] $arr
     * @return void
     */
    public function _format($arr){
        $newArray = array();
        for ($i=0; $i < count($arr); $i++) { 
            $newArray[ $arr[$i]['name'] ] = $this->_remove($arr[$i]['value']);
        }
        return $newArray;
    }
    /**
     * Função auxiliar para remover pontos e barra.
     * @param [string] $s
     * @return void
     */
    public function _remove($s){
        $remove = array (".",",","/","(",")","-");
        $r = str_replace($remove, "", $s);
        return $r;
    }


}
