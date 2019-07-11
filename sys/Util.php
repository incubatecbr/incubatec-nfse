<?php

class Util
{

    public static function sumValuesItens($service){
        $services = 0;
        for ($i = 0; $i < count($service); $i++){
            if (is_array($service[$i])): //verifica se o indice contem um array.
                $services++; //contagem de quantos itens.
            endif;
        }
        if(isset($services) && $services != 0){
            //return $services;
            $val = Util::sumValues($service);
            return $val;
        }else{
            //return $service[2];
            return str_replace (',', '', $service[2]);
            // return number_format($service[2], 2, '', '');
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

    public static function sumValues($array){
        $sum = 0;
        for ($i = 0; $i < count($array); $i++) {
            $sb = str_replace(',', '.', $array[$i][2]); //remove virgula e ponto para realizar a soma dos valores.
            $sum += $sb;
        }
        return number_format($sum, 2, '', '');
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
