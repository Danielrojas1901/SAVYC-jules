-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: seguridad
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
-- Current Database: `seguridad`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `seguridad` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish2_ci */;

USE `seguridad`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backup`
--

LOCK TABLES `backup` WRITE;
/*!40000 ALTER TABLE `backup` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=464 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bitacora`
--

LOCK TABLES `bitacora` WRITE;
/*!40000 ALTER TABLE `bitacora` DISABLE KEYS */;
INSERT INTO `bitacora` VALUES (1,1,'Acceso a Ventas','2025-06-07 23:11:47','','Ventas'),(2,1,'Acceso a Compras','2025-06-07 23:11:47','','Compras'),(3,1,'Acceso a Productos','2025-06-07 23:13:28','','Productos'),(4,1,'Acceso a Ajuste de Inventario','2025-06-07 23:13:29','','Ajuste de Inventario'),(5,1,'Acceso a Carga de productos','2025-06-07 23:13:29','','Carga de productos'),(6,1,'Acceso a Compras','2025-06-07 23:13:30','','Compras'),(7,1,'Acceso a Contabilidad','2025-06-07 23:13:32','','Contabilidad'),(8,1,'Acceso a Reportes contables','2025-06-07 23:13:33','','Reportes contables'),(9,1,'Acceso a Finanzas','2025-06-07 23:13:35','','Finanzas'),(10,1,'Acceso a Finanzas','2025-06-07 23:13:35','','Finanzas'),(11,1,'Acceso a Ajuste de Inventario','2025-06-07 23:13:51','','Ajuste de Inventario'),(12,1,'Acceso a Descarga de productos','2025-06-07 23:13:51','','Descarga de productos'),(13,1,'Acceso a Productos','2025-06-07 23:13:53','','Productos'),(14,1,'Acceso a Gastos','2025-06-07 23:15:43','','Gastos'),(15,1,'Acceso a Caja','2025-06-07 23:15:44','','Caja'),(16,1,'Acceso a Cuentas pendientes','2025-06-07 23:15:45','','Cuentas pendientes'),(17,1,'Acceso a Tipos de pago','2025-06-07 23:15:56','','Tipos de pago'),(18,1,'Acceso a Divisas','2025-06-07 23:15:58','','Divisas'),(19,1,'Acceso a Gastos','2025-06-07 23:16:03','','Gastos'),(20,1,'Acceso a Reporte De proveedores','2025-06-07 23:16:05','','Reporte De proveedores'),(21,1,'Acceso a Reporte De Cuentas Pendientes','2025-06-07 23:16:07','','Reporte De Cuentas Pendientes'),(22,1,'Acceso a Ajuste de Inventario','2025-06-07 23:16:08','','Ajuste de Inventario'),(23,1,'Acceso a Descarga de productos','2025-06-07 23:16:09','','Descarga de productos'),(24,1,'Acceso a Productos','2025-06-07 23:16:10','','Productos'),(25,1,'Acceso a Clientes','2025-06-07 23:16:12','','Clientes'),(26,1,'Acceso a Compras','2025-06-07 23:16:13','','Compras'),(27,1,'Acceso a Ventas','2025-06-07 23:16:15','','Ventas'),(28,1,'Acceso al sistema','2025-06-07 23:17:13','admin','Inicio'),(29,1,'Acceso a Compras','2025-06-07 23:17:51','','Compras'),(30,1,'Acceso a Gastos','2025-06-07 23:17:53','','Gastos'),(31,1,'Acceso a Ajuste Empresa','2025-06-07 23:17:55','','Ajuste Empresa'),(32,1,'Acceso a Cuentas pendientes','2025-06-07 23:17:57','','Cuentas pendientes'),(33,1,'Acceso a Productos','2025-06-07 23:19:35','','Productos'),(34,1,'Acceso a Contabilidad','2025-06-07 23:19:43','','Contabilidad'),(35,1,'Acceso a Catálogo de cuentas','2025-06-07 23:19:43','','Catálogo de cuentas'),(36,1,'Acceso a Reporte De proveedores','2025-06-07 23:20:06','','Reporte De proveedores'),(37,1,'Acceso a Compras','2025-06-07 23:20:09','','Compras'),(38,1,'Acceso a Contabilidad','2025-06-07 23:20:53','','Contabilidad'),(39,1,'Acceso a Catálogo de cuentas','2025-06-07 23:20:54','','Catálogo de cuentas'),(40,1,'Acceso a Contabilidad','2025-06-07 23:23:52','','Contabilidad'),(41,1,'Acceso a Gestionar asientos','2025-06-07 23:23:53','','Gestionar asientos'),(42,1,'Registro de Asiento de Apertura','2025-06-08 16:29:57','Cuenta contable','Contabilidad'),(43,1,'Registro de Asiento de Apertura','2025-06-08 18:57:05','Cuenta contable','Contabilidad'),(44,1,'Acceso a Contabilidad','2025-06-08 19:02:40','','Contabilidad'),(45,1,'Acceso a Gestionar asientos','2025-06-08 19:02:41','','Gestionar asientos'),(46,1,'Registro de Asiento de Apertura','2025-06-08 20:41:17','Códigos contables: 3.1.1.01.01, 1.1.3.01.01','Contabilidad'),(47,1,'Registro de Asiento de Apertura','2025-06-08 21:00:15','Códigos: 5.4.1.01.01, 1.1.4.01.01','Administración'),(48,1,'Acceso a Productos','2025-06-08 21:19:54','','Productos'),(49,1,'Acceso a Contabilidad','2025-06-08 21:19:57','','Contabilidad'),(50,1,'Acceso a Gestionar asientos','2025-06-08 21:19:58','','Gestionar asientos'),(51,1,'Registro de Asiento de Apertura','2025-06-08 22:20:51','Códigos: 5.1.1.01.01, 5.2.1.01.01','Administración'),(52,1,'Acceso a Contabilidad','2025-06-08 23:01:35','','Contabilidad'),(53,1,'Acceso a Catálogo de cuentas','2025-06-08 23:01:36','','Catálogo de cuentas'),(54,1,'Acceso a Contabilidad','2025-06-09 01:16:41','','Contabilidad'),(55,1,'Acceso a Catálogo de cuentas','2025-06-09 01:16:41','','Catálogo de cuentas'),(56,1,'Acceso a Contabilidad','2025-06-09 01:16:42','','Contabilidad'),(57,1,'Acceso a Gestionar asientos','2025-06-09 01:16:43','','Gestionar asientos'),(58,1,'Registro de Asiento de Apertura','2025-06-09 02:41:40','Códigos: 2.2.1.01.01, 3.1.1.01.01','Administración'),(59,1,'Registro de Asiento de Apertura','2025-06-09 02:52:53','Códigos: 2.2.1.01.01, 3.1.1.01.01','Administración'),(60,1,'Registro de Asiento de Apertura','2025-06-09 02:53:23','Códigos: 2.2.1.01.01, 3.1.1.01.01','Administración'),(61,1,'Registro de Asiento de Apertura','2025-06-09 02:55:29','Códigos: 2.2.1.01.01, 3.1.1.01.01','Administración'),(62,1,'Registro de Asiento de Apertura','2025-06-09 02:58:44','Códigos: 4.2.1.01.01, 1.2.1.01.01','Administración'),(63,1,'Acceso a Contabilidad','2025-06-09 03:13:25','','Contabilidad'),(64,1,'Acceso a Catálogo de cuentas','2025-06-09 03:13:25','','Catálogo de cuentas'),(65,1,'Acceso a Finanzas','2025-06-09 03:16:37','','Finanzas'),(66,1,'Acceso a Productos','2025-06-09 03:16:45','','Productos'),(67,1,'Acceso a Cuentas pendientes','2025-06-09 03:16:46','','Cuentas pendientes'),(68,1,'Acceso a Divisas','2025-06-09 03:16:53','','Divisas'),(69,1,'Acceso a Tipos de pago','2025-06-09 03:16:56','','Tipos de pago'),(70,1,'Acceso a Usuarios','2025-06-09 03:17:00','','Usuarios'),(71,1,'Acceso a Divisas','2025-06-09 03:17:20','','Divisas'),(72,1,'Acceso a Divisas','2025-06-09 03:20:37','','Divisas'),(73,1,'Acceso a Divisas','2025-06-09 03:20:48','','Divisas'),(74,1,'Actualizar tasa','2025-06-09 03:21:11','Actualizada la tasa de la divisa con código 2','Divisas'),(75,1,'Acceso a Compras','2025-06-09 03:24:11','','Compras'),(76,1,'Acceso a Productos','2025-06-09 03:24:13','','Productos'),(77,1,'Acceso a Caja','2025-06-09 03:24:16','','Caja'),(78,1,'Acceso a Cuenta Bancaria','2025-06-09 03:24:17','','Cuenta Bancaria'),(79,1,'Acceso a Caja','2025-06-09 03:24:52','','Caja'),(80,1,'Acceso a Cuenta Bancaria','2025-06-09 04:07:29','','Cuenta Bancaria'),(81,1,'Acceso a Cuenta Bancaria','2025-06-09 04:09:06','','Cuenta Bancaria'),(82,1,'Acceso a Caja','2025-06-09 04:09:47','','Caja'),(83,1,'Acceso a Cuenta Bancaria','2025-06-09 04:10:24','','Cuenta Bancaria'),(84,1,'Acceso a Caja','2025-06-09 04:10:40','','Caja'),(85,1,'Acceso a Clientes','2025-06-09 17:44:41','','Clientes'),(86,1,'Acceso a Cuentas pendientes','2025-06-10 06:43:46','','Cuentas pendientes'),(87,1,'Acceso a Conciliación bancaria','2025-06-10 15:20:41','','Conciliación bancaria'),(88,1,'Acceso a Banco','2025-06-10 15:24:28','','Banco'),(89,1,'Acceso a Banco','2025-06-10 15:24:49','','Banco'),(90,1,'Acceso a Contabilidad','2025-06-10 15:24:55','','Contabilidad'),(91,1,'Acceso a Reportes contables','2025-06-10 15:25:23','','Reportes contables'),(92,1,'Acceso a Ventas','2025-06-10 15:33:15','','Ventas'),(93,1,'Acceso a Productos','2025-06-10 15:33:42','','Productos'),(94,1,'Acceso a Ventas','2025-06-10 15:39:09','','Ventas'),(95,1,'Acceso a Productos','2025-06-10 15:39:14','','Productos'),(96,1,'Registro de categoría','2025-06-10 15:39:30','Jamones','Categorias'),(97,1,'Registro de unidad de medida','2025-06-10 15:39:39','Kg','Unidad de medida'),(98,1,'Registro de producto','2025-06-10 15:39:53','Jamón arepero','Productos'),(99,1,'Acceso a Ventas','2025-06-10 15:39:55','','Ventas'),(100,1,'Acceso a Productos','2025-06-10 15:40:12','','Productos'),(101,1,'Editar producto','2025-06-10 15:40:28','Jamón arepero','Productos'),(102,1,'Acceso a Ventas','2025-06-10 15:40:30','','Ventas'),(103,1,'Acceso a Compras','2025-06-10 15:40:33','','Compras'),(104,1,'Buscar proveedor','2025-06-10 15:40:40','J28516209','Proveedores'),(105,1,'Acceso a Proveedores','2025-06-10 15:40:47','','Proveedores'),(106,1,'Buscar proveedor','2025-06-10 15:40:52','J28516209','Proveedores'),(107,1,'Registro de proveedor','2025-06-10 15:40:54','Punta del Monte','Proveedores'),(108,1,'Acceso a Ventas','2025-06-10 15:40:56','','Ventas'),(109,1,'Acceso a Compras','2025-06-10 15:40:56','','Compras'),(110,1,'Buscar proveedor','2025-06-10 15:41:02','J28516209','Proveedores'),(111,1,'Acceso a Productos','2025-06-10 15:41:13','','Productos'),(112,1,'Acceso a Compras','2025-06-10 15:41:20','','Compras'),(113,1,'Acceso a Ventas','2025-06-10 15:41:31','','Ventas'),(114,1,'Acceso a Ventas','2025-06-10 15:41:31','','Ventas'),(115,1,'Acceso a Compras','2025-06-10 15:43:37','','Compras'),(116,1,'Acceso a Productos','2025-06-10 15:44:08','','Productos'),(117,1,'Editar producto','2025-06-10 15:45:19','Jamón arepero','Productos'),(118,1,'Acceso a Productos','2025-06-10 15:45:25','','Productos'),(119,1,'Acceso a Compras','2025-06-10 15:45:30','','Compras'),(120,1,'Acceso a Productos','2025-06-10 15:45:47','','Productos'),(121,1,'Registro de producto','2025-06-10 15:46:23','Leche descremada','Productos'),(122,1,'Acceso a Compras','2025-06-10 15:46:29','','Compras'),(123,1,'Acceso a Productos','2025-06-10 15:47:43','','Productos'),(124,1,'Registrar marca','2025-06-10 15:47:54','Puro Lomo','Marcas'),(125,1,'Editar producto','2025-06-10 15:48:01','Jamón arepero','Productos'),(126,1,'Acceso a Compras','2025-06-10 15:48:04','','Compras'),(127,1,'Acceso a Ventas','2025-06-10 15:48:48','','Ventas'),(128,1,'Acceso a Compras','2025-06-10 16:00:01','','Compras'),(129,1,'Buscar proveedor','2025-06-10 16:00:07','1','Proveedores'),(130,1,'Buscar proveedor','2025-06-10 16:00:07','1','Proveedores'),(131,1,'Buscar proveedor','2025-06-10 16:00:12','J28516209','Proveedores'),(132,1,'Registro de compra','2025-06-10 16:00:27','890.00','Compras'),(133,1,'Acceso a Ventas','2025-06-10 16:00:31','','Ventas'),(134,1,'Registro de venta','2025-06-10 16:00:44','1.00','Venta'),(135,1,'Acceso a Ajuste de Inventario','2025-06-10 16:01:18','','Ajuste de Inventario'),(136,1,'Acceso a Productos','2025-06-10 16:01:22','','Productos'),(137,1,'Acceso al sistema','2025-06-13 04:04:09','admin','Inicio'),(138,1,'Acceso a Contabilidad','2025-06-13 13:35:27','','Contabilidad'),(139,1,'Acceso a Catálogo de cuentas','2025-06-13 13:35:28','','Catálogo de cuentas'),(140,1,'Acceso a Contabilidad','2025-06-13 13:35:30','','Contabilidad'),(141,1,'Acceso a Gestionar asientos','2025-06-13 13:35:31','','Gestionar asientos'),(142,1,'Acceso al sistema','2025-06-13 13:36:24','admin','Inicio'),(143,1,'Acceso a Contabilidad','2025-06-13 13:36:28','','Contabilidad'),(144,1,'Acceso a Gestionar asientos','2025-06-13 13:36:30','','Gestionar asientos'),(145,1,'Acceso a Caja','2025-06-14 23:33:22','','Caja'),(146,1,'Acceso a Caja','2025-06-14 23:33:23','','Caja'),(147,1,'Acceso a Caja','2025-06-14 23:33:29','','Caja'),(148,1,'Acceso a Cuenta Bancaria','2025-06-14 23:40:25','','Cuenta Bancaria'),(149,1,'Acceso a Caja','2025-06-14 23:48:16','','Caja'),(150,1,'Acceso a Caja','2025-06-14 23:48:21','','Caja'),(151,1,'Acceso a Productos','2025-06-15 00:27:10','','Productos'),(152,1,'Acceso a Ventas','2025-06-15 00:27:14','','Ventas'),(153,1,'Acceso a Contabilidad','2025-06-15 00:27:17','','Contabilidad'),(154,1,'Acceso a Catálogo de cuentas','2025-06-15 00:27:17','','Catálogo de cuentas'),(155,1,'Acceso a Cuenta Bancaria','2025-06-15 00:27:19','','Cuenta Bancaria'),(156,1,'Acceso a Caja','2025-06-15 00:27:21','','Caja'),(157,1,'Acceso a Caja','2025-06-15 00:27:22','','Caja'),(158,1,'Acceso a Caja','2025-06-15 00:27:26','','Caja'),(159,1,'Acceso a Productos','2025-06-15 00:28:41','','Productos'),(160,1,'Acceso a Caja','2025-06-15 00:30:02','','Caja'),(161,1,'Acceso a Contabilidad','2025-06-15 00:30:23','','Contabilidad'),(162,1,'Acceso a Contabilidad','2025-06-15 00:30:24','','Contabilidad'),(163,1,'Acceso a Finanzas','2025-06-15 00:30:25','','Finanzas'),(164,1,'Acceso a Finanzas','2025-06-15 00:30:26','','Finanzas'),(165,1,'Acceso a Caja','2025-06-15 00:30:26','','Caja'),(166,1,'Acceso a Caja','2025-06-15 00:30:28','','Caja'),(167,1,'Acceso a Finanzas','2025-06-15 00:30:37','','Finanzas'),(168,1,'Acceso a Caja','2025-06-15 00:30:38','','Caja'),(169,1,'Acceso a Conciliación bancaria','2025-06-15 00:51:38','','Conciliación bancaria'),(170,1,'Acceso a Conciliación bancaria','2025-06-15 00:51:40','','Conciliación bancaria'),(171,1,'Acceso a Cuenta Bancaria','2025-06-15 00:54:29','','Cuenta Bancaria'),(172,1,'Acceso a Caja','2025-06-15 00:54:32','','Caja'),(173,1,'Acceso a Caja','2025-06-15 00:54:50','','Caja'),(174,1,'Acceso a Caja','2025-06-15 00:55:02','','Caja'),(175,1,'Acceso a Ventas','2025-06-15 00:55:35','','Ventas'),(176,1,'Acceso a Productos','2025-06-15 00:55:37','','Productos'),(177,1,'Acceso a Finanzas','2025-06-15 00:55:39','','Finanzas'),(178,1,'Acceso a Finanzas','2025-06-15 00:55:39','','Finanzas'),(179,1,'Acceso a Gastos','2025-06-15 00:55:40','','Gastos'),(180,1,'Acceso a Tipos de pago','2025-06-15 00:55:45','','Tipos de pago'),(181,1,'Acceso a Unidades de medida','2025-06-15 00:55:48','','Unidades de medida'),(182,1,'Acceso a Productos','2025-06-15 00:55:50','','Productos'),(183,1,'Acceso a Productos','2025-06-15 00:58:43','','Productos'),(184,1,'Acceso a Compras','2025-06-15 01:00:04','','Compras'),(185,1,'Acceso a Productos','2025-06-15 01:00:07','','Productos'),(186,1,'Acceso a Finanzas','2025-06-15 01:00:10','','Finanzas'),(187,1,'Acceso a Finanzas','2025-06-15 01:00:10','','Finanzas'),(188,1,'Acceso a Caja','2025-06-15 01:00:11','','Caja'),(189,1,'Acceso a Cuenta Bancaria','2025-06-15 01:00:12','','Cuenta Bancaria'),(190,1,'Acceso a Cuenta Bancaria','2025-06-15 01:00:16','','Cuenta Bancaria'),(191,1,'Acceso a Caja','2025-06-15 01:00:48','','Caja'),(192,1,'Acceso a Caja','2025-06-15 01:01:16','','Caja'),(193,1,'Acceso a Unidades de medida','2025-06-15 01:01:24','','Unidades de medida'),(194,1,'Acceso a Caja','2025-06-15 01:01:28','','Caja'),(195,1,'Acceso a Contabilidad','2025-06-15 03:27:32','','Contabilidad'),(196,1,'Acceso a Contabilidad','2025-06-15 03:27:34','','Contabilidad'),(197,1,'Acceso a Caja','2025-06-15 03:29:08','','Caja'),(198,1,'Acceso a Caja','2025-06-15 17:16:36','','Caja'),(199,1,'Registro de caja','2025-06-15 18:03:54','Caja prueba','Caja'),(200,1,'Acceso a Caja','2025-06-15 18:12:34','','Caja'),(201,1,'Acceso a Caja','2025-06-15 18:12:39','','Caja'),(202,1,'Acceso a Caja','2025-06-15 18:12:53','','Caja'),(203,1,'Editar Caja','2025-06-15 18:55:01','Caja prueba','Caja'),(204,1,'Editar Caja','2025-06-15 18:55:06','Caja prueba','Caja'),(205,1,'Editar Caja','2025-06-15 18:55:16','Caja prueba','Caja'),(206,1,'Editar Caja','2025-06-15 19:05:40','Caja prueba','Caja'),(207,1,'Editar Caja','2025-06-15 19:05:44','Caja prueba','Caja'),(208,1,'Acceso a Caja','2025-06-15 19:13:51','','Caja'),(209,1,'Acceso a Caja','2025-06-15 19:16:52','','Caja'),(210,1,'Registro de caja','2025-06-15 19:22:30','caja prueba dos','Caja'),(211,1,'Editar Caja','2025-06-15 19:22:38','caja prueba dos','Caja'),(212,1,'Eliminar Caja','2025-06-15 19:22:41','Eliminado la caja con el código 3','Caja'),(213,1,'Acceso a Tipos de pago','2025-06-15 19:23:01','','Tipos de pago'),(214,1,'Registro de tipo de pago','2025-06-15 19:23:12','1','Tipo de pago'),(215,1,'Acceso a Gastos','2025-06-15 19:23:16','','Gastos'),(216,1,'Acceso a Contabilidad','2025-06-15 19:23:18','','Contabilidad'),(217,1,'Acceso a Caja','2025-06-15 19:23:22','','Caja'),(218,1,'Registro de caja','2025-06-15 19:25:39','caja prueba dos','Caja'),(219,1,'Registro de caja','2025-06-15 19:25:57','Manuel','Caja'),(220,1,'Registro de caja','2025-06-15 19:26:38','Jorge','Caja'),(221,1,'Registro de caja','2025-06-15 22:44:35','Caja de prueba','Caja'),(222,1,'Acceso a Compras','2025-06-15 22:46:45','','Compras'),(223,1,'Acceso a Cuenta Bancaria','2025-06-15 22:46:48','','Cuenta Bancaria'),(224,1,'Acceso a Caja','2025-06-15 22:46:51','','Caja'),(225,1,'Registro de caja','2025-06-15 22:46:57','Manuela ','Caja'),(226,1,'Registro de caja','2025-06-15 22:51:51','Euros','Caja'),(227,1,'Acceso a Caja','2025-06-15 22:52:30','','Caja'),(228,1,'Registro de caja','2025-06-15 22:52:59','Daniel','Caja'),(229,1,'Acceso a Caja','2025-06-15 23:01:17','','Caja'),(230,1,'Editar Caja','2025-06-15 23:03:19','Daniel','Caja'),(231,1,'Eliminar Caja','2025-06-15 23:03:22','Eliminado la caja con el código 10','Caja'),(232,1,'Editar Caja','2025-06-15 23:07:52','Euros','Caja'),(233,1,'Eliminar Caja','2025-06-15 23:08:00','Eliminado la caja con el código 9','Caja'),(234,1,'Editar Caja','2025-06-15 23:08:05','Caja de prueba','Caja'),(235,1,'Editar Caja','2025-06-15 23:08:10','Manuela ','Caja'),(236,1,'Eliminar Caja','2025-06-15 23:08:13','Eliminado la caja con el código 7','Caja'),(237,1,'Eliminar Caja','2025-06-15 23:08:17','Eliminado la caja con el código 8','Caja'),(238,1,'Editar Caja','2025-06-15 23:08:27','Jorge','Caja'),(239,1,'Acceso a Cuenta Bancaria','2025-06-15 23:09:19','','Cuenta Bancaria'),(240,1,'Acceso a Cuentas pendientes','2025-06-15 23:09:36','','Cuentas pendientes'),(241,1,'Acceso a Caja','2025-06-15 23:10:38','','Caja'),(242,1,'Registro de caja','2025-06-16 00:03:15','Manuela ','Caja'),(243,1,'Editar Caja','2025-06-16 00:03:26','Manuela ','Caja'),(244,1,'Eliminar Caja','2025-06-16 00:03:30','Eliminado la caja con el código 11','Caja'),(245,1,'Acceso a Caja','2025-06-16 00:17:57','','Caja'),(246,1,'Acceso a Caja','2025-06-16 00:18:07','','Caja'),(247,1,'Acceso a Caja','2025-06-17 01:03:04','','Caja'),(248,1,'Acceso a Caja','2025-06-17 01:21:18','','Caja'),(249,1,'Acceso a Caja','2025-06-17 01:21:22','','Caja'),(250,1,'Acceso a Caja','2025-06-17 01:21:42','','Caja'),(251,1,'Acceso a Caja','2025-06-17 01:21:50','','Caja'),(252,1,'Acceso a Cuentas pendientes','2025-06-17 02:45:03','','Cuentas pendientes'),(253,1,'Acceso a Caja','2025-06-17 02:59:41','','Caja'),(254,1,'Acceso a Ventas','2025-06-17 03:43:00','','Ventas'),(255,1,'Acceso a Banco','2025-06-17 03:43:03','','Banco'),(256,1,'Acceso a Caja','2025-06-17 03:43:07','','Caja'),(257,1,'Acceso a Caja','2025-06-17 03:43:13','','Caja'),(258,1,'Acceso a Ajuste de Inventario','2025-06-17 03:44:04','','Ajuste de Inventario'),(259,1,'Acceso a Caja','2025-06-17 03:44:06','','Caja'),(260,1,'Acceso a Productos','2025-06-17 03:46:21','','Productos'),(261,1,'Acceso a Caja','2025-06-17 03:46:22','','Caja'),(262,1,'Acceso a Contabilidad','2025-06-17 03:46:23','','Contabilidad'),(263,1,'Acceso a Catálogo de cuentas','2025-06-17 03:46:24','','Catálogo de cuentas'),(264,1,'Acceso a Contabilidad','2025-06-17 03:46:25','','Contabilidad'),(265,1,'Acceso a Reportes contables','2025-06-17 03:46:25','','Reportes contables'),(266,1,'Acceso a Reporte De Inventario','2025-06-17 03:46:33','','Reporte De Inventario'),(267,1,'Acceso a Caja','2025-06-17 03:46:35','','Caja'),(268,1,'Acceso al sistema','2025-06-20 14:16:10','admin','Inicio'),(269,1,'Acceso a Ventas','2025-06-20 14:16:33','','Ventas'),(270,1,'Acceso a Clientes','2025-06-20 14:16:45','','Clientes'),(271,1,'Registro de cliente','2025-06-20 14:17:02','Manuela','Clientes'),(272,1,'Acceso a Ventas','2025-06-20 14:17:05','','Ventas'),(273,1,'Registro de venta','2025-06-20 14:17:23','3.00','Venta'),(274,1,'Acceso a Productos','2025-06-20 14:17:32','','Productos'),(275,1,'Acceso a Compras','2025-06-20 14:17:38','','Compras'),(276,1,'Buscar proveedor','2025-06-20 14:17:42','J28516209','Proveedores'),(277,1,'Acceso a Compras','2025-06-20 14:17:58','','Compras'),(278,1,'Acceso a Productos','2025-06-20 14:17:59','','Productos'),(279,1,'Acceso a Compras','2025-06-20 14:18:12','','Compras'),(280,1,'Buscar proveedor','2025-06-20 14:18:17','J28516209','Proveedores'),(281,1,'Acceso a Compras','2025-06-20 14:18:24','','Compras'),(282,1,'Acceso a Ajuste de Inventario','2025-06-20 14:18:26','','Ajuste de Inventario'),(283,1,'Acceso a Carga de productos','2025-06-20 14:18:27','','Carga de productos'),(284,1,'Acceso a Compras','2025-06-20 14:20:11','','Compras'),(285,1,'Acceso a Ventas','2025-06-20 14:20:11','','Ventas'),(286,1,'Acceso a Compras','2025-06-20 14:20:39','','Compras'),(287,1,'Buscar proveedor','2025-06-20 14:21:05','J505284797','Proveedores'),(288,1,'Buscar proveedor','2025-06-20 14:21:11','J28516209','Proveedores'),(289,1,'Acceso a Caja','2025-06-20 14:22:00','','Caja'),(290,1,'Acceso a Contabilidad','2025-06-20 14:22:23','','Contabilidad'),(291,1,'Acceso a Contabilidad','2025-06-20 14:22:27','','Contabilidad'),(292,1,'Acceso a Contabilidad','2025-06-20 14:22:34','','Contabilidad'),(293,1,'Acceso a Finanzas','2025-06-20 14:22:37','','Finanzas'),(294,1,'Acceso a Finanzas','2025-06-20 14:22:37','','Finanzas'),(295,1,'Acceso a Contabilidad','2025-06-20 14:22:45','','Contabilidad'),(296,1,'Acceso a Reportes contables','2025-06-20 14:22:46','','Reportes contables'),(297,1,'Acceso a Productos','2025-06-20 14:22:50','','Productos'),(298,1,'Acceso a Cuentas pendientes','2025-06-20 14:22:54','','Cuentas pendientes'),(299,1,'Acceso a Productos','2025-06-20 14:23:16','','Productos'),(300,1,'Acceso a Ventas','2025-06-20 14:23:18','','Ventas'),(301,1,'Acceso a Compras','2025-06-20 14:23:20','','Compras'),(302,1,'Acceso a Ventas','2025-06-20 14:23:22','','Ventas'),(303,1,'Acceso a Caja','2025-06-20 14:23:57','','Caja'),(304,1,'Acceso a Ventas','2025-06-20 14:25:25','','Ventas'),(305,1,'Acceso a Productos','2025-06-20 14:26:47','','Productos'),(306,1,'Acceso a Caja','2025-06-20 14:35:04','','Caja'),(307,1,'Acceso a Caja','2025-06-20 14:35:24','','Caja'),(308,1,'Acceso a Contabilidad','2025-06-20 14:36:24','','Contabilidad'),(309,1,'Acceso a Caja','2025-06-21 03:52:10','','Caja'),(310,1,'Acceso a Productos','2025-06-21 04:42:43','','Productos'),(311,1,'Acceso a Caja','2025-06-21 04:42:52','','Caja'),(312,1,'Apertura de Caja','2025-06-23 01:52:02','Caja cod #1','Caja'),(313,1,'Acceso a Caja','2025-06-23 01:54:40','','Caja'),(314,1,'Acceso a Ventas','2025-06-23 01:54:48','','Ventas'),(315,1,'Registro de pago completo','2025-06-23 01:54:54','Venta #10 - Monto: 3.00','Ventas - Pago recibido'),(316,1,'Acceso a Caja','2025-06-23 01:54:57','','Caja'),(317,1,'Acceso a Caja','2025-06-23 01:55:23','','Caja'),(318,1,'Acceso a Ventas','2025-06-23 02:03:33','','Ventas'),(319,1,'Acceso a Caja','2025-06-23 02:03:45','','Caja'),(320,1,'Acceso a Compras','2025-06-23 19:40:35','','Compras'),(321,1,'Registro de pago parcial','2025-06-23 19:40:43','Compra #1 - Monto: 1.00','Compras - Pago emitido'),(322,1,'Acceso a Caja','2025-06-23 19:40:46','','Caja'),(323,1,'Acceso a Ventas','2025-06-23 19:40:56','','Ventas'),(324,1,'Registro de pago completo','2025-06-23 19:41:08','Venta #9 - Monto: 2.00','Ventas - Pago recibido'),(325,1,'Acceso a Caja','2025-06-23 19:41:11','','Caja'),(326,1,'Acceso a Ventas','2025-06-23 22:26:11','','Ventas'),(327,1,'Acceso a Compras','2025-06-23 22:26:15','','Compras'),(328,1,'Registro de pago parcial','2025-06-23 22:26:18','Compra #1 - Monto: 1.00','Compras - Pago emitido'),(329,1,'Registro de pago parcial','2025-06-23 22:26:24','Compra #1 - Monto: 1.00','Compras - Pago emitido'),(330,1,'Acceso a Caja','2025-06-23 22:27:26','','Caja'),(331,1,'Apertura de Caja','2025-06-23 23:25:03','Caja cod #4','Caja'),(332,1,'Acceso a Caja','2025-06-24 03:21:38','','Caja'),(333,1,'Acceso a Tipos de pago','2025-06-24 04:28:50','','Tipos de pago'),(334,1,'Registro de metodo de pago','2025-06-24 04:29:08','Cheque','metodo de pago'),(335,1,'Edición de medio de pago','2025-06-24 04:29:35','Cheque','medio de pago'),(336,1,'Registro de tipo de pago','2025-06-24 04:29:43','3','Tipo de pago'),(337,1,'Acceso a Caja','2025-06-24 04:29:47','','Caja'),(338,1,'Acceso a Ventas','2025-06-24 04:30:10','','Ventas'),(339,1,'Acceso a Compras','2025-06-24 04:30:14','','Compras'),(340,1,'Registro de pago parcial','2025-06-24 04:30:25','Compra #1 - Monto: 1.00','Compras - Pago emitido'),(341,1,'Acceso a Caja','2025-06-24 04:30:27','','Caja'),(342,1,'Acceso a Caja','2025-06-24 04:34:51','','Caja'),(343,1,'Acceso a Caja','2025-06-24 04:36:05','','Caja'),(344,1,'Apertura de Caja','2025-06-24 23:44:50','Caja cod #5','Caja'),(345,1,'Acceso a Caja','2025-06-24 23:45:06','','Caja'),(346,1,'Acceso a Caja','2025-06-24 23:46:17','','Caja'),(347,1,'Acceso a Caja','2025-06-25 00:51:33','','Caja'),(348,1,'Acceso a Ventas','2025-06-25 02:43:52','','Ventas'),(349,1,'Registro de venta','2025-06-25 02:44:09','6.00','Venta'),(350,1,'Acceso a Caja','2025-06-25 02:44:25','','Caja'),(351,1,'Acceso a Caja','2025-06-25 02:45:13','','Caja'),(352,1,'Registro de caja','2025-06-25 02:45:23','Nombre:daniel caja','Caja'),(353,1,'Acceso a Caja','2025-06-25 02:45:36','','Caja'),(354,1,'Acceso a Caja','2025-06-25 02:46:43','','Caja'),(355,1,'Acceso a Tipos de pago','2025-06-25 02:48:07','','Tipos de pago'),(356,1,'Editar estado de tipo de pago','2025-06-25 02:48:20','Cheque','Tipo de pago'),(357,1,'Editar estado de tipo de pago','2025-06-25 02:48:33','','Tipo de pago'),(358,1,'Editar estado de tipo de pago','2025-06-25 02:48:36','','Tipo de pago'),(359,1,'Editar estado de tipo de pago','2025-06-25 02:49:12','Efectivo','Tipo de pago'),(360,1,'Acceso a Caja','2025-06-25 02:52:37','','Caja'),(361,1,'Acceso a Caja','2025-06-25 02:52:58','','Caja'),(362,1,'Acceso a Contabilidad','2025-06-25 02:53:41','','Contabilidad'),(363,1,'Acceso a Contabilidad','2025-06-25 02:53:42','','Contabilidad'),(364,1,'Acceso a Caja','2025-06-25 02:53:43','','Caja'),(365,1,'Acceso a Caja','2025-06-25 02:54:46','','Caja'),(366,1,'Acceso a Caja','2025-06-25 02:54:56','','Caja'),(367,1,'Acceso a Tipos de pago','2025-06-25 02:55:27','','Tipos de pago'),(368,1,'Acceso a Caja','2025-06-25 02:56:17','','Caja'),(369,1,'Acceso a Divisas','2025-06-25 02:59:18','','Divisas'),(370,1,'Acceso a Tipos de pago','2025-06-25 02:59:24','','Tipos de pago'),(371,1,'Acceso a Caja','2025-06-25 03:03:28','','Caja'),(372,1,'Acceso a Caja','2025-06-25 03:08:21','','Caja'),(373,1,'Acceso a Caja','2025-06-25 03:22:14','','Caja'),(374,1,'Acceso a Caja','2025-06-25 03:22:39','','Caja'),(375,1,'Acceso a Caja','2025-06-25 03:22:56','','Caja'),(376,1,'Acceso a Tipos de pago','2025-06-25 03:23:29','','Tipos de pago'),(377,1,'Acceso a Caja','2025-06-25 03:23:54','','Caja'),(378,1,'Registro de caja','2025-06-25 03:24:04','Nombre:Manuela caja','Caja'),(379,1,'Edición de medio de pago','2025-06-25 03:24:35','Efectivo','medio de pago'),(380,1,'Registro de tipo de pago','2025-06-25 03:25:01','3','Tipo de pago'),(381,1,'Acceso a Caja','2025-06-25 03:25:04','','Caja'),(382,1,'Apertura de Caja','2025-06-25 03:26:57','Caja cod #4','Caja'),(383,1,'Acceso a Ventas','2025-06-25 03:28:08','','Ventas'),(384,1,'Acceso a Banco','2025-06-25 03:28:45','','Banco'),(385,1,'Acceso a Cuenta Bancaria','2025-06-25 03:28:49','','Cuenta Bancaria'),(386,1,'Acceso a Finanzas','2025-06-25 03:28:58','','Finanzas'),(387,1,'Acceso a Finanzas','2025-06-25 03:28:58','','Finanzas'),(388,1,'Acceso a Caja','2025-06-25 03:28:59','','Caja'),(389,1,'Acceso a Ventas','2025-06-25 03:29:01','','Ventas'),(390,1,'Registro de pago parcial','2025-06-25 03:29:48','Venta #11 - Monto: 1.98','Ventas - Pago recibido'),(391,1,'Acceso a Caja','2025-06-25 03:29:51','','Caja'),(392,1,'Apertura de Caja','2025-06-25 03:33:15','Caja cod #1','Caja'),(393,1,'Cierre de Caja','2025-06-25 03:46:32','Caja cod #1','Caja'),(394,1,'Apertura de Caja','2025-06-25 03:46:36','Caja cod #1','Caja'),(395,1,'Acceso a Ventas','2025-06-25 04:00:55','','Ventas'),(396,1,'Registro de pago parcial','2025-06-25 04:01:03','Venta #11 - Monto: 2.00','Ventas - Pago recibido'),(397,1,'Acceso a Caja','2025-06-25 04:01:04','','Caja'),(398,1,'Acceso a Ventas','2025-06-25 04:13:20','','Ventas'),(399,1,'Registro de pago parcial','2025-06-25 04:13:27','Venta #11 - Monto: 0.98','Ventas - Pago recibido'),(400,1,'Acceso a Caja','2025-06-25 04:13:31','','Caja'),(401,1,'Cierre de Caja','2025-06-25 04:47:22','Caja cod #1','Caja'),(402,1,'Apertura de Caja','2025-06-25 04:47:29','Caja cod #1','Caja'),(403,1,'Acceso a Ventas','2025-06-25 04:47:40','','Ventas'),(404,1,'Registro de pago completo','2025-06-25 04:47:51','Venta #11 - Monto: 1.04','Ventas - Pago recibido'),(405,1,'Acceso a Caja','2025-06-25 04:47:55','','Caja'),(406,1,'Cierre de Caja','2025-06-25 04:48:19','Caja cod #1','Caja'),(407,1,'Acceso a Caja','2025-06-25 04:48:31','','Caja'),(408,1,'Acceso a Caja','2025-06-25 04:49:52','','Caja'),(409,1,'Apertura de Caja','2025-06-25 04:49:55','Caja cod #4','Caja'),(410,1,'Acceso a Caja','2025-06-25 04:50:14','','Caja'),(411,1,'Acceso a Caja','2025-06-25 04:51:03','','Caja'),(412,1,'Acceso a Productos','2025-06-25 04:52:39','','Productos'),(413,1,'Acceso a Tipos de pago','2025-06-25 04:52:47','','Tipos de pago'),(414,1,'Acceso a Cuentas pendientes','2025-06-25 04:52:50','','Cuentas pendientes'),(415,1,'Acceso a Caja','2025-06-25 04:58:20','','Caja'),(416,1,'Apertura de Caja','2025-06-25 13:42:37','Caja cod #1','Caja'),(417,1,'Acceso a Caja','2025-06-25 13:43:42','','Caja'),(418,1,'Acceso a Ventas','2025-06-25 13:52:57','','Ventas'),(419,1,'Acceso a Caja','2025-06-25 13:53:01','','Caja'),(420,1,'Acceso a Caja','2025-06-25 14:05:30','','Caja'),(421,1,'Registro de caja','2025-06-25 14:05:56','Nombre:caja brian','Caja'),(422,1,'Acceso a Caja','2025-06-25 14:07:24','','Caja'),(423,1,'Acceso a Contabilidad','2025-06-25 14:13:46','','Contabilidad'),(424,1,'Acceso a Contabilidad','2025-06-25 14:13:47','','Contabilidad'),(425,1,'Acceso a Caja','2025-06-25 14:27:12','','Caja'),(426,1,'Acceso a Ventas','2025-06-25 14:27:16','','Ventas'),(427,1,'Acceso a Contabilidad','2025-06-25 14:27:25','','Contabilidad'),(428,1,'Acceso a Compras','2025-06-25 14:28:55','','Compras'),(429,1,'Registro de pago parcial','2025-06-25 14:29:09','Compra #1 - Monto: 1.00','Compras - Pago emitido'),(430,1,'Acceso a Caja','2025-06-25 19:06:28','','Caja'),(431,1,'Cierre de Caja','2025-06-26 00:11:11','Caja cod #1','Caja'),(432,1,'Acceso a Caja','2025-06-26 00:12:44','','Caja'),(433,1,'Acceso a Caja','2025-06-26 02:03:10','','Caja'),(434,1,'Acceso a Caja','2025-06-26 02:20:21','','Caja'),(435,1,'Acceso a Caja','2025-06-26 03:18:29','','Caja'),(436,1,'Acceso a Caja','2025-06-26 03:27:26','','Caja'),(437,1,'Acceso a Caja','2025-06-26 03:30:04','','Caja'),(438,1,'Acceso a Ventas','2025-06-26 03:30:13','','Ventas'),(439,1,'Registro de venta','2025-06-26 03:30:26','2.00','Venta'),(440,1,'Registro de pago parcial','2025-06-26 03:30:50','Venta #12 - Monto: 1.98','Ventas - Pago recibido'),(441,1,'Acceso a Caja','2025-06-26 03:31:01','','Caja'),(442,1,'Acceso a Caja','2025-06-26 03:52:05','','Caja'),(443,1,'Acceso a Caja','2025-06-26 04:36:01','','Caja'),(444,1,'Cierre de Caja','2025-06-26 04:36:12','Caja cod #4','Caja'),(445,1,'Acceso a Caja','2025-06-26 04:36:19','','Caja'),(446,1,'Acceso a Caja','2025-06-26 04:45:12','','Caja'),(447,1,'Apertura de Caja','2025-06-26 04:45:23','Caja cod #1','Caja'),(448,1,'Acceso a Ventas','2025-06-26 04:45:29','','Ventas'),(449,1,'Registro de pago completo','2025-06-26 04:45:44','Venta #12 - Monto: 0.99','Ventas - Pago recibido'),(450,1,'Acceso a Caja','2025-06-26 04:45:47','','Caja'),(451,1,'Acceso a Ventas','2025-06-26 04:46:33','','Ventas'),(452,1,'Acceso a Caja','2025-06-26 04:47:01','','Caja'),(453,1,'Cierre de Caja','2025-06-26 04:47:11','Caja cod #1','Caja'),(454,1,'Acceso a Caja','2025-06-26 04:47:21','','Caja'),(455,1,'Acceso a Caja','2025-06-26 04:49:20','','Caja'),(456,1,'Apertura de Caja','2025-06-26 04:49:22','Caja cod #1','Caja'),(457,1,'Cierre de Caja','2025-06-26 04:49:40','Caja cod #1','Caja'),(458,1,'Acceso a Caja','2025-06-26 04:49:46','','Caja'),(459,1,'Acceso a Caja','2025-06-26 14:22:18','','Caja'),(460,1,'Apertura de Caja','2025-06-26 14:22:24','Caja cod #1','Caja'),(461,1,'Acceso a Caja','2025-06-26 14:55:50','','Caja'),(462,1,'Acceso a Caja','2025-06-26 14:59:38','','Caja'),(463,1,'Acceso a Caja','2025-06-26 14:59:51','','Caja');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_usuario`
--

LOCK TABLES `tipo_usuario` WRITE;
/*!40000 ALTER TABLE `tipo_usuario` DISABLE KEYS */;
INSERT INTO `tipo_usuario` VALUES (1,'Administrador',1);
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
INSERT INTO `tpu_permisos` VALUES (1,1,1),(1,1,3),(1,1,4),(1,2,1),(1,2,3),(1,2,4),(1,3,1),(1,3,3),(1,3,4),(1,4,1),(1,4,3),(1,4,4),(1,5,1),(1,5,3),(1,5,4),(1,6,1),(1,6,3),(1,6,4),(1,7,1),(1,7,3),(1,7,4),(1,8,1),(1,8,3),(1,8,4),(1,9,1),(1,9,3),(1,9,4),(1,10,1),(1,10,3),(1,10,4),(1,11,1),(1,11,3),(1,11,4),(1,12,1),(1,12,3),(1,12,4),(1,13,1),(1,13,3),(1,13,4),(1,14,1),(1,14,3),(1,14,4),(1,15,1),(1,15,3),(1,15,4),(1,1,2),(1,2,2),(1,3,2),(1,4,2),(1,5,2),(1,6,2),(1,7,2),(1,8,2),(1,9,2),(1,10,2),(1,11,2),(1,12,2),(1,13,2),(1,14,2),(1,15,2);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Administrador','admin','$2y$10$.nbh0vwGWNkBgsVzkBSoYurftn9Mg.TLYkxmK32KhMKOzaTjaRS3.',1,1);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Current Database: `savycplus`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `savycplus` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish2_ci */;

USE `savycplus`;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `analisis_rentabilidad`
--

LOCK TABLES `analisis_rentabilidad` WRITE;
/*!40000 ALTER TABLE `analisis_rentabilidad` DISABLE KEYS */;
INSERT INTO `analisis_rentabilidad` VALUES (1,1,'2025-06-01',4,4.00,0.00,0.00,'2025-06-25 03:28:58');
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
  `cod_mov` int(11) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  `total` decimal(18,2) NOT NULL,
  `status` enum('automático','apertura','manual') NOT NULL,
  PRIMARY KEY (`cod_asiento`),
  KEY `cod_mov` (`cod_mov`),
  CONSTRAINT `asientos_contables_ibfk_1` FOREIGN KEY (`cod_mov`) REFERENCES `movimientos` (`cod_mov`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asientos_contables`
