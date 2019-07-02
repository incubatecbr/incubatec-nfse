<?php

class Empresa extends IncubaMain{
    
    /**
     * Construtor para instanciar a classe banco de dados
     */
    public function __construct(){
        $db = new Database();
        $this->conn = $db->_connect();
    }

    /**
     * Função para retornar os dados da empresa caso exista no db.
     * @return $company = dados da empresa OU false.
     */
    public function getCompanyData(){
        $sql = "SELECT * FROM empresa";
        $res = $this->conn->query($sql);
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $company = array(
                'id' => $row['id'],
                'cnpj' => $row["cnpj_emp"],
                'razao_social' => $row["razao_social"],
                'inscricao_estadual' => $row["inscricao_estadual"],
                'endereco' => $row["endereco"],
                'numero' => $row["numero"],
                'bairro' => $row["bairro"],
                'municipio' => $row["municipio"],
                'cep' => $row["cep"],
                'uf' => $row["uf"],
                'nome_responsavel' => $row["nome_responsavel"],
                'email_responsavel' => $row["email_responsavel"],
                'cargo_responsavel' => $row["cargo_responsavel"],
                'tel_responsavel' => $row["tel_responsavel"],
            );
            return $company; 
        }else{
            return false;
        }
        $this->conn->close();//fecha conexao com db.

    }

    /**
     * Função para salvar os dados da empresa.
     * @return boolean
     */
    public function saveCompany(){
        $cpnj = $_POST['data'][3];//Captura CPF com pontuação.
        $cep = $_POST['data'][8];//CEP com pontuação.
        $email = $_POST['data'][11];//E-mail
        $tel = $_POST['data'][13];//Telefone.
        $data = $this->formatDataArray($_POST['data']);
        $sql = "INSERT INTO empresa(cnpj_emp, razao_social, inscricao_estadual, endereco, numero, bairro, municipio, cep, uf, nome_responsavel, email_responsavel, cargo_responsavel, tel_responsavel) VALUES('{$cpnj["value"]}', '{$data['razao_social']}', {$data['insc_estadual']}, '{$data['endereco']}', '{$data['numero']}', '{$data['bairro']}', '{$data['municipio']}', '{$cep["value"]}', '{$data['uf']}', '{$data['nome_responsavel']}', '{$email["value"]}', '{$data['cargo_responsavel']}', '{$tel["value"]}')";
        if($this->conn->query($sql) === TRUE) {
            return true;
        }else{
            return "Error {$this->conn->error}";
        }
        $this->conn->close();//fecha conexao com db.
    }

    /**
     * Função para atualizar os dados da empresa.
     * @return boolean
     */
    public function updateCompany(){
        $cpnj = $_POST['data'][3];//Captura CPF com pontuação.
        $cep = $_POST['data'][8];//CEP com pontuação.
        $email = $_POST['data'][11];//E-mail
        $tel = $_POST['data'][13];//Telefone.
        $data = $this->formatDataArray($_POST['data']);
        $sql = "UPDATE empresa SET cnpj_emp = '{$cpnj['value']}', razao_social = '{$data['razao_social']}', inscricao_estadual = '{$data['insc_estadual']}', endereco = '{$data['endereco']}', numero = '{$data['numero']}', bairro = '{$data['bairro']}', municipio = '{$data['municipio']}', cep = '{$cep["value"]}', uf = '{$data['uf']}', nome_responsavel = '{$data['nome_responsavel']}', email_responsavel = '{$email["value"]}', cargo_responsavel = '{$data['cargo_responsavel']}', tel_responsavel = '{$tel["value"]}' WHERE id = {$data['id']} ";

        if($this->conn->query($sql) === TRUE) {
            return true;
        }else{
            return "Error {$this->conn->error}";
        }
        $this->conn->close();//fecha conexao com db.
    }
}


