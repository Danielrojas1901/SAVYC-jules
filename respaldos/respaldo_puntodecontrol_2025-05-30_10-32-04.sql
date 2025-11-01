-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: seguridad2
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `seguridad2`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `seguridad2` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `seguridad2`;

--
-- Table structure for table `backup`
--

DROP TABLE IF EXISTS `backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `backup` (
  `cod_backup` int(11) NOT NULL AUTO_INCREMENT,
  `cod_config_backup` int(11) NOT NULL,
  `cod_usuario` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tipo` enum('manual','automatica') NOT NULL,
  `tamanio` float NOT NULL,
  PRIMARY KEY (`cod_backup`),
  KEY `configbackup` (`cod_config_backup`),
  KEY `usuariobackup` (`cod_usuario`),
  CONSTRAINT `configbackup` FOREIGN KEY (`cod_config_backup`) REFERENCES `config_backup` (`cod_config_backup`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `usuariobackup` FOREIGN KEY (`cod_usuario`) REFERENCES `usuarios` (`cod_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backup`
--

LOCK TABLES `backup` WRITE;
/*!40000 ALTER TABLE `backup` DISABLE KEYS */;
INSERT INTO `backup` VALUES (12,1,1,'Manuela_eliminar','Respaldo para probar eliminar','respaldos/Manuela_eliminar_2025-05-15_18-34-56.sql','2025-05-15 22:34:56','manual',0.089509);
/*!40000 ALTER TABLE `backup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bitacora`
--

DROP TABLE IF EXISTS `bitacora`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bitacora` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod_usuario` int(11) NOT NULL,
  `accion` varchar(255) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `detalles` text DEFAULT NULL,
  `modulo` varchar(220) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cod_usuario` (`cod_usuario`),
  CONSTRAINT `bitacora_ibfk_1` FOREIGN KEY (`cod_usuario`) REFERENCES `usuarios` (`cod_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=545 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bitacora`
--

LOCK TABLES `bitacora` WRITE;
/*!40000 ALTER TABLE `bitacora` DISABLE KEYS */;
INSERT INTO `bitacora` VALUES (542,1,'Acceso al sistema','2025-05-30 11:47:30','admin','Inicio'),(543,1,'Acceso a Usuarios','2025-05-30 11:48:50','','Usuarios'),(544,1,'Acceso a Ventas','2025-05-30 12:38:09','','Ventas');
/*!40000 ALTER TABLE `bitacora` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config_backup`
--

DROP TABLE IF EXISTS `config_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config_backup` (
  `cod_config_backup` int(11) NOT NULL AUTO_INCREMENT,
  `frecuencia` enum('diario','semanal','quincenal','mensual') NOT NULL,
  `modo` enum('ambos','automatico') NOT NULL,
  `retencion` int(11) NOT NULL,
  `hora` time NOT NULL,
  `dia` int(11) NOT NULL,
  `ult_respaldo` datetime DEFAULT NULL,
  `habilitado` int(11) NOT NULL,
  PRIMARY KEY (`cod_config_backup`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config_backup`
--

LOCK TABLES `config_backup` WRITE;
/*!40000 ALTER TABLE `config_backup` DISABLE KEYS */;
INSERT INTO `config_backup` VALUES (1,'','',5,'00:00:00',0,NULL,0);
/*!40000 ALTER TABLE `config_backup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modulos`
--

DROP TABLE IF EXISTS `modulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modulos` (
  `cod_modulo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`cod_modulo`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modulos`
--

LOCK TABLES `modulos` WRITE;
/*!40000 ALTER TABLE `modulos` DISABLE KEYS */;
INSERT INTO `modulos` VALUES (1,'producto'),(2,'inventario'),(3,'contabilidad'),(4,'compra'),(5,'venta'),(6,'cliente'),(7,'proveedor'),(8,'finanza'),(9,'reporte'),(10,'tesoreria'),(11,'gasto'),(12,'cuentas_pendiente'),(13,'seguridad'),(14,'config_producto'),(15,'config_finanza');
/*!40000 ALTER TABLE `modulos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permisos`
--

DROP TABLE IF EXISTS `permisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permisos` (
  `cod_crud` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(10) NOT NULL,
  PRIMARY KEY (`cod_crud`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permisos`
--

LOCK TABLES `permisos` WRITE;
/*!40000 ALTER TABLE `permisos` DISABLE KEYS */;
INSERT INTO `permisos` VALUES (1,'registrar'),(2,'consultar'),(3,'editar'),(4,'eliminar');
/*!40000 ALTER TABLE `permisos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_usuario`
--

DROP TABLE IF EXISTS `tipo_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_usuario` (
  `cod_tipo_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `rol` varchar(50) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_tipo_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_usuario`
--

LOCK TABLES `tipo_usuario` WRITE;
/*!40000 ALTER TABLE `tipo_usuario` DISABLE KEYS */;
INSERT INTO `tipo_usuario` VALUES (1,'Administrador',1),(2,'pruebaaaaaa',1),(3,'pruebaone',1),(6,'prueba',1),(7,'pruebatwo',1),(8,'manuela',0);
/*!40000 ALTER TABLE `tipo_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tpu_permisos`
--

DROP TABLE IF EXISTS `tpu_permisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tpu_permisos` (
  `cod_tipo_usuario` int(11) NOT NULL,
  `cod_modulo` int(11) NOT NULL,
  `cod_crud` int(11) DEFAULT NULL,
  KEY `cod_tipo_usuario` (`cod_tipo_usuario`),
  KEY `cod_permiso` (`cod_modulo`),
  KEY `tpu-crud` (`cod_crud`),
  CONSTRAINT `tpu-cod` FOREIGN KEY (`cod_tipo_usuario`) REFERENCES `tipo_usuario` (`cod_tipo_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tpu-crud` FOREIGN KEY (`cod_crud`) REFERENCES `permisos` (`cod_crud`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tpu-modulo` FOREIGN KEY (`cod_modulo`) REFERENCES `modulos` (`cod_modulo`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tpu_permisos`
--

LOCK TABLES `tpu_permisos` WRITE;
/*!40000 ALTER TABLE `tpu_permisos` DISABLE KEYS */;
INSERT INTO `tpu_permisos` VALUES (2,1,1),(2,1,3),(2,2,3),(2,3,1),(2,3,3),(2,4,3),(2,5,1),(2,5,3),(2,5,4),(2,7,3),(2,9,3),(1,1,1),(1,1,3),(1,1,4),(1,2,1),(1,2,3),(1,2,4),(1,3,1),(1,3,3),(1,3,4),(1,4,1),(1,4,3),(1,4,4),(1,5,1),(1,5,3),(1,5,4),(1,6,1),(1,6,3),(1,6,4),(1,7,1),(1,7,3),(1,7,4),(1,8,1),(1,8,3),(1,8,4),(1,9,1),(1,9,3),(1,9,4),(1,10,1),(1,10,3),(1,10,4),(1,11,1),(1,11,3),(1,11,4),(1,12,1),(1,12,3),(1,12,4),(1,13,1),(1,13,3),(1,13,4),(1,14,1),(1,14,3),(1,14,4),(1,15,1),(1,15,3),(1,15,4),(3,1,1),(3,1,3),(3,2,1),(3,4,1),(3,5,1),(3,6,3),(3,6,4),(3,14,1),(6,1,2),(6,3,2),(6,5,2),(6,7,2),(7,5,2),(8,1,2),(8,2,2),(8,3,2),(1,1,2),(1,2,2),(1,3,2),(1,4,2),(1,5,2),(1,6,2),(1,7,2),(1,8,2),(1,9,2),(1,10,2),(1,11,2),(1,12,2),(1,13,2),(1,14,2),(1,15,2);
/*!40000 ALTER TABLE `tpu_permisos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `cod_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `user` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `cod_tipo_usuario` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_usuario`),
  UNIQUE KEY `user` (`user`),
  KEY `usuario-tipousuario` (`cod_tipo_usuario`),
  CONSTRAINT `usuario-tipousuario` FOREIGN KEY (`cod_tipo_usuario`) REFERENCES `tipo_usuario` (`cod_tipo_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Administrador','admin','$2y$10$.nbh0vwGWNkBgsVzkBSoYurftn9Mg.TLYkxmK32KhMKOzaTjaRS3.',1,1),(2,'jorges','jorge','$2y$10$wRFU5jEfVEpp/jXR0OQ0YuycA5JvHQilBkXSwfHBds164nz1doz3e',1,1),(16,'Manuela ','manuela','$2y$10$gZ84jSgivO442KA.ltxwheZpFPGce9c21apbK4AiYc.xHV8/XNDyq',1,1);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Current Database: `savyc+v1`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `savyc+v1` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `savyc+v1`;

--
-- Table structure for table `analisis_rentabilidad`
--

DROP TABLE IF EXISTS `analisis_rentabilidad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `analisis_rentabilidad` (
  `cod_analisis` int(11) NOT NULL AUTO_INCREMENT,
  `cod_producto` int(11) NOT NULL,
  `mes` date NOT NULL,
  `ventas_totales` int(11) NOT NULL,
  `costo_ventas` decimal(10,2) DEFAULT NULL,
  `gastos` decimal(10,2) NOT NULL,
  `margen_bruto` decimal(10,2) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`cod_analisis`),
  UNIQUE KEY `unq_producto_mes` (`cod_producto`,`mes`),
  CONSTRAINT `analisis_rentabilidad_ibfk_1` FOREIGN KEY (`cod_producto`) REFERENCES `productos` (`cod_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `analisis_rentabilidad`
--

LOCK TABLES `analisis_rentabilidad` WRITE;
/*!40000 ALTER TABLE `analisis_rentabilidad` DISABLE KEYS */;
/*!40000 ALTER TABLE `analisis_rentabilidad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asientos_contables`
--

DROP TABLE IF EXISTS `asientos_contables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asientos_contables` (
  `cod_asiento` int(11) NOT NULL AUTO_INCREMENT,
  `cod_mov` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  `total` decimal(18,2) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_asiento`),
  KEY `cod_mov` (`cod_mov`),
  CONSTRAINT `asientos_contables_ibfk_1` FOREIGN KEY (`cod_mov`) REFERENCES `movimientos` (`cod_mov`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=176 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asientos_contables`
--

LOCK TABLES `asientos_contables` WRITE;
/*!40000 ALTER TABLE `asientos_contables` DISABLE KEYS */;
INSERT INTO `asientos_contables` VALUES (3,23,'2025-05-19 16:31:42','venta al contado #33',426.42,1),(4,23,'2025-05-19 16:31:42','venta al contado #33',571.40,1),(5,18,'2025-05-19 18:22:42','venta al contado #32',497.55,1),(6,18,'2025-05-19 18:22:42','venta al contado #32',589.92,1),(7,15,'2025-05-19 20:46:20','venta a credito #30',81.29,1),(19,16,'2025-05-19 22:11:29','venta a credito #31',85.28,1),(20,16,'2025-05-19 22:11:29','venta a credito #31',98.92,1),(23,24,'2025-05-19 23:17:31','venta al contado #34',540.13,1),(24,24,'2025-05-19 23:17:31','venta al contado #34',660.10,1),(25,19,'2025-05-20 02:48:27','venta al contado #10',213.21,1),(26,19,'2025-05-20 02:48:27','venta al contado #10',10.00,1),(27,12,'2025-05-20 02:49:08','venta a credito #27',677.43,1),(28,12,'2025-05-20 02:49:08','venta a credito #27',907.76,1),(29,10,'2025-05-20 02:49:46','venta al contado #25',270.97,1),(30,10,'2025-05-20 02:49:46','venta al contado #25',5.09,1),(31,13,'2025-05-20 02:54:37','venta a credito #28',852.84,1),(32,13,'2025-05-20 02:54:37','venta a credito #28',989.21,1),(33,25,'2025-05-20 10:06:03','venta al contado #35',852.84,1),(34,25,'2025-05-20 10:06:03','venta al contado #35',1142.81,1),(35,26,'2025-05-20 11:36:36','venta a credito #36',613.39,1),(36,26,'2025-05-20 11:36:36','venta a credito #36',821.94,1),(41,27,'2025-05-21 00:34:18','venta al contado #37',65.00,1),(42,27,'2025-05-21 00:34:18','venta al contado #37',100.00,1),(47,21,'2025-05-22 15:43:56','compra al contado #10',20847.20,1),(48,29,'2025-05-22 15:56:58','compra al contado #12',1215.00,1),(49,22,'2025-05-22 16:12:49','compra a credito #11',4775.90,1),(50,8,'2025-05-22 21:04:03','venta al contado #23',1757.50,1),(51,8,'2025-05-22 21:04:03','venta al contado #23',26.46,1),(52,34,'2025-05-22 21:09:48','venta al contado #41',22.75,1),(53,34,'2025-05-22 21:09:48','venta al contado #41',40.00,1),(54,30,'2025-05-22 21:43:26','venta a credito #40',65.00,1),(55,30,'2025-05-22 21:43:26','venta a credito #40',87.10,1),(56,28,'2025-05-22 21:47:23','venta a credito #39',13.00,1),(57,28,'2025-05-22 21:47:23','venta a credito #39',17.42,1),(58,36,'2025-05-22 22:42:40','venta al contado #42',421.35,1),(59,36,'2025-05-22 22:42:40','venta al contado #42',600.00,1),(60,31,'2025-05-22 23:43:12','pago recibido de venta #39',20.00,1),(63,38,'2025-05-23 01:13:48','pago recibido de venta #29',956.57,1),(64,39,'2025-05-23 01:19:25','pago recibido de venta #30',120.00,1),(65,40,'2025-05-23 01:20:50','pago recibido de venta #40',10.00,1),(66,37,'2025-05-23 01:20:58','pago recibido de venta #40',10.00,1),(67,33,'2025-05-23 01:22:36','pago recibido de venta #40',53.00,1),(68,32,'2025-05-24 16:25:29','pago recibido de venta #40',12.00,1),(69,48,'2025-05-25 03:36:50','ajuste por carga de inventario #16',2300.00,1),(72,47,'2025-05-25 03:44:53','ajuste por carga de inventario #15',2212.00,1),(73,49,'2025-05-25 03:55:11','ajuste por carga de inventario #17',2550.94,1),(74,51,'2025-05-25 04:19:30','ajuste por carga de inventario #21',2464.80,1),(75,51,'2025-05-25 04:25:16','ajuste por carga de inventario #21',2464.80,1),(76,51,'2025-05-25 04:34:46','ajuste por carga de inventario #21',2464.80,1),(77,52,'2025-05-25 04:34:52','ajuste por descarga de inventario #2',1215.00,1),(78,53,'2025-05-25 04:43:36','ajuste por descarga de inventario #3',2562.20,1),(80,57,'2025-05-25 19:49:18','compra a credito #15',100.00,1),(82,60,'2025-05-25 19:52:52','venta a credito #43',83.39,1),(83,60,'2025-05-25 19:52:52','venta a credito #43',100.07,1),(84,62,'2025-05-25 19:52:56','pago recibido de venta #43',70.00,1),(85,61,'2025-05-25 19:53:14','pago recibido de venta #43',40.07,1),(89,65,'2025-05-25 21:22:15','pago recibido de venta #44',70.00,1),(90,64,'2025-05-25 21:22:20','pago recibido de venta #44',40.78,1),(91,68,'2025-05-25 21:38:05','pago recibido de venta #45',70.00,1),(92,67,'2025-05-25 21:38:07','pago recibido de venta #45',40.07,1),(96,58,'2025-05-26 00:12:28','pago emitido de compra #15',40.00,1),(97,69,'2025-05-26 00:13:20','pago emitido de compra #14',58.43,1),(98,59,'2025-05-26 00:14:05','pago emitido de compra #15',70.00,1),(99,56,'2025-05-26 00:14:56','pago emitido de compra #7',57.00,1),(100,55,'2025-05-26 00:15:20','pago emitido de compra #14',324.00,1),(101,66,'2025-05-26 01:13:37','venta a credito #45',83.39,1),(102,66,'2025-05-26 01:13:37','venta a credito #45',100.07,1),(103,54,'2025-05-26 01:13:37','pago emitido de compra #14',100.00,1),(104,63,'2025-05-26 01:22:58','venta a credito #44',75.21,1),(105,63,'2025-05-26 01:22:58','venta a credito #44',100.78,1),(106,46,'2025-05-26 01:22:58','ajuste por carga de inventario #14',6129.04,1),(107,44,'2025-05-26 01:39:50','compra a credito #14',6320.00,1),(108,42,'2025-05-26 01:39:50','compra al contado #8',20847.20,1),(109,41,'2025-05-26 01:39:50','pago emitido de compra #13',300.00,1),(110,14,'2025-05-26 01:39:50','venta a credito #29',0.00,1),(111,14,'2025-05-26 01:39:50','venta a credito #29',956.57,1),(112,73,'2025-05-26 14:34:27','pago recibido de venta #26',1137.60,1),(113,72,'2025-05-26 14:34:27','pago recibido de venta #27',907.76,1),(114,71,'2025-05-26 14:34:27','pago recibido de venta #28',10000.00,1),(115,75,'2025-05-26 20:47:59','pago emitido de compra #14',20.00,1),(116,74,'2025-05-26 20:47:59','venta al contado #46',555.47,1),(117,74,'2025-05-26 20:47:59','venta al contado #46',948.80,1),(118,76,'2025-05-27 12:31:25','pago recibido de venta #19',1.37,1),(119,70,'2025-05-27 12:31:25','pago recibido de venta #40',5.00,1),(120,45,'2025-05-27 12:31:25','ajuste por carga de inventario #13',1280.00,1),(121,43,'2025-05-27 12:31:25','compra al contado #5',55.00,1),(122,35,'2025-05-27 12:31:51','compra a credito #13',500.00,1),(123,78,'2025-05-27 23:52:05','venta al contado #48',379.04,1),(124,78,'2025-05-27 23:52:05','venta al contado #48',480.00,1),(125,77,'2025-05-27 23:52:05','venta a credito #47',75.84,1),(126,77,'2025-05-27 23:52:05','venta a credito #47',101.63,1),(127,20,'2025-05-27 23:52:05','compra al contado #6',96.90,1),(128,11,'2025-05-27 23:52:05','venta a credito #26',0.00,1),(129,11,'2025-05-27 23:52:05','venta a credito #26',989.21,1),(130,9,'2025-05-27 23:52:05','venta a credito #24',0.00,1),(131,9,'2025-05-27 23:52:05','venta a credito #24',14.74,1),(132,7,'2025-05-27 23:52:05','venta al contado #22',0.00,1),(133,7,'2025-05-27 23:52:05','venta al contado #22',9.58,1),(134,6,'2025-05-27 23:52:05','venta al contado #21',0.00,1),(135,6,'2025-05-27 23:52:05','venta al contado #21',4.00,1),(136,5,'2025-05-27 23:52:05','venta al contado #20',0.00,1),(137,5,'2025-05-27 23:52:05','venta al contado #20',25.00,1),(138,4,'2025-05-27 23:52:05','venta a credito #19',0.00,1),(139,4,'2025-05-27 23:52:05','venta a credito #19',6.37,1),(140,84,'2025-05-28 02:43:33','compra al contado #18',400.00,1),(141,85,'2025-05-28 12:25:47','venta al contado #49',200.00,1),(142,85,'2025-05-28 12:25:47','venta al contado #49',288.00,1),(143,79,'2025-05-28 14:32:50','compra a credito #17',455.00,1),(144,87,'2025-05-28 14:35:40','compra al contado #19',2000.00,1),(145,80,'2025-05-28 15:14:27','gasto pospago #5',100.00,1),(146,88,'2025-05-28 15:14:42','gasto al contado #7',150.00,1),(147,82,'2025-05-28 15:16:01','gasto pospago #3',298.00,1),(148,81,'2025-05-28 15:16:01','gasto prepago #4',100.00,1),(149,83,'2025-05-28 16:02:35','gasto al contado #6',2200.00,1),(150,91,'2025-05-28 16:08:37','gasto pospago #11',983.00,1),(151,89,'2025-05-28 16:08:37','gasto prepago #9',500.00,1),(152,93,'2025-05-28 19:29:47','pago recibido de venta #50',191.62,1),(153,92,'2025-05-28 19:29:47','venta al contado #53',71.37,1),(154,92,'2025-05-28 19:29:47','venta al contado #53',100.00,1),(155,95,'2025-05-28 20:52:20','pago emitido de gasto #10',479.05,1),(156,99,'2025-05-28 20:53:32','pago emitido de gasto #12',287.43,1),(157,98,'2025-05-28 20:56:21','gasto pospago #12',200.00,1),(158,97,'2025-05-28 20:56:21','pago emitido de gasto #10',285.95,1),(159,96,'2025-05-28 20:56:21','pago emitido de gasto #8',287.43,1),(160,94,'2025-05-28 20:56:21','pago emitido de compra #17',285.00,1),(161,90,'2025-05-28 20:56:21','gasto pospago #8',243.00,1),(162,86,'2025-05-28 20:56:21','venta a credito #50',189.52,1),(163,86,'2025-05-28 20:56:21','venta a credito #50',227.43,1),(164,104,'2025-05-29 03:52:48','compra al contado #21',3424.30,1),(165,103,'2025-05-29 03:52:48','ajuste por descarga de inventario #4',68.00,1),(166,102,'2025-05-29 03:52:48','pago emitido de compra #20',195.81,1),(167,101,'2025-05-29 03:52:48','compra a credito #20',170.00,1),(168,100,'2025-05-29 03:52:48','ajuste por carga de inventario #22',400.00,1),(169,105,'2025-05-29 03:55:03','gasto pospago #13',632.00,1),(170,106,'2025-05-29 03:55:14','pago emitido de gasto #13',700.00,1),(171,108,'2025-05-29 23:36:23','venta al contado #57',771.00,1),(172,108,'2025-05-29 23:36:23','venta al contado #57',1061.83,1),(173,109,'2025-05-30 07:07:31','venta al contado #56',202.24,1),(174,109,'2025-05-30 07:07:31','venta al contado #56',289.59,1),(175,107,'2025-05-30 07:07:31','pago emitido de gasto #3',10.00,1);
/*!40000 ALTER TABLE `asientos_contables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banco`
--

DROP TABLE IF EXISTS `banco`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banco` (
  `cod_banco` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_banco` varchar(50) NOT NULL,
  PRIMARY KEY (`cod_banco`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banco`
--

LOCK TABLES `banco` WRITE;
/*!40000 ALTER TABLE `banco` DISABLE KEYS */;
INSERT INTO `banco` VALUES (1,'Banco Provincial'),(2,'Banco Mercanti'),(4,'Banco Venezuela');
/*!40000 ALTER TABLE `banco` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `caja`
--

DROP TABLE IF EXISTS `caja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `caja` (
  `cod_caja` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `saldo` decimal(10,2) NOT NULL,
  `cod_divisas` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_caja`),
  KEY `cod_divisas` (`cod_divisas`),
  CONSTRAINT `caja_ibfk_1` FOREIGN KEY (`cod_divisas`) REFERENCES `divisas` (`cod_divisa`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caja`
--

LOCK TABLES `caja` WRITE;
/*!40000 ALTER TABLE `caja` DISABLE KEYS */;
INSERT INTO `caja` VALUES (5,'Caja $',1254.24,1,1),(6,'caja dos',174.96,2,1),(7,'caja prueba',220.00,3,1),(8,'caja iris',50.00,4,1),(9,'caja angela',1000.00,1,1);
/*!40000 ALTER TABLE `caja` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cambio_divisa`
--

DROP TABLE IF EXISTS `cambio_divisa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cambio_divisa` (
  `cod_cambio` int(11) NOT NULL AUTO_INCREMENT,
  `cod_divisa` int(11) NOT NULL,
  `tasa` decimal(10,2) NOT NULL,
  `fecha` date NOT NULL,
  PRIMARY KEY (`cod_cambio`),
  KEY `cambiodivisa-divisa` (`cod_divisa`),
  CONSTRAINT `cambiodivisa-divisa` FOREIGN KEY (`cod_divisa`) REFERENCES `divisas` (`cod_divisa`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cambio_divisa`
--

LOCK TABLES `cambio_divisa` WRITE;
/*!40000 ALTER TABLE `cambio_divisa` DISABLE KEYS */;
INSERT INTO `cambio_divisa` VALUES (1,1,1.00,'0000-00-00'),(2,2,67.10,'2025-03-17'),(3,2,67.10,'2025-03-12'),(4,2,67.10,'2025-03-11'),(6,2,65.64,'2025-03-18'),(7,2,64.80,'2025-03-10'),(8,2,63.43,'2025-03-09'),(9,2,67.63,'2025-03-19'),(10,2,70.59,'2025-04-03'),(11,3,10.00,'2025-04-07'),(12,2,86.11,'2025-04-25'),(13,3,95.00,'2025-04-07'),(14,4,115.00,'2025-04-28'),(15,5,105.00,'2025-04-29'),(16,2,86.85,'2025-04-30'),(17,3,10.00,'2025-04-07'),(18,3,95.00,'2025-04-07'),(19,4,115.00,'2025-04-28'),(20,5,105.00,'2025-04-29'),(21,2,94.76,'2025-05-18'),(22,3,95.00,'2025-04-07'),(23,4,115.00,'2025-04-28'),(24,5,105.00,'2025-04-29'),(25,2,94.76,'2025-05-18'),(26,3,95.00,'2025-04-07'),(27,4,115.00,'2025-04-28'),(28,5,105.00,'2025-04-29'),(29,2,94.76,'2025-05-18'),(30,3,95.00,'2025-04-07'),(31,4,115.00,'2025-04-28'),(32,5,105.00,'2025-04-29'),(33,2,96.00,'2025-05-27'),(34,3,95.00,'2025-04-07'),(35,4,115.00,'2025-04-28'),(36,5,105.00,'2025-04-29'),(37,2,95.81,'2025-05-28'),(38,3,95.00,'2025-04-07'),(39,4,115.00,'2025-04-28'),(40,5,105.00,'2025-04-29'),(41,2,96.53,'2025-05-29'),(42,3,95.00,'2025-04-07'),(43,4,115.00,'2025-04-28'),(44,5,105.00,'2025-04-29');
/*!40000 ALTER TABLE `cambio_divisa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carga`
--

DROP TABLE IF EXISTS `carga`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carga` (
  `cod_carga` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` datetime NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_carga`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carga`
--

LOCK TABLES `carga` WRITE;
/*!40000 ALTER TABLE `carga` DISABLE KEYS */;
INSERT INTO `carga` VALUES (1,'2025-04-29 22:20:42','carga prueba',0.00,1),(2,'2025-05-15 02:53:38','charcuteria ',0.00,1),(3,'2025-05-15 02:53:38','charcuteria ',0.00,1),(4,'2025-05-15 02:53:38','charcuteria ',0.00,1),(5,'2025-05-15 02:53:38','charcuteria ',0.00,1),(6,'2025-05-15 02:53:38','charcuteria ',0.00,1),(7,'2025-05-15 02:53:38','charcuteria ',0.00,1),(8,'2025-05-15 02:53:38','charcuteria ',0.00,1),(9,'2025-05-15 02:53:38','charcuteria ',0.00,1),(10,'2025-05-15 02:53:38','charcuteria ',0.00,1),(11,'2025-05-15 03:17:37','venta',0.00,1),(13,'2025-05-22 02:32:57','prueba costo',1280.00,1),(14,'2025-05-22 02:40:39','costo dos',6129.04,1),(15,'2025-05-25 03:22:41','carga por movimiento',2212.00,1),(16,'2025-05-25 03:30:36','prueba para alerta',2300.00,1),(17,'2025-05-25 03:33:42','pruba para generar movimiento',2550.94,1),(18,'2025-05-25 03:56:48','carga por sueno',1453.60,1),(19,'2025-05-25 03:59:17','carga',2300.00,1),(20,'2025-05-25 04:04:50','carga por ajuste',1610.00,1),(21,'2025-05-25 04:17:44','AJUSTE DE INVENTARIO MAYO',2464.80,1),(22,'2025-05-29 00:00:38','carga de prueba',400.00,1);
/*!40000 ALTER TABLE `carga` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categoria_gasto`
--

DROP TABLE IF EXISTS `categoria_gasto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categoria_gasto` (
  `cod_cat_gasto` int(11) NOT NULL AUTO_INCREMENT,
  `cod_tipo_gasto` int(11) NOT NULL,
  `cod_frecuencia` int(11) DEFAULT NULL,
  `cod_naturaleza` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `fecha` date NOT NULL,
  `status_cat_gasto` int(11) NOT NULL,
  PRIMARY KEY (`cod_cat_gasto`),
  KEY `cod_tipo_gasto` (`cod_tipo_gasto`),
  KEY `cod_frecuencia` (`cod_frecuencia`),
  KEY `cod_naturaleza` (`cod_naturaleza`),
  CONSTRAINT `categoria_gasto_ibfk_1` FOREIGN KEY (`cod_tipo_gasto`) REFERENCES `tipo_gasto` (`cod_tipo_gasto`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `categoria_gasto_ibfk_2` FOREIGN KEY (`cod_frecuencia`) REFERENCES `frecuencia_gasto` (`cod_frecuencia`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `categoria_gasto_ibfk_3` FOREIGN KEY (`cod_naturaleza`) REFERENCES `naturaleza_gasto` (`cod_naturaleza`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoria_gasto`
--

LOCK TABLES `categoria_gasto` WRITE;
/*!40000 ALTER TABLE `categoria_gasto` DISABLE KEYS */;
INSERT INTO `categoria_gasto` VALUES (3,4,4,1,'inter','2025-05-21',1),(4,4,NULL,2,'varios','2025-05-24',1),(5,3,NULL,2,'gasto de bolsas','2025-05-28',1);
/*!40000 ALTER TABLE `categoria_gasto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias` (
  `cod_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'queso',1),(2,'Jamones',1),(4,'viveres',1),(5,'perueba',1),(6,'jmeter',1),(7,'nombre',1),(8,'Lácteos',1),(9,'Charcutería',1),(10,'Bebidas',1),(11,'Granos',1),(12,'Aseo',1),(13,'Verduras',1),(14,'Congelados',1),(15,'Panadería',1),(16,'Carnes',1),(17,'Cereales',1);
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes` (
  `cod_cliente` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) NOT NULL,
  `apellido` varchar(80) NOT NULL,
  `cedula_rif` varchar(12) NOT NULL,
  `telefono` varchar(12) DEFAULT NULL,
  `email` varchar(70) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,'generico','perez','12345678','','','',1),(2,'daniel','rojas','26779660','04245645108','danielrojas1901@gmail.com','av.florencio jimenez, parque residencial araguaney',1),(3,'Manuela','Mujica','28516209','12453145','manuelaalejandra.mujica@gmail.com','asdasda',1),(7,'generico','generico','1',NULL,NULL,NULL,1);
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compras`
--

DROP TABLE IF EXISTS `compras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `compras` (
  `cod_compra` int(11) NOT NULL AUTO_INCREMENT,
  `cod_prov` int(11) NOT NULL,
  `condicion_pago` enum('contado','credito') NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `impuesto_total` decimal(10,2) NOT NULL,
  `fecha` date NOT NULL,
  `descuento` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_compra`),
  KEY `compras-proveedores` (`cod_prov`),
  CONSTRAINT `compras-proveedores` FOREIGN KEY (`cod_prov`) REFERENCES `proveedores` (`cod_prov`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compras`
--

LOCK TABLES `compras` WRITE;
/*!40000 ALTER TABLE `compras` DISABLE KEYS */;
INSERT INTO `compras` VALUES (5,1,'contado',NULL,55.00,55.00,0.00,'2025-04-29',NULL,3),(6,1,'contado',NULL,96.90,96.90,0.00,'2025-05-15',NULL,3),(7,1,'credito','2025-05-24',12193.70,12193.70,0.00,'2025-05-17',NULL,2),(8,1,'contado',NULL,20847.20,20847.20,0.00,'2025-05-18',NULL,3),(9,1,'contado',NULL,20847.20,20847.20,0.00,'2025-05-18',NULL,2),(10,1,'contado',NULL,20847.20,20847.20,0.00,'2025-05-18',NULL,3),(11,1,'credito','2025-05-20',4775.90,4775.90,0.00,'2025-05-19',NULL,3),(12,1,'contado',NULL,1215.00,1215.00,0.00,'2025-05-20',NULL,3),(13,1,'credito','2025-05-29',500.00,500.00,0.00,'2025-05-22',NULL,3),(14,1,'credito','2025-05-31',6320.00,6320.00,0.00,'2025-05-24',NULL,2),(15,1,'credito','2025-06-01',100.00,100.00,0.00,'2025-05-25',NULL,3),(16,1,'contado',NULL,3620.91,3620.91,0.00,'2025-05-27',NULL,1),(17,1,'credito','2025-05-28',455.00,455.00,0.00,'2025-05-28',NULL,2),(18,1,'contado',NULL,400.00,400.00,0.00,'2025-05-28',NULL,3),(19,1,'contado',NULL,1615.00,1615.00,0.00,'2025-05-28',NULL,3),(20,1,'credito','2025-06-26',170.00,170.00,0.00,'2025-05-29',NULL,3),(21,1,'contado',NULL,3424.30,3424.30,0.00,'2025-05-29',NULL,3);
/*!40000 ALTER TABLE `compras` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_compras_update_status
AFTER UPDATE ON compras
FOR EACH ROW
BEGIN
    -- Solo ejecutar si cambió el status
    IF OLD.status <> NEW.status THEN
        CALL R_movimiento_operacion(NEW.cod_compra, 2); -- 2 = tipo_operacion para compras
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `conciliacion`
--

DROP TABLE IF EXISTS `conciliacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conciliacion` (
  `cod_conciliacion` int(11) NOT NULL,
  `url` varchar(200) NOT NULL,
  `fecha` datetime NOT NULL,
  `cod_cuenta_bancaria` int(11) NOT NULL,
  KEY `cod_cuenta_bancaria` (`cod_cuenta_bancaria`),
  CONSTRAINT `conciliacion_ibfk_1` FOREIGN KEY (`cod_cuenta_bancaria`) REFERENCES `cuenta_bancaria` (`cod_cuenta_bancaria`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conciliacion`
--

LOCK TABLES `conciliacion` WRITE;
/*!40000 ALTER TABLE `conciliacion` DISABLE KEYS */;
/*!40000 ALTER TABLE `conciliacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `condicion_pagoe`
--

DROP TABLE IF EXISTS `condicion_pagoe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `condicion_pagoe` (
  `cod_condicion` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_condicion` varchar(50) NOT NULL,
  PRIMARY KEY (`cod_condicion`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `condicion_pagoe`
--

LOCK TABLES `condicion_pagoe` WRITE;
/*!40000 ALTER TABLE `condicion_pagoe` DISABLE KEYS */;
INSERT INTO `condicion_pagoe` VALUES (1,'prepago'),(2,'pospago'),(3,'a credito'),(4,'al contado');
/*!40000 ALTER TABLE `condicion_pagoe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `control`
--

DROP TABLE IF EXISTS `control`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `control` (
  `cod_control` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_apertura` datetime NOT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `monto_apertura` decimal(10,2) NOT NULL,
  `monto_cierre` decimal(10,2) DEFAULT NULL,
  `cod_caja` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_control`),
  KEY `cod_caja` (`cod_caja`),
  CONSTRAINT `control_ibfk_1` FOREIGN KEY (`cod_caja`) REFERENCES `caja` (`cod_caja`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `control`
--

LOCK TABLES `control` WRITE;
/*!40000 ALTER TABLE `control` DISABLE KEYS */;
INSERT INTO `control` VALUES (1,'2025-05-20 12:19:00','0000-00-00 00:00:00',10.00,0.00,5,1),(2,'2025-05-20 12:28:00',NULL,10.00,NULL,7,1),(3,'2025-05-20 12:31:00',NULL,10.00,NULL,8,1),(4,'2025-05-20 12:46:00',NULL,10.00,NULL,6,1),(5,'2025-05-26 10:29:00',NULL,1124.97,NULL,5,1);
/*!40000 ALTER TABLE `control` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cuenta_bancaria`
--

DROP TABLE IF EXISTS `cuenta_bancaria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cuenta_bancaria` (
  `cod_cuenta_bancaria` int(11) NOT NULL AUTO_INCREMENT,
  `cod_banco` int(11) NOT NULL,
  `cod_tipo_cuenta` int(11) NOT NULL,
  `numero_cuenta` varchar(20) NOT NULL,
  `saldo` decimal(10,2) NOT NULL,
  `cod_divisa` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_cuenta_bancaria`),
  KEY `cod_banco` (`cod_banco`),
  KEY `cod_tipo_cuenta` (`cod_tipo_cuenta`),
  KEY `cod_divisa` (`cod_divisa`),
  CONSTRAINT `cuenta_bancaria_ibfk_1` FOREIGN KEY (`cod_banco`) REFERENCES `banco` (`cod_banco`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cuenta_bancaria_ibfk_2` FOREIGN KEY (`cod_tipo_cuenta`) REFERENCES `tipo_cuenta` (`cod_tipo_cuenta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cuenta_bancaria_ibfk_3` FOREIGN KEY (`cod_divisa`) REFERENCES `divisas` (`cod_divisa`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=515 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuenta_bancaria`
--

LOCK TABLES `cuenta_bancaria` WRITE;
/*!40000 ALTER TABLE `cuenta_bancaria` DISABLE KEYS */;
INSERT INTO `cuenta_bancaria` VALUES (1,1,2,'11111111111111111111',20025.20,1,1),(2,1,1,'13333333333333333333',1217.64,1,1),(3,4,1,'0108-234213212',120.00,2,1),(4,1,2,'12344444444564',50.00,3,1),(5,1,1,'57567744586695391724',628.51,4,1),(6,2,1,'18799403559093312029',447.36,1,1),(7,2,1,'75877511386454039994',1022.45,5,1),(8,1,1,'07782220015296829869',424.39,1,1),(9,1,1,'56436220566221773988',635.89,4,1),(10,2,1,'55546197188561237574',1150.27,2,1),(11,2,1,'27255641503051589274',631.67,2,1),(12,2,2,'76242015054406917046',1184.91,4,1),(13,2,1,'51396363967222041906',703.88,5,1),(14,2,1,'24833765851522608005',1195.58,5,1),(15,1,1,'99203437469325386527',1242.05,5,1),(16,1,2,'88347072447298962494',764.13,4,1),(17,2,2,'37104033250582712309',25.74,4,1),(19,1,2,'05973337900001462680',969.85,3,1),(20,2,1,'16313175190407454517',201.15,3,1),(25,1,2,'14804391128157304220',844.00,2,1),(26,2,2,'82008358546590806060',472.54,5,1),(27,2,1,'61895226842493973555',780.91,3,1),(28,2,1,'84002980913393078683',380.78,3,1),(29,2,1,'85777504154750881237',562.93,4,1),(30,2,2,'17590610540808029331',758.65,3,1),(31,1,1,'77090331320374128004',529.41,4,1),(32,2,2,'54463407295232346103',1209.90,5,1),(33,1,2,'97668166590827197324',1438.44,5,1),(35,2,1,'78427872941511555129',758.17,3,1),(36,2,1,'26188887745612582234',916.09,4,1),(37,1,1,'19503749564051320512',764.57,3,1),(39,2,2,'28606176578419744011',372.21,3,1),(41,2,2,'46259035997436652472',738.27,5,1),(42,1,2,'83964868851431953959',299.40,5,1),(43,1,1,'86856701251340406903',620.90,4,1),(45,1,2,'72637077951116888726',780.50,3,1),(46,2,2,'89694632116866769656',196.05,4,1),(48,2,2,'30428122322325438162',1430.62,1,1),(49,2,1,'48632299656546575224',334.73,1,1),(50,1,2,'41449549646902755248',706.87,4,1),(53,2,1,'96813148529415363786',798.66,4,1),(54,2,1,'18314004334939268837',1343.10,4,1),(55,2,2,'77389313191872460762',883.63,2,1),(56,2,1,'21119860017921002290',163.38,4,1),(58,2,1,'89033817128888249712',482.83,3,1),(61,1,1,'17338336645416324858',287.00,5,1),(65,2,1,'19726629710333333661',264.06,2,1),(67,1,2,'58520530373867704297',831.61,5,1),(68,2,1,'64035030990598742755',1191.60,3,1),(72,1,2,'22675861956511243136',429.33,3,1),(75,2,1,'57009889278870241050',1136.81,3,1),(76,1,1,'50523495812137057359',661.30,1,1),(77,1,1,'90317804274988849479',155.88,1,1),(78,2,1,'00309865137958561344',267.90,3,1),(80,2,1,'29249810309372046099',1404.77,1,1),(81,1,2,'22012454093789199680',1242.23,2,1),(82,2,1,'31556182394758637919',135.23,3,1),(84,2,2,'95968966156028522785',1432.42,4,1),(85,2,2,'51080042202639903379',614.56,5,1),(86,1,1,'89296837401561783642',1402.30,3,1),(88,1,2,'19812260315721035182',616.11,4,1),(89,1,2,'82102247458972919377',621.00,3,1),(91,2,1,'67318538463221308429',162.46,2,1),(92,1,2,'50679880584210645274',1286.84,4,1),(93,2,2,'38156170631931939190',872.80,3,1),(94,1,1,'12025532339308754377',523.61,5,1),(95,1,2,'14193392216791293547',463.61,3,1),(96,2,1,'80507490309442055211',559.96,2,1),(98,1,2,'00689267543012166187',926.97,2,1),(100,2,2,'00551777142910918698',114.72,1,1),(101,1,1,'77489510033321329113',381.98,3,1),(102,2,1,'41610736945492027484',211.33,1,1),(103,2,2,'10927689432779987307',1150.19,2,1),(104,1,1,'57375403048230409113',1111.12,3,1),(105,1,2,'61471724504484658418',809.05,2,1),(107,2,2,'36107961594691344422',548.18,3,1),(108,1,1,'72325747692981659833',738.92,3,1),(109,1,2,'02987959508302731392',1406.63,2,1),(111,2,1,'93204495265083247574',22.10,2,1),(112,2,1,'47970780299418547260',1307.66,4,1),(115,2,1,'49191560331286985954',123.38,1,1),(116,1,2,'77781390764489310674',1382.34,1,1),(117,2,2,'60805833757870657386',1486.94,4,1),(118,2,1,'79038431570622009266',472.55,1,1),(119,1,2,'68716985207480077164',1053.09,3,1),(120,1,2,'96388346742898503283',746.72,5,1),(121,2,2,'03527494228846241421',458.22,2,1),(122,1,2,'09405327679946288098',54.95,2,1),(124,2,1,'09258565366819346809',922.25,1,1),(126,1,2,'64351287599888977940',131.01,2,1),(127,1,1,'79652870143256478750',1370.71,1,1),(128,2,2,'97057810099552656676',64.82,5,1),(129,1,2,'06329526144465301115',182.90,3,1),(130,1,2,'40412413130585550890',1381.46,2,1),(132,2,1,'10878980991605095727',140.76,4,1),(133,2,2,'52635284704705362201',56.70,2,1),(134,1,1,'36382050884649991608',712.17,1,1),(136,1,2,'98277223087190153849',1428.48,4,1),(137,2,1,'70868167536752334881',270.32,5,1),(138,2,2,'03273425674437229057',197.07,5,1),(139,2,1,'87523543185138130255',1332.98,4,1),(140,1,1,'89370719779650985475',1042.69,2,1),(141,2,2,'16151477597922108051',315.47,4,1),(143,2,1,'62704929639191864376',1389.89,2,1),(144,2,2,'19995411789730076938',214.69,5,1),(145,2,1,'29373544197987240257',1045.38,1,1),(146,1,1,'85406763409062846930',1492.13,2,1),(147,2,2,'10456659852976832995',1253.58,4,1),(148,2,1,'61971722791778289806',1459.30,2,1),(149,2,1,'59903560088428320863',329.42,5,1),(150,2,2,'85786249367204170271',615.35,5,1),(151,1,1,'57537875453022261018',1468.83,1,1),(152,1,2,'97715509404476293649',1130.87,3,1),(155,1,2,'99571215234728649450',1387.52,4,1),(156,1,1,'00304570317416934321',446.04,2,1),(157,1,2,'29203389150358046260',1164.11,5,1),(158,1,2,'26695320577584407323',756.60,5,1),(160,1,2,'80843347036544815151',1334.65,3,1),(161,1,2,'51446163641867650978',474.58,5,1),(162,2,1,'64496937824385279066',1048.72,2,1),(163,2,1,'37513547243902632760',617.36,5,1),(170,1,2,'85739047372837542524',491.83,4,1),(171,1,1,'02877011639052972324',115.82,3,1),(172,1,2,'63696603010912757761',82.98,3,1),(175,1,2,'61720436010873381215',723.95,4,1),(176,2,1,'26108907712079182727',95.98,2,1),(179,1,2,'68850401081601145601',570.61,2,1),(180,2,2,'60582947618810632621',436.75,5,1),(181,1,2,'84108964911496351972',707.69,5,1),(182,2,2,'69618168980428027945',1473.36,2,1),(183,1,1,'70187059928667908201',1427.94,1,1),(185,2,1,'57795949892021697989',60.13,1,1),(186,1,2,'79341455888018928733',1473.63,4,1),(187,1,1,'74520455239250582014',1041.03,5,1),(188,1,2,'79149571788474155698',564.48,3,1),(189,2,2,'88875222452097406986',1476.03,4,1),(190,2,1,'45353767231332844058',1102.65,3,1),(191,1,2,'43379908427042555523',638.34,4,1),(192,2,2,'22750004758583188457',37.93,5,1),(193,1,1,'55116424691672257703',1301.53,4,1),(195,1,1,'57376696176011750472',451.54,3,1),(196,1,1,'97200487697042740659',385.92,1,1),(197,2,1,'58074054055714064337',671.25,1,1),(201,2,1,'93921498660781933997',519.02,4,1),(202,1,1,'02885113006635937133',553.20,4,1),(204,1,1,'81376354086564311226',588.03,5,1),(209,1,2,'88347072447298962494',764.13,4,1),(210,1,1,'99203437469325386527',1242.05,5,1),(211,1,2,'28530519768426675404',987.37,4,1),(212,1,2,'05973337900001462680',969.85,3,1),(214,2,2,'37104033250582712309',25.74,4,1),(216,2,2,'54463407295232346103',1209.90,5,1),(218,1,2,'40545386716848353132',215.16,2,1),(219,2,2,'64087441116363859863',377.14,3,1),(220,1,1,'08880083433454047464',1044.41,5,1),(221,2,1,'36244816611649937859',789.85,5,1),(223,2,1,'16313175190407454517',201.15,3,1),(224,1,2,'14804391128157304220',844.00,2,1),(225,2,1,'61895226842493973555',780.91,3,1),(229,2,2,'46259035997436652472',738.27,5,1),(231,2,1,'85777504154750881237',562.93,4,1),(232,2,1,'84002980913393078683',380.78,3,1),(233,2,2,'82008358546590806060',472.54,5,1),(234,1,2,'97668166590827197324',1438.44,5,1),(235,1,1,'77090331320374128004',529.41,4,1),(236,2,2,'17590610540808029331',758.65,3,1),(237,1,1,'19503749564051320512',764.57,3,1),(238,2,1,'78427872941511555129',758.17,3,1),(239,2,1,'96813148529415363786',798.66,4,1),(241,2,1,'48632299656546575224',334.73,1,1),(242,1,1,'86856701251340406903',620.90,4,1),(245,2,1,'26188887745612582234',916.09,4,1),(246,1,2,'72637077951116888726',780.50,3,1),(251,2,1,'89033817128888249712',482.83,3,1),(253,1,2,'83964868851431953959',299.40,5,1),(256,2,1,'18314004334939268837',1343.10,4,1),(258,2,1,'19726629710333333661',264.06,2,1),(259,1,2,'41449549646902755248',706.87,4,1),(261,2,1,'21119860017921002290',163.38,4,1),(262,1,1,'50523495812137057359',661.30,1,1),(263,1,1,'17338336645416324858',287.00,5,1),(265,2,1,'57009889278870241050',1136.81,3,1),(266,1,2,'58520530373867704297',831.61,5,1),(267,2,2,'77389313191872460762',883.63,2,1),(269,2,2,'28606176578419744011',372.21,3,1),(270,2,2,'30428122322325438162',1430.62,1,1),(272,2,1,'64035030990598742755',1191.60,3,1),(273,2,2,'89694632116866769656',196.05,4,1),(275,2,1,'00309865137958561344',267.90,3,1),(277,2,1,'29249810309372046099',1404.77,1,1),(278,1,1,'89296837401561783642',1402.30,3,1),(279,2,2,'95968966156028522785',1432.42,4,1),(280,1,1,'90317804274988849479',155.88,1,1),(282,1,2,'22012454093789199680',1242.23,2,1),(284,1,2,'50679880584210645274',1286.84,4,1),(286,2,1,'80507490309442055211',559.96,2,1),(289,1,2,'14193392216791293547',463.61,3,1),(290,1,2,'61471724504484658418',809.05,2,1),(291,1,2,'82102247458972919377',621.00,3,1),(292,2,2,'00551777142910918698',114.72,1,1),(293,1,2,'19812260315721035182',616.11,4,1),(294,1,2,'00689267543012166187',926.97,2,1),(295,1,1,'77489510033321329113',381.98,3,1),(297,2,1,'41610736945492027484',211.33,1,1),(300,2,1,'47970780299418547260',1307.66,4,1),(301,2,2,'10927689432779987307',1150.19,2,1),(302,1,2,'09405327679946288098',54.95,2,1),(303,2,1,'49191560331286985954',123.38,1,1),(305,2,2,'03527494228846241421',458.22,2,1),(306,1,1,'79652870143256478750',1370.71,1,1),(308,1,2,'96388346742898503283',746.72,5,1),(309,2,1,'79038431570622009266',472.55,1,1),(310,2,1,'70868167536752334881',270.32,5,1),(311,2,2,'52635284704705362201',56.70,2,1),(313,2,1,'09258565366819346809',922.25,1,1),(314,2,2,'97057810099552656676',64.82,5,1),(316,2,1,'10878980991605095727',140.76,4,1),(317,1,2,'64351287599888977940',131.01,2,1),(318,1,2,'06329526144465301115',182.90,3,1),(319,2,1,'59903560088428320863',329.42,5,1),(320,1,2,'68716985207480077164',1053.09,3,1),(321,2,1,'61971722791778289806',1459.30,2,1),(322,1,2,'98277223087190153849',1428.48,4,1),(323,2,1,'62704929639191864376',1389.89,2,1),(325,1,1,'36382050884649991608',712.17,1,1),(326,2,2,'03273425674437229057',197.07,5,1),(327,2,2,'51080042202639903379',614.56,5,1),(329,1,2,'22675861956511243136',429.33,3,1),(330,2,1,'31556182394758637919',135.23,3,1),(331,2,2,'38156170631931939190',872.80,3,1),(332,2,1,'67318538463221308429',162.46,2,1),(333,1,2,'97715509404476293649',1130.87,3,1),(334,1,2,'29203389150358046260',1164.11,5,1),(336,1,1,'12025532339308754377',523.61,5,1),(338,2,1,'64496937824385279066',1048.72,2,1),(339,1,2,'61720436010873381215',723.95,4,1),(342,1,2,'85739047372837542524',491.83,4,1),(343,1,2,'63696603010912757761',82.98,3,1),(345,1,1,'02877011639052972324',115.82,3,1),(348,2,2,'69618168980428027945',1473.36,2,1),(349,2,1,'37513547243902632760',617.36,5,1),(350,2,2,'60582947618810632621',436.75,5,1),(351,1,2,'68850401081601145601',570.61,2,1),(354,2,2,'88875222452097406986',1476.03,4,1),(355,1,2,'80843347036544815151',1334.65,3,1),(356,1,2,'02987959508302731392',1406.63,2,1),(358,1,1,'74520455239250582014',1041.03,5,1),(359,1,2,'77781390764489310674',1382.34,1,1),(361,1,2,'79341455888018928733',1473.63,4,1),(362,2,1,'93204495265083247574',22.10,2,1),(364,2,2,'36107961594691344422',548.18,3,1),(366,1,1,'70187059928667908201',1427.94,1,1),(367,1,2,'43379908427042555523',638.34,4,1),(368,2,1,'57795949892021697989',60.13,1,1),(369,1,1,'55116424691672257703',1301.53,4,1),(370,2,2,'22750004758583188457',37.93,5,1),(371,1,2,'40412413130585550890',1381.46,2,1),(372,1,1,'57537875453022261018',1468.83,1,1),(373,2,2,'19995411789730076938',214.69,5,1),(374,1,1,'89370719779650985475',1042.69,2,1),(375,1,2,'40545386716848353132',215.16,2,1),(376,1,2,'99571215234728649450',1387.52,4,1),(377,2,1,'87523543185138130255',1332.98,4,1),(379,2,1,'29373544197987240257',1045.38,1,1),(380,1,2,'28530519768426675404',987.37,4,1),(381,1,2,'79149571788474155698',564.48,3,1),(382,2,2,'64087441116363859863',377.14,3,1),(383,1,1,'08880083433454047464',1044.41,5,1),(385,1,1,'85406763409062846930',1492.13,2,1),(389,2,2,'16151477597922108051',315.47,4,1),(390,2,2,'10456659852976832995',1253.58,4,1),(393,2,2,'37104033250582712309',25.74,4,1),(397,1,2,'84108964911496351972',707.69,5,1),(398,2,2,'60805833757870657386',1486.94,4,1),(399,2,1,'26108907712079182727',95.98,2,1),(402,2,2,'82008358546590806060',472.54,5,1),(403,2,1,'84002980913393078683',380.78,3,1),(404,2,1,'26188887745612582234',916.09,4,1),(405,1,1,'77090331320374128004',529.41,4,1),(407,2,2,'46259035997436652472',738.27,5,1),(408,2,2,'28606176578419744011',372.21,3,1),(413,2,2,'30428122322325438162',1430.62,1,1),(414,1,1,'97200487697042740659',385.92,1,1),(415,2,1,'61895226842493973555',780.91,3,1),(416,2,1,'85777504154750881237',562.93,4,1),(417,1,2,'97668166590827197324',1438.44,5,1),(418,1,2,'14804391128157304220',844.00,2,1),(419,1,2,'26695320577584407323',756.60,5,1),(421,1,1,'17338336645416324858',287.00,5,1),(423,1,2,'83964868851431953959',299.40,5,1),(424,1,1,'00304570317416934321',446.04,2,1),(425,1,2,'41449549646902755248',706.87,4,1),(428,1,2,'72637077951116888726',780.50,3,1),(429,1,1,'02885113006635937133',553.20,4,1),(430,1,1,'81376354086564311226',588.03,5,1),(431,2,1,'19726629710333333661',264.06,2,1),(432,2,1,'78427872941511555129',758.17,3,1),(433,1,2,'51446163641867650978',474.58,5,1),(435,1,2,'88347072447298962494',764.13,4,1),(436,2,1,'31556182394758637919',135.23,3,1),(437,1,1,'90317804274988849479',155.88,1,1),(438,2,1,'00309865137958561344',267.90,3,1),(440,1,2,'22012454093789199680',1242.23,2,1),(441,1,1,'57375403048230409113',1111.12,3,1),(442,2,2,'51080042202639903379',614.56,5,1),(443,1,1,'89296837401561783642',1402.30,3,1),(444,2,2,'89694632116866769656',196.05,4,1),(445,1,2,'19812260315721035182',616.11,4,1),(446,2,2,'95968966156028522785',1432.42,4,1),(448,1,1,'12025532339308754377',523.61,5,1),(450,1,2,'00689267543012166187',926.97,2,1),(452,1,1,'99203437469325386527',1242.05,5,1),(453,2,2,'00551777142910918698',114.72,1,1),(454,1,1,'77489510033321329113',381.98,3,1),(455,1,1,'57375403048230409113',1111.12,3,1),(457,2,1,'45353767231332844058',1102.65,3,1),(458,2,2,'36107961594691344422',548.18,3,1),(459,1,2,'02987959508302731392',1406.63,2,1),(460,2,1,'89033817128888249712',482.83,3,1),(461,1,1,'72325747692981659833',738.92,3,1),(463,1,2,'61471724504484658418',809.05,2,1),(464,2,2,'10927689432779987307',1150.19,2,1),(465,1,2,'58520530373867704297',831.61,5,1),(467,2,1,'93921498660781933997',519.02,4,1),(468,2,2,'77389313191872460762',883.63,2,1),(469,1,1,'19503749564051320512',764.57,3,1),(471,2,1,'29249810309372046099',1404.77,1,1),(472,1,2,'22675861956511243136',429.33,3,1),(474,2,1,'57009889278870241050',1136.81,3,1),(475,2,2,'85786249367204170271',615.35,5,1),(476,1,1,'57376696176011750472',451.54,3,1),(478,2,1,'18314004334939268837',1343.10,4,1),(483,1,1,'72325747692981659833',738.92,3,1),(484,2,1,'64035030990598742755',1191.60,3,1),(485,2,1,'21119860017921002290',163.38,4,1),(487,1,2,'05973337900001462680',969.85,3,1),(489,2,1,'36244816611649937859',789.85,5,1),(491,2,2,'54463407295232346103',1209.90,5,1),(492,2,1,'58074054055714064337',671.25,1,1),(493,1,2,'50679880584210645274',1286.84,4,1),(495,2,2,'17590610540808029331',758.65,3,1),(496,2,2,'38156170631931939190',872.80,3,1),(497,1,1,'86856701251340406903',620.90,4,1),(498,2,1,'96813148529415363786',798.66,4,1),(499,2,1,'48632299656546575224',334.73,1,1),(501,2,1,'67318538463221308429',162.46,2,1),(502,1,2,'82102247458972919377',621.00,3,1),(503,2,1,'16313175190407454517',201.15,3,1),(507,1,1,'50523495812137057359',661.30,1,1),(508,2,1,'79038431570622009266',472.55,1,1),(510,2,1,'80507490309442055211',559.96,2,1),(511,2,1,'47970780299418547260',1307.66,4,1),(512,2,1,'93204495265083247574',22.10,2,1),(513,2,1,'41610736945492027484',211.33,1,1),(514,1,2,'14193392216791293547',463.61,3,1);
/*!40000 ALTER TABLE `cuenta_bancaria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cuentas_contables`
--

DROP TABLE IF EXISTS `cuentas_contables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cuentas_contables` (
  `cod_cuenta` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_contable` varchar(20) NOT NULL,
  `nombre_cuenta` varchar(100) NOT NULL,
  `naturaleza` enum('deudora','acreedora') NOT NULL,
  `cuenta_padreid` int(11) DEFAULT NULL,
  `nivel` int(11) NOT NULL,
  `saldo` decimal(20,2) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_cuenta`),
  UNIQUE KEY `codigo_contable` (`codigo_contable`),
  KEY `cuenta_padreid` (`cuenta_padreid`),
  CONSTRAINT `cuentas_contables_ibfk_1` FOREIGN KEY (`cuenta_padreid`) REFERENCES `cuentas_contables` (`cod_cuenta`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuentas_contables`
--

LOCK TABLES `cuentas_contables` WRITE;
/*!40000 ALTER TABLE `cuentas_contables` DISABLE KEYS */;
INSERT INTO `cuentas_contables` VALUES (1,'1','ACTIVO','deudora',NULL,1,0.00,1),(2,'1.1','ACTIVO CORRIENTE','deudora',1,2,0.00,1),(3,'1.1.1','EFECTIVO Y EQUIVALENTE DE EFECTIVO','deudora',1,3,0.00,1),(4,'1.1.1.01','CAJA','deudora',1,4,0.00,1),(5,'1.1.1.01.01','CAJA PRINCIPAL','deudora',1,5,0.00,1),(6,'1.1.1.02','BANCOS','deudora',1,4,0.00,1),(7,'1.1.1.02.01','DISPONIBILIDADES EN BANCOS NACIONALES','deudora',1,5,0.00,1),(8,'1.1.1.02.02','DISPONIBILIDADES EN BANCOS DEL EXTERIOR','deudora',1,5,0.00,1),(9,'1.1.2','CUENTAS POR COBRAR CORRIENTES','deudora',1,3,0.00,1),(10,'1.1.2.01','CLIENTES NACIONALES','deudora',1,4,0.00,1),(11,'1.1.4','INVENTARIOS','deudora',1,3,0.00,1),(12,'1.1.4.01','MERCANCÍA DISPONIBLE PARA LA VENTA','deudora',1,4,0.00,1),(13,'2','PASIVO','acreedora',NULL,1,0.00,1),(14,'2.1','PASIVO CORRIENTE','acreedora',13,2,0.00,1),(15,'2.1.1','CUENTAS POR PAGAR','acreedora',13,3,0.00,1),(16,'2.1.1.01','PROVEEDORES POR COMPRAS','acreedora',13,4,0.00,1),(17,'2.1.1.02','PROVEEDORES POR GASTOS','acreedora',13,4,0.00,1),(18,'2.1.1.03','PROVEEDORES INTERNACIONALES','acreedora',13,4,0.00,1),(19,'2.2','PASIVO NO CORRIENTE','acreedora',13,2,0.00,1),(20,'2.2.1','PRÉSTAMOS A LARGO PLAZO','acreedora',13,3,0.00,1),(21,'3','PATRIMONIO','acreedora',NULL,1,0.00,1),(22,'3.1','CAPITAL SOCIAL','acreedora',21,2,0.00,1),(23,'5','GASTOS','deudora',NULL,1,0.00,1),(24,'5.1','COSTO DE VENTAS','deudora',23,2,0.00,1),(25,'5.2','GASTOS DE OPERACIÓN','deudora',23,2,0.00,1),(26,'5.3','GASTOS FINANCIEROS','deudora',23,2,0.00,1),(27,'4','INGRESOS','acreedora',NULL,1,0.00,1),(28,'4.1','VENTAS DE PRODUCTOS','acreedora',27,2,0.00,1),(29,'4.2','SERVICIOS PRESTADOS','acreedora',27,2,0.00,1),(30,'4.3','OTROS INGRESOS','acreedora',27,2,0.00,1),(31,'4.3.1','INGRESOS EXTRAORDINARIOS','acreedora',30,3,0.00,1),(32,'4.3.1.01','GANANCIA POR AJUSTE DE INVENTARIO','acreedora',31,4,0.00,1),(33,'5.4','GASTOS NO OPERATIVOS','deudora',23,2,0.00,1),(34,'5.4.1','PERDIDAS EXTRAORDINARIAS','deudora',33,3,0.00,1),(35,'5.4.1.01','PERDIDA POR AJUSTE DE INVENTARIO','deudora',34,4,0.00,1);
/*!40000 ALTER TABLE `cuentas_contables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `descarga`
--

DROP TABLE IF EXISTS `descarga`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `descarga` (
  `cod_descarga` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` datetime NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_descarga`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `descarga`
--

LOCK TABLES `descarga` WRITE;
/*!40000 ALTER TABLE `descarga` DISABLE KEYS */;
INSERT INTO `descarga` VALUES (1,'2025-04-29 23:49:15','ajuste stock prueba',0.00,1),(2,'2025-05-22 12:10:01','prueba costo',1215.00,1),(3,'2025-05-25 04:42:47','prueba por movimineto',2562.20,1),(4,'2025-05-29 02:41:10','PRUEBA',68.00,1);
/*!40000 ALTER TABLE `descarga` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_asientos`
--

DROP TABLE IF EXISTS `detalle_asientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_asientos` (
  `cod_det_asiento` int(11) NOT NULL AUTO_INCREMENT,
  `cod_asiento` int(11) NOT NULL,
  `cod_cuenta` int(11) NOT NULL,
  `monto` decimal(18,2) NOT NULL,
  `tipo` enum('Debe','Haber') DEFAULT NULL,
  PRIMARY KEY (`cod_det_asiento`),
  KEY `asiento_id` (`cod_asiento`),
  KEY `cuenta_id` (`cod_cuenta`),
  CONSTRAINT `detalle_asientos_ibfk_1` FOREIGN KEY (`cod_asiento`) REFERENCES `asientos_contables` (`cod_asiento`) ON DELETE CASCADE,
  CONSTRAINT `detalle_asientos_ibfk_2` FOREIGN KEY (`cod_cuenta`) REFERENCES `cuentas_contables` (`cod_cuenta`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=363 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_asientos`
--

LOCK TABLES `detalle_asientos` WRITE;
/*!40000 ALTER TABLE `detalle_asientos` DISABLE KEYS */;
INSERT INTO `detalle_asientos` VALUES (1,3,24,426.42,'Debe'),(2,3,12,426.42,'Haber'),(3,4,28,571.40,'Haber'),(4,4,7,100.00,'Debe'),(5,4,5,200.00,'Debe'),(6,4,7,100.00,'Debe'),(7,4,5,94.76,'Debe'),(8,4,7,76.64,'Debe'),(9,5,24,497.55,'Debe'),(10,5,12,497.55,'Haber'),(11,6,28,589.92,'Haber'),(12,6,7,589.92,'Debe'),(13,7,24,81.29,'Debe'),(14,7,12,81.29,'Haber'),(43,19,24,85.28,'Debe'),(44,19,12,85.28,'Haber'),(45,20,10,98.92,'Debe'),(46,20,28,98.92,'Haber'),(50,23,24,540.13,'Debe'),(51,23,12,540.13,'Haber'),(52,24,28,660.10,'Haber'),(53,24,5,344.76,'Debe'),(54,24,7,315.34,'Debe'),(55,25,24,213.21,'Debe'),(56,25,12,213.21,'Haber'),(57,26,28,3.69,'Haber'),(58,26,7,10.00,'Debe'),(59,27,24,677.43,'Debe'),(60,27,12,677.43,'Haber'),(61,28,10,907.76,'Debe'),(62,28,28,907.76,'Haber'),(63,29,24,270.97,'Debe'),(64,29,12,270.97,'Haber'),(65,30,28,5.09,'Haber'),(66,30,7,5.09,'Debe'),(67,31,24,852.84,'Debe'),(68,31,12,852.84,'Haber'),(69,32,10,989.21,'Debe'),(70,32,28,989.21,'Haber'),(71,33,24,852.84,'Debe'),(72,33,12,852.84,'Haber'),(73,34,28,1142.81,'Haber'),(74,34,5,42.81,'Debe'),(75,34,7,1100.00,'Debe'),(76,35,24,613.39,'Debe'),(77,35,12,613.39,'Haber'),(78,36,10,821.94,'Debe'),(79,36,28,821.94,'Haber'),(88,41,24,65.00,'Debe'),(89,41,12,65.00,'Haber'),(90,42,28,87.10,'Haber'),(91,42,7,100.00,'Debe'),(92,42,5,5.00,'Haber'),(93,42,7,7.90,'Haber'),(95,47,12,20847.20,'Debe'),(96,47,7,20847.20,'Haber'),(97,48,12,1215.00,'Debe'),(98,48,5,584.28,'Haber'),(99,48,7,630.72,'Haber'),(100,49,12,4775.90,'Debe'),(101,49,16,4775.90,'Haber'),(102,50,24,1757.50,'Debe'),(103,50,12,1757.50,'Haber'),(104,51,28,26.46,'Haber'),(105,51,7,26.46,'Debe'),(106,52,24,22.75,'Debe'),(107,52,12,22.75,'Haber'),(108,53,28,30.48,'Haber'),(109,53,5,30.00,'Debe'),(110,53,7,10.00,'Debe'),(111,53,7,9.52,'Haber'),(112,54,24,65.00,'Debe'),(113,54,12,65.00,'Haber'),(114,55,10,87.10,'Debe'),(115,55,28,87.10,'Haber'),(116,56,24,13.00,'Debe'),(117,56,12,13.00,'Haber'),(118,57,10,17.42,'Debe'),(119,57,28,17.42,'Haber'),(120,58,24,421.35,'Debe'),(121,58,12,421.35,'Haber'),(122,59,28,564.61,'Haber'),(123,59,5,250.00,'Debe'),(124,59,7,350.00,'Debe'),(125,59,7,35.39,'Haber'),(126,60,10,20.00,'Haber'),(127,60,7,20.00,'Debe'),(128,60,7,2.58,'Haber'),(129,63,10,956.57,'Haber'),(130,63,5,506.00,'Debe'),(131,63,7,450.57,'Debe'),(132,64,10,108.93,'Haber'),(133,64,5,20.00,'Debe'),(134,64,7,100.00,'Debe'),(135,64,5,11.07,'Haber'),(136,65,10,10.00,'Haber'),(137,65,7,10.00,'Debe'),(138,66,10,10.00,'Haber'),(139,66,7,10.00,'Debe'),(140,67,10,53.00,'Haber'),(141,67,7,53.00,'Debe'),(142,68,10,12.00,'Haber'),(143,68,7,12.00,'Debe'),(144,69,12,2300.00,'Debe'),(145,69,32,2300.00,'Haber'),(150,72,12,2212.00,'Debe'),(151,72,32,2212.00,'Haber'),(152,73,12,2550.94,'Debe'),(153,73,32,2550.94,'Haber'),(154,74,12,2464.80,'Debe'),(155,74,32,2464.80,'Haber'),(156,75,12,2464.80,'Debe'),(157,75,32,2464.80,'Haber'),(158,76,12,2464.80,'Debe'),(159,76,32,2464.80,'Haber'),(160,77,35,1215.00,'Debe'),(161,77,12,1215.00,'Haber'),(162,78,35,2562.20,'Debe'),(163,78,12,2562.20,'Haber'),(164,80,12,100.00,'Debe'),(165,80,16,100.00,'Haber'),(166,82,24,83.39,'Debe'),(167,82,12,83.39,'Haber'),(168,83,10,100.07,'Debe'),(169,83,28,100.07,'Haber'),(170,84,10,100.07,'Haber'),(171,84,7,70.00,'Debe'),(172,84,7,10.00,'Haber'),(173,85,10,100.07,'Haber'),(174,85,7,40.07,'Debe'),(175,89,10,-10.00,'Haber'),(176,89,7,70.00,'Debe'),(177,89,7,10.00,'Haber'),(178,90,10,40.78,'Haber'),(179,90,7,40.78,'Debe'),(180,91,10,60.00,'Haber'),(181,91,7,70.00,'Debe'),(182,91,7,10.00,'Haber'),(183,92,10,40.07,'Haber'),(184,92,7,40.07,'Debe'),(185,96,16,40.00,'Debe'),(186,96,7,40.00,'Haber'),(187,97,16,58.43,'Debe'),(188,97,5,38.43,'Haber'),(189,97,7,20.00,'Haber'),(190,98,16,60.00,'Debe'),(191,98,7,70.00,'Haber'),(192,98,7,10.00,'Debe'),(193,99,16,57.00,'Debe'),(194,99,7,57.00,'Haber'),(195,100,16,324.00,'Debe'),(196,100,7,324.00,'Haber'),(197,101,24,83.39,'Debe'),(198,101,12,83.39,'Haber'),(199,102,10,100.07,'Debe'),(200,102,28,100.07,'Haber'),(201,103,16,100.00,'Debe'),(202,103,7,100.00,'Haber'),(203,104,24,75.21,'Debe'),(204,104,12,75.21,'Haber'),(205,105,10,100.78,'Debe'),(206,105,28,100.78,'Haber'),(207,106,12,6129.04,'Debe'),(208,106,32,6129.04,'Haber'),(209,107,12,6320.00,'Debe'),(210,107,16,6320.00,'Haber'),(211,108,12,20847.20,'Debe'),(212,108,7,100.00,'Haber'),(213,109,16,300.00,'Debe'),(214,109,7,300.00,'Haber'),(215,110,24,0.00,'Debe'),(216,110,12,0.00,'Haber'),(217,111,10,956.57,'Debe'),(218,111,28,956.57,'Haber'),(219,112,10,989.21,'Haber'),(220,112,5,1137.60,'Debe'),(221,112,7,148.39,'Haber'),(222,113,10,907.76,'Haber'),(223,113,5,852.84,'Debe'),(224,113,7,54.92,'Debe'),(225,114,10,989.21,'Haber'),(226,114,7,10000.00,'Debe'),(227,114,7,9010.79,'Haber'),(228,115,16,20.00,'Debe'),(229,115,7,20.00,'Haber'),(230,116,24,555.47,'Debe'),(231,116,12,555.47,'Haber'),(232,117,28,684.27,'Haber'),(233,117,5,948.80,'Debe'),(234,117,7,264.53,'Haber'),(235,118,10,1.37,'Haber'),(236,119,10,2.10,'Haber'),(237,120,12,1280.00,'Debe'),(238,120,32,1280.00,'Haber'),(239,121,12,55.00,'Debe'),(240,121,5,45.00,'Debe'),(241,122,12,500.00,'Debe'),(242,122,16,500.00,'Haber'),(243,123,24,379.04,'Debe'),(244,123,12,379.04,'Haber'),(245,124,28,454.85,'Haber'),(246,124,5,480.00,'Debe'),(247,124,5,25.15,'Haber'),(248,125,24,75.84,'Debe'),(249,125,12,75.84,'Haber'),(250,126,10,101.63,'Debe'),(251,126,28,101.63,'Haber'),(252,127,12,96.90,'Debe'),(253,128,24,0.00,'Debe'),(254,128,12,0.00,'Haber'),(255,129,10,989.21,'Debe'),(256,129,28,989.21,'Haber'),(257,130,24,0.00,'Debe'),(258,130,12,0.00,'Haber'),(259,131,10,14.74,'Debe'),(260,131,28,14.74,'Haber'),(261,132,24,0.00,'Debe'),(262,132,12,0.00,'Haber'),(263,133,28,9.58,'Haber'),(264,133,5,4.58,'Debe'),(265,134,24,0.00,'Debe'),(266,134,12,0.00,'Haber'),(267,135,28,3.14,'Haber'),(268,135,5,0.86,'Haber'),(269,136,24,0.00,'Debe'),(270,136,12,0.00,'Haber'),(271,137,28,21.37,'Haber'),(272,137,5,3.00,'Haber'),(273,138,24,0.00,'Debe'),(274,138,12,0.00,'Haber'),(275,139,10,6.37,'Debe'),(276,139,28,6.37,'Haber'),(277,140,12,400.00,'Debe'),(278,140,5,480.00,'Haber'),(279,140,5,80.00,'Debe'),(280,141,24,200.00,'Debe'),(281,141,12,200.00,'Haber'),(282,142,28,260.00,'Haber'),(283,142,5,288.00,'Debe'),(284,142,5,28.00,'Haber'),(285,143,12,455.00,'Debe'),(286,143,16,455.00,'Haber'),(287,144,12,1615.00,'Debe'),(288,144,7,2000.00,'Haber'),(289,144,5,385.00,'Debe'),(290,145,25,100.00,'Debe'),(291,145,17,100.00,'Haber'),(292,146,25,100.00,'Debe'),(293,146,5,150.00,'Haber'),(294,146,5,50.00,'Debe'),(295,147,25,298.00,'Debe'),(296,147,17,298.00,'Haber'),(297,148,25,67.00,'Debe'),(298,149,25,2178.23,'Debe'),(299,149,7,2200.00,'Haber'),(300,149,5,21.77,'Debe'),(301,150,25,983.00,'Debe'),(302,150,17,983.00,'Haber'),(303,151,25,459.20,'Debe'),(304,151,5,500.00,'Haber'),(305,151,5,40.80,'Debe'),(306,152,10,191.62,'Haber'),(307,152,5,191.62,'Debe'),(308,153,24,71.37,'Debe'),(309,153,12,71.37,'Haber'),(310,154,28,95.64,'Haber'),(311,154,5,100.00,'Debe'),(312,154,5,4.36,'Haber'),(313,155,17,479.05,'Debe'),(314,155,5,479.05,'Haber'),(315,156,17,200.00,'Debe'),(316,156,5,287.43,'Haber'),(317,156,5,87.43,'Debe'),(318,157,25,200.00,'Debe'),(319,157,17,200.00,'Haber'),(320,158,17,285.95,'Debe'),(321,158,5,190.00,'Haber'),(322,158,7,95.95,'Haber'),(323,159,17,243.00,'Debe'),(324,159,5,287.43,'Haber'),(325,159,5,44.43,'Debe'),(326,160,16,285.00,'Debe'),(327,160,5,285.00,'Haber'),(328,161,25,243.00,'Debe'),(329,161,17,243.00,'Haber'),(330,162,24,189.52,'Debe'),(331,162,12,189.52,'Haber'),(332,163,10,227.43,'Debe'),(333,163,28,227.43,'Haber'),(334,164,12,3424.30,'Debe'),(335,164,5,2895.90,'Haber'),(336,164,7,528.40,'Haber'),(337,165,35,68.00,'Debe'),(338,165,12,68.00,'Haber'),(339,166,16,170.00,'Debe'),(340,166,5,195.81,'Haber'),(341,166,5,25.81,'Debe'),(342,167,12,170.00,'Debe'),(343,167,16,170.00,'Haber'),(344,168,12,400.00,'Debe'),(345,168,32,400.00,'Haber'),(346,169,25,632.00,'Debe'),(347,169,17,632.00,'Haber'),(348,170,17,632.00,'Debe'),(349,170,7,700.00,'Haber'),(350,170,5,68.00,'Debe'),(351,171,24,771.00,'Debe'),(352,171,12,771.00,'Haber'),(353,172,28,1036.30,'Haber'),(354,172,5,1061.83,'Debe'),(355,172,5,25.53,'Haber'),(356,173,24,202.24,'Debe'),(357,173,12,202.24,'Haber'),(358,174,28,273.02,'Haber'),(359,174,5,289.59,'Debe'),(360,174,5,16.57,'Haber'),(361,175,17,10.00,'Debe'),(362,175,5,10.00,'Haber');
/*!40000 ALTER TABLE `detalle_asientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_carga`
--

DROP TABLE IF EXISTS `detalle_carga`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_carga` (
  `cod_det_carga` int(11) NOT NULL AUTO_INCREMENT,
  `cod_detallep` int(11) NOT NULL,
  `cod_carga` int(11) NOT NULL,
  `cantidad` float NOT NULL,
  PRIMARY KEY (`cod_det_carga`),
  KEY `detalle_carga-carga` (`cod_carga`),
  KEY `detalle_carga-detallep` (`cod_detallep`),
  CONSTRAINT `detalle_carga-carga` FOREIGN KEY (`cod_carga`) REFERENCES `carga` (`cod_carga`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_carga-detallep` FOREIGN KEY (`cod_detallep`) REFERENCES `detalle_productos` (`cod_detallep`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_carga`
--

LOCK TABLES `detalle_carga` WRITE;
/*!40000 ALTER TABLE `detalle_carga` DISABLE KEYS */;
INSERT INTO `detalle_carga` VALUES (1,5,1,6),(2,5,10,5),(3,5,11,1),(5,5,13,2),(6,7,13,1),(7,7,14,5),(8,10,14,1),(9,7,16,2),(10,7,19,2),(11,5,21,3.9),(12,23,22,20);
/*!40000 ALTER TABLE `detalle_carga` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_compras`
--

DROP TABLE IF EXISTS `detalle_compras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_compras` (
  `cod_detallec` int(11) NOT NULL AUTO_INCREMENT,
  `cod_compra` int(11) NOT NULL,
  `cod_detallep` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  PRIMARY KEY (`cod_detallec`),
  KEY `detalle_compras-compras` (`cod_compra`),
  KEY `detalle_compras-detalle_productos` (`cod_detallep`),
  CONSTRAINT `detalle_compras-compras` FOREIGN KEY (`cod_compra`) REFERENCES `compras` (`cod_compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_compras-detalle_productos` FOREIGN KEY (`cod_detallep`) REFERENCES `detalle_productos` (`cod_detallep`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_compras`
--

LOCK TABLES `detalle_compras` WRITE;
/*!40000 ALTER TABLE `detalle_compras` DISABLE KEYS */;
INSERT INTO `detalle_compras` VALUES (5,5,6,5.00,11.00),(6,6,7,10.20,9.50),(7,7,8,9.50,677.43),(8,7,9,7.80,738.22),(9,8,10,55.00,379.04),(10,9,11,55.00,379.04),(11,10,12,55.00,379.04),(12,11,13,5.60,852.84),(13,12,14,1.00,65.00),(14,12,15,1.00,1150.00),(15,13,16,5.00,100.00),(16,14,17,10.00,632.00),(17,15,14,1.00,100.00),(18,16,19,10.00,201.50),(19,16,20,4.50,356.87),(20,17,21,1.00,455.00),(21,18,19,2.00,200.00),(22,19,22,5.00,323.00),(23,20,24,5.00,34.00),(24,21,25,10.00,342.43);
/*!40000 ALTER TABLE `detalle_compras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_descarga`
--

DROP TABLE IF EXISTS `detalle_descarga`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_descarga` (
  `cod_det_descarga` int(11) NOT NULL AUTO_INCREMENT,
  `cod_detallep` int(11) NOT NULL,
  `cod_descarga` int(11) NOT NULL,
  `cantidad` float NOT NULL,
  PRIMARY KEY (`cod_det_descarga`),
  KEY `detalle_descarga-detallep` (`cod_detallep`),
  KEY `detalle_descarga-descarga` (`cod_descarga`),
  CONSTRAINT `detalle_descarga-descarga` FOREIGN KEY (`cod_descarga`) REFERENCES `descarga` (`cod_descarga`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_descarga-detallep` FOREIGN KEY (`cod_detallep`) REFERENCES `detalle_productos` (`cod_detallep`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_descarga`
--

LOCK TABLES `detalle_descarga` WRITE;
/*!40000 ALTER TABLE `detalle_descarga` DISABLE KEYS */;
INSERT INTO `detalle_descarga` VALUES (1,5,1,0.2),(2,5,2,1),(3,7,2,1),(4,10,3,5),(5,15,3,0.58),(6,23,4,2);
/*!40000 ALTER TABLE `detalle_descarga` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_operacion`
--

DROP TABLE IF EXISTS `detalle_operacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_operacion` (
  `cod_detalle_op` int(11) NOT NULL AUTO_INCREMENT,
  `detalle_operacion` varchar(50) NOT NULL,
  PRIMARY KEY (`cod_detalle_op`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_operacion`
--

LOCK TABLES `detalle_operacion` WRITE;
/*!40000 ALTER TABLE `detalle_operacion` DISABLE KEYS */;
INSERT INTO `detalle_operacion` VALUES (1,'al contado'),(2,'a credito'),(3,'recibido'),(4,'emitido de compra'),(5,'emitido de gasto'),(6,'carga'),(7,'descarga');
/*!40000 ALTER TABLE `detalle_operacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_pago_emitido`
--

DROP TABLE IF EXISTS `detalle_pago_emitido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_pago_emitido` (
  `cod_detallepagoe` int(11) NOT NULL AUTO_INCREMENT,
  `cod_pago_emitido` int(11) NOT NULL,
  `cod_tipo_pagoe` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  PRIMARY KEY (`cod_detallepagoe`),
  KEY `pagoe-dtpagoe` (`cod_pago_emitido`),
  KEY `dtpagoe-tipopagoe` (`cod_tipo_pagoe`),
  CONSTRAINT `detalle_pago_emitido_ibfk_1` FOREIGN KEY (`cod_pago_emitido`) REFERENCES `pago_emitido` (`cod_pago_emitido`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_pago_emitido_ibfk_2` FOREIGN KEY (`cod_tipo_pagoe`) REFERENCES `detalle_tipo_pago` (`cod_tipo_pago`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_pago_emitido`
--

LOCK TABLES `detalle_pago_emitido` WRITE;
/*!40000 ALTER TABLE `detalle_pago_emitido` DISABLE KEYS */;
INSERT INTO `detalle_pago_emitido` VALUES (8,7,2,300.00),(10,7,10,284.28),(32,33,2,78.00),(37,37,2,10.00),(39,37,10,28.43),(46,43,14,2200.00),(47,44,10,480.00),(48,45,2,10.00),(49,46,14,2000.00),(50,47,2,150.00),(51,48,2,500.00),(52,49,11,285.00),(53,50,10,479.05),(54,51,10,287.43),(55,52,11,190.00),(56,52,14,95.95),(57,53,10,287.43),(58,54,2,100.00),(59,54,10,95.81),(60,55,10,2895.90),(61,55,14,528.40),(62,56,14,700.00),(63,57,2,10.00);
/*!40000 ALTER TABLE `detalle_pago_emitido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_pago_recibido`
--

DROP TABLE IF EXISTS `detalle_pago_recibido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_pago_recibido` (
  `cod_detallepago` int(11) NOT NULL AUTO_INCREMENT,
  `cod_pago` int(11) NOT NULL,
  `cod_tipo_pago` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  PRIMARY KEY (`cod_detallepago`),
  KEY `detalle_pago-pago` (`cod_pago`),
  KEY `tipo_pago-detalle_pago` (`cod_tipo_pago`),
  CONSTRAINT `detalle_pago_recibido_ibfk_1` FOREIGN KEY (`cod_pago`) REFERENCES `pago_recibido` (`cod_pago`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_pago_recibido_ibfk_2` FOREIGN KEY (`cod_tipo_pago`) REFERENCES `detalle_tipo_pago` (`cod_tipo_pago`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_pago_recibido`
--

LOCK TABLES `detalle_pago_recibido` WRITE;
/*!40000 ALTER TABLE `detalle_pago_recibido` DISABLE KEYS */;
INSERT INTO `detalle_pago_recibido` VALUES (2,1,2,1.00),(6,5,2,20.00),(18,16,2,4.58),(20,18,2,14.74),(30,26,2,20.49),(34,26,10,28.43),(36,27,2,200.00),(38,27,10,94.76),(41,29,2,250.00),(43,29,10,94.76),(45,30,2,42.81),(51,35,2,30.00),(53,36,2,250.00),(56,38,2,506.00),(58,39,2,20.00),(70,50,10,852.84),(71,51,10,947.60),(72,51,11,190.00),(73,52,10,473.80),(74,52,11,475.00),(76,54,10,480.00),(77,55,10,288.00),(78,56,2,100.00),(79,57,10,191.62),(80,58,10,1061.83),(81,59,10,289.59),(82,60,2,200.00);
/*!40000 ALTER TABLE `detalle_pago_recibido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_productos`
--

DROP TABLE IF EXISTS `detalle_productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_productos` (
  `cod_detallep` int(11) NOT NULL AUTO_INCREMENT,
  `cod_presentacion` int(11) NOT NULL,
  `stock` float NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `lote` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`cod_detallep`),
  KEY `detalle_producto-productos` (`cod_presentacion`),
  CONSTRAINT `detalle_productos_ibfk_1` FOREIGN KEY (`cod_presentacion`) REFERENCES `presentacion_producto` (`cod_presentacion`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_productos`
--

LOCK TABLES `detalle_productos` WRITE;
/*!40000 ALTER TABLE `detalle_productos` DISABLE KEYS */;
INSERT INTO `detalle_productos` VALUES (1,1,0,'0000-00-00',''),(2,1,8,'0000-00-00',''),(3,1,67,'0000-00-00',''),(4,1,23.5,'0000-00-00',''),(5,2,0,'2025-04-29','26-12'),(6,2,1.699,'2025-08-21',''),(7,3,14.45,'0000-00-00',''),(8,3,9.5,'0000-00-00',''),(9,2,7.8,'0000-00-00',''),(10,4,46.728,'0000-00-00',''),(11,4,55,'0000-00-00',''),(12,4,55,'0000-00-00',''),(13,2,5.6,'0000-00-00',''),(14,2,2,'0000-00-00','1234'),(15,3,0.42,'0000-00-00','0890809'),(16,2,5,'0000-00-00',''),(17,2,10,'0000-00-00',''),(18,6,0,'2025-07-25','123-343'),(19,5,10,'2025-06-03','123'),(20,8,3.9,'0000-00-00','890-8'),(21,3,1,'0000-00-00',''),(22,5,5,'0000-00-00',''),(23,14,18,'2025-06-29','123-567'),(24,14,5,'0000-00-00',''),(25,14,10,'0000-00-00','');
/*!40000 ALTER TABLE `detalle_productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_tipo_pago`
--

DROP TABLE IF EXISTS `detalle_tipo_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_tipo_pago` (
  `cod_tipo_pago` int(11) NOT NULL AUTO_INCREMENT,
  `cod_metodo` int(11) NOT NULL,
  `tipo_moneda` enum('efectivo','digital','','') NOT NULL,
  `cod_cuenta_bancaria` int(11) DEFAULT NULL,
  `cod_caja` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_tipo_pago`),
  KEY `cod_cuenta_bancaria` (`cod_cuenta_bancaria`),
  KEY `cod_metodo` (`cod_metodo`),
  KEY `cod_caja` (`cod_caja`),
  CONSTRAINT `detalle_tipo_pago_ibfk_1` FOREIGN KEY (`cod_cuenta_bancaria`) REFERENCES `cuenta_bancaria` (`cod_cuenta_bancaria`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_tipo_pago_ibfk_3` FOREIGN KEY (`cod_metodo`) REFERENCES `tipo_pago` (`cod_metodo`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_tipo_pago_ibfk_4` FOREIGN KEY (`cod_caja`) REFERENCES `caja` (`cod_caja`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_tipo_pago`
--

LOCK TABLES `detalle_tipo_pago` WRITE;
/*!40000 ALTER TABLE `detalle_tipo_pago` DISABLE KEYS */;
INSERT INTO `detalle_tipo_pago` VALUES (2,1,'efectivo',NULL,5,1),(10,1,'efectivo',NULL,6,1),(11,1,'efectivo',NULL,7,1),(13,3,'digital',2,NULL,1),(14,4,'digital',1,NULL,1);
/*!40000 ALTER TABLE `detalle_tipo_pago` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_ventas`
--

DROP TABLE IF EXISTS `detalle_ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_ventas` (
  `cod_detallev` int(11) NOT NULL AUTO_INCREMENT,
  `cod_venta` int(11) NOT NULL,
  `cod_detallep` int(11) NOT NULL,
  `importe` decimal(10,2) NOT NULL,
  `costo_unitario` decimal(10,2) NOT NULL,
  `cantidad` float(10,3) NOT NULL,
  PRIMARY KEY (`cod_detallev`),
  KEY `cod_venta` (`cod_venta`),
  KEY `detalle_ventas-detalle_productos` (`cod_detallep`),
  CONSTRAINT `detalle_ventas-detalle_productos` FOREIGN KEY (`cod_detallep`) REFERENCES `detalle_productos` (`cod_detallep`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`cod_venta`) REFERENCES `ventas` (`cod_venta`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_ventas`
--

LOCK TABLES `detalle_ventas` WRITE;
/*!40000 ALTER TABLE `detalle_ventas` DISABLE KEYS */;
INSERT INTO `detalle_ventas` VALUES (1,7,1,70.00,0.00,10.000),(2,7,2,28.00,0.00,4.000),(3,8,5,4.31,0.00,0.322),(4,9,5,7.37,0.00,0.500),(5,10,5,3.69,0.00,0.250),(6,11,5,2.21,0.00,0.150),(7,12,5,3.39,0.00,0.230),(8,13,5,14.74,0.00,1.000),(9,14,5,10.98,0.00,0.745),(10,15,5,14.74,0.00,1.000),(11,16,5,6.38,0.00,0.433),(12,17,5,3.69,0.00,0.250),(13,18,5,2.95,0.00,0.200),(14,19,5,6.37,0.00,0.432),(15,20,5,4.25,0.00,0.288),(16,20,6,17.13,0.00,1.162),(17,21,6,3.14,0.00,0.213),(18,22,6,9.58,0.00,0.650),(19,23,5,7.37,0.00,0.500),(20,23,7,19.10,0.00,1.500),(21,24,5,14.74,0.00,1.000),(22,25,7,5.09,0.00,0.400),(23,26,5,989.21,0.00,1.000),(24,27,7,907.76,0.00,1.000),(25,28,5,989.21,0.00,1.000),(26,29,5,956.57,0.00,0.967),(27,30,7,108.93,0.00,0.120),(28,31,5,98.92,0.00,0.100),(29,32,5,494.61,0.00,0.500),(30,32,7,95.31,0.00,0.105),(31,33,5,571.41,0.00,0.500),(32,34,10,545.82,0.00,1.200),(33,34,5,114.28,0.00,0.100),(34,35,5,1142.81,0.00,1.000),(35,36,5,571.41,0.00,0.500),(36,36,7,250.54,0.00,0.276),(37,37,5,72.55,0.00,0.833),(38,37,6,14.55,0.00,0.167),(39,39,6,17.42,65.00,0.200),(40,40,5,87.10,65.00,1.000),(41,41,6,30.49,65.00,0.350),(42,42,6,26.80,100.00,0.200),(43,42,7,537.81,1150.00,0.349),(44,43,10,100.07,379.04,0.220),(45,44,5,100.78,632.00,0.119),(46,45,10,100.07,379.04,0.220),(47,46,10,514.89,379.04,1.132),(48,46,5,169.38,632.00,0.200),(49,47,5,101.63,632.00,0.120),(50,48,10,454.85,379.04,1.000),(51,49,19,260.00,200.00,1.000),(52,50,10,227.43,379.04,0.500),(53,51,5,2540.64,632.00,3.000),(54,52,19,260.00,200.00,1.000),(55,53,20,95.64,356.87,0.200),(56,54,20,95.64,356.87,0.200),(57,55,20,95.64,356.87,0.200),(58,56,5,273.02,632.00,0.320),(59,57,5,120.30,632.00,0.141),(60,57,6,306.30,632.00,0.359),(61,57,7,609.70,455.00,1.000);
/*!40000 ALTER TABLE `detalle_ventas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_vueltoe`
--

DROP TABLE IF EXISTS `detalle_vueltoe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_vueltoe` (
  `cod_detallev` int(11) NOT NULL AUTO_INCREMENT,
  `cod_vuelto` int(11) NOT NULL,
  `cod_tipo_pago` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  PRIMARY KEY (`cod_detallev`),
  KEY `cod_vuelto` (`cod_vuelto`),
  KEY `cod_tipo_pago` (`cod_tipo_pago`),
  CONSTRAINT `detalle_vueltoe_ibfk_1` FOREIGN KEY (`cod_vuelto`) REFERENCES `vuelto_emitido` (`cod_vuelto`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_vueltoe_ibfk_2` FOREIGN KEY (`cod_tipo_pago`) REFERENCES `detalle_tipo_pago` (`cod_tipo_pago`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_vueltoe`
--

LOCK TABLES `detalle_vueltoe` WRITE;
/*!40000 ALTER TABLE `detalle_vueltoe` DISABLE KEYS */;
INSERT INTO `detalle_vueltoe` VALUES (2,1,2,5.00),(7,5,2,3.00),(8,6,2,0.86),(10,7,2,5.00),(15,11,2,11.07),(24,20,2,25.15),(25,21,2,28.00),(26,22,2,4.36),(27,23,2,25.53),(28,24,2,16.57),(29,25,2,104.36);
/*!40000 ALTER TABLE `detalle_vueltoe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_vueltor`
--

DROP TABLE IF EXISTS `detalle_vueltor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `detalle_vueltor` (
  `cod_detallev_r` int(11) NOT NULL AUTO_INCREMENT,
  `cod_vuelto_r` int(11) NOT NULL,
  `cod_tipo_pago` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  PRIMARY KEY (`cod_detallev_r`),
  KEY `cod_vuelto_r` (`cod_vuelto_r`),
  KEY `cod_tipo_pago` (`cod_tipo_pago`),
  CONSTRAINT `detalle_vueltor_ibfk_1` FOREIGN KEY (`cod_vuelto_r`) REFERENCES `vuelto_recibido` (`cod_vuelto_r`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_vueltor_ibfk_2` FOREIGN KEY (`cod_tipo_pago`) REFERENCES `detalle_tipo_pago` (`cod_tipo_pago`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_vueltor`
--

LOCK TABLES `detalle_vueltor` WRITE;
/*!40000 ALTER TABLE `detalle_vueltor` DISABLE KEYS */;
INSERT INTO `detalle_vueltor` VALUES (3,3,2,45.00),(6,6,2,21.77),(7,7,2,80.00),(8,8,2,385.00),(9,9,2,50.00),(10,10,2,40.80),(11,11,2,44.43),(12,12,2,87.43),(13,13,2,25.81),(14,14,2,68.00);
/*!40000 ALTER TABLE `detalle_vueltor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `divisas`
--

DROP TABLE IF EXISTS `divisas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `divisas` (
  `cod_divisa` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `abreviatura` varchar(5) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_divisa`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `divisas`
--

LOCK TABLES `divisas` WRITE;
/*!40000 ALTER TABLE `divisas` DISABLE KEYS */;
INSERT INTO `divisas` VALUES (1,'Bolívares','Bs',1),(2,'Dolares','$',1),(3,'Euro','EUR',1),(4,'Binances','USDT',1),(5,'libra','Lb',1);
/*!40000 ALTER TABLE `divisas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa`
--

DROP TABLE IF EXISTS `empresa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `empresa` (
  `cod` int(11) NOT NULL AUTO_INCREMENT,
  `rif` varchar(15) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `direccion` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `telefono` varchar(12) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `email` varchar(70) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `descripcion` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `logo` varchar(255) CHARACTER SET utf8 COLLATE utf8_spanish2_ci DEFAULT NULL,
  PRIMARY KEY (`cod`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa`
--

LOCK TABLES `empresa` WRITE;
/*!40000 ALTER TABLE `empresa` DISABLE KEYS */;
INSERT INTO `empresa` VALUES (3,'J505284797','Quesera y Charcuteria Don Pedro 24','calle 60 entre carreras 12 y 13','04245645108','queseradonpedro24@gmail.com','mkmkm','vista/dist/img/logo-icono.png');
/*!40000 ALTER TABLE `empresa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `frecuencia_gasto`
--

DROP TABLE IF EXISTS `frecuencia_gasto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `frecuencia_gasto` (
  `cod_frecuencia` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `dias` int(11) NOT NULL,
  PRIMARY KEY (`cod_frecuencia`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `frecuencia_gasto`
--

LOCK TABLES `frecuencia_gasto` WRITE;
/*!40000 ALTER TABLE `frecuencia_gasto` DISABLE KEYS */;
INSERT INTO `frecuencia_gasto` VALUES (1,'diario',1),(2,'semanal',7),(3,'quincenal',15),(4,'mensual',30),(5,'trimestral',90),(6,'semestral',180),(7,'anual',365);
/*!40000 ALTER TABLE `frecuencia_gasto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gasto`
--

DROP TABLE IF EXISTS `gasto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gasto` (
  `cod_gasto` int(11) NOT NULL AUTO_INCREMENT,
  `cod_cat_gasto` int(11) NOT NULL,
  `cod_condicion` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_creacion` date NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_gasto`),
  KEY `cod_cat_gasto` (`cod_cat_gasto`),
  KEY `cod_condicion` (`cod_condicion`),
  CONSTRAINT `gasto_ibfk_1` FOREIGN KEY (`cod_cat_gasto`) REFERENCES `categoria_gasto` (`cod_cat_gasto`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `gasto_ibfk_2` FOREIGN KEY (`cod_condicion`) REFERENCES `condicion_pagoe` (`cod_condicion`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gasto`
--

LOCK TABLES `gasto` WRITE;
/*!40000 ALTER TABLE `gasto` DISABLE KEYS */;
INSERT INTO `gasto` VALUES (2,3,2,'pago inter',100.00,'2025-05-21',NULL,3),(3,3,2,'Pago de inter',298.00,'2025-05-24',NULL,2),(4,4,1,'pago de cisterna',67.00,'2025-05-24','2025-05-31',3),(5,3,2,'pago prueba',100.00,'2025-05-28','2025-06-05',1),(6,5,4,'compra de desechables ',2178.23,'2025-05-28','2025-05-28',3),(7,5,4,'pago bolsa',100.00,'2025-05-28','2025-05-28',3),(8,3,2,'pago inter mayo',243.00,'2025-05-28','2025-06-05',3),(9,4,1,'pago a pablo',459.20,'2025-05-28','2025-06-04',3),(10,3,2,'internet',765.00,'2025-05-28','2025-06-04',3),(11,4,2,'pueba movimineto',983.00,'2025-05-28','2025-06-04',1),(12,4,2,'movimientos',200.00,'2025-05-28','2025-06-04',3),(13,4,2,'pueba merge',632.00,'2025-05-29','2025-06-05',3);
/*!40000 ALTER TABLE `gasto` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_update_movimiento_gasto
AFTER UPDATE ON gasto
FOR EACH ROW
BEGIN
    -- Solo ejecutar si cambió el status
    IF OLD.status <> NEW.status THEN
        CALL R_movimiento_operacion(NEW.cod_gasto, 3);
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `horarios`
--

DROP TABLE IF EXISTS `horarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `horarios` (
  `cod_dia` int(11) NOT NULL AUTO_INCREMENT,
  `cod` int(11) NOT NULL,
  `dia` varchar(15) NOT NULL,
  `desde` time NOT NULL,
  `hasta` time NOT NULL,
  `cerrado` int(11) NOT NULL,
  PRIMARY KEY (`cod_dia`),
  KEY `cod` (`cod`),
  CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`cod`) REFERENCES `empresa` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horarios`
--

LOCK TABLES `horarios` WRITE;
/*!40000 ALTER TABLE `horarios` DISABLE KEYS */;
INSERT INTO `horarios` VALUES (1,3,'lunes','10:00:00','20:00:00',0),(2,3,'martes','10:00:00','20:00:00',0),(3,3,'miercoles','11:00:00','19:00:00',0),(4,3,'jueves','11:00:00','19:00:00',0),(5,3,'viernes','11:00:00','19:00:00',0),(6,3,'sabado','11:00:00','18:00:00',0),(7,3,'domingo','10:00:00','15:00:00',0);
/*!40000 ALTER TABLE `horarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marcas`
--

DROP TABLE IF EXISTS `marcas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marcas` (
  `cod_marca` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_marca`),
  UNIQUE KEY `marca_unica` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marcas`
--

LOCK TABLES `marcas` WRITE;
/*!40000 ALTER TABLE `marcas` DISABLE KEYS */;
INSERT INTO `marcas` VALUES (6,'Alimex',1),(7,'Campo Rico',1),(8,'mavesa',1);
/*!40000 ALTER TABLE `marcas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `movimientos`
--

DROP TABLE IF EXISTS `movimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movimientos` (
  `cod_mov` int(11) NOT NULL AUTO_INCREMENT,
  `cod_operacion` int(11) NOT NULL,
  `cod_tipo_op` int(11) NOT NULL,
  `cod_detalle_op` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_mov`),
  KEY `cod_tipo_op` (`cod_tipo_op`),
  KEY `cod_detalle_op` (`cod_detalle_op`),
  CONSTRAINT `movimientos_ibfk_1` FOREIGN KEY (`cod_tipo_op`) REFERENCES `tipo_operacion` (`cod_tipo_op`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `movimientos_ibfk_2` FOREIGN KEY (`cod_detalle_op`) REFERENCES `detalle_operacion` (`cod_detalle_op`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movimientos`
--

LOCK TABLES `movimientos` WRITE;
/*!40000 ALTER TABLE `movimientos` DISABLE KEYS */;
INSERT INTO `movimientos` VALUES (4,19,1,2,'2025-05-13',2),(5,20,1,1,'2025-05-13',2),(6,21,1,1,'2025-05-14',2),(7,22,1,1,'2025-05-15',2),(8,23,1,1,'2025-05-15',2),(9,24,1,2,'2025-05-16',2),(10,25,1,1,'2025-05-16',2),(11,26,1,2,'2025-05-17',2),(12,27,1,2,'2025-05-17',2),(13,28,1,2,'2025-05-17',2),(14,29,1,2,'2025-05-17',2),(15,30,1,2,'2025-05-18',2),(16,31,1,2,'2025-05-18',2),(18,32,1,1,'2025-05-18',2),(19,10,1,1,'2025-05-18',2),(20,6,2,1,'2025-05-15',2),(21,10,2,1,'2025-05-18',2),(22,11,2,2,'2025-05-19',2),(23,33,1,1,'2025-05-19',2),(24,34,1,1,'2025-05-19',2),(25,35,1,1,'2025-05-20',2),(26,36,1,2,'2025-05-20',2),(27,37,1,1,'2025-05-21',2),(28,39,1,2,'2025-05-22',2),(29,12,2,1,'2025-05-20',2),(30,40,1,2,'2025-05-22',2),(31,32,4,3,'2025-05-22',2),(32,33,4,3,'2025-05-22',2),(33,34,4,3,'2025-05-22',2),(34,41,1,1,'2025-05-22',2),(35,13,2,2,'2025-05-22',2),(36,42,1,1,'2025-05-22',2),(37,37,4,3,'2025-05-22',2),(38,38,4,3,'2025-05-17',2),(39,39,4,3,'2025-05-18',2),(40,40,4,3,'2025-05-22',2),(41,10,4,4,'2025-05-22',2),(42,8,2,1,'2025-05-18',2),(43,5,2,1,'2025-04-29',2),(44,14,2,2,'2025-05-24',2),(45,13,5,6,'2025-05-22',2),(46,14,5,6,'2025-05-22',2),(47,15,5,6,'2025-05-25',2),(48,16,5,6,'2025-05-25',2),(49,17,5,6,'2025-05-25',2),(51,21,5,6,'2025-05-25',2),(52,2,5,7,'2025-05-22',2),(53,3,5,7,'2025-05-25',2),(54,29,4,4,'2025-05-24',2),(55,32,4,4,'2025-05-24',2),(56,34,4,4,'2025-05-17',2),(57,15,2,2,'2025-05-25',2),(58,35,4,4,'2025-05-25',2),(59,36,4,4,'2025-05-25',2),(60,43,1,2,'2025-05-25',2),(61,42,4,3,'2025-05-25',2),(62,43,4,3,'2025-05-25',2),(63,44,1,2,'2025-05-25',2),(64,44,4,3,'2025-05-25',2),(65,45,4,3,'2025-05-25',2),(66,45,1,2,'2025-05-25',2),(67,46,4,3,'2025-05-25',2),(68,47,4,3,'2025-05-25',2),(69,37,4,4,'2025-05-24',2),(70,48,4,3,'2025-05-22',2),(71,49,4,3,'2025-05-17',2),(72,50,4,3,'2025-05-17',2),(73,51,4,3,'2025-05-17',2),(74,46,1,1,'2025-05-26',2),(75,41,4,4,'2025-05-24',2),(76,53,4,3,'2025-05-13',2),(77,47,1,2,'2025-05-27',2),(78,48,1,1,'2025-05-27',2),(79,17,2,2,'2025-05-28',2),(80,5,3,2,'2025-05-28',2),(81,4,3,1,'2025-05-24',2),(82,3,3,2,'2025-05-24',2),(83,6,3,1,'2025-05-28',2),(84,18,2,1,'2025-05-28',2),(85,49,1,1,'2025-05-28',2),(86,50,1,2,'2025-05-28',2),(87,19,2,1,'2025-05-28',2),(88,7,3,1,'2025-05-28',2),(89,9,3,1,'2025-05-28',2),(90,8,3,2,'2025-05-28',2),(91,11,3,2,'2025-05-28',2),(92,53,1,1,'2025-05-28',2),(93,57,4,3,'2025-05-28',2),(94,49,4,4,'2025-05-28',2),(95,50,4,5,'2025-05-28',2),(96,51,4,5,'2025-05-28',2),(97,52,4,5,'2025-05-28',2),(98,12,3,2,'2025-05-28',2),(99,53,4,5,'2025-05-28',2),(100,22,5,6,'2025-05-29',2),(101,20,2,2,'2025-05-29',2),(102,54,4,4,'2025-05-29',2),(103,4,5,7,'2025-05-29',2),(104,21,2,1,'2025-05-29',2),(105,13,3,2,'2025-05-29',2),(106,56,4,5,'2025-05-29',2),(107,57,4,5,'2025-05-24',2),(108,57,1,1,'2025-05-29',2),(109,56,1,1,'2025-05-29',2),(110,55,1,1,'2025-05-28',1);
/*!40000 ALTER TABLE `movimientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `naturaleza_gasto`
--

DROP TABLE IF EXISTS `naturaleza_gasto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `naturaleza_gasto` (
  `cod_naturaleza` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_naturaleza` varchar(20) NOT NULL,
  PRIMARY KEY (`cod_naturaleza`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `naturaleza_gasto`
--

LOCK TABLES `naturaleza_gasto` WRITE;
/*!40000 ALTER TABLE `naturaleza_gasto` DISABLE KEYS */;
INSERT INTO `naturaleza_gasto` VALUES (1,'fijo'),(2,'variable');
/*!40000 ALTER TABLE `naturaleza_gasto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pago_emitido`
--

DROP TABLE IF EXISTS `pago_emitido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pago_emitido` (
  `cod_pago_emitido` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_pago` enum('compra','gasto') NOT NULL,
  `cod_vuelto_r` int(11) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `cod_compra` int(11) DEFAULT NULL,
  `cod_gasto` int(11) DEFAULT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`cod_pago_emitido`),
  KEY `compra-pago` (`cod_compra`),
  KEY `cod_gasto` (`cod_gasto`),
  KEY `cod_vuelto_r` (`cod_vuelto_r`),
  CONSTRAINT `pago_emitido_ibfk_1` FOREIGN KEY (`cod_compra`) REFERENCES `compras` (`cod_compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pago_emitido_ibfk_2` FOREIGN KEY (`cod_gasto`) REFERENCES `gasto` (`cod_gasto`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pago_emitido_ibfk_3` FOREIGN KEY (`cod_vuelto_r`) REFERENCES `vuelto_recibido` (`cod_vuelto_r`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pago_emitido`
--

LOCK TABLES `pago_emitido` WRITE;
/*!40000 ALTER TABLE `pago_emitido` DISABLE KEYS */;
INSERT INTO `pago_emitido` VALUES (1,'compra',1,'2025-05-16 00:00:00',6,NULL,100.00),(6,'compra',NULL,'2025-05-19 00:00:00',10,NULL,20847.20),(7,'compra',NULL,'2025-05-22 00:00:00',12,NULL,1215.00),(8,'compra',NULL,'2025-05-22 00:00:00',11,NULL,4775.90),(9,'compra',NULL,'2025-05-22 00:00:00',9,NULL,20.00),(10,'compra',NULL,'2025-05-23 00:00:00',13,NULL,300.00),(11,'gasto',NULL,'2025-05-24 00:00:00',NULL,2,100.00),(19,'compra',NULL,'2025-05-24 00:00:00',8,NULL,100.00),(20,'compra',NULL,'2025-05-24 00:00:00',13,NULL,200.00),(21,'compra',NULL,'2025-05-24 00:00:00',9,NULL,100.00),(24,'compra',3,'2025-05-24 00:00:00',5,NULL,100.00),(25,'compra',NULL,'2025-05-24 15:29:15',7,NULL,100.00),(26,'gasto',4,'2025-05-24 00:00:00',NULL,4,100.00),(27,'compra',NULL,'2025-05-24 16:15:00',14,NULL,1000.00),(28,'compra',NULL,'2025-05-24 00:00:00',14,NULL,500.00),(29,'compra',NULL,'2025-05-25 14:56:12',14,NULL,100.00),(32,'compra',NULL,'2025-05-25 15:30:34',14,NULL,324.00),(33,'compra',NULL,'2025-05-25 15:34:26',9,NULL,78.00),(34,'compra',NULL,'2025-05-25 18:05:52',7,NULL,57.00),(35,'compra',NULL,'2025-05-25 19:44:05',15,NULL,40.00),(36,'compra',5,'2025-05-25 19:48:45',15,NULL,70.00),(37,'compra',NULL,'2025-05-25 23:46:53',14,NULL,58.43),(38,'compra',NULL,'2025-05-26 20:36:36',14,NULL,10.00),(39,'compra',NULL,'2025-05-26 20:36:46',14,NULL,20.00),(40,'compra',NULL,'2025-05-26 20:45:49',14,NULL,10.00),(41,'compra',NULL,'2025-05-26 20:47:39',14,NULL,20.00),(43,'gasto',6,'2025-05-28 02:17:50',NULL,6,2200.00),(44,'compra',7,'2025-05-28 02:43:02',18,NULL,480.00),(45,'gasto',NULL,'2025-05-28 12:20:40',NULL,3,10.00),(46,'compra',8,'2025-05-28 14:33:46',19,NULL,2000.00),(47,'gasto',9,'2025-05-28 15:08:56',NULL,7,150.00),(48,'gasto',10,'2025-05-28 16:01:13',NULL,9,500.00),(49,'compra',NULL,'2025-05-28 19:41:35',17,NULL,285.00),(50,'gasto',NULL,'2025-05-28 19:43:44',NULL,10,479.05),(51,'gasto',11,'2025-05-28 20:28:35',NULL,8,287.43),(52,'gasto',NULL,'2025-05-28 20:33:59',NULL,10,285.95),(53,'gasto',12,'2025-05-28 20:36:05',NULL,12,287.43),(54,'compra',13,'2025-05-29 00:13:29',20,NULL,195.81),(55,'compra',NULL,'2025-05-29 03:50:28',21,NULL,3424.30),(56,'gasto',14,'2025-05-29 03:54:24',NULL,13,700.00),(57,'gasto',NULL,'2025-05-29 12:11:24',NULL,3,10.00);
/*!40000 ALTER TABLE `pago_emitido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pago_recibido`
--

DROP TABLE IF EXISTS `pago_recibido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pago_recibido` (
  `cod_pago` int(11) NOT NULL AUTO_INCREMENT,
  `cod_venta` int(11) NOT NULL,
  `cod_vuelto` int(11) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`cod_pago`),
  KEY `pagos-ventas` (`cod_venta`),
  KEY `cod_vuelto` (`cod_vuelto`),
  CONSTRAINT `pago_recibido_ibfk_1` FOREIGN KEY (`cod_venta`) REFERENCES `ventas` (`cod_venta`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pago_recibido_ibfk_2` FOREIGN KEY (`cod_vuelto`) REFERENCES `vuelto_emitido` (`cod_vuelto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pago_recibido`
--

LOCK TABLES `pago_recibido` WRITE;
/*!40000 ALTER TABLE `pago_recibido` DISABLE KEYS */;
INSERT INTO `pago_recibido` VALUES (1,11,NULL,'2025-05-12 13:47:17',2.21),(2,10,NULL,'2025-05-12 18:09:31',10.00),(3,9,NULL,'2025-05-12 18:20:54',10.00),(4,8,NULL,'2025-05-12 18:43:39',5.00),(5,13,1,'2025-05-12 19:40:11',20.00),(6,12,2,'2025-05-12 19:41:34',10.00),(7,14,NULL,'2025-05-12 21:47:07',5.00),(8,15,NULL,'2025-05-12 22:02:05',5.00),(9,16,NULL,'2025-05-12 22:46:41',5.00),(10,14,3,'2025-05-12 22:47:03',10.00),(11,17,4,'2025-05-13 09:31:17',10.00),(12,16,NULL,'2025-05-13 09:32:59',1.00),(13,20,5,'2025-05-13 23:40:46',25.00),(14,19,NULL,'2025-05-13 23:56:17',5.00),(15,21,6,'2025-05-14 17:53:43',4.00),(16,22,NULL,'2025-05-15 02:32:09',9.58),(17,23,NULL,'2025-05-15 12:38:10',26.46),(18,24,NULL,'2025-05-16 23:49:59',14.74),(19,25,NULL,'2025-05-16 23:51:50',5.09),(25,32,NULL,'2025-05-18 22:58:01',589.92),(26,31,NULL,'2025-05-19 12:49:20',98.92),(27,33,NULL,'2025-05-19 13:39:02',494.76),(28,33,NULL,'2025-05-19 13:40:19',76.64),(29,34,NULL,'2025-05-19 23:10:28',660.10),(30,35,NULL,'2025-05-20 10:05:20',1142.81),(31,37,7,'2025-05-21 00:25:06',100.00),(32,39,8,'2025-05-22 01:46:41',20.00),(33,40,NULL,'2025-05-22 19:46:14',12.00),(34,40,NULL,'2025-05-22 20:59:20',53.00),(35,41,9,'2025-05-22 21:09:10',40.00),(36,42,10,'2025-05-22 22:41:25',600.00),(37,40,NULL,'2025-05-22 22:50:52',10.00),(38,29,NULL,'2025-05-22 23:35:16',956.57),(39,30,11,'2025-05-23 01:18:33',120.00),(40,40,NULL,'2025-05-23 01:20:26',10.00),(41,36,12,'2025-05-24 01:54:47',850.00),(42,43,NULL,'2025-05-25 19:51:48',40.07),(43,43,13,'2025-05-25 19:52:25',70.00),(44,44,NULL,'2025-05-25 21:14:41',40.78),(45,44,14,'2025-05-25 21:14:52',70.00),(46,45,NULL,'2025-05-25 21:33:48',40.07),(47,45,15,'2025-05-25 21:34:00',70.00),(48,40,16,'2025-05-26 02:07:03',5.00),(49,28,17,'2025-05-26 12:56:53',10000.00),(50,27,NULL,'2025-05-26 13:19:23',907.76),(51,26,18,'2025-05-26 14:08:30',1137.60),(52,46,19,'2025-05-26 14:42:27',948.80),(53,19,NULL,'2025-05-27 02:44:51',1.37),(54,48,20,'2025-05-27 23:51:15',480.00),(55,49,21,'2025-05-28 12:25:13',288.00),(56,53,22,'2025-05-28 19:28:43',100.00),(57,50,NULL,'2025-05-28 19:29:20',191.62),(58,57,23,'2025-05-29 23:35:55',1061.83),(59,56,24,'2025-05-30 02:57:14',289.59),(60,55,25,'2025-05-30 07:08:05',200.00);
/*!40000 ALTER TABLE `pago_recibido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presentacion_producto`
--

DROP TABLE IF EXISTS `presentacion_producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `presentacion_producto` (
  `cod_presentacion` int(11) NOT NULL AUTO_INCREMENT,
  `cod_unidad` int(11) NOT NULL,
  `cod_producto` int(11) NOT NULL,
  `presentacion` varchar(30) DEFAULT NULL,
  `cantidad_presentacion` varchar(20) DEFAULT NULL,
  `costo` decimal(10,2) NOT NULL,
  `porcen_venta` int(11) NOT NULL,
  `excento` int(11) NOT NULL,
  PRIMARY KEY (`cod_presentacion`),
  KEY `cod_producto` (`cod_producto`),
  KEY `cod_unidad` (`cod_unidad`),
  CONSTRAINT `presentacion_producto_ibfk_1` FOREIGN KEY (`cod_producto`) REFERENCES `productos` (`cod_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `presentacion_producto_ibfk_2` FOREIGN KEY (`cod_unidad`) REFERENCES `unidades_medida` (`cod_unidad`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presentacion_producto`
--

LOCK TABLES `presentacion_producto` WRITE;
/*!40000 ALTER TABLE `presentacion_producto` DISABLE KEYS */;
INSERT INTO `presentacion_producto` VALUES (1,1,1,'pieza','10',7.00,0,1),(2,1,2,'una pieza','4.5',632.00,35,1),(3,1,3,'pieza','3.5',455.00,34,1),(4,1,4,'Pieza','10',379.04,20,1),(5,3,5,NULL,NULL,323.00,30,1),(6,3,2,NULL,NULL,0.00,0,1),(7,1,6,NULL,NULL,0.00,0,1),(8,1,7,'pieza','2.1',356.87,34,1),(10,1,1,'prueba','5',10.00,20,1),(11,1,1,'prueba','5',10.00,20,1),(12,1,1,'prueba','5',10.00,20,1),(14,4,9,'paquete','300',342.43,50,1);
/*!40000 ALTER TABLE `presentacion_producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presupuestos`
--

DROP TABLE IF EXISTS `presupuestos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `presupuestos` (
  `cod_presupuesto` int(11) NOT NULL AUTO_INCREMENT,
  `cod_cat_gasto` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `mes` date NOT NULL,
  `notas` text DEFAULT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`cod_presupuesto`),
  KEY `fk_presupuestos_categoria_gasto` (`cod_cat_gasto`),
  CONSTRAINT `fk_presupuestos_categoria_gasto` FOREIGN KEY (`cod_cat_gasto`) REFERENCES `categoria_gasto` (`cod_cat_gasto`)
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuestos`
--

LOCK TABLES `presupuestos` WRITE;
/*!40000 ALTER TABLE `presupuestos` DISABLE KEYS */;
INSERT INTO `presupuestos` VALUES (126,3,200.00,'2025-05-01','esto es lo que pretendemos gastar','2025-05-25 02:11:04');
/*!40000 ALTER TABLE `presupuestos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productos` (
  `cod_producto` int(11) NOT NULL AUTO_INCREMENT,
  `cod_categoria` int(11) NOT NULL,
  `cod_marca` int(11) DEFAULT NULL,
  `nombre` varchar(40) NOT NULL,
  `imagen` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`cod_producto`),
  KEY `productos-categorias` (`cod_categoria`),
  KEY `cod_marca` (`cod_marca`),
  CONSTRAINT `productos-categorias` FOREIGN KEY (`cod_categoria`) REFERENCES `categorias` (`cod_categoria`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`cod_marca`) REFERENCES `marcas` (`cod_marca`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,1,NULL,'Queso Duro',NULL),(2,2,6,'jamon de pierna','vista/dist/img/productos/ImgThumb2.jpg'),(3,1,7,'Queso amarillo','vista/dist/img/productos/default.png'),(4,1,7,'queso blanco','vista/dist/img/productos/default.png'),(5,4,8,'mantequilla','vista/dist/img/productos/default.png'),(6,1,NULL,'queso semi','vista/dist/img/productos/default.png'),(7,2,6,'jamon arepero','vista/dist/img/productos/default.png'),(9,10,8,'carga','vista/dist/img/productos/default.png');
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `prov_representantes`
--

DROP TABLE IF EXISTS `prov_representantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prov_representantes` (
  `cod_representante` int(11) NOT NULL AUTO_INCREMENT,
  `cod_prov` int(11) NOT NULL,
  `cedula` varchar(12) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `apellido` varchar(80) DEFAULT NULL,
  `telefono` varchar(12) DEFAULT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_representante`),
  KEY `prov_representantes_ibfk_1` (`cod_prov`),
  CONSTRAINT `prov_representantes_ibfk_1` FOREIGN KEY (`cod_prov`) REFERENCES `proveedores` (`cod_prov`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prov_representantes`
--

LOCK TABLES `prov_representantes` WRITE;
/*!40000 ALTER TABLE `prov_representantes` DISABLE KEYS */;
INSERT INTO `prov_representantes` VALUES (1,2,'10771716','samuel','Rojas','12453145',1);
/*!40000 ALTER TABLE `prov_representantes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedores` (
  `cod_prov` int(11) NOT NULL AUTO_INCREMENT,
  `rif` varchar(15) NOT NULL,
  `razon_social` varchar(50) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `direccion` varchar(250) DEFAULT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_prov`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedores`
--

LOCK TABLES `proveedores` WRITE;
/*!40000 ALTER TABLE `proveedores` DISABLE KEYS */;
INSERT INTO `proveedores` VALUES (1,'J505284788','generico','','',1),(2,'J28516209','ST3M c.a','Pedroperez@gmail.com','av. libertador',1);
/*!40000 ALTER TABLE `proveedores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proyecciones_futuras`
--

DROP TABLE IF EXISTS `proyecciones_futuras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proyecciones_futuras` (
  `cod_proyeccion` int(11) NOT NULL AUTO_INCREMENT,
  `cod_producto` int(11) NOT NULL,
  `mes` date NOT NULL,
  `valor_proyectado` decimal(10,2) NOT NULL,
  `ventana_ma` int(11) NOT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`cod_proyeccion`),
  UNIQUE KEY `unq_producto_mes` (`cod_producto`,`mes`),
  KEY `fk_proyecciones_futuras_productos` (`cod_producto`),
  CONSTRAINT `fk_proyecciones_futuras_productos` FOREIGN KEY (`cod_producto`) REFERENCES `productos` (`cod_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proyecciones_futuras`
--

LOCK TABLES `proyecciones_futuras` WRITE;
/*!40000 ALTER TABLE `proyecciones_futuras` DISABLE KEYS */;
/*!40000 ALTER TABLE `proyecciones_futuras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proyecciones_historicas`
--

DROP TABLE IF EXISTS `proyecciones_historicas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proyecciones_historicas` (
  `cod_historico` int(11) NOT NULL AUTO_INCREMENT,
  `cod_producto` int(11) NOT NULL,
  `mes` date NOT NULL,
  `valor_proyectado` decimal(10,2) NOT NULL,
  `valor_real` decimal(10,2) DEFAULT NULL,
  `precision_valor` int(11) DEFAULT NULL,
  `ventana_ma` int(11) NOT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`cod_historico`),
  KEY `fk_proyecciones_historicas_productos` (`cod_producto`),
  CONSTRAINT `fk_proyecciones_historicas_productos` FOREIGN KEY (`cod_producto`) REFERENCES `productos` (`cod_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proyecciones_historicas`
--

LOCK TABLES `proyecciones_historicas` WRITE;
/*!40000 ALTER TABLE `proyecciones_historicas` DISABLE KEYS */;
/*!40000 ALTER TABLE `proyecciones_historicas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_mensual`
--

DROP TABLE IF EXISTS `stock_mensual`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_mensual` (
  `cod_stock_mensual` int(11) NOT NULL AUTO_INCREMENT,
  `cod_presentacion` int(11) NOT NULL,
  `mes` varchar(20) NOT NULL,
  `stock_inicial` decimal(10,2) DEFAULT NULL,
  `stock_final` decimal(10,2) DEFAULT NULL,
  `ventas_cantidad` decimal(10,2) DEFAULT NULL,
  `rotacion` decimal(8,2) DEFAULT NULL,
  `dias_rotacion` decimal(8,2) DEFAULT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`cod_stock_mensual`) USING BTREE,
  UNIQUE KEY `unq_presentacion_mes` (`cod_presentacion`,`mes`),
  CONSTRAINT `stock_mensual_ibfk_1` FOREIGN KEY (`cod_presentacion`) REFERENCES `presentacion_producto` (`cod_presentacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_mensual`
--

LOCK TABLES `stock_mensual` WRITE;
/*!40000 ALTER TABLE `stock_mensual` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_mensual` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_cuenta`
--

DROP TABLE IF EXISTS `tipo_cuenta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_cuenta` (
  `cod_tipo_cuenta` int(11) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  PRIMARY KEY (`cod_tipo_cuenta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_cuenta`
--

LOCK TABLES `tipo_cuenta` WRITE;
/*!40000 ALTER TABLE `tipo_cuenta` DISABLE KEYS */;
INSERT INTO `tipo_cuenta` VALUES (1,'AHORRO'),(2,'CORRIENTE');
/*!40000 ALTER TABLE `tipo_cuenta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_gasto`
--

DROP TABLE IF EXISTS `tipo_gasto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_gasto` (
  `cod_tipo_gasto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`cod_tipo_gasto`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_gasto`
--

LOCK TABLES `tipo_gasto` WRITE;
/*!40000 ALTER TABLE `tipo_gasto` DISABLE KEYS */;
INSERT INTO `tipo_gasto` VALUES (3,'producto'),(4,'servicio');
/*!40000 ALTER TABLE `tipo_gasto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_operacion`
--

DROP TABLE IF EXISTS `tipo_operacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_operacion` (
  `cod_tipo_op` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(50) NOT NULL,
  PRIMARY KEY (`cod_tipo_op`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_operacion`
--

LOCK TABLES `tipo_operacion` WRITE;
/*!40000 ALTER TABLE `tipo_operacion` DISABLE KEYS */;
INSERT INTO `tipo_operacion` VALUES (1,'venta'),(2,'compra'),(3,'gasto'),(4,'pago'),(5,'ajuste');
/*!40000 ALTER TABLE `tipo_operacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_pago`
--

DROP TABLE IF EXISTS `tipo_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_pago` (
  `cod_metodo` int(11) NOT NULL AUTO_INCREMENT,
  `medio_pago` varchar(50) NOT NULL,
  `modalidad` enum('efectivo','digital') NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_metodo`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_pago`
--

LOCK TABLES `tipo_pago` WRITE;
/*!40000 ALTER TABLE `tipo_pago` DISABLE KEYS */;
INSERT INTO `tipo_pago` VALUES (1,'Efectivo','efectivo',1),(2,'Efectivo USD','efectivo',1),(3,'Punto de Venta','digital',1),(4,'Pago Movil','digital',1),(5,'Transferencia','digital',1),(7,'biopago','digital',1);
/*!40000 ALTER TABLE `tipo_pago` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tlf_proveedores`
--

DROP TABLE IF EXISTS `tlf_proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tlf_proveedores` (
  `cod_tlf` int(11) NOT NULL AUTO_INCREMENT,
  `cod_prov` int(11) NOT NULL,
  `telefono` varchar(12) NOT NULL,
  PRIMARY KEY (`cod_tlf`),
  KEY `cod_prov` (`cod_prov`),
  CONSTRAINT `tlf_proveedores_ibfk_1` FOREIGN KEY (`cod_prov`) REFERENCES `proveedores` (`cod_prov`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tlf_proveedores`
--

LOCK TABLES `tlf_proveedores` WRITE;
/*!40000 ALTER TABLE `tlf_proveedores` DISABLE KEYS */;
INSERT INTO `tlf_proveedores` VALUES (1,2,'04245645108'),(2,2,'12453145213');
/*!40000 ALTER TABLE `tlf_proveedores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unidades_medida`
--

DROP TABLE IF EXISTS `unidades_medida`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unidades_medida` (
  `cod_unidad` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_medida` char(10) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_unidad`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unidades_medida`
--

LOCK TABLES `unidades_medida` WRITE;
/*!40000 ALTER TABLE `unidades_medida` DISABLE KEYS */;
INSERT INTO `unidades_medida` VALUES (1,'kg',1),(3,'UND',1),(4,'ml',1);
/*!40000 ALTER TABLE `unidades_medida` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ventas`
--

DROP TABLE IF EXISTS `ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ventas` (
  `cod_venta` int(11) NOT NULL AUTO_INCREMENT,
  `cod_cliente` int(11) NOT NULL,
  `condicion_pago` enum('contado','credito') NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `fecha` datetime NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_venta`),
  KEY `ventas-clientes` (`cod_cliente`),
  CONSTRAINT `ventas-clientes` FOREIGN KEY (`cod_cliente`) REFERENCES `clientes` (`cod_cliente`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ventas`
--

LOCK TABLES `ventas` WRITE;
/*!40000 ALTER TABLE `ventas` DISABLE KEYS */;
INSERT INTO `ventas` VALUES (7,1,'contado',NULL,98.00,'2025-04-08 20:03:36',3),(8,2,'contado',NULL,4.31,'2025-04-29 22:32:39',3),(9,2,'credito','2025-05-19',7.37,'2025-05-12 01:23:22',3),(10,2,'contado','0000-00-00',3.69,'2025-05-12 01:49:17',3),(11,2,'credito','2025-05-19',2.21,'2025-05-12 13:15:38',3),(12,2,'contado','0000-00-00',3.39,'2025-05-12 19:08:33',3),(13,3,'contado','0000-00-00',14.74,'2025-05-12 19:33:50',3),(14,3,'contado','0000-00-00',10.98,'2025-05-12 19:45:45',3),(15,2,'contado','0000-00-00',14.74,'2025-05-12 21:47:13',2),(16,2,'contado','0000-00-00',6.38,'2025-05-12 22:35:56',2),(17,2,'credito','2025-05-21',3.69,'2025-05-12 22:47:43',3),(18,3,'contado','0000-00-00',2.95,'2025-05-13 20:34:34',1),(19,2,'credito','2025-05-20',6.37,'2025-05-13 23:35:37',3),(20,3,'contado','0000-00-00',21.37,'2025-05-13 23:36:53',3),(21,2,'contado','0000-00-00',3.14,'2025-05-14 17:52:50',3),(22,3,'contado','0000-00-00',9.58,'2025-05-15 02:28:56',3),(23,2,'contado','0000-00-00',26.46,'2025-05-15 12:36:49',3),(24,2,'credito','2025-05-23',14.74,'2025-05-16 14:43:01',3),(25,3,'contado','0000-00-00',5.09,'2025-05-16 23:50:54',3),(26,2,'credito','2025-05-22',989.21,'2025-05-17 22:55:53',3),(27,3,'credito','2025-05-19',907.76,'2025-05-17 22:56:42',3),(28,3,'credito','2025-05-19',989.21,'2025-05-17 22:57:42',3),(29,2,'credito','2025-05-24',956.57,'2025-05-17 23:52:03',3),(30,3,'credito','2025-05-20',108.93,'2025-05-18 00:13:18',3),(31,3,'credito','2025-05-25',98.92,'2025-05-18 00:23:09',3),(32,2,'contado','0000-00-00',589.92,'2025-05-18 22:47:35',3),(33,3,'contado','0000-00-00',571.40,'2025-05-19 13:36:36',3),(34,2,'contado','0000-00-00',660.10,'2025-05-19 23:09:09',3),(35,2,'contado','0000-00-00',1142.81,'2025-05-20 10:04:57',3),(36,2,'credito','2025-05-27',821.94,'2025-05-20 11:35:21',3),(37,2,'contado','0000-00-00',87.10,'2025-05-21 00:16:39',3),(39,2,'credito','2025-05-29',17.42,'2025-05-22 00:40:44',3),(40,3,'credito','2025-05-22',87.10,'2025-05-22 16:36:12',3),(41,3,'contado','0000-00-00',30.48,'2025-05-22 21:08:43',3),(42,2,'contado','0000-00-00',564.61,'2025-05-22 22:40:33',3),(43,3,'credito','2025-06-01',100.07,'2025-05-25 19:50:33',3),(44,2,'credito','2025-06-01',100.78,'2025-05-25 21:13:23',3),(45,2,'credito','2025-06-01',100.07,'2025-05-25 21:31:30',3),(46,2,'contado','0000-00-00',684.27,'2025-05-26 14:32:57',3),(47,2,'credito','2025-06-03',101.63,'2025-05-27 23:49:31',1),(48,2,'contado','0000-00-00',454.85,'2025-05-27 23:50:33',3),(49,2,'contado','0000-00-00',260.00,'2025-05-28 12:24:57',3),(50,2,'credito','2025-06-04',227.43,'2025-05-28 13:26:27',2),(51,3,'contado','0000-00-00',2540.64,'2025-05-28 13:27:15',1),(52,2,'contado','0000-00-00',260.00,'2025-05-28 13:31:50',1),(53,3,'contado','0000-00-00',95.64,'2025-05-28 13:32:55',3),(54,3,'contado','0000-00-00',95.64,'2025-05-28 13:32:55',1),(55,3,'contado','0000-00-00',95.64,'2025-05-28 13:32:55',3),(56,2,'contado','0000-00-00',273.02,'2025-05-29 20:51:34',3),(57,2,'contado','0000-00-00',1036.30,'2025-05-29 22:46:57',3);
/*!40000 ALTER TABLE `ventas` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_ventas_update_status` AFTER UPDATE ON `ventas` FOR EACH ROW BEGIN
    -- Solo ejecutar si cambió el status
    IF OLD.status <> NEW.status THEN
        CALL R_movimiento_operacion(NEW.cod_venta, 1);
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Temporary table structure for view `vista_pendientes_compras_gastos`
--

DROP TABLE IF EXISTS `vista_pendientes_compras_gastos`;
/*!50001 DROP VIEW IF EXISTS `vista_pendientes_compras_gastos`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vista_pendientes_compras_gastos` AS SELECT
 1 AS `cod_transaccion`,
  1 AS `tipo`,
  1 AS `asunto`,
  1 AS `monto_total`,
  1 AS `fecha_vencimiento`,
  1 AS `fecha`,
  1 AS `monto_pagado`,
  1 AS `monto_pendiente`,
  1 AS `dias_restantes`,
  1 AS `status` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `vuelto_emitido`
--

DROP TABLE IF EXISTS `vuelto_emitido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vuelto_emitido` (
  `cod_vuelto` int(11) NOT NULL AUTO_INCREMENT,
  `vuelto_total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`cod_vuelto`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vuelto_emitido`
--

LOCK TABLES `vuelto_emitido` WRITE;
/*!40000 ALTER TABLE `vuelto_emitido` DISABLE KEYS */;
INSERT INTO `vuelto_emitido` VALUES (1,5.26),(2,6.61),(3,4.02),(4,6.31),(5,3.63),(6,0.86),(7,12.90),(8,2.58),(9,9.52),(10,35.39),(11,11.07),(12,28.06),(13,10.00),(14,10.00),(15,10.00),(16,2.90),(17,9010.79),(18,148.39),(19,264.53),(20,25.15),(21,28.00),(22,4.36),(23,25.53),(24,16.57),(25,104.36);
/*!40000 ALTER TABLE `vuelto_emitido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vuelto_recibido`
--

DROP TABLE IF EXISTS `vuelto_recibido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vuelto_recibido` (
  `cod_vuelto_r` int(11) NOT NULL AUTO_INCREMENT,
  `vuelto_total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`cod_vuelto_r`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vuelto_recibido`
--

LOCK TABLES `vuelto_recibido` WRITE;
/*!40000 ALTER TABLE `vuelto_recibido` DISABLE KEYS */;
INSERT INTO `vuelto_recibido` VALUES (1,3.10),(2,0.20),(3,45.00),(4,33.00),(5,10.00),(6,21.77),(7,80.00),(8,385.00),(9,50.00),(10,40.80),(11,44.43),(12,87.43),(13,25.81),(14,68.00);
/*!40000 ALTER TABLE `vuelto_recibido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Current Database: `seguridad2`
--

USE `seguridad2`;

--
-- Current Database: `savyc+v1`
--

USE `savyc+v1`;

--
-- Final view structure for view `vista_pendientes_compras_gastos`
--

/*!50001 DROP VIEW IF EXISTS `vista_pendientes_compras_gastos`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_pendientes_compras_gastos` AS select `c`.`cod_compra` AS `cod_transaccion`,concat('Compra','-',`c`.`condicion_pago`) AS `tipo`,`p`.`razon_social` AS `asunto`,`c`.`total` AS `monto_total`,`c`.`fecha_vencimiento` AS `fecha_vencimiento`,`c`.`fecha` AS `fecha`,coalesce(sum(`pe`.`monto_total`),0) AS `monto_pagado`,`c`.`total` - coalesce(sum(`pe`.`monto_total`),0) AS `monto_pendiente`,coalesce(to_days(`c`.`fecha_vencimiento`) - to_days(curdate()),0) AS `dias_restantes`,case when `c`.`status` = 3 then 'Pagado' when `c`.`fecha_vencimiento` is null then 'Vencido' when `c`.`fecha_vencimiento` < curdate() then 'Vencido' when `c`.`status` = 2 then 'Pago parcial' else 'Pendiente' end AS `status` from ((`compras` `c` join `proveedores` `p` on(`p`.`cod_prov` = `c`.`cod_prov`)) left join `pago_emitido` `pe` on(`pe`.`cod_compra` = `c`.`cod_compra`)) where `c`.`status` in (1,2) group by `c`.`cod_compra`,`p`.`razon_social`,`c`.`total`,`c`.`fecha`,`c`.`fecha_vencimiento`,`c`.`status` union all select `g`.`cod_gasto` AS `cod_transaccion`,concat('Gasto','-',`cp`.`nombre_condicion`) AS `tipo`,concat(`cg`.`nombre`,': ',`g`.`descripcion`) AS `asunto`,`g`.`monto` AS `monto_total`,case when `fg`.`dias` is not null then `g`.`fecha_creacion` + interval `fg`.`dias` day else NULL end AS `fecha_vencimiento`,`g`.`fecha_creacion` AS `fecha`,coalesce(sum(`pe`.`monto_total`),0) AS `monto_pagado`,`g`.`monto` - coalesce(sum(`pe`.`monto_total`),0) AS `monto_pendiente`,coalesce(case when `fg`.`dias` is not null then to_days(`g`.`fecha_creacion` + interval `fg`.`dias` day) - to_days(curdate()) else NULL end,0) AS `dias_restantes`,case when `g`.`status` = 3 then 'Pagado' when `g`.`status` = 2 then 'Pago parcial' when `g`.`fecha_creacion` + interval `fg`.`dias` day < curdate() then 'Vencido' else 'Pendiente' end AS `status` from ((((`gasto` `g` join `categoria_gasto` `cg` on(`cg`.`cod_cat_gasto` = `g`.`cod_cat_gasto`)) join `condicion_pagoe` `cp` on(`cp`.`cod_condicion` = `g`.`cod_condicion`)) left join `frecuencia_gasto` `fg` on(`fg`.`cod_frecuencia` = `cg`.`cod_frecuencia`)) left join `pago_emitido` `pe` on(`pe`.`cod_gasto` = `g`.`cod_gasto`)) where `g`.`status` in (1,2) group by `g`.`cod_gasto`,`g`.`descripcion`,`g`.`monto`,`g`.`fecha_creacion`,`fg`.`dias`,`g`.`status` order by `dias_restantes` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-30 10:32:06