--

LOCK TABLES `asientos_contables` WRITE;
/*!40000 ALTER TABLE `asientos_contables` DISABLE KEYS */;
INSERT INTO `asientos_contables` VALUES (2,NULL,'2025-06-01 00:00:00','Asiento de apertura',90.00,''),(3,NULL,'2025-06-08 00:00:00','Asiento de apertura',20.00,'apertura'),(4,NULL,'2025-06-08 00:00:00','Asiento de apertura',10.00,'apertura'),(5,NULL,'2025-06-08 00:00:00','Asiento de apertura',10.00,'apertura'),(6,NULL,'2025-06-08 00:00:00','Asiento de apertura',15.00,'apertura'),(7,NULL,'2025-06-08 00:00:00','preba',0.00,'manual'),(8,NULL,'2025-06-08 00:00:00','preba',0.00,'manual'),(9,NULL,'2025-06-08 00:00:00','preba',0.00,'manual'),(10,NULL,'2025-06-08 00:00:00','preba',80.00,'manual'),(11,NULL,'2025-06-08 00:00:00','Prueba asiento manual',17.00,'manual');
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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banco`
--

LOCK TABLES `banco` WRITE;
/*!40000 ALTER TABLE `banco` DISABLE KEYS */;
INSERT INTO `banco` VALUES (1,'Banco de Venezuela'),(2,'Banco Nacional de Crédito'),(3,'BBVA Provincial'),(4,'Banesco'),(5,'Mercantil Banco'),(6,'Banco del Tesoro'),(7,'Bancamiga'),(8,'Banplus'),(9,'Bancaribe'),(10,'Venezolano de Crédito'),(11,'Banco Plaza'),(12,'Banco Fondo Común'),(13,'Banco DELSUR'),(14,'Banco Exterior'),(15,'Banco Sofitasa'),(16,'Bancrecer'),(17,'Banco Caroní'),(18,'Banco Activo'),(19,'100% Banco'),(20,'Mi Banco');
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caja`
--

