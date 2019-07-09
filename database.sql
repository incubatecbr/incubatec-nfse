CREATE TABLE `incubatec_nf`.`empresa` (
  `cnpj_emp` INT NOT NULL,
  `razao_social` VARCHAR(45) NOT NULL,
  `inscricao_estadual` INT NOT NULL,
  `endereco` VARCHAR(45) NOT NULL,
  `bairro` VARCHAR(15) NOT NULL,
  `municipio` VARCHAR(30) NOT NULL,
  `cep` VARCHAR(9) NOT NULL,
  `uf` VARCHAR(2) NOT NULL,
  `nome_responsavel` VARCHAR(45) NOT NULL,
  `email_responsavel` VARCHAR(45) NOT NULL,
  `cargo_responsavel` VARCHAR(10) NOT NULL,
  `tel_responsavel` VARCHAR(12) NOT NULL,
  PRIMARY KEY (`cnpj_emp`),
  UNIQUE INDEX `cnpj_UNIQUE` (`cnpj_emp` ASC));


  CREATE TABLE `incubatec_nf`.`nota_fiscal` (
  `id_nota` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_nota` VARCHAR(9) NOT NULL,
  `cpf_cnpj_cliente` VARCHAR(14) NOT NULL,
  `razao_social` VARCHAR(35) NOT NULL,
  `inscricao_estadual` VARCHAR(14) NOT NULL,
  `razao_social` VARCHAR(35) NOT NULL,
  `cod_consumidor_cliente` VARCHAR(12) NOT NULL,
  `data_emissao_nf` VARCHAR(8) NOT NULL,
  `ano_mes_apuracao` VARCHAR(4) NOT NULL,
  `modelo` VARCHAR(2) NOT NULL,
  `serie` VARCHAR(3) GENERATED ALWAYS AS (001) VIRTUAL,
  `cfop` VARCHAR(4) NOT NULL,
  `situacao_doc` VARCHAR(1) NOT NULL,
  `referencia_item_nota` VARCHAR(9) NULL,
  `telefone_cliente` VARCHAR(12) NULL,
  `ind_tipo_cpf_cnpj` VARCHAR(1) NULL,
  `tipo_cliente` VARCHAR(2) NOT NULL,
  `telefone_empresa` VARCHAR(12) NULL,
  `cnpj_emitente` VARCHAR(14) NOT NULL,
  `valor_total_nf` VARCHAR(12) NOT NULL,
  PRIMARY KEY (`id_nota`),
  UNIQUE INDEX `num_nota_UNIQUE` (`num_nota` ASC));

  CREATE TABLE `incubatec_nf`.`item_nota` (
  `id_item_nota` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_nota` INT NOT NULL,
  `cod_item` VARCHAR(10) NOT NULL,
  `descricao` VARCHAR(40) NOT NULL,
  `undade` VARCHAR(6) NULL DEFAULT 'UN     ',
  PRIMARY KEY (`id_item_nota`));

