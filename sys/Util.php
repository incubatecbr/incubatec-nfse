<?php

class Util
{

    /**
     * Função para somar os valores com base no indice do array
     * @param Array $array
     * @param Int $indice
     * @return String $sum Somatoria dos valores formatado com decimal ( 1000 = 10,00 ).
     */
    public static function sumValuesArray($array, $indice){
        for ($i = 0; $i < count($array); $i++) {
            if (is_array($array[$i])):
                $rows = true;
            endif;
        }
        if (isset($rows) || !empty($rows)) {
            $sum = 0;
            for ($i = 0; $i < count($array); $i++) {
                $sb = str_replace(',', '.', $array[$i][$indice]); //remove virgula e ponto para realizar a soma dos valores.
                $sum += $sb;
            }
        } else {
            $sum = str_replace(',', '.', $array[0][$indice]);
        }
        return number_format($sum, 2, '', ''); //number format ex: 500 (5,00).
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
            $newArray[$array[$i]['name']] = Util::replaceArray($array[$i]['value']);
        }
        return $newArray;
    }

    /**
     * Função para remover ponto, virgula barra, parenteses e traço.
     * @param String $string 
     * @return String $r Nova string
     */
    public static function replaceArray($string){
        $remove = array(".", ",", "/", "(", ")", "-");
        $r = str_replace($remove, "", $string);
        return $r;
    }

    
}
