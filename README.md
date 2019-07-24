# IncubaNFSe :page_facing_up:
[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![Build](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![Versao](https://img.shields.io/badge/version-1.0-orange.svg)]()

Incuba NFSe é um projeto open source feito em PHP voltado para emissão nota fiscal de serviço, nos modelos 21 (*Nota Fiscal de Serviço de Comunicações*) e 22 (*Nota Fiscal de Serviço de Telecomunicações*).Desenvolvido por [incubatec](http://incubatec.net.br/).

[![Logo incubatec](https://user-images.githubusercontent.com/38577695/61813461-208dec00-ae1c-11e9-9da2-b262bd66798c.jpg)](http://incubatec.net.br/)

## Instalação

Faça o download ou clone este repositório e importe o banco de dados. Leia atentamente as instruções de uso localizado no menu lateral.

### Configurações
Encontre a classe [Database.php](https://github.com/igorraphael/incubatec-nf/blob/master/sys/Database.php) e modifique se precisar.

```php
<?php
 class Database{
    //ip do servidor.
    private $server = 'localhost';
    //usuário do banco de dados.
    private $user = 'root';
    //senha do usuário.
    private $password = '';
    //nome da tabela.
    private $db = 'incubatec_nf';
```

## Utilizado
[Bootstrap](https://getbootstrap.com/)\
[jQuery-Confirm](https://craftpip.github.io/jquery-confirm/)\
[jQuery-Mask-Plugin](https://igorescobar.github.io/jQuery-Mask-Plugin/)\
[DataTables](https://datatables.net/)\
[API -> ViaCep](https://viacep.com.br/)

## Contribuição :coffee:
Pull requests são bem-vindos. Para mudanças importantes, por favor, abra um problema primeiro para discutir o que você gostaria de mudar.

## Licença
Este software é distribuído sob a licença [LGPL 3.0](http://www.gnu.org/licenses/lgpl-3.0.html). Por favor, leia LICENÇA para informações sobre a disponibilidade e distribuição do software.