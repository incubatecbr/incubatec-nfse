-- MySQL dump 10.13  Distrib 8.0.16, for Win64 (x86_64)
--
-- Host: localhost    Database: incubatec_nf
-- ------------------------------------------------------
-- Server version	5.7.24

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 SET NAMES utf8 ;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `clientes` (
  `id` int(12) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `razao_social` varchar(35) NOT NULL,
  `cpf_cnpj` varchar(14) NOT NULL,
  `inscricao_estadual` varchar(14) NOT NULL,
  `logradouro` varchar(45) NOT NULL,
  `numero` varchar(5) NOT NULL,
  `complemento` varchar(15) NOT NULL,
  `bairro` varchar(15) NOT NULL,
  `municipio` varchar(30) NOT NULL,
  `cod_muni_ibge` varchar(7) NOT NULL,
  `uf` varchar(2) NOT NULL,
  `cep` varchar(8) NOT NULL,
  `telefone` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `empresa`
--

DROP TABLE IF EXISTS `empresa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `empresa` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cnpj_emp` varchar(18) NOT NULL,
  `razao_social` varchar(45) NOT NULL,
  `inscricao_estadual` varchar(10) NOT NULL,
  `endereco` varchar(45) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `bairro` varchar(15) NOT NULL,
  `municipio` varchar(30) NOT NULL,
  `cep` varchar(11) NOT NULL,
  `uf` varchar(2) NOT NULL,
  `nome_responsavel` varchar(45) NOT NULL,
  `email_responsavel` varchar(45) NOT NULL,
  `cargo_responsavel` varchar(20) NOT NULL,
  `tel_responsavel` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `item_nota`
--

DROP TABLE IF EXISTS `item_nota`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `item_nota` (
  `id_item_nota` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_nota` int(11) NOT NULL,
  `cod_item` varchar(10) NOT NULL,
  `descricao` varchar(40) NOT NULL,
  `valor_item` varchar(11) NOT NULL,
  `tipo_uni_med` varchar(6) DEFAULT 'UN    ',
  PRIMARY KEY (`id_item_nota`),
  KEY `fk_id_nota` (`id_nota`),
  CONSTRAINT `fk_id_nota` FOREIGN KEY (`id_nota`) REFERENCES `nota_fiscal` (`id_nota`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nota_fiscal`
--

DROP TABLE IF EXISTS `nota_fiscal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `nota_fiscal` (
  `id_nota` int(11) NOT NULL AUTO_INCREMENT,
  `num_nota` varchar(9) NOT NULL,
  `cpf_cnpj_cliente` varchar(14) NOT NULL,
  `razao_social` varchar(35) NOT NULL,
  `inscricao_estadual` varchar(14) NOT NULL,
  `cod_consumidor_cliente` varchar(12) NOT NULL,
  `data_emissao_nf` varchar(8) NOT NULL,
  `ano_mes_apuracao` varchar(4) NOT NULL,
  `modelo` varchar(2) NOT NULL,
  `serie` varchar(3) GENERATED ALWAYS AS ('001') VIRTUAL,
  `cfop` varchar(4) NOT NULL,
  `situacao_doc` varchar(1) NOT NULL,
  `telefone_cliente` varchar(12) DEFAULT NULL,
  `ind_tipo_cpf_cnpj` varchar(1) DEFAULT NULL,
  `tipo_cliente` varchar(2) NOT NULL,
  `telefone_empresa` varchar(12) DEFAULT NULL,
  `cnpj_emitente` varchar(14) NOT NULL,
  `valor_total_nf` varchar(12) NOT NULL,
  `data_remessa` date DEFAULT NULL,
  PRIMARY KEY (`id_nota`),
  UNIQUE KEY `num_nota_UNIQUE` (`num_nota`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-07-16 16:16:56
