-- MySQL dump 10.13  Distrib 5.5.32, for Linux (x86_64)
--
-- Host: localhost    Database: impoinga_inventory
-- ------------------------------------------------------
-- Server version	5.5.32-cll

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `TABLE 61`
--

DROP TABLE IF EXISTS `TABLE 61`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TABLE 61` (
  `COL 1` int(6) DEFAULT NULL,
  `COL 2` int(1) DEFAULT NULL,
  `COL 3` int(5) DEFAULT NULL,
  `COL 4` int(1) DEFAULT NULL,
  `COL 5` decimal(6,2) DEFAULT NULL,
  `COL 6` decimal(5,2) DEFAULT NULL,
  `COL 7` varchar(1) DEFAULT NULL,
  `COL 8` varchar(10) DEFAULT NULL,
  `COL 9` varchar(10) DEFAULT NULL,
  `COL 10` varchar(6) DEFAULT NULL,
  `COL 11` varchar(6) DEFAULT NULL,
  `COL 12` varchar(7) DEFAULT NULL,
  `COL 13` varchar(6) DEFAULT NULL,
  `COL 14` varchar(4) DEFAULT NULL,
  `COL 15` varchar(3) DEFAULT NULL,
  `COL 16` varchar(3) DEFAULT NULL,
  `COL 17` varchar(3) DEFAULT NULL,
  `COL 18` varchar(3) DEFAULT NULL,
  `COL 19` varchar(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bancos`
--

