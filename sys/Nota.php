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
        
        $data = Util::formatArray($_POST['dataFor']);
        if ( strlen($data['cpf_cnpj']) < 12 ) {
            $ind_tipo_cpf_cnpj = 2;
        }else{
            $ind_tipo_cpf_cnpj = 1;
        }
        $lastNum = $this->getMaxNumberNota();
        $numNota = (!isset($lastNum) OR ($lastNum == null) ) ? "000000001" : Util::nextNumberOfNota($lastNum);

        $data_e = $this->_dataEnterprise();//Dados empresa
        $data_c = $this->_dataCliente($data['id']);//Dados do cliente.
   
        $vlrTotalNF = Util::sumValuesItens($service);
        $cnpj_e = Util::replaceString($data_e["cnpj"]);
        $telC = Util::replaceString($data_c["telefone"]);
        $telE = Util::replaceString($data_e["tel_responsavel"]);

        //$sql = "INSERT INTO nota_fiscal(num_nota, cpf_cnpj_cliente, razao_social, inscricao_estadual, cod_consumidor_cliente, data_emissao_nf, ano_mes_apuracao, modelo, cfop, situacao_doc, referencia_item_nota, telefone_cliente, ind_tipo_cpf_cnpj, tipo_cliente, telefone_empresa, cnpj_emitente, valor_total_nf) VALUES('{$numNota}','{$data_c["cpf_cnpj"]}', '{$data_c["razao_social"]}', '{$data_c["inscricao_estadual"]}', '{$data_c["id"]}', '{$data["dt_emissao"]}', '{$data["ano_mes_apu"]}', '{$data["modelo"]}', '{$data["cfop"]}', '{$data["situacao_doc"]}', 'ref', '{$telC}', $ind_tipo_cpf_cnpj, '{$data["tipo_cliente"]}', '{$telE}', '{$cnpj_e}','{$vlrTotalNF}')";
        
        $new = [];
        for ($i=0; $i < count($service); $i++) { 
            if (is_array($service[$i])): 
                //$new["itens"][$i] = $service[$i];
                for ($j=0; $j < 6; $j++) { 
                    //$new["itens"][$i] = $service[$j];
                    //var_dump( $service[$i][$j] ) ;
                    $new["itens"][$i] = $service[$i];
                }
            endif;
        }
        var_dump($new);
        //
        
        // foreach ($service as $s => $val) {
        //     //$sql = "INSERT INTO item_nota(id_nota, cod_item, descricao, valor_item) VALUES(``,``,``,``,``)";

        //     print_r("{$val} <br>");
        // }

        die();


        if($this->conn->query($sql) === TRUE) {
            //return true; //INSERIR ITENS DA NOTA
            $last_id = $this->conn->insert_id;
            
            //return $last_id;
            
        }else{
            return "Error {$this->conn->error}";
        }
        $this->conn->close();//fecha conexao com db.
        
        //return $sql;
    }

    
    /**
     * Função para retornar o maior id de nota fiscal.
     * @return $max Retorna nulll ou numero.
     */
    public function getMaxNumberNota(){
        $sql = "SELECT MAX(id_nota) AS numMax from nota_fiscal";
        $exe = $this->conn->query($sql);
        $result = $exe->fetch_assoc();
        $max = ( !isset($result) ) ? null : $result['numMax'];
        return $max;
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



 
}