LOCK TABLES `caja` WRITE;
/*!40000 ALTER TABLE `caja` DISABLE KEYS */;
INSERT INTO `caja` VALUES (1,'Caja Principal',3.04,1,1),(2,'Caja prueba',34.00,2,0),(4,'caja prueba dos',80.04,2,1),(5,'Manuel',80.00,1,1),(6,'Jorge',80.00,2,0),(12,'daniel caja',100.00,2,1),(13,'Manuela caja',120.00,2,1),(14,'caja brian',900.00,1,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cambio_divisa`
--

LOCK TABLES `cambio_divisa` WRITE;
/*!40000 ALTER TABLE `cambio_divisa` DISABLE KEYS */;
INSERT INTO `cambio_divisa` VALUES (1,1,1.00,'0000-00-00'),(45,2,90.00,'2025-06-06'),(46,2,99.09,'2025-06-08');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carga`
--

LOCK TABLES `carga` WRITE;
/*!40000 ALTER TABLE `carga` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoria_gasto`
--

LOCK TABLES `categoria_gasto` WRITE;
/*!40000 ALTER TABLE `categoria_gasto` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Jamones',1);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,'Generico','Generico','1','1',NULL,NULL,1),(2,'Manuela','Mujica','28516209','04265507191','hola@gmail.com',NULL,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compras`
--

LOCK TABLES `compras` WRITE;
/*!40000 ALTER TABLE `compras` DISABLE KEYS */;
INSERT INTO `compras` VALUES (1,1,'contado',NULL,890.00,890.00,0.00,'2025-06-10',NULL,2);
/*!40000 ALTER TABLE `compras` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_compras_update_status` AFTER UPDATE ON `compras` FOR EACH ROW BEGIN
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
  `observacion` varchar(100) NOT NULL,
  `fecha_apertura` datetime NOT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `monto_apertura` decimal(10,2) NOT NULL,
  `monto_cierre` decimal(10,2) DEFAULT NULL,
  `cod_caja` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_control`),
  KEY `cod_caja` (`cod_caja`),
  CONSTRAINT `control_ibfk_1` FOREIGN KEY (`cod_caja`) REFERENCES `caja` (`cod_caja`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `control`
--

LOCK TABLES `control` WRITE;
/*!40000 ALTER TABLE `control` DISABLE KEYS */;
INSERT INTO `control` VALUES (1,'Cierre OK. Montos coinciden.','2025-06-22 21:51:00','2025-06-24 21:37:20',0.00,0.00,1,'0',0),(2,'Cierre OK. Montos coinciden.','2025-06-23 19:25:00','2025-06-24 22:46:51',80.00,0.00,4,'0',0),(3,'Cierre OK. Montos coinciden.','2025-06-24 19:44:00','2025-06-24 23:23:07',80.00,0.00,5,'0',0),(4,'Cierre OK. Montos coinciden. hola','2025-06-24 23:26:00','2025-06-24 23:31:46',80.00,1.98,4,'0',0),(5,'Cierre OK. Montos coinciden.','2025-06-24 23:33:00','2025-06-24 23:46:32',0.00,0.00,1,'0',0),(6,'Diferencia detectada en el cierre.','2025-06-24 23:46:00','2025-06-25 00:47:22',0.00,1.00,1,'0',0),(7,'Cierre OK. Montos coinciden.','2025-06-25 00:47:00','2025-06-25 00:48:19',2.98,4.02,1,'0',0),(8,'Cierre OK. Montos coinciden.','2025-06-25 00:49:00','2025-06-26 00:36:12',80.02,3.96,4,'0',0),(9,'Cierre OK. Montos coinciden.','2025-06-25 09:42:00','2025-06-25 20:11:11',4.02,3.02,1,'1',0),(10,'Cierre OK. Montos coinciden.','2025-06-26 00:45:00','2025-06-26 00:47:11',3.02,3.04,1,'0',0),(11,'Cierre OK. Montos coinciden.','2025-06-26 00:49:00','2025-06-26 00:49:40',3.04,3.04,1,'admin',0),(12,'','2025-06-26 10:22:00',NULL,3.04,NULL,1,'',1);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuenta_bancaria`
--

LOCK TABLES `cuenta_bancaria` WRITE;
/*!40000 ALTER TABLE `cuenta_bancaria` DISABLE KEYS */;
INSERT INTO `cuenta_bancaria` VALUES (1,2,2,'01910073172173072299',0.00,1,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuentas_contables`
--

LOCK TABLES `cuentas_contables` WRITE;
/*!40000 ALTER TABLE `cuentas_contables` DISABLE KEYS */;
INSERT INTO `cuentas_contables` VALUES (1,'1','ACTIVO','deudora',NULL,1,0.00,1),(2,'1.1','ACTIVO CORRIENTE','deudora',1,2,0.00,1),(3,'1.1.1','EFECTIVO Y EQUIVALENTE DE EFECTIVO','deudora',2,3,0.00,1),(4,'1.1.1.01','CAJA','deudora',3,4,0.00,1),(5,'1.1.1.01.01','CAJA PRINCIPAL','deudora',4,5,0.00,1),(6,'1.1.1.02','BANCOS','deudora',3,4,0.00,1),(7,'1.1.1.02.01','DISPONIBILIDADES EN BANCOS','deudora',6,5,0.00,1),(8,'1.1.2','CUENTAS POR COBRAR CORRIENTES','deudora',2,3,0.00,1),(9,'1.1.2.01','CLIENTES','deudora',8,4,0.00,1),(10,'1.1.2.01.01','CLIENTES NACIONALES','deudora',9,5,0.00,1),(11,'1.1.3','IMPUESTOS POR RECUPERAR','deudora',2,3,0.00,1),(12,'1.1.3.01','IVA SOPORTADO','deudora',11,4,0.00,1),(13,'1.1.3.01.01','IVA CRÉDITO FISCAL','deudora',12,5,0.00,1),(14,'1.1.4','INVENTARIOS','deudora',2,3,0.00,1),(15,'1.1.4.01','INVENTARIO DE PRODUCTOS','deudora',14,4,0.00,1),(16,'1.1.4.01.01','INVENTARIO GENERAL DE PRODUCTOS','deudora',15,5,0.00,1),(17,'1.2','ACTIVO NO CORRIENTE','deudora',1,2,0.00,1),(18,'1.2.1','PROPIEDAD, PLANTA Y EQUIPO','deudora',17,3,0.00,1),(19,'1.2.1.01','MAQUINARIA Y EQUIPOS','deudora',18,4,0.00,1),(20,'1.2.1.01.01','MAQUINARIA INDUSTRIAL GENERAL','deudora',19,5,0.00,1),(21,'2','PASIVO','acreedora',NULL,1,0.00,1),(22,'2.1','PASIVO CORRIENTE','acreedora',21,2,0.00,1),(23,'2.1.1','CUENTAS POR PAGAR','acreedora',22,3,0.00,1),(24,'2.1.1.01','PROVEEDORES POR COMPRAS','acreedora',23,4,0.00,1),(25,'2.1.1.01.01','PROVEEDORES NACIONALES','acreedora',24,5,0.00,1),(26,'2.1.1.02','PROVEEDORES POR GASTOS','acreedora',23,4,0.00,1),(27,'2.1.1.02.01','GASTOS PENDIENTES','acreedora',26,5,0.00,1),(28,'2.1.2','IMPUESTOS POR PAGAR','acreedora',22,3,0.00,1),(29,'2.1.2.01','IVA RECAUDADO','acreedora',28,4,0.00,1),(30,'2.1.2.01.01','IVA DÉBITO FISCAL','acreedora',29,5,0.00,1),(31,'2.2','PASIVO NO CORRIENTE','acreedora',21,2,0.00,1),(32,'2.2.1','PRÉSTAMOS A LARGO PLAZO','acreedora',31,3,0.00,1),(33,'2.2.1.01','PRÉSTAMO BANCO XYZ','acreedora',32,4,0.00,1),(34,'2.2.1.01.01','CUOTA PRÉSTAMO XYZ 2025','acreedora',33,5,0.00,1),(35,'3','PATRIMONIO','acreedora',NULL,1,0.00,1),(36,'3.1','CAPITAL SOCIAL','acreedora',35,2,0.00,1),(37,'3.1.1','CAPITAL SOCIAL GENERAL','acreedora',36,3,0.00,1),(38,'3.1.1.01','APORTES DE SOCIOS','acreedora',37,4,0.00,1),(39,'3.1.1.01.01','APORTE INICIAL GENERAL','acreedora',38,5,0.00,1),(40,'4','INGRESOS','acreedora',NULL,1,0.00,1),(41,'4.1','VENTAS DE PRODUCTOS','acreedora',40,2,0.00,1),(42,'4.1.1','INGRESOS POR VENTAS','acreedora',41,3,0.00,1),(43,'4.1.1.01','VENTA DE MERCANCÍA','acreedora',42,4,0.00,1),(44,'4.1.1.01.01','INGRESOS POR VENTA AL DETAL','acreedora',43,5,0.00,1),(45,'4.2','OTROS INGRESOS','acreedora',40,2,0.00,1),(46,'4.2.1','INGRESOS EXTRAORDINARIOS','acreedora',45,3,0.00,1),(47,'4.2.1.01','AJUSTES DE INVENTARIO','acreedora',46,4,0.00,1),(48,'4.2.1.01.01','GANANCIA POR AJUSTE DE INVENTARIO','acreedora',47,5,0.00,1),(49,'5','GASTOS','deudora',NULL,1,0.00,1),(50,'5.1','GASTOS OPERATIVOS','deudora',49,2,0.00,1),(51,'5.1.1','GASTOS GENERALES','deudora',50,3,0.00,1),(52,'5.1.1.01','GASTOS DEL PERIODO','deudora',51,4,0.00,1),(53,'5.1.1.01.01','GASTOS POR OPERACIÓN','deudora',52,5,0.00,1),(54,'5.2','COSTOS DE VENTAS','deudora',49,2,0.00,1),(55,'5.2.1','COSTO DE MERCANCÍA','deudora',54,3,0.00,1),(56,'5.2.1.01','COSTO GENERAL DE PRODUCTOS','deudora',55,4,0.00,1),(57,'5.2.1.01.01','COSTO DE VENTA','deudora',56,5,0.00,1),(58,'5.3','GASTOS FINANCIEROS','deudora',49,2,0.00,1),(59,'5.3.1','INTERESES Y COMISIONES','deudora',58,3,0.00,1),(60,'5.3.1.01','INTERESES PAGADOS','deudora',59,4,0.00,1),(61,'5.3.1.01.01','GASTOS FINANCIEROS GENERALES','deudora',60,5,0.00,1),(62,'5.4','GASTOS EXTRAORDINARIOS','deudora',49,2,0.00,1),(63,'5.4.1','AJUSTES DE INVENTARIO','deudora',62,3,0.00,1),(64,'5.4.1.01','PÉRDIDAS DE INVENTARIO','deudora',63,4,0.00,1),(65,'5.4.1.01.01','PÉRDIDA POR AJUSTE DE INVENTARIO','deudora',64,5,0.00,1);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `descarga`
--

LOCK TABLES `descarga` WRITE;
/*!40000 ALTER TABLE `descarga` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_asientos`
--

LOCK TABLES `detalle_asientos` WRITE;
/*!40000 ALTER TABLE `detalle_asientos` DISABLE KEYS */;
INSERT INTO `detalle_asientos` VALUES (1,2,30,90.00,'Debe'),(2,2,25,90.00,'Haber'),(3,3,5,20.00,'Debe'),(4,3,27,20.00,'Haber'),(5,4,39,10.00,'Debe'),(6,4,13,10.00,'Haber'),(7,5,16,10.00,'Haber'),(8,6,53,15.00,'Debe'),(9,6,57,15.00,'Haber'),(10,11,48,17.00,'Debe'),(11,11,20,17.00,'Haber');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_carga`
--

LOCK TABLES `detalle_carga` WRITE;
/*!40000 ALTER TABLE `detalle_carga` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_compras`
--

LOCK TABLES `detalle_compras` WRITE;
/*!40000 ALTER TABLE `detalle_compras` DISABLE KEYS */;
INSERT INTO `detalle_compras` VALUES (1,1,1,890.00,1.00);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_descarga`
--

LOCK TABLES `detalle_descarga` WRITE;
/*!40000 ALTER TABLE `detalle_descarga` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_pago_emitido`
--

LOCK TABLES `detalle_pago_emitido` WRITE;
/*!40000 ALTER TABLE `detalle_pago_emitido` DISABLE KEYS */;
INSERT INTO `detalle_pago_emitido` VALUES (1,1,1,1.00),(2,2,1,1.00),(3,3,1,1.00),(4,4,4,1.00),(5,5,1,1.00);
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_pago_recibido`
--

LOCK TABLES `detalle_pago_recibido` WRITE;
/*!40000 ALTER TABLE `detalle_pago_recibido` DISABLE KEYS */;
INSERT INTO `detalle_pago_recibido` VALUES (1,1,1,3.00),(2,2,1,2.00),(3,3,5,1.98),(4,4,1,2.00),(5,5,1,0.98),(6,6,1,1.04),(7,7,5,1.98),(8,8,1,0.99);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_productos`
--

LOCK TABLES `detalle_productos` WRITE;
/*!40000 ALTER TABLE `detalle_productos` DISABLE KEYS */;
INSERT INTO `detalle_productos` VALUES (1,1,878,'0000-00-00','829280');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_tipo_pago`
--

LOCK TABLES `detalle_tipo_pago` WRITE;
/*!40000 ALTER TABLE `detalle_tipo_pago` DISABLE KEYS */;
INSERT INTO `detalle_tipo_pago` VALUES (1,1,'efectivo',NULL,1,1),(2,2,'digital',1,NULL,1),(3,1,'efectivo',NULL,2,0),(4,3,'efectivo',NULL,1,0),(5,3,'efectivo',NULL,4,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_ventas`
--

LOCK TABLES `detalle_ventas` WRITE;
/*!40000 ALTER TABLE `detalle_ventas` DISABLE KEYS */;
INSERT INTO `detalle_ventas` VALUES (1,9,1,1.00,1.00,1.000),(2,10,1,3.00,1.00,3.000),(3,11,1,6.00,1.00,6.000),(4,12,1,2.00,1.00,2.000);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_vueltoe`
--

LOCK TABLES `detalle_vueltoe` WRITE;
/*!40000 ALTER TABLE `detalle_vueltoe` DISABLE KEYS */;
INSERT INTO `detalle_vueltoe` VALUES (1,1,1,1.00),(2,2,1,0.97);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_vueltor`
--

LOCK TABLES `detalle_vueltor` WRITE;
/*!40000 ALTER TABLE `detalle_vueltor` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `divisas`
--

LOCK TABLES `divisas` WRITE;
/*!40000 ALTER TABLE `divisas` DISABLE KEYS */;
INSERT INTO `divisas` VALUES (1,'Bolívares','Bs',1),(2,'dolares','USD',1);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa`
--

LOCK TABLES `empresa` WRITE;
/*!40000 ALTER TABLE `empresa` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gasto`
--

LOCK TABLES `gasto` WRITE;
/*!40000 ALTER TABLE `gasto` DISABLE KEYS */;
/*!40000 ALTER TABLE `gasto` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_update_movimiento_gasto` AFTER UPDATE ON `gasto` FOR EACH ROW BEGIN
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
  `cod` int(11) DEFAULT NULL,
  `dia` varchar(15) NOT NULL,
  `desde` time NOT NULL,
  `hasta` time NOT NULL,
  `cerrado` int(11) NOT NULL,
  PRIMARY KEY (`cod_dia`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horarios`
--

LOCK TABLES `horarios` WRITE;
/*!40000 ALTER TABLE `horarios` DISABLE KEYS */;
INSERT INTO `horarios` VALUES (1,NULL,'lunes','10:00:00','20:00:00',0),(2,NULL,'martes','10:00:00','20:00:00',0),(3,NULL,'miercoles','11:00:00','19:00:00',0),(4,NULL,'jueves','11:00:00','19:00:00',0),(5,NULL,'viernes','11:00:00','19:00:00',0),(6,NULL,'sabado','11:00:00','18:00:00',0),(7,NULL,'domingo','10:00:00','15:00:00',0);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marcas`
--

LOCK TABLES `marcas` WRITE;
/*!40000 ALTER TABLE `marcas` DISABLE KEYS */;
INSERT INTO `marcas` VALUES (1,'Puro Lomo',1);
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movimientos`
--

LOCK TABLES `movimientos` WRITE;
/*!40000 ALTER TABLE `movimientos` DISABLE KEYS */;
INSERT INTO `movimientos` VALUES (1,10,1,1,'2025-06-20',1),(2,9,1,1,'2025-06-10',1),(3,11,1,1,'2025-06-24',1),(4,12,1,1,'2025-06-25',1);
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
  KEY `cod_vuelto_r` (`cod_vuelto_r`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pago_emitido`
--

LOCK TABLES `pago_emitido` WRITE;
/*!40000 ALTER TABLE `pago_emitido` DISABLE KEYS */;
INSERT INTO `pago_emitido` VALUES (1,'compra',NULL,'2025-06-23 15:40:38',1,NULL,1.00),(2,'compra',NULL,'2025-06-23 18:26:16',1,NULL,1.00),(3,'compra',NULL,'2025-06-23 18:26:21',1,NULL,1.00),(4,'compra',NULL,'2025-06-24 00:30:17',1,NULL,1.00),(5,'compra',NULL,'2025-06-25 10:28:58',1,NULL,1.00);
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
  KEY `cod_vuelto` (`cod_vuelto`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pago_recibido`
--

LOCK TABLES `pago_recibido` WRITE;
/*!40000 ALTER TABLE `pago_recibido` DISABLE KEYS */;
INSERT INTO `pago_recibido` VALUES (1,10,NULL,'2025-06-22 21:54:50',3.00),(2,9,1,'2025-06-23 15:40:58',2.00),(3,11,NULL,'2025-06-24 23:29:43',1.98),(4,11,NULL,'2025-06-25 00:00:57',2.00),(5,11,NULL,'2025-06-25 00:13:22',0.98),(6,11,NULL,'2025-06-25 00:47:41',1.04),(7,12,NULL,'2025-06-25 23:30:46',1.98),(8,12,2,'2025-06-26 00:45:31',0.99);
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
  KEY `cod_unidad` (`cod_unidad`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presentacion_producto`
--

LOCK TABLES `presentacion_producto` WRITE;
/*!40000 ALTER TABLE `presentacion_producto` DISABLE KEYS */;
INSERT INTO `presentacion_producto` VALUES (1,1,1,'Pieza','100',1.00,0,1),(2,1,2,'Pieza','4.5',25.00,0,2);
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
  KEY `fk_presupuestos_categoria_gasto` (`cod_cat_gasto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuestos`
--

LOCK TABLES `presupuestos` WRITE;
/*!40000 ALTER TABLE `presupuestos` DISABLE KEYS */;
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
  KEY `cod_marca` (`cod_marca`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,1,1,'Jamón arepero','vista/dist/img/productos/default.png'),(2,1,NULL,'Leche descremada','vista/dist/img/productos/default.png');
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
  KEY `prov_representantes_ibfk_1` (`cod_prov`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prov_representantes`
--

LOCK TABLES `prov_representantes` WRITE;
/*!40000 ALTER TABLE `prov_representantes` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedores`
--

LOCK TABLES `proveedores` WRITE;
/*!40000 ALTER TABLE `proveedores` DISABLE KEYS */;
INSERT INTO `proveedores` VALUES (1,'J28516209','Punta del Monte',NULL,NULL,1);
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
  KEY `fk_proyecciones_futuras_productos` (`cod_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proyecciones_futuras`
--

LOCK TABLES `proyecciones_futuras` WRITE;
/*!40000 ALTER TABLE `proyecciones_futuras` DISABLE KEYS */;
INSERT INTO `proyecciones_futuras` VALUES (1,1,'2025-07-01',4.00,1,'2025-06-25 03:28:58'),(2,1,'2025-08-01',4.00,2,'2025-06-25 03:28:58'),(3,1,'2025-09-01',4.00,3,'2025-06-25 03:28:58'),(4,1,'2025-10-01',4.00,4,'2025-06-25 03:28:58'),(5,1,'2025-11-01',4.00,5,'2025-06-25 03:28:58'),(6,1,'2025-12-01',4.00,6,'2025-06-25 03:28:58'),(7,1,'2026-01-01',4.00,6,'2025-06-25 03:28:58'),(8,1,'2026-02-01',4.00,6,'2025-06-25 03:28:58'),(9,1,'2026-03-01',4.00,6,'2025-06-25 03:28:58'),(10,1,'2026-04-01',4.00,6,'2025-06-25 03:28:58'),(11,1,'2026-05-01',4.00,6,'2025-06-25 03:28:58'),(12,1,'2026-06-01',4.00,6,'2025-06-25 03:28:58');
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
  KEY `fk_proyecciones_historicas_productos` (`cod_producto`)
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
  UNIQUE KEY `unq_presentacion_mes` (`cod_presentacion`,`mes`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_mensual`
--

LOCK TABLES `stock_mensual` WRITE;
/*!40000 ALTER TABLE `stock_mensual` DISABLE KEYS */;
INSERT INTO `stock_mensual` VALUES (1,1,'2025-06-01',889.00,889.00,0.00,0.00,0.00,'2025-06-25 03:28:58'),(2,2,'2025-06-01',0.00,0.00,0.00,0.00,0.00,'2025-06-25 03:28:58');
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
INSERT INTO `tipo_gasto` VALUES (1,'producto'),(2,'servicio');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_pago`
--

LOCK TABLES `tipo_pago` WRITE;
/*!40000 ALTER TABLE `tipo_pago` DISABLE KEYS */;
INSERT INTO `tipo_pago` VALUES (1,'','efectivo',1),(2,'Punto de Venta','digital',1),(3,'Efectivo','efectivo',1);
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
  KEY `cod_prov` (`cod_prov`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tlf_proveedores`
--

LOCK TABLES `tlf_proveedores` WRITE;
/*!40000 ALTER TABLE `tlf_proveedores` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unidades_medida`
--

LOCK TABLES `unidades_medida` WRITE;
/*!40000 ALTER TABLE `unidades_medida` DISABLE KEYS */;
INSERT INTO `unidades_medida` VALUES (1,'Kg',1);
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
  KEY `ventas-clientes` (`cod_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ventas`
--

LOCK TABLES `ventas` WRITE;
/*!40000 ALTER TABLE `ventas` DISABLE KEYS */;
INSERT INTO `ventas` VALUES (9,1,'contado','0000-00-00',1.00,'2025-06-10 12:00:31',3),(10,2,'contado','0000-00-00',3.00,'2025-06-20 10:17:06',3),(11,2,'contado','0000-00-00',6.00,'2025-06-24 22:43:53',3),(12,2,'contado','0000-00-00',2.00,'2025-06-25 23:30:14',3);
/*!40000 ALTER TABLE `ventas` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vuelto_emitido`
--

LOCK TABLES `vuelto_emitido` WRITE;
/*!40000 ALTER TABLE `vuelto_emitido` DISABLE KEYS */;
INSERT INTO `vuelto_emitido` VALUES (1,1.00),(2,0.97);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vuelto_recibido`
--

LOCK TABLES `vuelto_recibido` WRITE;
/*!40000 ALTER TABLE `vuelto_recibido` DISABLE KEYS */;
/*!40000 ALTER TABLE `vuelto_recibido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Current Database: `seguridad`
--

USE `seguridad`;

--
-- Current Database: `savycplus`
--

USE `savycplus`;

--
-- Final view structure for view `vista_pendientes_compras_gastos`
--

/*!50001 DROP VIEW IF EXISTS `vista_pendientes_compras_gastos`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
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

-- Dump completed on 2025-06-26 11:01:52
