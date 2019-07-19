<?php

class Util
{
    /**
     * Função para limpar a pasta de _remessa.
     */
    public static function cleaningFolderRemessa(){
        $folder = '_remessa';
        $files = glob($folder . '/*');
        foreach($files as $file){
            if(is_file($file)){
                unlink($file);
            }
        }
    }
    public static function testeZip(){
        global $APP_PATH;
        // Normaliza o caminho do diretório a ser compactado
        //$source_path = "_remessa/";
        $source_path = realpath("_remessa/");
        // Caminho com nome completo do arquivo compactado
        // Nesse exemplo, será criado no mesmo diretório de onde está executando o script
        $d = date('Ymd');
        $zip_file = $APP_PATH['remessa']."NFSe-{$d}.zip";
        // Inicializa o objeto ZipArchive
        $zip = new ZipArchive();
        $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($APP_PATH['remessa']),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($files as $name => $file) {
            // Pula os diretórios. O motivo é que serão inclusos automaticamente
            if (!$file->isDir()) {
            // Obtém o caminho normalizado da iteração corrente
            $file_path = $file->getRealPath();
            // Obtém o caminho relativo do mesmo.
            $relative_path = substr($file_path, strlen($source_path) + 1);
            // Adiciona-o ao objeto para compressão
            $zip->addFile($file_path, $relative_path);
            }
        }
        //Fecha o objeto. Necessário para gerar o arquivo zip final.
        $zip->close();
        //Retorna o caminho completo do arquivo gerado
        return $zip_file;
    }


    /**
     * Função para encryptar hash MD5
     * @param String $row
     * @return String MD5
     */
    public static function generateMd5($row){
        $hashConvert = mb_convert_encoding($row, "ISO-8859-1", "UTF-8");
        return md5($hashConvert);
    }

    /**
     * Função checa se no indice existe um array de dados
     * caso exista, passa array para função de soma.
     * @param Array $service
     * @return $val Somatoria dos valores do array ou retorna o unico valor do array formatado.
     */
    public static function sumValuesItens($service){
        $c_services = 0;
        for ($i = 0; $i < count($service); $i++){
            if (is_array($service[$i])): //verifica se o indice contem um array.
                $c_services++; //contagem de quantos itens.
            endif;
        }
        if(isset($c_services) && $c_services != 0){
            $val = Util::sumValues($service);
            return $val;
        }else{
            //return $service[2];
            return str_replace (',', '', $service[2]);
            //return number_format($service[2], 2, '', '');
        }    
    }
    
    /**
     * Função para retornar o proximo numero incrementado +1.
     * @param Int $num
     * @return String numero formatado com zero a esquerda. 
     */
    public static function nextNumberOfNota($num){
        $num++;//increment +1.
        return sprintf("%'.09d", $num);
    }

    /**
     * Função responsavel por somar os valores contidos no indice [2]
     * @param Array $array
     * @return String Somatoria dos valores Ex: 5000 (50,00).
     */
    public static function sumValues($array){
        $sum = 0;
        for ($i = 0; $i < count($array); $i++) {
            $sb = str_replace(',', '.', $array[$i][2]); //remove virgula e ponto para realizar a soma dos valores.
            $sum += $sb;
        }
        //return number_format($sum, 2, '', '');
        return $sum;
    }


    /**
     * Função para formatar o array contendo os itens de serviço.
     * @param Array $array para formatar.
     * @return Array $newArray formatado.
     */
    public static function formatArrayServices($array){
        $newArray = array();
        for ($i=0; $i < count($array); $i++) { 
            for ($j=0; $j < 6; $j++) { 
                $newArray[$i] = Util::replaceString($array[$i]);
            }
        }
        return $newArray;
    }

    /**
     * Função para reorganizar o array. 
     * $newArray = ["name"] => ["value"];
     * @param Array $array
     * @return Array $newArray
     */
    public static function formatArray($array){
        $newArray = array();
        for ($i = 0; $i < count($array); $i++) {
            $newArray[$array[$i]['name']] = Util::replaceString($array[$i]['value']);
        }
        return $newArray;
    }

    /**
     * Função para remover ponto, virgula barra, parenteses e traço.
     * @param String $string 
     * @return String $upperCase Nova string com uppercase
     */
    public static function replaceString($string){
        $remove = array(".", ",", "/", "(", ")", "-");
        $r = str_replace($remove, "", $string);
        return $r;
    }

    /**
     * Função unicode para adicionar espaço ou 0 em uma string.
     * @param String $str 
     * @param Int $pad_len
     * @param String $pad_str 
     * @param $dir STR_PAD_RIGHT/STR_PAD_LEFT/STR_PAD_BOTH.
     * @return String
     */
    public static function str_pad_unicode($str, $pad_len, $pad_str = ' ', $dir = STR_PAD_RIGHT){
        mb_internal_encoding("utf-8");
        $str_len = mb_strlen($str);
        $pad_str_len = mb_strlen($pad_str);
        if (!$str_len && ($dir == STR_PAD_RIGHT || $dir == STR_PAD_LEFT)) {
            $str_len = 1; // @debug
        }
        if (!$pad_len || !$pad_str_len || $pad_len <= $str_len) {
            return $str;
        }

        $result = null;
        if ($dir == STR_PAD_BOTH) {
            $length = ($pad_len - $str_len) / 2;
            $repeat = ceil($length / $pad_str_len);
            $result = mb_substr(str_repeat($pad_str, $repeat), 0, floor($length))
                    . $str
                    . mb_substr(str_repeat($pad_str, $repeat), 0, ceil($length));
        } else {
            $repeat = ceil($str_len - $pad_str_len + $pad_len);
            if ($dir == STR_PAD_RIGHT) {
                $result = $str . str_repeat($pad_str, $repeat);
                $result = mb_substr($result, 0, $pad_len);
            } else if ($dir == STR_PAD_LEFT) {
                $result = str_repeat($pad_str, $repeat);
                $result = mb_substr($result, 0, 
                            $pad_len - (($str_len - $pad_str_len) + $pad_str_len))
                        . $str;
            }
        }
        return $result;
    }

    
}
