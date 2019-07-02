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
                'cnpj' => $row["cnpj_emp"],
                'razao_social' => $row["razao_social"],
                'inscricao_estadual' => $row["inscricao_estadual"],
                'endereco' => $row["endereco"],
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
 
        //$cpnj = $_POST['data'][2];
        $email = $_POST['data'][10];
        //array_splice($_POST['data'], 2, 1);//Remove cpnj do array.
        $data = $this->formatDataArray($_POST['data']);
        $sql = "INSERT INTO empresa VALUES({$data['cnpj']}, {$data['razao_social']}, {$data['insc_estadual']}, {$data['endereco']}, {$data['bairro']}, {$data['municipio']}, {$data['cep']}, {$data['uf']}, {$data['nome_responsavel']}, {$email['value']}, {$data['cargo_responsavel']} )";
        //$sql = "INSERT INTO `empresa` VALUES ({$cpnj['value']})";
        if($this->conn->query($sql) === TRUE) {
            return true;
        }else{
            //echo "Error: " . $sql . "<br>" . $conn->error;
            return "Error {$this->conn->error}";
        }
        $this->conn->close();//fecha conexao com db.
    }
}
