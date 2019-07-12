<?php

class Nota extends IncubaMain{
    
    /**
     * Construtor para instanciar a classe banco de dados
     */
    public function __construct(){
        $db = new Database();
        $this->conn = $db->_connect();
    }

    public function newNotaFiscal(){
        $ret = '';
        $services = Util::formatArrayServices($_POST['data2']);//Array contendo o(s) serviço(s).
        $data = Util::formatArray($_POST['dataFor']);//Array contendo os dados do formulário.
        $ind_tipo_cpf_cnpj = (strlen($data['cpf_cnpj']) < 12 ) ? 2 : 1;//  2 para CPF, 1 para CNPJ.
        $lastNum = $this->getMaxNumberNota();
        $numNota = (!isset($lastNum) OR ($lastNum == null) ) ? "000000001" : Util::nextNumberOfNota($lastNum);
        $data_e = $this->_dataEnterprise();//Dados empresa
        $data_c = $this->_dataCliente($data['id']);//Dados do cliente.
        $vlrTotalNF = Util::sumValuesItens($services);
        $cnpj_e = Util::replaceString($data_e["cnpj"]);
        $telC = Util::replaceString($data_c["telefone"]);
        $telE = Util::replaceString($data_e["tel_responsavel"]);
        //sql
        $sql = "INSERT INTO nota_fiscal(num_nota, cpf_cnpj_cliente, razao_social, inscricao_estadual, cod_consumidor_cliente, data_emissao_nf, ano_mes_apuracao, modelo, cfop, situacao_doc, referencia_item_nota, telefone_cliente, ind_tipo_cpf_cnpj, tipo_cliente, telefone_empresa, cnpj_emitente, valor_total_nf) VALUES('{$numNota}','{$data_c["cpf_cnpj"]}', '{$data_c["razao_social"]}', '{$data_c["inscricao_estadual"]}', '{$data_c["id"]}', '{$data["dt_emissao"]}', '{$data["ano_mes_apu"]}', '{$data["modelo"]}', '{$data["cfop"]}', '{$data["situacao_doc"]}', 'ref', '{$telC}', $ind_tipo_cpf_cnpj, '{$data["tipo_cliente"]}', '{$telE}', '{$cnpj_e}','{$vlrTotalNF}')";
     
        if($this->conn->query($sql) === TRUE) {
            //INSERIR ITENS DA NOTA
            $last_id = $this->conn->insert_id;
            $c_services = $this->newItemService($services, $last_id);
            $ret = "Nota fiscal finalizada contendo {$c_services} serviço";
        }else{
            $ret = "Error {$this->conn->error}";
        }
        $this->conn->close();//fecha conexao com db.
        return $ret;
    }

    public function newItemService($array, $last_id){
        $c = 0;
        if(is_array($array[0])){
            for ($i=0; $i < count($array); $i++) { 
                $sql = "INSERT INTO item_nota(id_nota, cod_item, descricao, valor_item) VALUES($last_id,'{$array[$i][0]}','{$array[$i][1]}','{$array[$i][2]}')";
                if($this->conn->query($sql) === TRUE) {
                    $c++;
                }else{
                    return "Error {$this->conn->error}";
                    die();
                }
            }
        }else{
            $sql = "INSERT INTO item_nota(id_nota, cod_item, descricao, valor_item) VALUES($last_id,'{$array[0]}','{$array[1]}','{$array[2]}')";
            if($this->conn->query($sql) === TRUE) {
                $c++;
            }else{
                return "Error {$this->conn->error}";
                die();
            }
        }
        $this->conn->close();//fecha conexao com db.
        return $c;
    }

    /**
     * Função para retornar o maior id de nota fiscal.
     * @return $max Retorna nulll ou numero.
     */
    public function getMaxNumberNota(){
        $sql = "SELECT MAX(id_nota) AS numMax from nota_fiscal";
        $exe = $this->conn->query($sql);
        $result = $exe->fetch_assoc();
        $this->conn->close();//fecha conexao com db.
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


