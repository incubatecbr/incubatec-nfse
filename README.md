# IncubaNFSe
[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![Build](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![Versao](https://img.shields.io/badge/version-1.0-orange.svg)]()



Incuba NFSe é um projeto open source feito em PHP voltado para emissão nota fiscal de serviço, nos modelos 21 (*Nota Fiscal de Serviço de Comunicações*) e 22 (*Nota Fiscal de Serviço de Telecomunicações*).

## Instalação

Faça o download ou clone este repositório. Importe o banco de dados.


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

## Contribuição
Pull requests são bem-vindos. Para mudanças importantes, por favor, abra um problema primeiro para discutir o que você gostaria de mudar.



## Licença
Este software é distribuído sob a licença [LGPL 3.0](http://www.gnu.org/licenses/lgpl-3.0.html). Por favor, leia LICENÇA para informações sobre a disponibilidade e distribuição do software.