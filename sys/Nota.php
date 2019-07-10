<?php

class Nota extends IncubaMain{
    
    /**
     * Construtor para instanciar a classe banco de dados
     */
    public function __construct(){
        $db = new Database();
        $this->conn = $db->_connect();
    }

    public function insertNewNF(){
        $service = $_POST['data2'];
        $data = $this->newFormat($_POST['dataFor']);
        $numNF = "000000001";
        if ( strlen($data['cpf_cnpj']) < 12 ) {
            $ind_tipo_cpf_cnpj = 00;
        }else{
            $ind_tipo_cpf_cnpj = 01;
        }
        $data_e = $this->_dataEnterprise();
        $data_c = $this->_dataCliente($data['id']);//Dados do cliente.
        $number = $this->_numberNF();
        $vlrTotalNF = $this->somaVlrTotalNF($service);
        $sql = "INSERT INTO nota_fiscal(num_nota, cpf_cnpj_cliente, razao_social, inscricao_estadual, cod_consumidor_cliente, data_emissao_nf, ano_mes_apuracao, modelo, cfop, situacao_doc, referencia_item_nota, telefone_cliente, ind_tipo_cpf_cnpj, tipo_cliente, telefone_empresa, cnpj_emitente, valor_total_nf) VALUES('{$numNF}','{$data_c["cpf_cnpj"]}', '{$data_c["razao_social"]}', '{$data_c["inscricao_estadual"]}', '{$data_c["id"]}', '{$data["dt_emissao"]}', '{$data["ano_mes_apu"]}', '{$data["modelo"]}', '{$data["cfop"]}', '{$data["situacao_doc"]}', 'ref', 'telefone', $ind_tipo_cpf_cnpj, '{$data["tipo_cliente"]}', '{$data_e["tel_responsavel"]}', '{$data_e["cnpj"]}', 'VlrTotal')";


        return $vlrTotalNF;
    }
    //CONTINUAR A SOMATORIA DOS VALORES
    //função para salvar o valor total da nota
    public function somaVlrTotalNF($arr){
        print_r(array_keys($arr) );
        //return var_dump($arr);
        
    }
    //formata data
    public function newFormat($array){
        $newArray = array();
        for ($i=0; $i < count($array); $i++) { 
            $newArray[$array[$i]['name']] = $this->remove($array[$i]['value']);
        }
        return $newArray;
    }//remove os pontos
    public function remove($s){
        $remove = array (".",",","/","(",")","-");
        $r = str_replace($remove, "", $s);
        return $r;
    }

    public function _dataEnterprise(){
        $emp = new Empresa();
        return $emp->getCompanyData();
    }


    /**
     * Função responsavel por instanciar a classe de cliente e utilizar o methodo para buscar todos os dados de um determinado cliente. 
     * @param $id do usuário.
     * @return array 
     */
    public function _dataCliente($id){
        $cliente = new Cliente();
        return $cliente->getAllDataCliente($id);
    }

    public function _numberNF(){
        $sql = "SELECT MAX(id_nota) from nota_fiscal";
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
            $res = $result->fetch_assoc();
        }else{
            $res = null;
        }
        return $res;
        
    }
    

 
}


