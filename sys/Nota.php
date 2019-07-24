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
        $dt_emissao = Util::replaceString($data["dt_emissao"]);
        //sql
        $sql = "INSERT INTO nota_fiscal(num_nota, cpf_cnpj_cliente, razao_social, inscricao_estadual, cod_consumidor_cliente, data_emissao_nf, ano_mes_apuracao, modelo, fase_utilizacao, cfop, situacao_doc, telefone_cliente, ind_tipo_cpf_cnpj, tipo_cliente, telefone_empresa, cnpj_emitente, valor_total_nf, data_remessa) VALUES('{$numNota}','{$data_c["cpf_cnpj"]}', '{$data_c["razao_social"]}', '{$data_c["inscricao_estadual"]}', '{$data_c["id"]}', '{$data["dt_emissao"]}', '{$data["ano_mes_apu"]}', '{$data["modelo"]}', '{$data["tipo_utilizacao"]}','{$data["cfop"]}', '{$data["situacao_doc"]}', '{$telC}', $ind_tipo_cpf_cnpj, '{$data["tipo_cliente"]}', '{$telE}', '{$cnpj_e}','{$vlrTotalNF}', '{$data_r}')";
        if($this->conn->query($sql) === TRUE) {
            $last_id = $this->conn->insert_id;
            $c_services = $this->newItemService($services, $last_id);//INSERIR ITENS DA NOTA
            $ret = "Nota fiscal finalizada contendo {$c_services} serviço";
        }else{
            $ret = "Error {$this->conn->error}";
        }
        $this->conn->close();//fecha conexao com db.
        return $ret;
    }
    


    /**
     * Função responsavel por inserir os dados contidos no array.
     * @param Array $array contendo os itens
     * @param Int $last_id do ultimo id de nota inserida. 
     */
    public function newItemService($array, $last_id){
        $c = 0;
        if(is_array($array[0])){
            for ($i=0; $i < count($array); $i++) {
                $des = mb_strtoupper($array[$i][1]); 
                $sql = "INSERT INTO item_nota(id_nota, cod_item, descricao, valor_item, qnt_faturada) VALUES($last_id,'{$array[$i][0]}','{$des}','{$array[$i][2]}', '{$array[$i][4]}')";
                if($this->conn->query($sql) === TRUE) {
                    $c++;
                }else{
                    return "Error {$this->conn->error}";
                    die();
                }
            }
        }else{
            $des = mb_strtoupper($array[1]); 
            $sql = "INSERT INTO item_nota(id_nota, cod_item, descricao, valor_item, qnt_faturada) VALUES($last_id,'{$array[0]}','{$des}','{$array[2]}', '{$array[4]}' )";
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
    * Funções para CRIAR E INSERIR AS LINHAS nos arquivos M, D e I.
    * -----------------------------------------------------
    */   

    
    /**
     * Função para criar o arquivo de Identificação.ini 
     * @return void
     */
    public function createIdenficacao(){
        global $APP_PATH; //para acessar a variavel global
        $enterprise = $this->_dataEnterprise();
        $filepath = $APP_PATH['remessa'].'Identificacao.ini';//caminho + nome do arquivo.
        $file = fopen( $filepath, "w+") or die("Erro ao abrir!");
        $txt = "[IDENTIFICACAO]\nRAZAO SOCIAL={$enterprise['razao_social']}\nIE={$enterprise['inscricao_estadual']}\nCNPJ={$enterprise['cnpj']}\n[ENDERECO]\nENDERECO={$enterprise['endereco']} {$enterprise['numero']}\nBAIRRO={$enterprise['bairro']}\nMUNICIPIO={$enterprise['municipio']}\nCEP={$enterprise['cep']}\nUF={$enterprise['uf']}\n[RESPONSAVEL]\nNOME={$enterprise['nome_responsavel']}\nCARGO={$enterprise['cargo_responsavel']}\nTELEFONE={$enterprise['tel_responsavel']}\nEMAIL={$enterprise['email_responsavel']}";
        fwrite($file, $txt);
        fclose($file);
    }

    /**
     * Função para criar o nome do documento fiscal segundo o convênio ICM 115/03.
     * @param String $uf 
     * @param String $cnpj
     * @param String $modelo
     * @param String $serie
     * @param String $anoMes
     * @param String $status
     * @param String $tipo
     * @return $filepath caminho completo do arquivo.
     */
    public function createFileName($uf, $cnpj, $modelo, $serie, $anoMes, $status, $tipo){
        global $APP_PATH; //para acessar a variavel global
        $filepath = $APP_PATH['remessa']."$uf$cnpj$modelo$serie$anoMes$status"."01".$tipo.".001";//nome do arquivo como descrito no convenio 115/03.
        $file = fopen( $filepath, "w+") or die("Não foi possivel abrir!");
        if(!$file)://verificação de segurança caso não tenha conseguido criar arquivo..
            echo "<script>alert('NAO FOI POSSIVEL CRIAR ARQUIVO -> {$tipo}')</script>";
            die();
        endif;
        fclose($file);
        return $filepath;
    }

    /**
     * Função responsavel por gerar a remessa dos arquivos para validação.
     * @return void
     */
    public function generateRemessa(){
        Util::cleaningFolderRemessa();//Limpa pasta de remessa
        sleep(2);//aguardar 2 segundos para garantir que os arquivos foram apagados.
        $begin = $_POST['data']['ano'].'-'.$_POST['data']['mes'].'-01';
        $end = $_POST['data']['ano'].'-'.$_POST['data']['mes'].'-31';
        $modelo = $_POST['data']['modelo'];
        $notas = $this->getNotaByDateWithModel($begin, $end, $modelo);
        if(!$notas):
           return false;
        endif;
        $iden = $this->createIdenficacao();
        $emitente = $this->_dataEnterprise();
        $cnpj = Util::replaceString($emitente['cnpj']);
        $f = ['D', 'M', 'I'];
        $files = [];
        //foreach para criar a remessa.
        foreach($f as $k => $v){
            $files[$k] = $this->createFileName($emitente['uf'], $cnpj, $notas[0]['modelo'], $notas[0]['serie'], $notas[0]['ano_mes_apuracao'], $notas[0]['situacao_doc'], $v  );
        }
        
        $doc_fiscal_destinatario = $this->createFileDestinatario($notas, $files[0]);//0 = Destinatario; 1 = Mestre; 2 = Item;
        $doc_fiscal_mestre = $this->createFileMestre($notas, $files[1],  $emitente);
        $doc_fiscal_item = $this->createFileItem($notas, $files[2]);
        if( ($doc_fiscal_destinatario == true) AND ($doc_fiscal_mestre == TRUE) AND ($doc_fiscal_item == TRUE) ){
            $zip = Util::testeZip();
            
            if(file_exists($zip)){
                header('Content-type: application/zip');
                header('Content-Disposition: attachment; filename="'.$zip.'"');
                header('Content-Length: '.filesize($zip));
                header('Pragma: no-cache'); 
                return $zip;//retorna arquivo zip forçando donwload pelo navegador.
            }else{
                return 'Nao foi possivel criar arquivo zip';
            }
        }else{
            return "error[ {$doc_fiscal_destinatario}, {$doc_fiscal_mestre}, {$doc_fiscal_item}]";
        }
        
    }


    public function createFileMestre($notas, $filepath, $emitente){
        $fileOpen = fopen($filepath, "w+") or die("Não foi possivel abrir!");
        if(!$fileOpen)://debug..
            echo "<script>alert('NAO FOI POSSIVEL CRIAR ARQUIVO -> {$tipo}')</script>";
            die();
        endif;
        if(!$emitente)://debug..
            echo "<script>alert('NAO EXISTE EMITENTE')</script>";
            die();
        endif;
        
        $cnpj_e = Util::replaceString($emitente['cnpj']);
        for ($i=0; $i < count($notas); $i++) { 
            $cliente = $this->_dataCliente($notas[$i]['cod_consumidor_cliente']);
            $cpf_cnpj = Util::str_pad_unicode($cliente['cpf_cnpj'], 14, '0', STR_PAD_LEFT);
            if($cliente['inscricao_estadual'] == 'ISENTO'){
                $ie = Util::str_pad_unicode($cliente['inscricao_estadual'], 14);
            }else{
                $ie = Util::str_pad_unicode($cliente['inscricao_estadual'], 14, '0',  STR_PAD_LEFT);
            }
            $rzs = Util::str_pad_unicode($cliente['razao_social'], 35);
            $clas_con = '0';
            $grp_ten = '00';
            $cod_id_consu = Util::str_pad_unicode($cliente['id'], 12);
            $tel = Util::str_pad_unicode($notas[$i]['telefone_cliente'], 12);
            //---
            $vlr_total_nf = Util::str_pad_unicode($notas[$i]['valor_total_nf'], 12, '0', STR_PAD_LEFT);//valor total da nota ( campo 14).
            $bc_icm = $vlr_total_nf;//BC ICMS( campo 15).
            $icms = '000000000000';//ICMS destacado (campo 16).
            //---
            $op_i = '000000000000';
            $outros_v = '000000000000';
            $id = intval($notas[$i]['num_nota']);
            $ref_item = $this->getCountItensById($id);
            $ref_item = Util::str_pad_unicode($ref_item, 9, '0', STR_PAD_LEFT);
            //$num_ter_cons = '000000000000';
            $num_ter_cons = $tel;
            $sub_c = '00';
            //$num_ter_prin = '000000000000';
            $num_ter_prin = $tel;
            $num_cod_fat = '                    ';
            $dt_leit_ant = '00000000';
            $dt_leit_atu = '00000000';
            $banco32 = '                                                  ';//50
            $banco33 = '00000000';//8
            $inf_add = '                              ';
            $banco35 = '     ';
            //---hash 13
            $cod_auth_dig13 = $cpf_cnpj.$notas[$i]['num_nota'].$vlr_total_nf.$bc_icm.$icms.$notas[$i]['data_emissao_nf'].$cnpj_e;
            $cod_auth_dig13 = Util::generateMd5($cod_auth_dig13);
            //---hash 36
            $cod_auth_dig36 = $cpf_cnpj.$ie.$rzs.$cliente['uf'].$clas_con.$notas[$i]['fase_utilizacao'].$grp_ten.$cod_id_consu.$notas[$i]['data_emissao_nf'].$notas[$i]['modelo'].$notas[$i]['serie'].$notas[$i]['num_nota'].$cod_auth_dig13.$vlr_total_nf.$bc_icm.$icms.$op_i.$outros_v.$notas[$i]['situacao_doc'].$notas[$i]['ano_mes_apuracao'].$ref_item.$num_ter_cons.$notas[$i]['ind_tipo_cpf_cnpj'].$notas[$i]['tipo_cliente'].$sub_c.$num_ter_prin.$cnpj_e.$num_cod_fat.$vlr_total_nf.$dt_leit_ant.$dt_leit_atu.$banco32.$banco33.$inf_add.$banco35;
            $cod_auth_dig36 = Util::generateMd5($cod_auth_dig36);
            //----row
            $row = $cpf_cnpj.$ie.$rzs.$cliente['uf'].$clas_con.$notas[$i]['fase_utilizacao'].$grp_ten.$cod_id_consu.$notas[$i]['data_emissao_nf'].$notas[$i]['modelo'].$notas[$i]['serie'].$notas[$i]['num_nota'].$cod_auth_dig13.$vlr_total_nf.$bc_icm.$icms.$op_i.$outros_v.$notas[$i]['situacao_doc'].$notas[$i]['ano_mes_apuracao'].$ref_item.$num_ter_cons.$notas[$i]['ind_tipo_cpf_cnpj'].$notas[$i]['tipo_cliente'].$sub_c.$num_ter_prin.$cnpj_e.$num_cod_fat.$vlr_total_nf.$dt_leit_ant.$dt_leit_atu.$banco32.$banco33.$inf_add.$banco35.$cod_auth_dig36;
            //escreve no arquivo.
            $nRow = mb_convert_encoding($row, "ISO-8859-1", "UTF-8");//linha convertida para ISO-8859-1
            fwrite($fileOpen, $nRow.PHP_EOL);
        }
        fclose($fileOpen);
        return true;
    }

    /**
     * Função para criar o item de documento fiscal.
     *
     * @param Array $notas Contendo todas as notas buscadas no db.
     * @param String $file pathname do arquivo
     * @return void
     */
    public function createFileItem($notas, $filepath){
        $fileOpen = fopen($filepath, "w+") or die("Não foi possivel abrir!");
        if(!$fileOpen)://debug..
            echo "<script>alert('NAO FOI POSSIVEL CRIAR ARQUIVO -> {$tipo}')</script>";
            die();
        endif;

        for ($i=0; $i < count($notas); $i++) { 
            $cliente = $this->_dataCliente($notas[$i]['cod_consumidor_cliente']);
            $cpf_cnpj = Util::str_pad_unicode($cliente['cpf_cnpj'], 14, '0', STR_PAD_LEFT);
            //$ie = Util::str_pad_unicode($cliente['inscricao_estadual'], 14);
            $rzs = Util::str_pad_unicode($cliente['razao_social'], 35);
            $clas_con = '0';//classe de consumo
            $grp_ten = '00';//grupo de tensão
            $cod_id_consu = Util::str_pad_unicode($cliente['id'], 12);//código de identificação do consumidor ou assinate
            $id = intval($notas[$i]['num_nota']);
            $itens = $this->getItensById($id);
        
            foreach ($itens as $key => $val) {
                $num_ordem_item = Util::str_pad_unicode( ($key+1) , 3, '0', STR_PAD_LEFT);//numero ordem do item
                $cod_item = Util::str_pad_unicode($val['cod_item'], 10);//codigo do item formatado com 10 caracteres
                $des_item = Util::str_pad_unicode($val['descricao'], 40);//descrição do item formatada 40 caracteres
                $unidade = Util::str_pad_unicode($val['tipo_uni_med'], 6);//tipo de medida do item
                $valor_total_item = Util::str_pad_unicode($val['valor_item'], 11, '0', STR_PAD_LEFT);//valor total do item
                $qntContr = '000000000000';//quantidade contratada
                $qntMedi = '000000000000';//quantidade medida 
                $desc = '00000000000';//descontos
                $acres = '00000000000';//acrescimos
                $bc_icms = $valor_total_item;//base de calculo de ICMS.
                $icms = '00000000000';
                $op_i = '00000000000';//operações isentas
                $outros_v = '00000000000';//outros valores
                $aliq = '0000';//aliquota
                $num_contr = '               ';//numero do contrato
                //$qntFatu = '000000001000';
                $qntFatu = Util::str_pad_unicode($val['qnt_faturada'], 12, '0', STR_PAD_LEFT);//quantidade fatura
                $tarifa = '00000000000';//tarifa aplicada
                $ali_pis = '000000';//aliquota PIS/PASEP
                $pis_pas = '00000000000';//PIS/PASEP
                $aliq_c = '000000';//aliquota cofins
                $cofins = '00000000000';//cofins
                $row = $cpf_cnpj.$cliente['uf'].$clas_con.$notas[$i]['fase_utilizacao'].$grp_ten.$notas[$i]['data_emissao_nf'].$notas[$i]['modelo'].$notas[$i]['serie'].$notas[$i]['num_nota'].$notas[$i]['cfop'].$num_ordem_item.$cod_item.$des_item.$notas[$i]['cfop'].$unidade.$qntContr.$qntMedi.$valor_total_item.$desc.$acres.$bc_icms.$icms.$op_i.$outros_v.$aliq.$notas[$i]['situacao_doc'].$notas[$i]['ano_mes_apuracao'].$num_contr.$qntFatu.$tarifa.$ali_pis.$pis_pas.$aliq_c.$cofins.' 00     ';
                $cod_auth_dig = Util::generateMd5($row);//cod de autenticação digital do registro (CAMPO 38).
                $rowCode = $row.$cod_auth_dig;//Linha + cod de autenticação digital.
                $nRow = mb_convert_encoding($rowCode, "ISO-8859-1", "UTF-8");//linha convertida para ISO-8859-1
                fwrite($fileOpen, $nRow.PHP_EOL);
            }
            
        }
        fclose($fileOpen);
        return true;
    }

    /**
     * Função para criar o documento fiscal dados do destinatario.
     * @param Array $notas Contendo todas as notas buscadas no db.
     * @param String $file pathname do arquivo
     * @return void
     */
    public function createFileDestinatario($notas, $filepath){
        $fileOpen = fopen($filepath, "w+") or die("Não foi possivel abrir!");
        if(!$fileOpen)://debug..
            echo "<script>alert('NAO FOI POSSIVEL CRIAR ARQUIVO -> {$tipo}')</script>";
            die();
        endif;
        for ($i=0; $i < count($notas); $i++) { 
            $cliente = $this->_dataCliente($notas[$i]['cod_consumidor_cliente']);  
            $cpf_ = Util::str_pad_unicode($cliente['cpf_cnpj'], 14, '0', STR_PAD_LEFT);
            if($cliente['inscricao_estadual'] == 'ISENTO'){
                $ie_ = Util::str_pad_unicode($cliente['inscricao_estadual'], 14);
            }else{
                $ie_ = Util::str_pad_unicode($cliente['inscricao_estadual'], 14, '0',  STR_PAD_LEFT);
            }
            $rzs_ = Util::str_pad_unicode($cliente['razao_social'], 35);
            $log_ = Util::str_pad_unicode($cliente['logradouro'], 45);
            $num_ = Util::str_pad_unicode($cliente['numero'], 5, '0', STR_PAD_LEFT);
            $comp_ = Util::str_pad_unicode($cliente['complemento'], 15);
            $bai_ = Util::str_pad_unicode($cliente['bairro'], 15);
            $tel_ = Util::str_pad_unicode($cliente['telefone'], 12);
            $mun_ = Util::str_pad_unicode($cliente['municipio'], 30);
            $id_c = Util::str_pad_unicode($cliente['id'], 12);
            //linha do arquivo ( CPF_CNPJ, INSC_ESTADUAL, RAZAO_SOCIAL, LOGRADOURO, NUMERO, COMPLEMENTO, CEP, BAIRRO, MUNICIPIO, UF, COD_CLIENTE, NUM_TERMINAL, UF_HABILITAÇAO, DT_EMISSAO, MODELO, SERIE, NUMERO_NOTA, COD_MUNI, BANCOS, COD_AUTENTICAÇÃO).            
            $row = $cpf_.$ie_.$rzs_.$log_.$num_.$comp_.$cliente['cep'].$bai_.$mun_.$cliente['uf'].$tel_.$id_c.$tel_.$cliente['uf'].$notas[$i]['data_emissao_nf'].$notas[$i]['modelo'].$notas[$i]['serie'].$notas[$i]['num_nota'].$cliente['cod_muni_ibge'].'     ';
            $cod_auth_dig = Util::generateMd5($row);
            $rowCode = $row.$cod_auth_dig;
            $nRow = mb_convert_encoding($rowCode, "ISO-8859-1", "UTF-8");//linha convertida para ISO-8859-1
            fwrite($fileOpen, $nRow.PHP_EOL);//escreve a linha no arquivo
        }
        fclose($fileOpen);//fecha o arquivo.
        return true;
    }

    /**
     * Função responsavel retornar a quantidade de itens de cada nota.
     * @param Int $id_nota
     * @return Int $itens['qt'] 
     */
    public function getCountItensById($id_nota){
        if(!$id_nota)://debug.
            print_r("ID vazio.");
            die();
        endif;
        $sql = "SELECT COUNT(*) as qt FROM item_nota WHERE id_nota = {$id_nota};";
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
            $itens = $result->fetch_assoc();
            return $itens['qt'];
        }else{
            return false;
        }
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

    /**
     * Função responsavel por trazer todos itens com base no id da nota fiscal.
     * @param Int $id_nota
     * @return Array $itens
     */
    public function getItensById($id_nota){
        if(!$id_nota)://debug.
            print_r("ID vazio.");
            die();
        endif;
        $sql = "SELECT cod_item, descricao, valor_item, qnt_faturada, tipo_uni_med FROM item_nota WHERE id_nota = {$id_nota}";
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
            $itens = mysqli_fetch_all ($result, MYSQLI_ASSOC);
            return $itens;
        }else{
            return false;
        }

    }
 
}


