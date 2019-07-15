'use strict';


$(document).ready(function () {
    let content = document.getElementById("content");
    let titlePage = document.getElementById("title-page");
    titlePage.innerHTML = 'Gerador de Arquivos Mestre, Identificação e Destinatario.';
    content.innerHTML = "<h1 class='text-center my-5'>Desenvolvido por Incubatec</h1>";

});

//Constante com nome dos estados e respectiva sigla.
const estadosUF = [
    {"nome": "Acre", "sigla": "AC"},
    {"nome": "Alagoas", "sigla": "AL"},
    {"nome": "Amapá", "sigla": "AP"},
    {"nome": "Amazonas", "sigla": "AM"},
    {"nome": "Bahia", "sigla": "BA"},
    {"nome": "Ceará", "sigla": "CE"},
    {"nome": "Distrito Federal", "sigla": "DF"},
    {"nome": "Espírito Santo", "sigla": "ES"},
    {"nome": "Goiás", "sigla": "GO"},
    {"nome": "Maranhão", "sigla": "MA"},
    {"nome": "Mato Grosso", "sigla": "MT"},
    {"nome": "Mato Grosso do Sul", "sigla": "MS"},
    {"nome": "Minas Gerais", "sigla": "MG"},
    {"nome": "Pará", "sigla": "PA"},
    {"nome": "Paraíba", "sigla": "PB"},
    {"nome": "Paraná", "sigla": "PR"},
    {"nome": "Pernambuco", "sigla": "PE"},
    {"nome": "Piauí", "sigla": "PI"},
    {"nome": "Rio de Janeiro", "sigla": "RJ"},
    {"nome": "Rio Grande do Norte", "sigla": "RN"},
    {"nome": "Rio Grande do Sul", "sigla": "RS"},
    {"nome": "Rondônia", "sigla": "RO"},
    {"nome": "Roraima", "sigla": "RR"},
    {"nome": "Santa Catarina", "sigla": "SC"},
    {"nome": "São Paulo", "sigla": "SP"},
    {"nome": "Sergipe", "sigla": "SE"},
    {"nome": "Tocantins", "sigla": "TO"}
];

//click menu
$("#menu li").click(function () {
    const view = './views/';
    $(".active").removeClass('active');
    $(this).children().addClass('active');
    let titleContent = document.getElementById("title-page");
    let title = titlePage( $(this).children().data("menu") );
    titleContent.innerHTML = title;
    let page = $(this).children().data("menu");
    page = view + page + '.html';
    //carrega as paginas
    $("#content").load(page, function (response, status, xhr) {
        if (status == "error") {//Se não encontrar pagina na pasta VIEW.
            var msg = "Desculpe, mas houve um erro: ";
            $("#content").html(msg + xhr.status + " " + xhr.statusText);
        }
        //Mascaras nos inputs para todas as paginas
        $('#cpf_cnpj').mask('00.000.000/0000-00');
        $('#cnpj').mask('00.000.000/0000-00');
        $('#insc_estadual').mask("9999999999");
        $('#cep').mask("99999-999");
        $('.tel').mask("(99)99999-9999");
        $('#numero').mask("99999");
        $('#tel_contato').mask("(99)99999-9999");
        //mascara cpf/cnpj no mesmo input
        var options = {
            onKeyPress: function (cpf, ev, el, op) {
                var masks = ['000.000.000-000', '00.000.000/0000-00'];
                $('#cpf_cnpj').mask((cpf.length > 14) ? masks[1] : masks[0], op);
            }
        }
        $('#cpf_cnpj').length > 11 ? $('#cpf_cnpj').mask('00.000.000/0000-00', options) : $('#cpf_cnpj').mask('000.000.000-00#', options); 
        //     
    });
});

/**
 * Função responsavel por formatar o valor passado com a expressão regular.
 */
function formatData(event, keyRE) {
    if (	( typeof(event.keyCode) != 'undefined' && event.keyCode > 0 && String.fromCharCode(event.keyCode).search(keyRE) != (-1) ) || 
        ( typeof(event.charCode) != 'undefined' && event.charCode > 0 && String.fromCharCode(event.charCode).search(keyRE) != (-1) ) ||
        ( typeof(event.charCode) != 'undefined' && event.charCode != event.keyCode && typeof(event.keyCode) != 'undefined' && event.keyCode.toString().search(/^(8|9|13|45|46|35|36|37|39)$/) != (-1) ) ||
        ( typeof(event.charCode) != 'undefined' && event.charCode == event.keyCode && typeof(event.keyCode) != 'undefined' && event.keyCode.toString().search(/^(8|9|13)$/) != (-1) ) ) {
        return true;
    } else {
        return false;
    }
}
/**
 * Função para formatadar os dados da tabela de serviço.
 */
function dataTableFormated(array){
    let newArr = [];
    if(array.length < 2){
        newArr = array[0];
        newArr.pop();//Remove o ultimo elemento. É o botao excluir.
    }else{
        for (let i = 0; i < array.length; i++) {
            //[$array[$i]['name']]
            newArr[i] = array[i];
            newArr[i].pop();//Remove o ultimo elemento. É o botao excluir.
        }
    }
    return newArr;
}

function titlePage(name) {
    return name.replace('-', ' ');
}

function removePoints(string){
    let regExp = /[\.,/()-]/g;
    let ret = string.replace(regExp, "");
    return ret;
}