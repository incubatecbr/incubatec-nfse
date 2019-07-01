'use strict';


$(document).ready(function () {
    let content = document.getElementById("content");
    let titlePage = document.getElementById("title-page");
    titlePage.innerHTML = 'Gerador de Arquivos Mestre, Identificação e Destinatario.';
    content.innerHTML = "<h1 class='text-center my-5'>Desenvolvido por Incubatec</h1>";
});

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
        $('#cnpj').mask('00.000.000/0000-00');
        $('#insc_estadual').mask("9999999999");
        $('#cep').mask("99999-999");
        $('.tel').mask("(99)99999-9999");
        //      

    });
});

function titlePage(name) {
    return name.replace('-', ' ');
}

function removePoints(string){
    let regExp = /[\.,/()-]/g;
    let ret = string.replace(regExp, "");
    return ret;
}