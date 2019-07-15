<?php

class Util
{


    public function generateMd5($row){
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
     * Função para retornar o proximo numero de NF.
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
     * @return String $r Nova string
     */
    public static function replaceString($string){
        $remove = array(".", ",", "/", "(", ")", "-");
        $r = str_replace($remove, "", $string);
        return $r;
    }

    
}