DROP TABLE IF EXISTS `bancos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bancos` (
  `ban_id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` char(7) DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ban_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `canje`
--

DROP TABLE IF EXISTS `canje`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `canje` (
  `canje_id` bigint(20) NOT NULL DEFAULT '0',
  `canje_codigo` bigint(20) DEFAULT NULL,
  `cpa_id` int(11) DEFAULT NULL,
  `fecha_canje` date DEFAULT NULL,
  `formato` char(1) DEFAULT NULL,
  `mon_id` int(11) DEFAULT NULL,
  `total_canje` decimal(18,2) DEFAULT NULL,
  `tie_id` int(11) DEFAULT NULL,
  `cli_id` int(11) DEFAULT NULL,
  `estado` char(1) DEFAULT 'N',
  PRIMARY KEY (`canje_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `concepto_contable`
--

DROP TABLE IF EXISTS `concepto_contable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `concepto_contable` (
  `cco_id` int(11) NOT NULL AUTO_INCREMENT,
  `rubro` char(3) DEFAULT NULL,
  `nombre` varchar(30) DEFAULT NULL,
  `cuenta` char(8) DEFAULT NULL,
  PRIMARY KEY (`cco_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `concepto_pago`
--

DROP TABLE IF EXISTS `concepto_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `concepto_pago` (
  `coc_id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` char(9) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `indicador` char(1) DEFAULT NULL,
  PRIMARY KEY (`coc_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `condiciones_pago`
--

DROP TABLE IF EXISTS `condiciones_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `condiciones_pago` (
  `cpa_id` int(10) NOT NULL AUTO_INCREMENT,
  `codigo` char(8) DEFAULT '0',
  `descripcion` varchar(100) DEFAULT NULL,
  `dias` varchar(150) DEFAULT NULL,
  `letras` int(10) NOT NULL,
  PRIMARY KEY (`cpa_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `configuracion`
--

DROP TABLE IF EXISTS `configuracion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configuracion` (
  `con_id` int(11) NOT NULL AUTO_INCREMENT,
  `tie_id` int(11) DEFAULT NULL,
  `apertura_stock` date DEFAULT NULL,
  `apertura_clientes` date DEFAULT NULL,
  `apertura_proveedores` date DEFAULT NULL,
  `moneda_almacen` char(1) DEFAULT NULL,
  `utilidad` decimal(18,2) DEFAULT NULL,
  `desc1` decimal(18,2) DEFAULT NULL,
  `desc2` decimal(18,2) DEFAULT NULL,
  `desc3` decimal(18,2) DEFAULT NULL,
  `desc4` decimal(18,2) DEFAULT NULL,
  `calculo_precios` char(1) DEFAULT NULL,
  `igv` decimal(18,2) DEFAULT NULL,
  `anio_inicial` int(11) DEFAULT NULL,
  PRIMARY KEY (`con_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ctabanco`
--

DROP TABLE IF EXISTS `ctabanco`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ctabanco` (
  `cta_id` int(11) NOT NULL AUTO_INCREMENT,
  `ban_id` int(11) DEFAULT NULL,
  `mon_id` int(11) DEFAULT NULL,
  `nro_cta` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`cta_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cuota`
--

DROP TABLE IF EXISTS `cuota`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cuota` (
  `cuo_id` bigint(6) NOT NULL AUTO_INCREMENT,
  `tipo` char(2) DEFAULT NULL,
  `canje_id` bigint(6) DEFAULT NULL,
  `cuota` int(11) DEFAULT NULL,
  `letra` varchar(20) DEFAULT NULL,
  `fec_can` date DEFAULT NULL,
  `total` decimal(18,2) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  `ban_id` int(11) DEFAULT NULL,
  `cta_id` int(11) DEFAULT NULL,
  `nro_cob` char(15) DEFAULT NULL,
  `cod_uni` char(15) DEFAULT NULL,
  `situacion` char(1) DEFAULT NULL,
  `saldo` decimal(18,2) DEFAULT NULL,
  PRIMARY KEY (`cuo_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `detalle_altas`
--

DROP TABLE IF EXISTS `detalle_altas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_altas` (
  `mal_id` bigint(20) NOT NULL DEFAULT '0',
  `nro_id` int(11) NOT NULL DEFAULT '0',
  `pro_id` bigint(20) DEFAULT NULL,
  `ume_id` int(11) DEFAULT NULL,
  `cantidad` decimal(18,2) DEFAULT NULL,
  `precio_compra` decimal(18,2) DEFAULT NULL,
  `valor_bruto` decimal(18,2) DEFAULT NULL,
  `descuento` varchar(50) DEFAULT NULL,
  `valor_descuento` decimal(18,2) DEFAULT NULL,
  `valor_compra` decimal(18,2) DEFAULT NULL,
  `igv` decimal(18,2) DEFAULT NULL,
  `total` decimal(18,2) DEFAULT NULL,
  `afecta_stock` char(1) DEFAULT NULL,
  PRIMARY KEY (`mal_id`,`nro_id`),
  UNIQUE KEY `pk_dal` (`mal_id`,`nro_id`) USING BTREE,
  KEY `filtro_producto` (`pro_id`) USING BTREE,
  KEY `filtro_stock` (`afecta_stock`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `detalle_bajas`
--

DROP TABLE IF EXISTS `detalle_bajas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_bajas` (
  `mba_id` bigint(10) NOT NULL DEFAULT '0',
  `nro_id` int(11) NOT NULL DEFAULT '0',
  `pro_id` bigint(10) DEFAULT NULL,
  `ume_id` int(11) DEFAULT NULL,
  `cantidad` decimal(18,2) DEFAULT NULL,
  `precio_venta` decimal(18,2) DEFAULT NULL,
  `valor_bruto` decimal(18,2) DEFAULT NULL,
  `descuento` varchar(50) DEFAULT NULL,
  `valor_descuento` decimal(18,2) DEFAULT NULL,
  `valor_venta` decimal(18,2) DEFAULT NULL,
  `igv` decimal(18,2) DEFAULT NULL,
  `total` decimal(18,2) DEFAULT NULL,
  `afecta_stock` char(1) NOT NULL,
  `ind_sw` char(1) DEFAULT NULL,
  PRIMARY KEY (`mba_id`,`nro_id`),
  UNIQUE KEY `pk_dba` (`mba_id`,`nro_id`),
  KEY `filtro_pro` (`pro_id`),
  KEY `filtro_stock` (`afecta_stock`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `detalle_caja`
--

DROP TABLE IF EXISTS `detalle_caja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_caja` (
  `mca_id` smallint(6) NOT NULL DEFAULT '0',
  `item` int(11) NOT NULL DEFAULT '0',
  `cco_id` int(11) DEFAULT NULL,
  `cuo_id` bigint(20) DEFAULT NULL,
  `doc_id` bigint(20) DEFAULT NULL,
  `tip_doc` char(2) DEFAULT NULL,
  `nro_doc` varchar(20) DEFAULT NULL,
  `detalle` varchar(150) DEFAULT NULL,
  `cuenta` char(16) DEFAULT NULL,
  `deb_hab` char(1) DEFAULT NULL,
  `monto_mov` decimal(18,2) DEFAULT NULL,
  `monto_doc` decimal(18,2) DEFAULT NULL,
  `tc` decimal(18,2) DEFAULT NULL,
  `ben_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`mca_id`,`item`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `detalle_canje`
--

DROP TABLE IF EXISTS `detalle_canje`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_canje` (
  `canje_id` bigint(1) NOT NULL DEFAULT '0',
  `tabla` char(1) NOT NULL DEFAULT '',
  `tabla_id` bigint(20) NOT NULL DEFAULT '0',
  `monto` decimal(18,2) DEFAULT NULL,
  `tc` decimal(18,5) DEFAULT NULL,
  `monto_doc` decimal(18,2) DEFAULT NULL,
  `tipo` char(2) DEFAULT NULL,
  PRIMARY KEY (`canje_id`,`tabla`,`tabla_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `detalle_compras`
--

DROP TABLE IF EXISTS `detalle_compras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_compras` (
  `mco_id` bigint(20) NOT NULL DEFAULT '0',
  `nro_id` int(11) NOT NULL DEFAULT '0',
  `pro_id` bigint(20) DEFAULT NULL,
  `ume_id` int(11) DEFAULT NULL,
  `cantidad` decimal(18,2) DEFAULT NULL,
  `precio_compra` decimal(18,2) DEFAULT NULL,
  `valor_bruto` decimal(18,2) DEFAULT NULL,
  `descuento` varchar(50) DEFAULT NULL,
  `valor_descuento` decimal(18,2) DEFAULT NULL,
  `valor_compra` decimal(18,2) DEFAULT NULL,
  `igv` decimal(18,2) DEFAULT NULL,
  `total` decimal(18,2) DEFAULT NULL,
  `afecta_stock` char(1) DEFAULT NULL,
  PRIMARY KEY (`mco_id`,`nro_id`),
  UNIQUE KEY `pk_dco` (`mco_id`,`nro_id`),
  KEY `filtro_pro` (`pro_id`),
  KEY `filtro_stock` (`afecta_stock`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `detalle_notacompras`
--

DROP TABLE IF EXISTS `detalle_notacompras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_notacompras` (
  `nco_id` bigint(10) NOT NULL DEFAULT '0',
  `nro_id` int(11) NOT NULL DEFAULT '0',
  `pro_id` bigint(10) DEFAULT NULL,
  `ume_id` int(11) DEFAULT NULL,
  `cantidad` decimal(18,2) DEFAULT NULL,
  `precio_compra` decimal(18,2) DEFAULT NULL,
  `valor_bruto` decimal(18,2) DEFAULT NULL,
  `descuento` varchar(50) DEFAULT NULL,
  `valor_descuento` decimal(18,2) DEFAULT NULL,
  `valor_compra` decimal(18,2) DEFAULT NULL,
  `igv` decimal(18,2) DEFAULT NULL,
  `total` decimal(18,2) DEFAULT NULL,
  `afecta_stock` char(1) NOT NULL,
  `ind_sw` char(1) DEFAULT NULL,
  PRIMARY KEY (`nco_id`,`nro_id`),
  UNIQUE KEY `pk_dnc` (`nco_id`,`nro_id`),
  KEY `filtro_pro` (`pro_id`),
  KEY `filtro_stock` (`afecta_stock`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `detalle_notaventas`
--

DROP TABLE IF EXISTS `detalle_notaventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_notaventas` (
  `nve_id` bigint(20) NOT NULL DEFAULT '0',
  `nro_id` int(11) NOT NULL DEFAULT '0',
  `pro_id` bigint(20) DEFAULT NULL,
  `ume_id` int(11) DEFAULT NULL,
  `cantidad` decimal(18,2) DEFAULT NULL,
  `precio_venta` decimal(18,2) DEFAULT NULL,
  `valor_bruto` decimal(18,2) DEFAULT NULL,
  `descuento` varchar(50) DEFAULT NULL,
  `valor_descuento` decimal(18,2) DEFAULT NULL,
  `valor_venta` decimal(18,2) DEFAULT NULL,
  `igv` decimal(18,2) DEFAULT NULL,
  `total` decimal(18,2) DEFAULT NULL,
  `afecta_stock` char(1) DEFAULT NULL,
  PRIMARY KEY (`nve_id`,`nro_id`),
  UNIQUE KEY `pk_dnv` (`nve_id`,`nro_id`),
  KEY `filtro_pro` (`pro_id`),
  KEY `filtro_stock` (`afecta_stock`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `detalle_prerecibo`
--

DROP TABLE IF EXISTS `detalle_prerecibo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_prerecibo` (
  `mpr_id` bigint(20) NOT NULL DEFAULT '0',
  `item` int(11) NOT NULL DEFAULT '0',
  `cuo_id` bigint(20) DEFAULT NULL,
  `doc_id` bigint(20) DEFAULT NULL,
  `tip_doc` char(2) DEFAULT NULL,
  `nro_doc` varchar(20) DEFAULT NULL,
  `cco_id` int(11) DEFAULT NULL,
  `cuenta` char(16) DEFAULT NULL,
  `deb_hab` char(1) DEFAULT NULL,
  `monto` decimal(18,2) DEFAULT NULL,
  PRIMARY KEY (`item`,`mpr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `detalle_trasladoing`
--

DROP TABLE IF EXISTS `detalle_trasladoing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_trasladoing` (
  `tin_id` bigint(20) NOT NULL DEFAULT '0',
  `nro_id` int(11) NOT NULL DEFAULT '0',
  `pro_id` bigint(20) DEFAULT NULL,
  `ume_id` int(11) DEFAULT NULL,
  `cantidad` decimal(18,2) DEFAULT NULL,
  `precio_compra` decimal(18,2) DEFAULT NULL,
  `valor_bruto` decimal(18,2) DEFAULT NULL,
  `descuento` varchar(50) DEFAULT NULL,
  `valor_descuento` decimal(18,2) DEFAULT NULL,
  `valor_compra` decimal(18,2) DEFAULT NULL,
  `igv` decimal(18,2) DEFAULT NULL,
  `total` decimal(18,2) DEFAULT NULL,
  `afecta_stock` char(1) DEFAULT NULL,
  PRIMARY KEY (`tin_id`,`nro_id`),
  UNIQUE KEY `pk_str` (`tin_id`,`nro_id`),
  KEY `filtro_pro` (`pro_id`),
  KEY `filtro_stock` (`afecta_stock`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `detalle_ventas`
--

DROP TABLE IF EXISTS `detalle_ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_ventas` (
  `mve_id` bigint(10) NOT NULL DEFAULT '0',
  `nro_id` int(11) NOT NULL DEFAULT '0',
  `pro_id` bigint(10) DEFAULT NULL,
  `ume_id` int(11) DEFAULT NULL,
  `cantidad` decimal(18,2) DEFAULT NULL,
  `precio_venta` decimal(18,2) DEFAULT NULL,
  `valor_bruto` decimal(18,2) DEFAULT NULL,
  `descuento` varchar(50) DEFAULT NULL,
  `valor_descuento` decimal(18,2) DEFAULT NULL,
  `valor_venta` decimal(18,2) DEFAULT NULL,
  `igv` decimal(18,2) DEFAULT NULL,
  `total` decimal(18,2) DEFAULT NULL,
  `afecta_stock` char(1) NOT NULL,
  `ind_sw` char(1) DEFAULT NULL,
  PRIMARY KEY (`mve_id`,`nro_id`),
  UNIQUE KEY `pk_dve` (`mve_id`,`nro_id`),
  KEY `filtro_pro` (`pro_id`),
  KEY `filtro_stock` (`afecta_stock`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `documentos`
--

DROP TABLE IF EXISTS `documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documentos` (
  `doc_id` int(11) NOT NULL AUTO_INCREMENT,
  `abrev` char(2) DEFAULT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `compras` char(2) DEFAULT NULL,
  `ventas` char(2) DEFAULT NULL,
  `n_compras` varchar(20) DEFAULT NULL,
  `n_ventas` varchar(20) DEFAULT NULL,
  `nn_credito` varchar(20) DEFAULT NULL,
  `nn_debito` varchar(20) DEFAULT NULL,
  `codigo` char(7) DEFAULT NULL,
  `altas` char(2) DEFAULT NULL,
  `bajas` char(2) DEFAULT NULL,
  `ncompras` char(2) DEFAULT NULL,
  `nventas` char(2) DEFAULT NULL,
  `tingresos` char(2) DEFAULT NULL,
  `tsalidas` char(2) DEFAULT NULL,
  PRIMARY KEY (`doc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eventos`
--

DROP TABLE IF EXISTS `eventos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eventos` (
  `eve_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `usr_id` bigint(20) DEFAULT NULL,
  `evento` char(1) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `tabla` varchar(25) DEFAULT NULL,
  `registro` bigint(20) DEFAULT NULL,
  `detalle` text,
  PRIMARY KEY (`eve_id`),
  UNIQUE KEY `pk_eve` (`eve_id`) USING BTREE,
  KEY `filtro_usu` (`usr_id`),
  KEY `filtro_fec` (`fecha`)
) ENGINE=MyISAM AUTO_INCREMENT=73419 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `familias`
--

DROP TABLE IF EXISTS `familias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `familias` (
  `fam_id` int(10) NOT NULL AUTO_INCREMENT,
  `codigo` char(6) NOT NULL DEFAULT '0',
  `nombre` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`fam_id`),
  UNIQUE KEY `pk_fam` (`fam_id`),
  KEY `filtro_des` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formato_cabecera`
--

DROP TABLE IF EXISTS `formato_cabecera`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formato_cabecera` (
  `nombre_cliente` varchar(250) DEFAULT NULL,
  `descripcion_documento` varchar(250) DEFAULT NULL,
  `mve_id` bigint(20) DEFAULT NULL,
  `codigo` varchar(250) DEFAULT NULL,
  `cli_id` bigint(20) DEFAULT NULL,
  `tipo_ingreso` char(1) DEFAULT NULL,
  `doc_id` int(11) DEFAULT NULL,
  `doc_n` varchar(20) DEFAULT NULL,
  `n_guia` varchar(20) DEFAULT NULL,
  `cpa_id` int(11) DEFAULT NULL,
  `mon_id` int(11) DEFAULT NULL,
  `valor_venta` decimal(18,4) DEFAULT NULL,
  `impuesto_igv` decimal(18,4) DEFAULT NULL,
  `total_venta` decimal(18,4) DEFAULT NULL,
  `fec_ven` varchar(20) DEFAULT NULL,
  `fec_mov` varchar(20) DEFAULT NULL,
  `moneda` varchar(20) DEFAULT NULL,
  `anulado` char(1) DEFAULT NULL,
  `cli_codigo` varchar(20) DEFAULT NULL,
  `ruc` varchar(20) DEFAULT NULL,
  `eliminado` varchar(1) DEFAULT NULL,
  `afecta` char(1) DEFAULT NULL,
  `formato` char(1) DEFAULT NULL,
  `observacion` varchar(200) DEFAULT NULL,
  `direccion` varchar(210) DEFAULT NULL,
  `fec_ori` varchar(20) DEFAULT NULL,
  `condicion` varchar(30) DEFAULT NULL,
  `saldo` decimal(18,4) DEFAULT NULL,
  `letras` varchar(20) DEFAULT NULL,
  `dias` varchar(20) DEFAULT NULL,
  `agencia` varchar(100) DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `usr_id` int(11) DEFAULT NULL,
  `age_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `formato_detalle`
--

DROP TABLE IF EXISTS `formato_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `formato_detalle` (
  `mve_id` bigint(10) NOT NULL DEFAULT '0',
  `nro_id` int(11) NOT NULL DEFAULT '0',
  `pro_id` bigint(10) DEFAULT NULL,
  `ume_id` int(11) DEFAULT NULL,
  `cantidad` decimal(18,2) DEFAULT NULL,
  `precio_venta` decimal(18,2) DEFAULT NULL,
  `valor_bruto` decimal(18,2) DEFAULT NULL,
  `descuento` varchar(50) DEFAULT NULL,
  `valor_descuento` decimal(18,2) DEFAULT NULL,
  `valor_venta` decimal(18,2) DEFAULT NULL,
  `igv` decimal(18,2) DEFAULT NULL,
  `total` decimal(18,2) DEFAULT NULL,
  `afecta_stock` char(1) NOT NULL,
  `ind_sw` char(1) DEFAULT NULL,
  `codigo1` varchar(20) DEFAULT NULL,
  `producto` varchar(50) DEFAULT NULL,
  `marca` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`mve_id`,`nro_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `igv`
--

DROP TABLE IF EXISTS `igv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `igv` (
  `igv_id` int(10) NOT NULL AUTO_INCREMENT,
  `valor` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`igv_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lineas`
--

DROP TABLE IF EXISTS `lineas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lineas` (
  `lin_id` int(10) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `fam_id` int(11) DEFAULT NULL,
  `codigo` char(8) NOT NULL,
  PRIMARY KEY (`lin_id`),
  UNIQUE KEY `pk_lin` (`lin_id`) USING BTREE,
  KEY `filtro_des` (`nombre`),
  KEY `fk_fam` (`fam_id`)
) ENGINE=InnoDB AUTO_INCREMENT=395 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `maestros_agencias_transporte`
--

DROP TABLE IF EXISTS `maestros_agencias_transporte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maestros_agencias_transporte` (
  `age_id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` char(6) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `ruc` char(20) NOT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  `tip_per` char(1) NOT NULL,
  `tip_doc` char(1) NOT NULL,
  `rep_leg` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`age_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `maestros_clientes`
--

DROP TABLE IF EXISTS `maestros_clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maestros_clientes` (
  `cli_id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` char(9) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `ruc` char(20) NOT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `web` varchar(150) DEFAULT NULL,
  `dep_id` int(11) NOT NULL,
  `ciu_id` int(11) NOT NULL,
  `dis_id` int(11) NOT NULL,
  `direccion` varchar(200) NOT NULL,
  `observacion` text,
  `activo` int(1) NOT NULL DEFAULT '1',
  `tip_per` char(1) NOT NULL,
  `tip_doc` char(1) NOT NULL,
  `rep_leg` varchar(150) DEFAULT NULL,
  `sit_id` int(11) DEFAULT NULL,
  `fec_ing` date DEFAULT NULL,
  PRIMARY KEY (`cli_id`),
  UNIQUE KEY `pk_cli` (`cli_id`) USING BTREE,
  KEY `filtro_des` (`nombre`),
  KEY `filtro_ruc` (`ruc`)
) ENGINE=InnoDB AUTO_INCREMENT=15186 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `maestros_mercaderias`
--

DROP TABLE IF EXISTS `maestros_mercaderias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maestros_mercaderias` (
  `mcd_id` bigint(13) NOT NULL AUTO_INCREMENT,
  `codigo1` char(20) NOT NULL,
  `codigo2` char(20) NOT NULL,
  `codigo3` char(20) NOT NULL,
  `nombre` varchar(200) DEFAULT NULL,
  `ume_id` int(50) NOT NULL,
  `mar_id` int(13) NOT NULL,
  `lin_id` int(13) DEFAULT NULL,
  `activo` int(11) NOT NULL DEFAULT '0',
  `observacion` text,
  `url_imagen` varchar(150) DEFAULT NULL,
  `tipo_mcd` char(1) DEFAULT NULL,
  PRIMARY KEY (`mcd_id`),
  KEY `ind1` (`codigo1`),
  KEY `ind2` (`codigo2`,`codigo3`),
  KEY `ind3` (`nombre`),
  KEY `ind4` (`ume_id`),
  KEY `ind5` (`mar_id`),
  KEY `ind6` (`lin_id`),
  KEY `ind7` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=50709 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `maestros_proveedores`
--

DROP TABLE IF EXISTS `maestros_proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maestros_proveedores` (
  `prv_id` int(13) NOT NULL AUTO_INCREMENT,
  `codigo` char(13) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `ruc` char(20) NOT NULL DEFAULT '0',
  `direccion` varchar(100) NOT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  `tip_per` char(1) NOT NULL,
  `tip_doc` char(1) NOT NULL,
  `rep_leg` varchar(150) NOT NULL,
  `fec_cre` date DEFAULT NULL,
  `dir_loc` varchar(150) DEFAULT NULL,
  `observacion` text,
  `directorio` text,
  PRIMARY KEY (`prv_id`),
  UNIQUE KEY `pk_prv` (`prv_id`),
  KEY `filtro_des` (`nombre`),
  KEY `filtro_ruc` (`ruc`)
) ENGINE=InnoDB AUTO_INCREMENT=495 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `marcas`
--

DROP TABLE IF EXISTS `marcas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marcas` (
  `mar_id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(40) DEFAULT NULL,
  `codigo` char(7) NOT NULL,
  PRIMARY KEY (`mar_id`),
  UNIQUE KEY `pk_mar` (`mar_id`) USING BTREE,
  KEY `filtro_des` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=1585 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `monedas`
--

DROP TABLE IF EXISTS `monedas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `monedas` (
  `mon_id` int(10) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`mon_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `movimientos_altas`
--

DROP TABLE IF EXISTS `movimientos_altas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movimientos_altas` (
  `mal_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `codigo` bigint(20) DEFAULT '0',
  `prv_id` int(10) DEFAULT NULL,
  `tipo_ingreso` int(10) DEFAULT NULL,
  `doc_id` int(10) DEFAULT NULL,
  `doc_n` varchar(20) DEFAULT NULL,
  `tmv_id` int(11) DEFAULT NULL,
  `fec_mov` date DEFAULT NULL,
  `fec_ven` date DEFAULT NULL,
  `n_guia` varchar(20) DEFAULT NULL,
  `cpa_id` int(10) DEFAULT NULL,
  `mon_id` int(10) DEFAULT NULL,
  `valor_bruto` decimal(18,2) DEFAULT NULL,
  `descuento` varchar(50) DEFAULT NULL,
  `valor_descuento` decimal(18,2) DEFAULT NULL,
  `valor_compra` decimal(10,2) DEFAULT NULL,
  `impuesto_igv` decimal(10,2) DEFAULT NULL,
  `total_compra` decimal(10,2) DEFAULT NULL,
  `afecta` char(1) DEFAULT NULL,
  `formato` char(1) DEFAULT NULL,
  `tipo_cambio` decimal(18,2) DEFAULT NULL,
  `igv` decimal(18,2) DEFAULT NULL,
  `age_id` int(11) DEFAULT NULL,
  `observacion` text,
  `eliminado` char(1) DEFAULT NULL,
  `anulado` char(1) DEFAULT NULL,
  UNIQUE KEY `pk_movi` (`mal_id`),
  UNIQUE KEY `filtro_codigo` (`codigo`,`age_id`),
  KEY `filtro_fecha` (`fec_ven`),
  KEY `filtro_prv` (`prv_id`),
  KEY `filtro_ind` (`eliminado`,`anulado`)
) ENGINE=InnoDB AUTO_INCREMENT=511 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `movimientos_bajas`
--

DROP TABLE IF EXISTS `movimientos_bajas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movimientos_bajas` (
  `mba_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `codigo` bigint(20) DEFAULT '0',
  `cli_id` int(10) DEFAULT NULL,
  `cliente` varchar(100) DEFAULT NULL,
  `cli_ruc` varchar(11) DEFAULT NULL,
  `tipo_ingreso` int(10) DEFAULT NULL,
  `doc_id` int(10) DEFAULT NULL,
  `doc_n` varchar(20) DEFAULT NULL,
  `fec_mov` date DEFAULT NULL,
  `fec_ven` date DEFAULT NULL,
  `tmv_id` int(11) DEFAULT NULL,
  `n_guia` varchar(20) DEFAULT NULL,
  `cpa_id` int(10) DEFAULT NULL,
  `mon_id` int(10) DEFAULT NULL,
  `valor_bruto` decimal(18,2) DEFAULT NULL,
  `descuento` varchar(50) DEFAULT NULL,
  `valor_descuento` decimal(18,2) DEFAULT NULL,
  `valor_venta` decimal(18,2) DEFAULT NULL,
  `impuesto_igv` decimal(18,2) DEFAULT NULL,
  `total_venta` decimal(18,2) DEFAULT NULL,
  `afecta` char(1) DEFAULT NULL,
  `formato` char(1) DEFAULT NULL,
  `tipo_cambio` decimal(18,2) DEFAULT NULL,
  `igv` decimal(18,2) DEFAULT NULL,
  `age_id` int(11) DEFAULT NULL,
  `observacion` text,
  `eliminado` char(1) DEFAULT NULL,
  `anulado` char(1) DEFAULT NULL,
  PRIMARY KEY (`mba_id`),
  UNIQUE KEY `pk_mba` (`mba_id`),
  UNIQUE KEY `pk_codigo` (`codigo`,`age_id`),
  KEY `filtro_fecha` (`fec_ven`),
  KEY `filtro_cli` (`cli_id`),
  KEY `filtro_ind` (`eliminado`,`anulado`)
) ENGINE=InnoDB AUTO_INCREMENT=290 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `movimientos_caja`
--

DROP TABLE IF EXISTS `movimientos_caja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movimientos_caja` (
  `mca_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `tie_id` int(12) DEFAULT NULL,
  `nro` smallint(6) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `fecha_pago` date DEFAULT NULL,
  `tipo_cpa` char(1) DEFAULT NULL,
  `cpa_id` int(11) DEFAULT NULL,
  `tipo_per` char(1) DEFAULT NULL,
  `per_id` smallint(6) DEFAULT NULL,
  `per_cod` varchar(20) DEFAULT NULL,
  `per_nom` varchar(150) DEFAULT NULL,
  `mon_id` int(11) DEFAULT NULL,
  `monto` decimal(18,2) DEFAULT NULL,
  `tipo_pag` char(1) DEFAULT NULL,
  `tpa_id` int(11) DEFAULT NULL,
  `nro_cheque` varchar(50) DEFAULT NULL,
  `comentario` text,
  `reg_id` smallint(6) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  `fec_reg` datetime DEFAULT NULL,
  `tc` decimal(12,4) DEFAULT NULL,
  PRIMARY KEY (`mca_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `movimientos_compras`
--

DROP TABLE IF EXISTS `movimientos_compras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movimientos_compras` (
  `mco_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `codigo` bigint(20) DEFAULT '0',
  `prv_id` int(10) DEFAULT NULL,
  `tipo_ingreso` int(10) DEFAULT NULL,
  `doc_id` int(10) DEFAULT NULL,
  `doc_n` varchar(20) DEFAULT NULL,
  `fec_mov` date DEFAULT NULL,
  `fec_ven` date DEFAULT NULL,
  `n_guia` varchar(20) DEFAULT NULL,
  `cpa_id` int(10) DEFAULT NULL,
  `mon_id` int(10) DEFAULT NULL,
  `valor_bruto` decimal(18,2) DEFAULT NULL,
  `descuento` varchar(50) DEFAULT NULL,
  `valor_descuento` decimal(18,2) DEFAULT NULL,
  `valor_compra` decimal(10,2) DEFAULT NULL,
  `impuesto_igv` decimal(10,2) DEFAULT NULL,
  `total_compra` decimal(10,2) DEFAULT NULL,
  `afecta` char(1) DEFAULT NULL,
  `formato` char(1) DEFAULT NULL,
  `tipo_cambio` decimal(18,2) DEFAULT NULL,
  `igv` decimal(18,2) DEFAULT NULL,
  `age_id` int(11) DEFAULT NULL,
  `observacion` text,
  `eliminado` char(1) DEFAULT NULL,
  `anulado` char(1) DEFAULT NULL,
  `saldo` decimal(18,2) DEFAULT NULL,
  `saldo_inicial` decimal(18,2) DEFAULT NULL,
  PRIMARY KEY (`mco_id`),
  UNIQUE KEY `pk_mco` (`age_id`,`mco_id`) USING BTREE,
  KEY `ind1` (`prv_id`),
  KEY `ind2` (`doc_id`),
  KEY `ind3` (`fec_mov`),
  KEY `ind4` (`eliminado`,`anulado`),
  KEY `filtro_fecha` (`fec_ven`),
  KEY `pk_codigo` (`codigo`,`age_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2063 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `movimientos_notacompras`
--

DROP TABLE IF EXISTS `movimientos_notacompras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movimientos_notacompras` (
  `nco_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `codigo` bigint(20) DEFAULT '0',
  `prv_id` int(10) DEFAULT NULL,
  `proveedor` varchar(150) DEFAULT NULL,
  `prv_ruc` varchar(11) DEFAULT NULL,
  `tipo_ingreso` int(10) DEFAULT NULL,
  `doc_id` int(10) DEFAULT NULL,
  `doc_n` varchar(20) DEFAULT NULL,
  `fec_mov` date DEFAULT NULL,
  `fec_ven` date DEFAULT NULL,
  `tnt_id` int(11) DEFAULT NULL,
  `n_guia` varchar(20) DEFAULT NULL,
  `cpa_id` int(10) DEFAULT NULL,
  `mon_id` int(10) DEFAULT NULL,
  `valor_bruto` decimal(18,2) DEFAULT NULL,
  `descuento` varchar(50) DEFAULT NULL,
  `valor_descuento` decimal(18,2) DEFAULT NULL,
  `valor_compra` decimal(18,2) DEFAULT NULL,
  `impuesto_igv` decimal(18,2) DEFAULT NULL,
  `total_compra` decimal(18,2) DEFAULT NULL,
  `afecta` char(1) DEFAULT NULL,
  `formato` char(1) DEFAULT NULL,
  `tipo_cambio` decimal(18,2) DEFAULT NULL,
  `igv` decimal(18,2) DEFAULT NULL,
  `age_id` int(11) DEFAULT NULL,
  `observacion` text,
  `eliminado` char(1) DEFAULT NULL,
  `anulado` char(1) DEFAULT NULL,
  `saldo` decimal(18,2) DEFAULT NULL,
  PRIMARY KEY (`nco_id`),
  UNIQUE KEY `pk_nco` (`nco_id`) USING BTREE,
  UNIQUE KEY `pk_codigo` (`codigo`,`age_id`),
  KEY `filtro_fecha` (`fec_ven`),
  KEY `filtro_prv` (`prv_id`),
  KEY `filtro_ind` (`eliminado`,`anulado`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `movimientos_notaventas`
--

DROP TABLE IF EXISTS `movimientos_notaventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movimientos_notaventas` (
  `nve_id` bigint(10) NOT NULL AUTO_INCREMENT,
  `codigo` bigint(8) DEFAULT '0',
  `cli_id` int(10) DEFAULT NULL,
  `cliente` varchar(150) DEFAULT NULL,
  `cli_ruc` varchar(11) DEFAULT NULL,
  `tipo_ingreso` int(10) DEFAULT NULL,
  `doc_id` int(10) DEFAULT NULL,
  `doc_n` varchar(20) DEFAULT NULL,
  `tnt_id` int(11) DEFAULT NULL,
  `fec_mov` date DEFAULT NULL,
  `fec_ven` date DEFAULT NULL,
  `n_guia` varchar(20) DEFAULT NULL,
  `cpa_id` int(10) DEFAULT NULL,
  `mon_id` int(10) DEFAULT NULL,
  `valor_bruto` decimal(18,2) DEFAULT NULL,
  `descuento` varchar(50) DEFAULT NULL,
  `valor_descuento` decimal(18,2) DEFAULT NULL,
  `valor_venta` decimal(10,2) DEFAULT NULL,
  `impuesto_igv` decimal(10,2) DEFAULT NULL,
  `total_venta` decimal(10,2) DEFAULT NULL,
  `afecta` char(1) DEFAULT NULL,
  `formato` char(1) DEFAULT NULL,
  `tipo_cambio` decimal(18,2) DEFAULT NULL,
  `igv` decimal(18,2) DEFAULT NULL,
  `age_id` int(11) DEFAULT NULL,
  `observacion` text,
  `eliminado` char(1) DEFAULT NULL,
  `anulado` char(1) DEFAULT NULL,
  `saldo` decimal(18,2) DEFAULT NULL,
  PRIMARY KEY (`nve_id`),
  UNIQUE KEY `pk_nve` (`nve_id`) USING BTREE,
  UNIQUE KEY `pk_codigo` (`codigo`,`age_id`),
  KEY `filtro_fecha` (`fec_ven`),
  KEY `filtro_cli` (`cli_id`),
  KEY `filtro_ind` (`eliminado`,`anulado`)
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `movimientos_prerecibo`
--

DROP TABLE IF EXISTS `movimientos_prerecibo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movimientos_prerecibo` (
  `mpr_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tie_id` int(11) DEFAULT NULL,
  `codigo` bigint(20) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `fecha_reg` datetime DEFAULT NULL,
  `tipo_pagador` char(1) DEFAULT NULL,
  `per_id` bigint(20) DEFAULT NULL,
  `per_doc` varchar(15) DEFAULT NULL,
  `per_nom` varchar(150) DEFAULT NULL,
  `comentario` text,
  `estado` char(1) DEFAULT NULL,
  `procesado` char(1) DEFAULT NULL,
  PRIMARY KEY (`mpr_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `movimientos_saldocompras`
--

DROP TABLE IF EXISTS `movimientos_saldocompras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movimientos_saldocompras` (
  `sco_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `codigo` bigint(20) DEFAULT '0',
  `prv_id` int(10) DEFAULT NULL,
  `cliente` varchar(100) DEFAULT NULL,
  `prv_ruc` varchar(11) DEFAULT NULL,
  `tipo_ingreso` int(10) DEFAULT NULL,
  `doc_id` int(10) DEFAULT NULL,
  `doc_n` varchar(20) DEFAULT NULL,
  `fec_mov` date DEFAULT NULL,
  `fec_ven` date DEFAULT NULL,
  `n_guia` varchar(20) DEFAULT NULL,
  `cpa_id` int(10) DEFAULT NULL,
  `mon_id` int(10) DEFAULT NULL,
  `valor_bruto` decimal(18,2) DEFAULT NULL,
  `descuento` varchar(50) DEFAULT NULL,
  `valor_descuento` decimal(18,2) DEFAULT NULL,
  `valor_compra` decimal(18,2) DEFAULT NULL,
  `impuesto_igv` decimal(18,2) DEFAULT NULL,
  `total_compra` decimal(18,2) DEFAULT NULL,
  `afecta` char(1) DEFAULT NULL,
  `formato` char(1) DEFAULT NULL,
  `tipo_cambio` decimal(18,2) DEFAULT NULL,
  `igv` decimal(18,2) DEFAULT NULL,
  `age_id` int(11) DEFAULT NULL,
  `observacion` text,
  `eliminado` char(1) DEFAULT NULL,
  `anulado` char(1) DEFAULT NULL,
  `saldo` decimal(18,2) DEFAULT NULL,
  `saldo_inicial` decimal(18,2) DEFAULT NULL,
  PRIMARY KEY (`sco_id`),
  UNIQUE KEY `pk_mve` (`sco_id`,`age_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `movimientos_saldoventas`
--

DROP TABLE IF EXISTS `movimientos_saldoventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movimientos_saldoventas` (
  `sve_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `codigo` bigint(20) DEFAULT '0',
  `cli_id` int(10) DEFAULT NULL,
  `cliente` varchar(100) DEFAULT NULL,
  `cli_ruc` varchar(11) DEFAULT NULL,
  `tipo_ingreso` int(10) DEFAULT NULL,
  `doc_id` int(10) DEFAULT NULL,
  `doc_n` varchar(20) DEFAULT NULL,
  `fec_mov` date DEFAULT NULL,
  `fec_ven` date DEFAULT NULL,
  `n_guia` varchar(20) DEFAULT NULL,
  `cpa_id` int(10) DEFAULT NULL,
  `mon_id` int(10) DEFAULT NULL,
  `valor_bruto` decimal(18,2) DEFAULT NULL,
  `descuento` varchar(50) DEFAULT NULL,
  `valor_descuento` decimal(18,2) DEFAULT NULL,
  `valor_venta` decimal(18,2) DEFAULT NULL,
  `impuesto_igv` decimal(18,2) DEFAULT NULL,
  `total_venta` decimal(18,2) DEFAULT NULL,
  `afecta` char(1) DEFAULT NULL,
  `formato` char(1) DEFAULT NULL,
  `tipo_cambio` decimal(18,2) DEFAULT NULL,
  `igv` decimal(18,2) DEFAULT NULL,
  `age_id` int(11) DEFAULT NULL,
  `observacion` text,
  `eliminado` char(1) DEFAULT NULL,
  `anulado` char(1) DEFAULT NULL,
  `saldo` decimal(18,2) DEFAULT NULL,
  `saldo_inicial` decimal(18,2) DEFAULT NULL,
  PRIMARY KEY (`sve_id`),
  UNIQUE KEY `pk_mve` (`sve_id`,`age_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `movimientos_trasladoing`
--

DROP TABLE IF EXISTS `movimientos_trasladoing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movimientos_trasladoing` (
  `tin_id` bigint(10) NOT NULL AUTO_INCREMENT,
  `codigo` bigint(8) DEFAULT '0',
  `prv_id` int(10) DEFAULT NULL,
  `tipo_ingreso` int(10) DEFAULT NULL,
  `doc_id` int(10) DEFAULT NULL,
  `doc_n` varchar(20) DEFAULT NULL,
  `tmv_id` int(11) DEFAULT NULL,
  `fec_mov` date DEFAULT NULL,
  `fec_ven` date DEFAULT NULL,
  `n_guia` varchar(20) DEFAULT NULL,
  `cpa_id` int(10) DEFAULT NULL,
  `mon_id` int(10) DEFAULT NULL,
  `valor_bruto` decimal(18,2) DEFAULT NULL,
  `descuento` varchar(50) DEFAULT NULL,
  `valor_descuento` decimal(18,2) DEFAULT NULL,
  `valor_compra` decimal(10,2) DEFAULT NULL,
  `impuesto_igv` decimal(10,2) DEFAULT NULL,
  `total_compra` decimal(10,2) DEFAULT NULL,
  `afecta` char(1) DEFAULT NULL,
  `formato` char(1) DEFAULT NULL,
  `tipo_cambio` decimal(18,2) DEFAULT NULL,
  `igv` decimal(18,2) DEFAULT NULL,
  `age_id` int(11) DEFAULT NULL,
  `tie_des` int(11) DEFAULT NULL,
  `observacion` text,
  `eliminado` char(1) DEFAULT NULL,
  `anulado` char(1) DEFAULT NULL,
  PRIMARY KEY (`tin_id`),
  UNIQUE KEY `pk_trl` (`tin_id`),
  UNIQUE KEY `pk_codigo` (`codigo`,`age_id`),
  KEY `filtro_fecha` (`fec_ven`),
  KEY `filtro_ind` (`eliminado`,`anulado`),
  KEY `filtro_prv` (`prv_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `movimientos_ventas`
--

DROP TABLE IF EXISTS `movimientos_ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movimientos_ventas` (
  `mve_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `codigo` bigint(20) DEFAULT '0',
  `cli_id` int(10) DEFAULT NULL,
  `cliente` varchar(100) DEFAULT NULL,
  `cli_ruc` varchar(11) DEFAULT NULL,
  `tipo_ingreso` int(10) DEFAULT NULL,
  `doc_id` int(10) DEFAULT NULL,
  `doc_n` varchar(20) DEFAULT NULL,
  `fec_mov` date DEFAULT NULL,
  `fec_ven` date DEFAULT NULL,
  `n_guia` varchar(20) DEFAULT NULL,
  `cpa_id` int(10) DEFAULT NULL,
  `mon_id` int(10) DEFAULT NULL,
  `valor_bruto` decimal(18,2) DEFAULT NULL,
  `descuento` varchar(50) DEFAULT NULL,
  `valor_descuento` decimal(18,2) DEFAULT NULL,
  `valor_venta` decimal(18,2) DEFAULT NULL,
  `impuesto_igv` decimal(18,2) DEFAULT NULL,
  `total_venta` decimal(18,2) DEFAULT NULL,
  `afecta` char(1) DEFAULT NULL,
  `formato` char(1) DEFAULT NULL,
  `tipo_cambio` decimal(18,2) DEFAULT NULL,
  `igv` decimal(18,2) DEFAULT NULL,
  `age_id` int(11) DEFAULT NULL,
  `observacion` text,
  `eliminado` char(1) DEFAULT NULL,
  `anulado` char(1) DEFAULT NULL,
  `saldo` decimal(18,2) DEFAULT NULL,
  `saldo_inicial` decimal(18,2) DEFAULT NULL,
  `impreso` char(1) DEFAULT NULL,
  `usr_id` int(11) NOT NULL,
  UNIQUE KEY `pk_mve` (`mve_id`) USING BTREE,
  KEY `pk_codigo` (`codigo`,`age_id`),
  KEY `filtro_fecha` (`fec_ven`),
  KEY `filtro_cli` (`cli_id`),
  KEY `filtro_ind` (`eliminado`,`anulado`)
) ENGINE=InnoDB AUTO_INCREMENT=18716 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `numeracion`
--

DROP TABLE IF EXISTS `numeracion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `numeracion` (
  `num_id` int(11) NOT NULL AUTO_INCREMENT,
  `age_id` int(11) DEFAULT NULL,
  `doc_id` int(11) DEFAULT NULL,
  `tipo` char(1) DEFAULT NULL COMMENT 'Tipo ; 1:Compra, 2:Venta, 3:Alta, 4:Baja, 5:NC Compra, 6:NC Venta, 7:Tras Ent, 8:Tras Sal',
  `serie` char(4) DEFAULT NULL,
  `lon` int(11) DEFAULT NULL,
  `nro` decimal(10,0) DEFAULT NULL,
  PRIMARY KEY (`num_id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plancuenta`
--

DROP TABLE IF EXISTS `plancuenta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plancuenta` (
  `pcu_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `cuenta` char(8) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `tipo` char(1) DEFAULT NULL,
  `nivel` char(1) DEFAULT NULL,
  PRIMARY KEY (`pcu_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1708 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `situacion`
--

DROP TABLE IF EXISTS `situacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `situacion` (
  `sit_id` int(11) NOT NULL DEFAULT '0',
  `nombre` varchar(50) DEFAULT NULL,
  `abreviatura` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`sit_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stock_cierre`
--

DROP TABLE IF EXISTS `stock_cierre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_cierre` (
  `sci_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `anio` int(11) DEFAULT NULL,
  `age_id` int(11) DEFAULT NULL,
  `pro_id` int(11) DEFAULT NULL,
  `stock` decimal(18,2) DEFAULT NULL,
  `stock_inservible` decimal(18,2) DEFAULT NULL,
  `precio` decimal(18,2) DEFAULT NULL,
  `precio_inservible` decimal(18,2) DEFAULT NULL,
  `observacion` varchar(250) DEFAULT NULL,
  `tipo_cambio` decimal(18,2) DEFAULT NULL,
  `mon_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`sci_id`),
  UNIQUE KEY `pk_sc` (`sci_id`),
  UNIQUE KEY `pk_mov` (`anio`,`age_id`,`pro_id`) USING BTREE,
  KEY `ind_stock` (`stock`)
) ENGINE=MyISAM AUTO_INCREMENT=13201050 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stock_producto`
--

DROP TABLE IF EXISTS `stock_producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_producto` (
  `stock_id` bigint(10) NOT NULL AUTO_INCREMENT,
  `age_id` int(11) DEFAULT NULL,
  `pro_id` bigint(10) DEFAULT NULL,
  `ume_id` int(11) DEFAULT NULL,
  `stock` decimal(18,2) DEFAULT NULL,
  `stock_inicial` decimal(18,2) DEFAULT NULL,
  `editado` char(1) DEFAULT NULL,
  `stock_inservible` decimal(18,2) DEFAULT NULL,
  `stock_inservible_inicial` decimal(18,2) DEFAULT NULL,
  `precio_costo` decimal(18,2) DEFAULT NULL,
  `precio_compra` decimal(18,2) DEFAULT NULL,
  `precio_venta` decimal(18,2) DEFAULT NULL,
  `stock_minimo` decimal(18,2) DEFAULT NULL,
  `utilidad` decimal(18,2) DEFAULT NULL,
  `desc1` decimal(18,2) DEFAULT NULL,
  `desc2` decimal(18,2) DEFAULT NULL,
  `desc3` decimal(18,2) DEFAULT NULL,
  `desc4` decimal(18,2) DEFAULT NULL,
  `ind_calculo` char(1) DEFAULT NULL,
  `ubicacion` varchar(20) DEFAULT NULL,
  `aplicacion` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`stock_id`),
  UNIQUE KEY `ind_stock` (`age_id`,`pro_id`) USING BTREE,
  KEY `fk_pro` (`pro_id`),
  KEY `ind_ume` (`ume_id`)
) ENGINE=MyISAM AUTO_INCREMENT=755767 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tiendas`
--

DROP TABLE IF EXISTS `tiendas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tiendas` (
  `tie_id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` char(6) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `ruc` char(20) NOT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  `tip_per` char(1) NOT NULL,
  `tip_doc` char(1) NOT NULL,
  `rep_leg` varchar(150) DEFAULT NULL,
  `valida_stock` char(1) DEFAULT NULL,
  PRIMARY KEY (`tie_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipo_cambio`
--

DROP TABLE IF EXISTS `tipo_cambio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_cambio` (
  `tic_id` int(10) NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `valor_compra` decimal(10,4) DEFAULT NULL,
  `valor_venta` decimal(10,4) DEFAULT NULL,
  PRIMARY KEY (`tic_id`)
) ENGINE=InnoDB AUTO_INCREMENT=489 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipo_movimiento`
--

DROP TABLE IF EXISTS `tipo_movimiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_movimiento` (
  `tmv_id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` char(5) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `indicador` char(1) DEFAULT NULL,
  PRIMARY KEY (`tmv_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipo_nota`
--

DROP TABLE IF EXISTS `tipo_nota`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_nota` (
  `tnt_id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` char(7) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `indicador` char(1) DEFAULT NULL,
  PRIMARY KEY (`tnt_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipo_pago`
--

DROP TABLE IF EXISTS `tipo_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_pago` (
  `tpa_id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` char(9) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `indicador` char(1) DEFAULT NULL,
  PRIMARY KEY (`tpa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ubigeo`
--

DROP TABLE IF EXISTS `ubigeo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ubigeo` (
  `ubg_id` int(10) DEFAULT NULL,
  `dep_id` int(10) DEFAULT NULL,
  `ciu_id` int(10) DEFAULT NULL,
  `dis_id` int(10) DEFAULT NULL,
  `nom_dep` varchar(100) DEFAULT NULL,
  `nom_ciu` varchar(100) DEFAULT NULL,
  `nom_dis` varchar(100) DEFAULT NULL,
  UNIQUE KEY `pk_ubi` (`ubg_id`) USING BTREE,
  UNIQUE KEY `ind_ubi` (`dep_id`,`ciu_id`,`dis_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unidad_medida`
--

DROP TABLE IF EXISTS `unidad_medida`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unidad_medida` (
  `ume_id` int(10) NOT NULL AUTO_INCREMENT,
  `codigo` char(4) DEFAULT NULL,
  `abreviatura` char(3) DEFAULT NULL,
  `descripcion` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ume_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `usr_id` int(11) NOT NULL AUTO_INCREMENT,
  `nombres` varchar(70) NOT NULL,
  `apellidos` varchar(70) NOT NULL,
  `cargo` varchar(70) DEFAULT NULL,
  `area` varchar(50) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `direccion` varchar(50) DEFAULT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(50) NOT NULL,
  `ust_id` int(11) NOT NULL,
  `usp_id` int(11) DEFAULT '0',
  `tie_id` int(11) DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  `acc_lun` char(17) DEFAULT NULL,
  `acc_mar` char(17) DEFAULT NULL,
  `acc_mie` char(17) DEFAULT NULL,
  `acc_jue` char(17) DEFAULT NULL,
  `acc_vie` char(17) DEFAULT NULL,
  `acc_sab` char(17) DEFAULT NULL,
  `acc_dom` char(17) DEFAULT NULL,
  PRIMARY KEY (`usr_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios_menu`
--

DROP TABLE IF EXISTS `usuarios_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios_menu` (
  `usm_id` int(10) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `alias` varchar(100) DEFAULT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  `url` varchar(150) DEFAULT NULL,
  `icono` varchar(50) DEFAULT NULL,
  `umo_id` int(11) NOT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  `orden` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`usm_id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios_modulo`
--

DROP TABLE IF EXISTS `usuarios_modulo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios_modulo` (
  `umo_id` int(10) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `icono` varchar(50) NOT NULL DEFAULT '0.png',
  `activo` int(1) NOT NULL DEFAULT '1',
  `orden` int(11) DEFAULT NULL,
  PRIMARY KEY (`umo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios_perfil`
--

DROP TABLE IF EXISTS `usuarios_perfil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios_perfil` (
  `usp_id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`usp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios_perfil_detalle`
--

DROP TABLE IF EXISTS `usuarios_perfil_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios_perfil_detalle` (
  `usd_id` int(11) NOT NULL AUTO_INCREMENT,
  `usm_id` int(11) NOT NULL,
  `READ` char(1) NOT NULL DEFAULT 'Y',
  `ADD` char(1) NOT NULL DEFAULT 'Y',
  `UPDATE` char(1) NOT NULL DEFAULT 'Y',
  `DELETE` char(1) NOT NULL DEFAULT 'Y',
  `usp_id` int(1) NOT NULL,
  PRIMARY KEY (`usd_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2632 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios_tipo`
--

DROP TABLE IF EXISTS `usuarios_tipo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios_tipo` (
  `ust_id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `activo` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ust_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'impoinga_inventory'
--

-- insufficient privileges to SHOW CREATE FUNCTION `uf_stock_producto`
-- does impoinga have permissions on mysql.proc?

