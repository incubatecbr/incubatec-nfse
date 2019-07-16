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
        $data_r = date('Y-m-d');
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
        $dt_emissao = Util::replaceString($data["dt_emissao"], 8, 'N');
        //sql
        $sql = "INSERT INTO nota_fiscal(num_nota, cpf_cnpj_cliente, razao_social, inscricao_estadual, cod_consumidor_cliente, data_emissao_nf, ano_mes_apuracao, modelo, cfop, situacao_doc, telefone_cliente, ind_tipo_cpf_cnpj, tipo_cliente, telefone_empresa, cnpj_emitente, valor_total_nf, data_remessa) VALUES('{$numNota}','{$data_c["cpf_cnpj"]}', '{$data_c["razao_social"]}', '{$data_c["inscricao_estadual"]}', '{$data_c["id"]}', '{$data["dt_emissao"]}', '{$data["ano_mes_apu"]}', '{$data["modelo"]}', '{$data["cfop"]}', '{$data["situacao_doc"]}', '{$telC}', $ind_tipo_cpf_cnpj, '{$data["tipo_cliente"]}', '{$telE}', '{$cnpj_e}','{$vlrTotalNF}', '{$data_r}')";
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
                $des = mb_strtoupper($array[$i][1]); 
                $sql = "INSERT INTO item_nota(id_nota, cod_item, descricao, valor_item) VALUES($last_id,'{$array[$i][0]}','{$des}','{$array[$i][2]}')";
                if($this->conn->query($sql) === TRUE) {
                    $c++;
                }else{
                    return "Error {$this->conn->error}";
                    die();
                }
            }
        }else{
            $des = mb_strtoupper($array[1]); 
            $sql = "INSERT INTO item_nota(id_nota, cod_item, descricao, valor_item) VALUES($last_id,'{$array[0]}','{$des}','{$array[2]}')";
            if($this->conn->query($sql) === TRUE) {
                $c++;
            }else{
                return "Error {$this->conn->error}";
                die();
            }
        }
        //$this->conn->close();//fecha conexao com db.
        return $c;
    }

    /**
     * Função para retornar o maior id de nota fiscal.
     * @return $max Retorna nulll ou numero.
     */
    public function getMaxNumberNota(){
        $sql = "SELECT MAX(num_nota) AS numMax from nota_fiscal";
        $exe = $this->conn->query($sql);
        $result = $exe->fetch_assoc();
        //$this->conn->close();//fecha conexao com db.
        $max = ( !isset($result) ) ? null : $result['numMax'];
        return $max;
    }

    /**
     * Função responsavel instanciar a class empresa e retornar dados da mesma
     * @return Array
     */
    public function _dataEnterprise(){
        $emp = new Empresa();
        return $emp->getCompanyData();
    }
   

    /**
     * Função responsavel por instanciar a classe de cliente e utilizar o methodo para buscar todos os dados de um determinado cliente. 
     * @param $id do usuário.
     * @return Array 
     */
    public function _dataCliente($id){
        $cliente = new Cliente();
        return $cliente->getAllDataCliente($id);
    }

    /**
    * Funções para CRIAR, ABRIR os arquivos para validação.
    * -----------------------------------------------------
    */   

    
    /**
     * Função para criar o arquivo de Identificação.ini 
     * @return void
     */
    public function createIdenficacao(){
        global $APP_PATH; //para acessar a variavel global
        $enterprise = $this->_dataEnterprise();
        $filepath = $APP_PATH['remessa'].'Identificacao.ini';
        $file = fopen( $filepath, "w+") or die("Unable to open file!");
        $txt = "[IDENTIFICACAO]\nRAZAO SOCIAL={$enterprise['razao_social']}\nIE={$enterprise['inscricao_estadual']}\nCNPJ={$enterprise['cnpj']}\n[ENDERECO]\nENDERECO={$enterprise['endereco']} {$enterprise['numero']}\nBAIRRO={$enterprise['bairro']}\nMUNICIPIO={$enterprise['municipio']}\nCEP={$enterprise['cep']}\nUF={$enterprise['uf']}\n[RESPONSAVEL]\nNOME={$enterprise['nome_responsavel']}\nCARGO={$enterprise['cargo_responsavel']}\nTELEFONE={$enterprise['tel_responsavel']}\nEMAIL={$enterprise['email_responsavel']}";
        fwrite($file, $txt);
        fclose($file);
    }


    public function createFileName($uf, $cnpj, $modelo, $serie, $anoMes, $status, $tipo){
        global $APP_PATH; //para acessar a variavel global
        $filepath = $APP_PATH['remessa']."$uf$cnpj$modelo$serie$anoMes$status$tipo.001";
        $file = fopen( $filepath, "w+") or die("Não foi possivel abrir!");
        if(!$file)://verificação de segurança caso não tenha conseguido criar arquivo..
            echo "<script>alert('NAO FOI POSSIVEL CRIAR ARQUIVO -> {$tipo}')</script>";
            die();
        endif;
        fclose($file);
        return $filepath;
    }

    public function createFileDestinatario($notas, $file){
        $file = fopen($file, "w+") or die("Não foi possivel abrir!");
        if(!$file)://debug..
            echo "<script>alert('NAO FOI POSSIVEL CRIAR ARQUIVO -> {$tipo}')</script>";
            die();
        endif;
        
        for ($i=0; $i < count($notas); $i++) { 
            $cliente = $this->_dataCliente($notas[$i]['cod_consumidor_cliente']);  
            $cpf_ = Util::str_pad_unicode($cliente['cpf_cnpj'], 14, '0', STR_PAD_LEFT);
            $ie_ = Util::str_pad_unicode($cliente['inscricao_estadual'], 14);
            $rzs_ = Util::str_pad_unicode($cliente['razao_social'], 35);
            $log_ = Util::str_pad_unicode($cliente['logradouro'], 45);
            $num_ = Util::str_pad_unicode($cliente['numero'], 5, '0', STR_PAD_LEFT);
            $comp_ = Util::str_pad_unicode($cliente['complemento'], 15);
            $bai_ = Util::str_pad_unicode($cliente['bairro'], 15);
            $tel_ = Util::str_pad_unicode($cliente['telefone'], 12);
            $mun_ = Util::str_pad_unicode($cliente['municipio'], 30);
            //linha do arquivo ( CPF_CNPJ, INSC_ESTADUAL, RAZAO_SOCIAL, LOGRADOURO, NUMERO, COMPLEMENTO, CEP, BAIRRO, MUNICIPIO, UF, COD_CLIENTE, NUM_TERMINAL, UF_HABILITAÇAO, DT_EMISSAO, MODELO, SERIE, NUMERO_NOTA, COD_MUNI, BANCOS, COD_AUTENTICAÇÃO).            
            $row = $cpf_.$ie_.$rzs_.$log_.$n_.$comp_.$cliente['cep'].$bai_.$mun_.$cliente['uf'].$tel_.$cliente['id'].'              '.$notas[$i]['data_emissao_nf'].$notas[$i]['modelo'].$notas[$i]['serie'].$notas[$i]['num_nota'].$cliente['cod_muni_ibge'].'     ';
            $cod_auth_dig = Util::generateMd5($row);
            $rowCode = $row.$cod_auth_dig;
            fwrite($file, "{$rowCode}\n");//escreve a linha no arquivo
        }
        fclose($file);//fecha o arquivo.
    }

    public function generateRemessa(){

        $begin = $_POST['data']['ano'].'-'.$_POST['data']['mes'].'-01';
        $end = $_POST['data']['ano'].'-'.$_POST['data']['mes'].'-31';
        $modelo = $_POST['data']['modelo'];
        $notas = $this->getNotaByDateWithModel($begin, $end, $modelo);
        if(!$notas):
           return false;
        endif;
        // echo "<pre>";
        // print_r($notas);
        // echo "</pre>";die();
        //cliente = $this->_dataCliente($notas[1]['cod_consumidor_cliente']);  
        //echo mb_strlen($cliente['razao_social']);
        //die();
        $emitente = $this->_dataEnterprise();
        $cnpj = Util::replaceString($emitente['cnpj']);
        
        //pega os dados da primeira nota para criar o arquivo com seu respectivo nome obedecendo as normas ICMS.
        $file = $this->createFileName($emitente['uf'], $cnpj, $notas[0]['modelo'], $notas[0]['serie'], $notas[0]['ano_mes_apuracao'], $notas[0]['situacao_doc'], 'D'  );
        $D = $this->createFileDestinatario($notas, $file);
        
        

            
        
                    
    }


    /**
     * Função para retornar as notas com base na data e no modelo.
     * @param Date $dtBegin data inicial
     * @param Date $dtEnd data final
     * @return Array com as notas.
     */
    public function getNotaByDateWithModel($dtBegin, $dtEnd, $modelo){
        $sql = "SELECT * FROM nota_fiscal WHERE data_remessa BETWEEN '{$dtBegin}' AND '{$dtEnd}' AND modelo = {$modelo}";
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
            $notas = mysqli_fetch_all ($result, MYSQLI_ASSOC);
            return $notas;
        }else{
            return false;
        }
        
    }

   




 
}


