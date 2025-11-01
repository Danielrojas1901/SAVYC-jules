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

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `seguridad` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci */;

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
) ENGINE=InnoDB AUTO_INCREMENT=1760 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bitacora`
--

LOCK TABLES `bitacora` WRITE;
/*!40000 ALTER TABLE `bitacora` DISABLE KEYS */;
INSERT INTO `bitacora` VALUES (850,1,'Acceso a Gastos','2025-05-20 07:24:42','','Gastos'),(851,1,'Acceso a Categorías','2025-05-20 07:24:47','','Categorías'),(852,1,'Acceso a Ventas','2025-05-20 07:24:57','','Ventas'),(853,1,'Acceso a Ajuste de Inventario','2025-05-20 07:25:00','','Ajuste de Inventario'),(854,1,'Acceso a Descarga de productos','2025-05-20 07:25:01','','Descarga de productos'),(855,1,'Acceso a Productos','2025-05-20 07:25:04','','Productos'),(856,1,'Acceso a Ajuste de Inventario','2025-05-20 07:25:17','','Ajuste de Inventario'),(857,1,'Acceso a Carga de productos','2025-05-20 07:25:19','','Carga de productos'),(858,1,'Acceso a Gastos','2025-05-20 07:25:28','','Gastos'),(859,1,'Acceso a Cuentas pendientes','2025-05-20 07:25:30','','Cuentas pendientes'),(860,1,'Acceso a Compras','2025-05-20 07:25:33','','Compras'),(861,1,'Acceso a Cuentas pendientes','2025-05-20 07:25:35','','Cuentas pendientes'),(862,1,'Acceso a Gastos','2025-05-20 07:37:53','','Gastos'),(863,1,'Acceso a Reporte De proveedores','2025-05-20 07:38:00','','Reporte De proveedores'),(864,1,'Acceso a Reporte De compras','2025-05-20 07:38:08','','Reporte De compras'),(865,1,'Acceso a Contabilidad','2025-05-20 07:38:16','','Contabilidad'),(866,1,'Acceso a Gastos','2025-05-20 07:38:20','','Gastos'),(867,1,'Acceso a Productos','2025-05-20 07:38:32','','Productos'),(868,1,'Acceso a Cuenta Bancaria','2025-05-20 07:38:59','','Cuenta Bancaria'),(869,1,'Acceso a Conciliación bancaria','2025-05-20 07:39:04','','Conciliación bancaria'),(870,1,'Acceso a Gastos','2025-05-20 07:39:11','','Gastos'),(871,1,'Acceso a Cuentas pendientes','2025-05-20 07:39:14','','Cuentas pendientes'),(872,1,'Acceso a Caja','2025-05-20 07:39:19','','Caja'),(873,1,'Acceso a Cuentas pendientes','2025-05-20 07:39:50','','Cuentas pendientes'),(874,1,'Acceso a Contabilidad','2025-05-20 07:39:51','','Contabilidad'),(875,1,'Acceso a Contabilidad','2025-05-20 07:39:52','','Contabilidad'),(876,1,'Acceso a Conciliación bancaria','2025-05-20 07:40:01','','Conciliación bancaria'),(877,1,'Acceso a Clientes','2025-05-20 07:40:19','','Clientes'),(878,1,'Acceso a Proveedores','2025-05-20 07:40:23','','Proveedores'),(879,1,'Acceso a Cuenta Bancaria','2025-05-20 07:40:38','','Cuenta Bancaria'),(880,1,'Acceso a Gastos','2025-05-20 07:40:43','','Gastos'),(881,1,'Registro de gasto','2025-05-20 07:41:40','prueba','Gasto'),(882,1,'Acceso a Gastos','2025-05-20 07:41:52','','Gastos'),(883,1,'Acceso a Gastos','2025-05-20 07:42:54','','Gastos'),(884,1,'Acceso a Reporte De Inventario','2025-05-20 07:42:58','','Reporte De Inventario'),(885,1,'Acceso a Cuentas pendientes','2025-05-20 07:43:34','','Cuentas pendientes'),(886,1,'Acceso a Ajuste Empresa','2025-05-20 07:43:52','','Ajuste Empresa'),(887,1,'Acceso a Usuarios','2025-05-20 07:44:00','','Usuarios'),(888,1,'Acceso a Marcas','2025-05-20 07:44:24','','Marcas'),(889,1,'Acceso a Gastos','2025-05-20 07:44:29','','Gastos'),(890,1,'Acceso a Banco','2025-05-20 07:44:35','','Banco'),(891,1,'Editar banco','2025-05-20 07:44:45','Banco Mercantil','Banco'),(892,1,'Acceso a Cuenta Bancaria','2025-05-20 07:44:51','','Cuenta Bancaria'),(893,1,'Acceso a Banco','2025-05-20 07:44:56','','Banco'),(894,1,'Eliminar Banco','2025-05-20 07:45:04','Eliminado el banco con el código 4','Banco'),(895,1,'Acceso a Cuenta Bancaria','2025-05-20 07:45:08','','Cuenta Bancaria'),(896,1,'Acceso a Contabilidad','2025-05-20 14:11:30','','Contabilidad'),(897,1,'Acceso a Gestionar asientos','2025-05-20 14:11:32','','Gestionar asientos'),(898,1,'Acceso a Contabilidad','2025-05-20 14:11:37','','Contabilidad'),(899,1,'Acceso a Reportes contables','2025-05-20 14:11:38','','Reportes contables'),(900,1,'Acceso a Reportes contables','2025-05-20 14:11:39','','Reportes contables'),(901,1,'Acceso a Productos','2025-05-20 14:11:50','','Productos'),(902,1,'Acceso a Compras','2025-05-20 14:11:55','','Compras'),(903,1,'Acceso a Ventas','2025-05-20 14:12:08','','Ventas'),(904,1,'Acceso a Contabilidad','2025-05-20 14:15:48','','Contabilidad'),(905,1,'Acceso a Catálogo de cuentas','2025-05-20 14:15:50','','Catálogo de cuentas'),(906,1,'Edicion de cuenta contable con el codigo','2025-05-20 14:15:58','1','Contabilidad'),(907,1,'Acceso a Contabilidad','2025-05-20 15:25:07','','Contabilidad'),(908,1,'Acceso a Gestionar asientos','2025-05-20 15:25:08','','Gestionar asientos'),(909,1,'Acceso a Contabilidad','2025-05-20 15:25:13','','Contabilidad'),(910,1,'Acceso a Catálogo de cuentas','2025-05-20 15:25:14','','Catálogo de cuentas'),(911,1,'Edicion de cuenta contable con el codigo','2025-05-20 15:30:51','1','Contabilidad'),(912,1,'Eliminar cuenta contable con el codigo','2025-05-20 15:31:45','43','Contabilidad'),(913,1,'Acceso a Conciliación bancaria','2025-05-20 15:51:48','','Conciliación bancaria'),(914,1,'Acceso a Contabilidad','2025-05-20 15:53:04','','Contabilidad'),(915,1,'Acceso a Gestionar asientos','2025-05-20 15:53:08','','Gestionar asientos'),(916,1,'Acceso a Cuentas pendientes','2025-05-20 15:53:11','','Cuentas pendientes'),(917,1,'Acceso a Contabilidad','2025-05-20 15:53:29','','Contabilidad'),(918,1,'Acceso a Caja','2025-05-20 15:53:35','','Caja'),(919,1,'Registro de caja','2025-05-20 15:53:48',NULL,'Caja'),(920,1,'Acceso a Tipos de pago','2025-05-20 16:04:00','','Tipos de pago'),(921,1,'Editar Caja','2025-05-20 16:04:18','caja dos','Caja'),(922,1,'Eliminar Caja','2025-05-20 16:04:27','Eliminado la caja con el código 6','Caja'),(923,1,'Acceso a Tipos de pago','2025-05-20 16:04:50','','Tipos de pago'),(924,1,'Acceso a Caja','2025-05-20 16:10:24','','Caja'),(925,1,'Acceso a Caja','2025-05-20 16:18:52','','Caja'),(926,1,'Editar Caja','2025-05-20 16:22:15','Caja dolares','Caja'),(927,1,'Eliminar Caja','2025-05-20 16:22:22','Eliminado la caja con el código 5','Caja'),(928,1,'Acceso a Cuenta Bancaria','2025-05-20 16:25:30','','Cuenta Bancaria'),(929,1,'Acceso a Conciliación bancaria','2025-05-20 16:27:53','','Conciliación bancaria'),(930,1,'Acceso a Gastos','2025-05-20 16:27:59','','Gastos'),(931,1,'Acceso a Caja','2025-05-20 16:32:18','','Caja'),(932,1,'Acceso a Gastos','2025-05-20 16:38:15','','Gastos'),(933,1,'Acceso a Cuenta Bancaria','2025-05-20 16:46:06','','Cuenta Bancaria'),(934,1,'Acceso a Cuenta Bancaria','2025-05-20 16:47:07','','Cuenta Bancaria'),(935,1,'Acceso a Cuenta Bancaria','2025-05-20 16:48:27','','Cuenta Bancaria'),(936,1,'Registro de Cuenta','2025-05-20 16:49:20','01050000000000000000','Cuenta Bancaria'),(937,1,'Acceso a Cuenta Bancaria','2025-05-20 16:49:49','','Cuenta Bancaria'),(938,1,'Editar Cuenta','2025-05-20 16:50:25',NULL,'Cuenta Bancaria'),(939,1,'Acceso a Gastos','2025-05-20 16:50:43','','Gastos'),(940,1,'Acceso a Ventas','2025-05-20 16:50:46','','Ventas'),(941,1,'Acceso a Gastos','2025-05-20 16:51:16','','Gastos'),(942,1,'Registro de pago de gasto','2025-05-20 16:51:24','80.00','Pago'),(943,1,'Registro de pago parcial','2025-05-20 16:51:30','50','Pago'),(944,1,'Acceso a Contabilidad','2025-05-20 16:52:40','','Contabilidad'),(945,1,'Acceso a Caja','2025-05-20 16:52:44','','Caja'),(946,1,'Acceso a Cuenta Bancaria','2025-05-20 16:52:47','','Cuenta Bancaria'),(947,1,'Acceso a Gastos','2025-05-20 16:53:01','','Gastos'),(948,1,'Acceso a Cuenta Bancaria','2025-05-20 16:53:24','','Cuenta Bancaria'),(949,1,'Acceso a Tipos de pago','2025-05-20 16:54:15','','Tipos de pago'),(950,1,'Registro de pago parcial','2025-05-20 16:54:34','50','Pago'),(951,1,'Registro de pago de gasto','2025-05-20 16:55:23','99.98','Pago'),(952,1,'Registro de pago de gasto','2025-05-20 16:56:09','100.00','Pago'),(953,1,'Edición de gasto','2025-05-20 16:56:43',' Compra de lapiceros ','Gasto'),(954,1,'Edición de gasto','2025-05-20 16:57:32',' Pago hola','Gasto'),(955,1,'Registro de gasto','2025-05-20 16:57:57','Comercio','Gasto'),(956,1,'Eliminación de gasto','2025-05-20 16:58:13','5','Gasto'),(957,1,'Acceso a Clientes','2025-05-20 16:59:38','','Clientes'),(958,1,'Acceso a Proveedores','2025-05-20 16:59:41','','Proveedores'),(959,1,'Acceso a Proveedores','2025-05-20 17:01:48','','Proveedores'),(960,1,'Acceso a Clientes','2025-05-20 17:03:06','','Clientes'),(961,1,'Acceso a Contabilidad','2025-05-20 17:03:08','','Contabilidad'),(962,1,'Acceso a Finanzas','2025-05-20 17:03:10','','Finanzas'),(963,1,'Acceso a Finanzas','2025-05-20 17:03:10','','Finanzas'),(964,1,'Acceso a Cuentas pendientes','2025-05-20 17:03:20','','Cuentas pendientes'),(965,1,'Acceso a Gastos','2025-05-20 17:06:27','','Gastos'),(966,1,'Acceso a Cuentas pendientes','2025-05-20 17:06:32','','Cuentas pendientes'),(967,1,'Acceso a Contabilidad','2025-05-20 17:19:03','','Contabilidad'),(968,1,'Acceso a Ajuste de Inventario','2025-05-20 17:19:06','','Ajuste de Inventario'),(969,1,'Acceso a Carga de productos','2025-05-20 17:19:07','','Carga de productos'),(970,1,'Registro de carga','2025-05-20 17:20:22','prueba manuela','Carga'),(971,1,'Acceso a Gastos','2025-05-20 17:24:18','','Gastos'),(972,19,'Acceso al sistema','2025-05-20 17:28:05','manuel','Inicio'),(973,19,'Acceso a Contabilidad','2025-05-20 17:28:18','','Contabilidad'),(974,19,'Acceso a Contabilidad','2025-05-20 17:28:19','','Contabilidad'),(975,19,'Acceso a Contabilidad','2025-05-20 17:28:20','','Contabilidad'),(976,19,'Acceso a Catálogo de cuentas','2025-05-20 17:28:21','','Catálogo de cuentas'),(977,1,'Acceso al sistema','2025-05-20 17:30:54','admin','Inicio'),(978,1,'Acceso a Productos','2025-05-20 17:30:58','','Productos'),(979,1,'Acceso a Proveedores','2025-05-20 17:33:51','','Proveedores'),(980,1,'Eliminar proveedor','2025-05-20 17:34:45','Eliminado el proveedor con el código 2','Proveedores'),(981,1,'Buscar proveedor','2025-05-20 17:35:23','J28516209','Proveedores'),(982,1,'Registro de proveedor','2025-05-20 17:35:28','Manuela prueba','Proveedores'),(983,1,'Registro de teléfono','2025-05-20 17:35:45','04145389780','Teléfonos de proveedores'),(984,1,'Registro de representante','2025-05-20 17:35:56','daniel','Representantes'),(985,1,'Editar representante','2025-05-20 17:36:03','daniel','Representantes'),(986,1,'Editar proveedor','2025-05-20 17:36:27','Manuela prueba','Proveedores'),(987,1,'Eliminar proveedor','2025-05-20 17:36:43','Eliminado el proveedor con el código 3','Proveedores'),(988,1,'Registro de representante','2025-05-20 17:37:03','Mariangeles','Representantes'),(989,1,'Eliminar representante','2025-05-20 17:37:08','Eliminado el representante con el código 3','Representantes'),(990,1,'Acceso a Contabilidad','2025-05-21 01:41:33','','Contabilidad'),(991,1,'Acceso a Reporte De proveedores','2025-05-21 01:42:16','','Reporte De proveedores'),(992,1,'Acceso a Reporte De Inventario','2025-05-21 01:42:31','','Reporte De Inventario'),(993,1,'Acceso a Reporte De compras','2025-05-21 01:42:51','','Reporte De compras'),(994,1,'Acceso a Reporte De Inventario','2025-05-21 01:43:50','','Reporte De Inventario'),(995,1,'Acceso a Reporte De proveedores','2025-05-21 01:44:38','','Reporte De proveedores'),(996,1,'Acceso a Reporte De Inventario','2025-05-21 01:44:48','','Reporte De Inventario'),(997,1,'Acceso a Reporte De compras','2025-05-21 01:44:58','','Reporte De compras'),(998,1,'Acceso a Reporte De compras','2025-05-21 01:45:25','','Reporte De compras'),(999,1,'Acceso a Reporte De Clientes','2025-05-21 01:45:54','','Reporte De Clientes'),(1000,1,'Acceso a Reporte De Inventario','2025-05-21 01:46:07','','Reporte De Inventario'),(1001,1,'Acceso a Gastos','2025-05-21 04:25:29','','Gastos'),(1002,1,'Acceso a Gastos','2025-05-21 04:25:54','','Gastos'),(1003,1,'Registro de gasto','2025-05-21 04:26:38','Hojas','Gasto'),(1004,1,'Acceso a Gastos','2025-05-21 05:21:25','','Gastos'),(1005,1,'Acceso a Gastos','2025-05-21 05:40:19','','Gastos'),(1006,1,'Acceso a Contabilidad','2025-05-21 05:48:46','','Contabilidad'),(1007,1,'Acceso a Gastos','2025-05-21 05:48:48','','Gastos'),(1008,1,'Acceso a Gastos','2025-05-21 06:29:29','','Gastos'),(1009,1,'Acceso a Cuentas pendientes','2025-05-21 06:29:30','','Cuentas pendientes'),(1010,1,'Acceso a Productos','2025-05-21 06:29:31','','Productos'),(1011,1,'Acceso a Compras','2025-05-21 06:29:33','','Compras'),(1012,1,'Acceso a Ventas','2025-05-21 06:30:09','','Ventas'),(1013,1,'Acceso a Caja','2025-05-21 06:30:12','','Caja'),(1014,1,'Acceso a Gastos','2025-05-21 06:30:14','','Gastos'),(1015,1,'Acceso a Cuentas pendientes','2025-05-21 20:52:06','','Cuentas pendientes'),(1016,1,'Acceso a Contabilidad','2025-05-21 20:54:52','','Contabilidad'),(1017,1,'Acceso a Catálogo de cuentas','2025-05-21 20:54:52','','Catálogo de cuentas'),(1018,1,'Eliminar cuenta contable con el codigo','2025-05-21 20:54:59','2','Contabilidad'),(1019,1,'Eliminar cuenta contable con el codigo','2025-05-21 20:55:03','3','Contabilidad'),(1020,1,'Eliminar cuenta contable con el codigo','2025-05-21 20:55:15','4','Contabilidad'),(1021,1,'Eliminar cuenta contable con el codigo','2025-05-21 20:55:28','12','Contabilidad'),(1022,1,'Acceso a Gastos','2025-05-21 20:56:02','','Gastos'),(1023,1,'Registro de categoría de gastos','2025-05-21 21:04:04','Marketing ','Categoría de gastos'),(1024,1,'Acceso a Gastos','2025-05-21 21:05:40','','Gastos'),(1025,1,'Acceso a Gastos','2025-05-21 21:06:30','','Gastos'),(1026,1,'Editar categoría de gastos','2025-05-21 21:06:50','3','Categoría de gastos'),(1027,1,'Acceso a Gastos','2025-05-21 21:07:00','','Gastos'),(1028,1,'Registro de gasto','2025-05-21 21:07:12','volantes','Gasto'),(1029,1,'Acceso a Cuentas pendientes','2025-05-21 21:11:11','','Cuentas pendientes'),(1030,1,'Acceso a Gastos','2025-05-21 21:11:41','','Gastos'),(1031,1,'Acceso a Cuentas pendientes','2025-05-21 21:11:47','','Cuentas pendientes'),(1032,1,'Acceso a Gastos','2025-05-21 21:11:56','','Gastos'),(1033,1,'Acceso a Gastos','2025-05-21 21:12:02','','Gastos'),(1034,1,'Acceso a Gastos','2025-05-21 21:12:04','','Gastos'),(1035,1,'Acceso a Gastos','2025-05-21 21:12:32','','Gastos'),(1036,1,'Registro de categoría de gastos','2025-05-21 21:12:53','Alquiler local','Categoría de gastos'),(1037,1,'Acceso a Gastos','2025-05-21 21:12:57','','Gastos'),(1038,1,'Registro de gasto','2025-05-21 21:13:12','mes de abril','Gasto'),(1039,1,'Acceso a Cuentas pendientes','2025-05-21 21:13:17','','Cuentas pendientes'),(1040,1,'Acceso a Gastos','2025-05-21 21:14:16','','Gastos'),(1041,1,'Acceso a Gastos','2025-05-21 21:14:33','','Gastos'),(1042,1,'Acceso a Gastos','2025-05-21 21:14:51','','Gastos'),(1043,1,'Registro de gasto','2025-05-21 21:15:12','papeles','Gasto'),(1044,1,'Acceso a Cuentas pendientes','2025-05-21 21:15:26','','Cuentas pendientes'),(1045,1,'Acceso a Gastos','2025-05-21 21:45:17','','Gastos'),(1046,1,'Acceso a Gastos','2025-05-21 22:19:46','','Gastos'),(1047,1,'Acceso a Cuentas pendientes','2025-05-21 22:19:50','','Cuentas pendientes'),(1048,1,'Acceso a Contabilidad','2025-05-21 22:38:45','','Contabilidad'),(1049,1,'Acceso a Catálogo de cuentas','2025-05-21 22:38:45','','Catálogo de cuentas'),(1050,1,'Eliminar cuenta contable con el codigo','2025-05-21 22:39:16','5','Contabilidad'),(1051,1,'Acceso a Cuentas pendientes','2025-05-21 22:40:08','','Cuentas pendientes'),(1052,1,'Acceso a Gastos','2025-05-21 22:48:59','','Gastos'),(1053,1,'Acceso a Banco','2025-05-21 22:49:40','','Banco'),(1054,1,'Acceso a Gastos','2025-05-21 22:49:43','','Gastos'),(1055,1,'Acceso a Compras','2025-05-21 22:51:25','','Compras'),(1056,1,'Acceso a Gastos','2025-05-21 22:51:26','','Gastos'),(1057,1,'Acceso a Cuentas pendientes','2025-05-21 22:55:30','','Cuentas pendientes'),(1058,1,'Acceso a Gastos','2025-05-21 23:09:06','','Gastos'),(1059,1,'Acceso a Contabilidad','2025-05-21 23:09:53','','Contabilidad'),(1060,1,'Acceso a Contabilidad','2025-05-21 23:09:55','','Contabilidad'),(1061,1,'Acceso a Gastos','2025-05-21 23:09:56','','Gastos'),(1062,1,'Acceso a Cuentas pendientes','2025-05-21 23:10:03','','Cuentas pendientes'),(1063,1,'Acceso a Gastos','2025-05-21 23:45:26','','Gastos'),(1064,1,'Acceso a Cuentas pendientes','2025-05-21 23:58:44','','Cuentas pendientes'),(1065,1,'Acceso a Cuentas pendientes','2025-05-22 02:56:43','','Cuentas pendientes'),(1066,1,'Acceso a Gastos','2025-05-22 03:02:18','','Gastos'),(1067,1,'Registro de pago parcial','2025-05-22 03:02:38','200','Pago'),(1068,1,'Acceso a Cuentas pendientes','2025-05-22 03:02:45','','Cuentas pendientes'),(1069,1,'Registro de pago','2025-05-22 04:02:37','9.74','Pago'),(1070,1,'Acceso a Cuentas pendientes','2025-05-22 04:02:59','','Cuentas pendientes'),(1071,1,'Registro de pago','2025-05-22 04:33:02','7.98','Pago'),(1072,1,'Acceso a Cuentas pendientes','2025-05-22 04:33:39','','Cuentas pendientes'),(1073,1,'Registro de pago','2025-05-22 05:15:11','8.00','Pago'),(1074,1,'Acceso a Cuentas pendientes','2025-05-22 05:15:17','','Cuentas pendientes'),(1075,1,'Registro de pago parcial','2025-05-22 05:16:20','989.21','Pago'),(1076,1,'Registro de pago parcial','2025-05-22 05:16:36','956.57','Pago'),(1077,1,'Registro de pago','2025-05-22 05:17:08','3.00','Pago'),(1078,1,'Acceso a Ventas','2025-05-22 05:22:27','','Ventas'),(1079,1,'Registro de pago parcial','2025-05-22 05:22:32','821.94','Pago recibido'),(1080,1,'Acceso a Contabilidad','2025-05-22 05:22:58','','Contabilidad'),(1081,1,'Acceso a Contabilidad','2025-05-22 05:22:59','','Contabilidad'),(1082,1,'Acceso a Gastos','2025-05-22 05:23:06','','Gastos'),(1083,1,'Acceso a Ventas','2025-05-22 05:23:19','','Ventas'),(1084,1,'Registro de pago','2025-05-22 05:23:27','Venta #30Monto: 108.93','Pago recibido'),(1085,1,'Acceso a Ventas','2025-05-22 05:24:06','','Ventas'),(1086,1,'Registro de pago parcial','2025-05-22 05:24:25','989.21','Pago recibido'),(1087,1,'Registro de pago completo','2025-05-22 05:24:34','Venta #28- Monto: 0.01','Pago recibido'),(1088,1,'Acceso a Ventas','2025-05-22 05:25:23','','Ventas'),(1089,1,'Registro de pago completo','2025-05-22 05:25:41','Venta #36 - Monto: 801.94','Ventas - Pago recibido'),(1090,1,'Acceso a Cuentas pendientes','2025-05-22 05:29:56','','Cuentas pendientes'),(1091,1,'Registro de pago completo','2025-05-22 05:30:08','Venta #29 - Monto: 876.57','Cuentas por Cobrar - Pago recibido'),(1092,1,'Acceso a Ventas','2025-05-22 05:30:32','','Ventas'),(1093,1,'Registro de pago completo','2025-05-22 05:30:42','Venta #27 - Monto: 907.76','Ventas - Pago recibido'),(1094,1,'Acceso a Cuenta Bancaria','2025-05-22 05:31:00','','Cuenta Bancaria'),(1095,1,'Acceso a Ventas','2025-05-22 05:32:28','','Ventas'),(1096,1,'Registro de pago parcial','2025-05-22 05:32:40','Venta #26 - Monto: 889.21','Ventas - Pago recibido'),(1097,1,'Acceso a Ventas','2025-05-22 05:33:31','','Ventas'),(1098,1,'Acceso a Ventas','2025-05-22 05:33:51','','Ventas'),(1099,1,'Acceso a Ventas','2025-05-22 05:34:11','','Ventas'),(1100,1,'Registro de pago parcial','2025-05-22 05:34:22','Venta #26 - Monto: 809.21','Ventas - Pago recibido'),(1101,1,'Registro de pago parcial','2025-05-22 05:34:45','Venta #26 - Monto: 789.21','Ventas - Pago recibido'),(1102,1,'Acceso a Ventas','2025-05-22 05:35:49','','Ventas'),(1103,1,'Registro de pago parcial','2025-05-22 05:35:58','Venta #26 - Monto: 80.00','Ventas - Pago recibido'),(1104,1,'Acceso a Cuentas pendientes','2025-05-22 05:37:44','','Cuentas pendientes'),(1105,1,'Registro de pago completo','2025-05-22 05:38:23','Venta #26 - Monto: 700.00','Cuentas por Cobrar - Pago recibido'),(1106,1,'Acceso a Ventas','2025-05-22 05:42:50','','Ventas'),(1107,1,'Registro de venta','2025-05-22 05:43:29','93.37','Venta'),(1108,1,'Registro de pago completo','2025-05-22 05:43:43','Venta #38 - Monto: 100.00','Ventas - Pago recibido'),(1109,1,'Acceso a Compras','2025-05-22 06:10:59','','Compras'),(1110,1,'Registro de pago de gasto','2025-05-22 06:12:52','1215.00','Pago'),(1111,1,'Registro de pago parcial','2025-05-22 06:12:58','4775.9','Pago'),(1112,1,'Acceso a Productos','2025-05-22 06:22:45','','Productos'),(1113,1,'Acceso a Cuentas pendientes','2025-05-22 06:59:26','','Cuentas pendientes'),(1114,1,'Acceso a Gastos','2025-05-22 07:00:32','','Gastos'),(1115,1,'Acceso a Cuentas pendientes','2025-05-22 07:05:11','','Cuentas pendientes'),(1116,1,'Acceso a Ventas','2025-05-22 07:37:53','','Ventas'),(1117,1,'Registro de venta','2025-05-22 07:38:28','1628.10','Venta'),(1118,1,'Acceso a Cuentas pendientes','2025-05-22 07:38:30','','Cuentas pendientes'),(1119,1,'Registro de pago parcial','2025-05-22 07:53:02','12193.7','Pago'),(1120,1,'Acceso a Ventas','2025-05-22 15:57:55','','Ventas'),(1121,1,'Registro de pago parcial','2025-05-22 15:58:13','Venta #39 - Monto: 134.76','Ventas - Pago recibido'),(1122,1,'Acceso a Cuentas pendientes','2025-05-22 16:04:14','','Cuentas pendientes'),(1123,1,'Registro de pago parcial','2025-05-22 16:23:43','55','Pago'),(1124,1,'Registro de pago parcial','2025-05-22 16:24:39','55','Pago'),(1125,1,'Registro de pago parcial','2025-05-22 16:40:38','Venta #39 - Monto: 29.97','Cuentas por Cobrar - Pago recibido'),(1126,1,'Registro de pago parcial','2025-05-22 16:41:49','Venta #39 - Monto: 9.99','Cuentas por Cobrar - Pago recibido'),(1127,1,'Acceso a Gastos','2025-05-22 19:16:57','','Gastos'),(1128,1,'Acceso a Caja','2025-05-22 19:19:09','','Caja'),(1129,1,'Acceso a Gastos','2025-05-22 19:19:43','','Gastos'),(1130,1,'Acceso a Cuentas pendientes','2025-05-22 19:36:29','','Cuentas pendientes'),(1131,1,'Acceso a Compras','2025-05-22 20:50:28','','Compras'),(1132,1,'Registro de pago emitido','2025-05-22 20:51:09','20.00','Pago'),(1133,1,'Registro de pago parcial','2025-05-22 20:51:46','4775.9','Pago'),(1134,1,'Acceso a Cuentas pendientes','2025-05-22 20:51:52','','Cuentas pendientes'),(1135,1,'Acceso a Compras','2025-05-22 20:54:00','','Compras'),(1136,1,'Registro de pago parcial','2025-05-22 20:54:09','4775.9','Pago'),(1137,1,'Acceso a Cuentas pendientes','2025-05-22 20:54:15','','Cuentas pendientes'),(1138,1,'Acceso a Cuentas pendientes','2025-05-22 21:00:27','','Cuentas pendientes'),(1139,1,'Acceso a Cuentas pendientes','2025-05-22 21:03:12','','Cuentas pendientes'),(1140,1,'Acceso a Compras','2025-05-22 21:20:14','','Compras'),(1141,1,'Acceso a Ventas','2025-05-22 21:24:24','','Ventas'),(1142,1,'Acceso a Compras','2025-05-22 21:24:27','','Compras'),(1143,1,'Registro de pago parcial','2025-05-22 21:25:20','4775.9','Pago'),(1144,1,'Acceso a Cuentas pendientes','2025-05-22 21:25:58','','Cuentas pendientes'),(1145,1,'Acceso a Cuentas pendientes','2025-05-22 21:26:09','','Cuentas pendientes'),(1146,1,'Acceso a Cuentas pendientes','2025-05-22 21:27:10','','Cuentas pendientes'),(1147,1,'Acceso a Cuentas pendientes','2025-05-22 21:27:31','','Cuentas pendientes'),(1148,1,'Acceso a Compras','2025-05-22 21:30:36','','Compras'),(1149,1,'Acceso a Cuentas pendientes','2025-05-22 21:30:43','','Cuentas pendientes'),(1150,1,'Acceso a Cuentas pendientes','2025-05-22 21:54:17','','Cuentas pendientes'),(1151,1,'Acceso a Compras','2025-05-22 22:06:09','','Compras'),(1152,1,'Registro de pago parcial','2025-05-22 22:06:15','4775.9','Pago'),(1153,1,'Registro de pago emitido','2025-05-22 22:07:00','4999.99','Pago'),(1154,1,'Acceso a Cuentas pendientes','2025-05-22 22:11:05','','Cuentas pendientes'),(1155,1,'Registro de pago completo','2025-05-22 23:37:54','Venta #39 - Monto: 2000.00','Cuentas por Cobrar - Pago recibido'),(1156,1,'Acceso a Ventas','2025-05-22 23:38:27','','Ventas'),(1157,1,'Registro de venta','2025-05-22 23:38:43','261.30','Venta'),(1158,1,'Acceso a Compras','2025-05-22 23:39:17','','Compras'),(1159,1,'Acceso a Ventas','2025-05-22 23:39:57','','Ventas'),(1160,1,'Acceso a Cuentas pendientes','2025-05-22 23:41:04','','Cuentas pendientes'),(1161,1,'Acceso a Ventas','2025-05-22 23:42:53','','Ventas'),(1162,1,'Registro de pago parcial','2025-05-22 23:43:00','Venta #40 - Monto: 21.00','Ventas - Pago recibido'),(1163,1,'Acceso a Cuentas pendientes','2025-05-22 23:43:07','','Cuentas pendientes'),(1164,1,'Registro de pago parcial','2025-05-23 00:28:19','Venta #40 - Monto: 60.00','Cuentas por Cobrar - Pago recibido'),(1165,1,'Acceso al sistema','2025-05-23 00:38:12','admin','Inicio'),(1166,1,'Acceso a Cuentas pendientes','2025-05-23 00:38:50','','Cuentas pendientes'),(1167,1,'Registro de pago parcial','2025-05-23 00:39:00','Venta #40 - Monto: 20.00','Cuentas por Cobrar - Pago recibido'),(1168,1,'Registro de pago completo','2025-05-23 00:39:14','Venta #40 - Monto: 200.00','Cuentas por Cobrar - Pago recibido'),(1169,1,'Acceso a Cuentas pendientes','2025-05-23 01:39:28','','Cuentas pendientes'),(1170,1,'Acceso a Productos','2025-05-23 01:39:31','','Productos'),(1171,1,'Acceso a Cuentas pendientes','2025-05-23 01:43:02','','Cuentas pendientes'),(1172,1,'Acceso al sistema','2025-05-23 01:50:06','admin','Inicio'),(1173,1,'Acceso a Cuentas pendientes','2025-05-23 01:58:29','','Cuentas pendientes'),(1174,1,'Acceso a Compras','2025-05-23 02:39:02','','Compras'),(1175,1,'Registro de pago parcial','2025-05-23 02:39:07','20847.2','Pago'),(1176,1,'Acceso a Cuentas pendientes','2025-05-23 02:39:13','','Cuentas pendientes'),(1177,1,'Acceso a Ventas','2025-05-23 02:51:54','','Ventas'),(1178,1,'Registro de venta','2025-05-23 02:52:23','87.10','Venta'),(1179,1,'Acceso a Cuentas pendientes','2025-05-23 02:52:28','','Cuentas pendientes'),(1180,1,'Acceso a Compras','2025-05-23 03:00:14','','Compras'),(1181,1,'Registro de pago parcial','2025-05-23 03:00:30','20847.2','Pago'),(1182,1,'Acceso a Cuentas pendientes','2025-05-23 03:02:06','','Cuentas pendientes'),(1183,1,'Registro de pago parcial','2025-05-23 03:25:06','12193.7','Pago'),(1184,1,'Registro de pago parcial','2025-05-23 03:25:44','20847.2','Pago'),(1185,1,'Registro de pago parcial','2025-05-23 03:26:47','Venta #41 - Monto: 8.00','Cuentas por Cobrar - Pago recibido'),(1186,1,'Registro de pago completo','2025-05-23 03:27:04','Venta #41 - Monto: 100.00','Cuentas por Cobrar - Pago recibido'),(1187,1,'Acceso a Ventas','2025-05-23 03:31:12','','Ventas'),(1188,1,'Registro de venta','2025-05-23 03:31:32','783.90','Venta'),(1189,1,'Registro de venta','2025-05-23 03:31:52','1819.40','Venta'),(1190,1,'Registro de venta','2025-05-23 03:32:24','4225.42','Venta'),(1191,1,'Acceso a Cuentas pendientes','2025-05-23 03:32:27','','Cuentas pendientes'),(1192,1,'Registro de pago parcial','2025-05-23 03:34:10','Venta #43 - Monto: 10.00','Cuentas por Cobrar - Pago recibido'),(1193,1,'Registro de pago parcial','2025-05-23 03:34:24','20847.2','Pago'),(1194,1,'Registro de pago emitido','2025-05-23 03:36:08','15000.00','Pago'),(1195,1,'Acceso a Compras','2025-05-23 03:36:38','','Compras'),(1196,1,'Acceso a Cuenta Bancaria','2025-05-23 03:38:10','','Cuenta Bancaria'),(1197,1,'Acceso a Finanzas','2025-05-23 03:38:34','','Finanzas'),(1198,1,'Acceso a Finanzas','2025-05-23 03:38:34','','Finanzas'),(1199,1,'Acceso a Productos','2025-05-23 03:38:38','','Productos'),(1200,1,'Acceso a Ventas','2025-05-23 03:38:39','','Ventas'),(1201,1,'Acceso a Compras','2025-05-23 03:38:41','','Compras'),(1202,1,'Buscar proveedor','2025-05-23 03:38:46','J28516209','Proveedores'),(1203,1,'Registro de compra','2025-05-23 03:39:08','10000.00','Compras'),(1204,1,'Acceso a Cuentas pendientes','2025-05-23 03:39:11','','Cuentas pendientes'),(1205,1,'Registro de pago emitido','2025-05-23 03:39:48','11000.00','Pago'),(1206,1,'Acceso a Compras','2025-05-23 03:39:56','','Compras'),(1207,1,'Buscar proveedor','2025-05-23 03:40:01','J505284797','Proveedores'),(1208,1,'Buscar proveedor','2025-05-23 03:40:07','J28516209','Proveedores'),(1209,1,'Registro de compra','2025-05-23 03:40:25','6.00','Compras'),(1210,1,'Registro de pago emitido','2025-05-23 03:40:36','8.00','Pago'),(1211,1,'Buscar proveedor','2025-05-23 03:42:06','J28516209','Proveedores'),(1212,1,'Registro de compra','2025-05-23 03:42:25','20.00','Compras'),(1213,1,'Acceso a Cuentas pendientes','2025-05-23 03:42:30','','Cuentas pendientes'),(1214,1,'Registro de pago emitido','2025-05-23 03:47:29','21.00','Pago'),(1215,1,'Registro de pago parcial','2025-05-23 03:47:41','Venta #42 - Monto: 10.00','Cuentas por Cobrar - Pago recibido'),(1216,1,'Registro de pago parcial','2025-05-23 03:48:01','20847.2','Pago'),(1217,1,'Acceso a Compras','2025-05-23 03:50:29','','Compras'),(1218,1,'Acceso a Gastos','2025-05-23 03:54:58','','Gastos'),(1219,1,'Acceso a Compras','2025-05-23 03:55:04','','Compras'),(1220,1,'Registro de pago parcial','2025-05-23 03:55:22','20847.2','Pago'),(1221,1,'Acceso a Cuentas pendientes','2025-05-23 03:55:41','','Cuentas pendientes'),(1222,1,'Acceso a Compras','2025-05-23 03:56:10','','Compras'),(1223,1,'Acceso a Ventas','2025-05-23 03:56:10','','Ventas'),(1224,1,'Acceso a Ventas','2025-05-23 04:16:46','','Ventas'),(1225,1,'Acceso a Compras','2025-05-23 04:16:47','','Compras'),(1226,1,'Registro de pago completo','2025-05-23 04:16:53','Compras # - Monto: 10.00','Compras - Pago recibido'),(1227,1,'Acceso a Cuentas pendientes','2025-05-23 04:21:48','','Cuentas pendientes'),(1228,1,'Registro de pago completo','2025-05-23 04:21:53','Pago emitido',''),(1229,1,'Acceso a Gastos','2025-05-23 04:22:21','','Gastos'),(1230,1,'Acceso a Cuentas pendientes','2025-05-23 04:26:09','','Cuentas pendientes'),(1231,1,'Acceso a Cuentas pendientes','2025-05-23 04:30:01','','Cuentas pendientes'),(1232,1,'Acceso a Compras','2025-05-23 04:35:22','','Compras'),(1233,1,'Acceso a Cuentas pendientes','2025-05-23 04:37:34','','Cuentas pendientes'),(1234,1,'Acceso a Cuentas pendientes','2025-05-23 04:38:59','','Cuentas pendientes'),(1235,1,'Acceso a Cuentas pendientes','2025-05-23 04:41:30','','Cuentas pendientes'),(1236,1,'Acceso a Compras','2025-05-23 04:43:35','','Compras'),(1237,1,'Acceso a Contabilidad','2025-05-23 04:47:00','','Contabilidad'),(1238,1,'Acceso a Compras','2025-05-23 04:47:00','','Compras'),(1239,1,'Acceso a Contabilidad','2025-05-23 04:48:43','','Contabilidad'),(1240,1,'Acceso a Catálogo de cuentas','2025-05-23 04:48:44','','Catálogo de cuentas'),(1241,1,'Acceso a Compras','2025-05-23 04:48:45','','Compras'),(1242,1,'Buscar proveedor','2025-05-23 04:49:53','','Proveedores'),(1243,1,'Registro de pago parcial','2025-05-23 04:50:42','Compras #9 - Monto: 10.00','Pago emitido'),(1244,1,'Acceso a Contabilidad','2025-05-23 04:52:20','','Contabilidad'),(1245,1,'Acceso a Cuentas pendientes','2025-05-23 04:52:21','','Cuentas pendientes'),(1246,1,'Registro de pago parcial','2025-05-23 04:52:28','Cuentas por Pagar #8 - Monto: 12.00','Pago emitido'),(1247,1,'Acceso a Compras','2025-05-23 04:56:15','','Compras'),(1248,1,'Registro de pago parcial','2025-05-23 04:56:24','Compra #9 # - Monto: 9.99','Compras - Pago emitido'),(1249,1,'Acceso a Cuentas pendientes','2025-05-23 04:56:50','','Cuentas pendientes'),(1250,1,'Registro de pago parcial','2025-05-23 04:56:58','Compra #8 - Monto: 10.00','Cuentas por Pagar - Pago emitido'),(1251,1,'Acceso a Cuentas pendientes','2025-05-23 05:03:24','','Cuentas pendientes'),(1252,1,'Registro de pago parcial','2025-05-23 05:03:30','Compra #8 - Monto: 12.00','Cuentas por Pagar - Pago emitido'),(1253,1,'Acceso a Compras','2025-05-23 05:03:52','','Compras'),(1254,1,'Acceso a Ventas','2025-05-23 05:04:16','','Ventas'),(1255,1,'Registro de pago parcial','2025-05-23 05:04:28','Venta #44 - Monto: 10.00','Ventas - Pago recibido'),(1256,1,'Registro de pago completo','2025-05-23 05:04:51','Venta #44 - Monto: 5000.00','Ventas - Pago recibido'),(1257,1,'Acceso a Cuentas pendientes','2025-05-23 22:51:07','','Cuentas pendientes'),(1258,1,'Registro de pago parcial','2025-05-24 01:27:39','Compra #8 - Monto: 10.00','Cuentas por Pagar - Pago emitido'),(1259,1,'Acceso a Gastos','2025-05-24 01:27:52','','Gastos'),(1260,1,'Acceso a Compras','2025-05-24 01:28:08','','Compras'),(1261,1,'Registro de pago parcial','2025-05-24 01:28:20','Compra #9 - Monto: 20.00','Compras - Pago emitido'),(1262,1,'Buscar proveedor','2025-05-24 01:28:39','J28516209','Proveedores'),(1263,1,'Registro de compra','2025-05-24 01:29:00','50.00','Compras'),(1264,1,'Registro de pago completo','2025-05-24 01:31:07','Compra #16 - Monto: 51.00','Compras - Pago emitido'),(1265,1,'Buscar proveedor','2025-05-24 01:51:39','J28516209','Proveedores'),(1266,1,'Registro de compra','2025-05-24 01:51:51','316.00','Compras'),(1267,1,'Registro de pago completo','2025-05-24 01:52:24','Compra #17 - Monto: 317.00','Compras - Pago emitido'),(1268,1,'Acceso a Cuentas pendientes','2025-05-24 01:55:18','','Cuentas pendientes'),(1269,1,'Acceso a Cuentas pendientes','2025-05-24 01:56:03','','Cuentas pendientes'),(1270,1,'Registro de pago parcial','2025-05-24 01:56:13','Venta #42 - Monto: 10.00','Cuentas por Cobrar - Pago recibido'),(1271,1,'Acceso a Contabilidad','2025-05-24 02:33:09','','Contabilidad'),(1272,1,'Acceso a Contabilidad','2025-05-24 02:33:10','','Contabilidad'),(1273,1,'Acceso a Catálogo de cuentas','2025-05-24 02:33:11','','Catálogo de cuentas'),(1274,1,'Eliminar cuenta contable con el codigo','2025-05-24 02:33:22','6','Contabilidad'),(1275,1,'Eliminar cuenta contable con el codigo','2025-05-24 02:33:26','7','Contabilidad'),(1276,1,'Eliminar cuenta contable con el codigo','2025-05-24 02:33:52','8','Contabilidad'),(1277,1,'Eliminar cuenta contable con el codigo','2025-05-24 02:37:20','9','Contabilidad'),(1278,1,'Eliminar cuenta contable con el codigo','2025-05-24 02:37:52','10','Contabilidad'),(1279,1,'Eliminar cuenta contable con el codigo','2025-05-24 02:38:55','11','Contabilidad'),(1280,1,'Eliminar cuenta contable con el codigo','2025-05-24 02:40:02','1','Contabilidad'),(1281,1,'Eliminar cuenta contable con el codigo','2025-05-24 02:40:11','14','Contabilidad'),(1282,1,'Eliminar cuenta contable con el codigo','2025-05-24 02:40:47','15','Contabilidad'),(1283,1,'Eliminar cuenta contable con el código','2025-05-24 02:45:06','16','Contabilidad'),(1284,1,'Eliminar cuenta contable con el código','2025-05-24 02:45:09','17','Contabilidad'),(1285,1,'Eliminar cuenta contable con el código','2025-05-24 02:45:50','24','Contabilidad'),(1286,1,'Eliminar cuenta contable con el código','2025-05-24 02:47:50','22','Contabilidad'),(1287,1,'Eliminar cuenta contable con el código','2025-05-24 02:47:54','29','Contabilidad'),(1288,1,'Eliminar cuenta contable con el código','2025-05-24 02:48:00','19','Contabilidad'),(1289,1,'Eliminar cuenta contable con el código','2025-05-24 03:48:23','32','Contabilidad'),(1290,1,'Eliminar cuenta contable con el código','2025-05-24 03:52:38','33','Contabilidad'),(1291,1,'Eliminar cuenta contable con el código','2025-05-24 03:55:29','34','Contabilidad'),(1292,1,'Eliminar cuenta contable con el código','2025-05-24 03:56:34','35','Contabilidad'),(1293,1,'Eliminar cuenta contable con el código','2025-05-24 03:56:46','36','Contabilidad'),(1294,1,'Eliminar cuenta contable con el código','2025-05-24 03:58:32','37','Contabilidad'),(1295,1,'Eliminar cuenta contable con el código','2025-05-24 04:00:48','39','Contabilidad'),(1296,1,'Eliminar cuenta contable con el código','2025-05-24 04:31:22','41','Contabilidad'),(1297,1,'Eliminar cuenta contable con el código','2025-05-24 04:37:50','44','Contabilidad'),(1298,1,'Acceso a Gastos','2025-05-24 04:46:39','','Gastos'),(1299,1,'Acceso a Cuentas pendientes','2025-05-24 04:59:05','','Cuentas pendientes'),(1300,1,'Acceso a Contabilidad','2025-05-24 04:59:06','','Contabilidad'),(1301,1,'Acceso a Catálogo de cuentas','2025-05-24 04:59:07','','Catálogo de cuentas'),(1302,1,'Eliminar cuenta contable con el código','2025-05-24 04:59:20','5','Contabilidad'),(1303,1,'Eliminar cuenta contable con el código','2025-05-24 05:02:56','27','Contabilidad'),(1304,1,'Acceso a Ventas','2025-05-24 05:30:35','','Ventas'),(1305,1,'Acceso a Cuentas pendientes','2025-05-24 05:30:39','','Cuentas pendientes'),(1306,1,'Acceso a Ventas','2025-05-24 05:37:11','','Ventas'),(1307,1,'Registro de venta','2025-05-24 05:37:29','2.68','Venta'),(1308,1,'Registro de venta','2025-05-24 05:41:52','1.34','Venta'),(1309,1,'Acceso a Compras','2025-05-24 05:47:58','','Compras'),(1310,1,'Acceso a Gastos','2025-05-24 07:16:32','','Gastos'),(1311,1,'Acceso a Compras','2025-05-24 07:25:56','','Compras'),(1312,1,'Buscar proveedor','2025-05-24 07:26:03','J28516208','Proveedores'),(1313,1,'Buscar proveedor','2025-05-24 07:26:11','J10283114','Proveedores'),(1314,1,'Buscar proveedor','2025-05-24 07:26:24','J505284797','Proveedores'),(1315,1,'Acceso a Proveedores','2025-05-24 07:26:35','','Proveedores'),(1316,1,'Acceso a Compras','2025-05-24 07:26:42','','Compras'),(1317,1,'Buscar proveedor','2025-05-24 07:26:47','J28516209','Proveedores'),(1318,1,'Registro de compra','2025-05-24 07:27:19','30.00','Compras'),(1319,1,'Registro de pago parcial','2025-05-24 07:27:32','Compra #18 - Monto: 1.00','Compras - Pago emitido'),(1320,1,'Acceso a Compras','2025-05-24 07:35:50','','Compras'),(1321,1,'Registro de pago completo','2025-05-24 07:36:34','Compra #18 - Monto: 29.00','Compras - Pago emitido'),(1322,1,'Buscar proveedor','2025-05-24 16:06:01','J28516209','Proveedores'),(1323,1,'Registro de compra','2025-05-24 16:06:12','2.00','Compras'),(1324,1,'Acceso a Productos','2025-05-24 16:31:51','','Productos'),(1325,1,'Acceso a Productos','2025-05-24 16:31:57','','Productos'),(1326,1,'Acceso a Gastos','2025-05-24 17:48:42','','Gastos'),(1327,1,'Acceso a Gastos','2025-05-24 17:49:47','','Gastos'),(1328,1,'Acceso a Caja','2025-05-24 18:59:18','','Caja'),(1329,1,'Registro de caja','2025-05-24 19:07:33',NULL,'Caja'),(1330,1,'Acceso a Cuenta Bancaria','2025-05-24 19:17:30','','Cuenta Bancaria'),(1331,1,'Acceso a Ventas','2025-05-24 19:18:48','','Ventas'),(1332,1,'Registro de pago completo','2025-05-24 19:18:57','Venta #46 - Monto: 1.34','Ventas - Pago recibido'),(1333,1,'Acceso a Cuentas pendientes','2025-05-24 19:18:59','','Cuentas pendientes'),(1334,1,'Acceso a Cuenta Bancaria','2025-05-24 19:19:00','','Cuenta Bancaria'),(1335,1,'Acceso a Compras','2025-05-24 19:19:32','','Compras'),(1336,1,'Acceso a Ventas','2025-05-24 19:19:33','','Ventas'),(1337,1,'Registro de pago parcial','2025-05-24 19:19:38','Venta #45 - Monto: 1.00','Ventas - Pago recibido'),(1338,1,'Acceso a Cuenta Bancaria','2025-05-24 19:19:41','','Cuenta Bancaria'),(1339,1,'Acceso a Compras','2025-05-24 19:20:19','','Compras'),(1340,1,'Registro de pago parcial','2025-05-24 19:20:30','Compra #19 - Monto: 1.20','Compras - Pago emitido'),(1341,1,'Acceso a Cuenta Bancaria','2025-05-24 19:20:34','','Cuenta Bancaria'),(1342,1,'Acceso a Ventas','2025-05-24 19:23:55','','Ventas'),(1343,1,'Registro de pago completo','2025-05-24 19:24:03','Venta #45 - Monto: 1.68','Ventas - Pago recibido'),(1344,1,'Acceso a Cuenta Bancaria','2025-05-24 19:24:06','','Cuenta Bancaria'),(1345,1,'Acceso a Gastos','2025-05-24 19:29:54','','Gastos'),(1346,1,'Acceso a Productos','2025-05-24 19:30:25','','Productos'),(1347,1,'Acceso a Cuenta Bancaria','2025-05-24 19:31:25','','Cuenta Bancaria'),(1348,1,'Acceso a Reporte De Cuentas Pendientes','2025-05-24 19:36:54','','Reporte De Cuentas Pendientes'),(1349,1,'Acceso a Cuentas pendientes','2025-05-24 19:37:02','','Cuentas pendientes'),(1350,1,'Acceso a Reporte De Inventario','2025-05-24 19:37:07','','Reporte De Inventario'),(1351,1,'Acceso a Conciliación bancaria','2025-05-24 19:38:00','','Conciliación bancaria'),(1352,1,'Acceso a Reporte De Cuentas Pendientes','2025-05-24 19:38:46','','Reporte De Cuentas Pendientes'),(1353,1,'Acceso a Gastos','2025-05-24 19:43:44','','Gastos'),(1354,1,'Acceso a Reporte De Gastos','2025-05-24 19:43:50','','Reporte De Gastos'),(1355,1,'Acceso a Reporte De compras','2025-05-24 19:43:53','','Reporte De compras'),(1356,1,'Acceso a Reporte De Clientes','2025-05-24 19:44:00','','Reporte De Clientes'),(1357,1,'Acceso a Productos','2025-05-24 19:46:42','','Productos'),(1358,1,'Registro de producto','2025-05-24 19:48:15','queso blanco','Productos'),(1359,1,'Acceso a Proveedores','2025-05-24 19:49:57','','Proveedores'),(1360,1,'Buscar proveedor','2025-05-24 19:50:51','J28516209','Proveedores'),(1361,1,'Buscar proveedor','2025-05-24 19:50:57','J10283114','Proveedores'),(1362,1,'Registro de proveedor','2025-05-24 19:51:01','Arichuna','Proveedores'),(1363,1,'Editar proveedor','2025-05-24 19:51:09','Arichuna','Proveedores'),(1364,1,'Registro de teléfono','2025-05-24 19:51:16','04145389780','Teléfonos de proveedores'),(1365,1,'Registro de representante','2025-05-24 19:51:25','Daniel','Representantes'),(1366,1,'Editar proveedor','2025-05-24 19:52:13','generico','Proveedores'),(1367,1,'Eliminar representante','2025-05-24 19:52:25','Eliminado el representante con el código 1','Representantes'),(1368,1,'Eliminar representante','2025-05-24 19:52:29','Eliminado el representante con el código 2','Representantes'),(1369,1,'Eliminar proveedor','2025-05-24 19:52:36','Eliminado el proveedor con el código 3','Proveedores'),(1370,1,'Buscar proveedor','2025-05-24 19:53:54','','Proveedores'),(1371,1,'Buscar proveedor','2025-05-24 19:54:00','J505284797','Proveedores'),(1372,1,'Registro de proveedor','2025-05-24 19:54:02','Manuela prueba','Proveedores'),(1373,1,'Buscar proveedor','2025-05-24 19:54:10','J505284797s','Proveedores'),(1374,1,'Editar proveedor','2025-05-24 19:54:11','Manuela prueba','Proveedores'),(1375,1,'Buscar proveedor','2025-05-24 19:54:20','J505284797f','Proveedores'),(1376,1,'Buscar proveedor','2025-05-24 19:54:27','J505284797s','Proveedores'),(1377,1,'Buscar proveedor','2025-05-24 19:54:31','J505284797ggg','Proveedores'),(1378,1,'Buscar proveedor','2025-05-24 19:54:33','J505284797gg','Proveedores'),(1379,1,'Acceso a Cuentas pendientes','2025-05-24 19:54:59','','Cuentas pendientes'),(1380,1,'Acceso a Gastos','2025-05-24 19:54:59','','Gastos'),(1381,1,'Acceso a Proveedores','2025-05-24 19:55:37','','Proveedores'),(1382,1,'Acceso a Gastos','2025-05-24 20:02:50','','Gastos'),(1383,1,'Acceso a Cuentas pendientes','2025-05-24 20:03:52','','Cuentas pendientes'),(1384,1,'Acceso a Productos','2025-05-24 20:04:24','','Productos'),(1385,1,'Acceso a Gastos','2025-05-24 20:07:53','','Gastos'),(1386,1,'Acceso a Proveedores','2025-05-24 20:14:40','','Proveedores'),(1387,1,'Acceso a Contabilidad','2025-05-24 20:14:42','','Contabilidad'),(1388,1,'Acceso a Clientes','2025-05-24 20:14:43','','Clientes'),(1389,1,'Acceso a Proveedores','2025-05-24 20:14:47','','Proveedores'),(1390,1,'Acceso a Compras','2025-05-24 20:14:52','','Compras'),(1391,1,'Acceso a Finanzas','2025-05-24 20:14:54','','Finanzas'),(1392,1,'Acceso a Finanzas','2025-05-24 20:14:55','','Finanzas'),(1393,1,'Acceso a Contabilidad','2025-05-24 20:14:56','','Contabilidad'),(1394,1,'Acceso a Proveedores','2025-05-24 20:15:35','','Proveedores'),(1395,1,'Acceso a Contabilidad','2025-05-24 20:18:13','','Contabilidad'),(1396,1,'Acceso a Catálogo de cuentas','2025-05-24 20:18:13','','Catálogo de cuentas'),(1397,1,'Acceso a Finanzas','2025-05-24 20:56:45','','Finanzas'),(1398,1,'Acceso a Finanzas','2025-05-24 20:56:46','','Finanzas'),(1399,1,'Acceso a Contabilidad','2025-05-24 21:04:49','','Contabilidad'),(1400,1,'Acceso a Gestionar asientos','2025-05-24 21:04:50','','Gestionar asientos'),(1401,1,'Acceso a Contabilidad','2025-05-24 21:05:14','','Contabilidad'),(1402,1,'Acceso a Reportes contables','2025-05-24 21:05:17','','Reportes contables'),(1403,1,'Acceso a Conciliación bancaria','2025-05-24 21:06:26','','Conciliación bancaria'),(1404,1,'Acceso a Productos','2025-05-24 21:06:48','','Productos'),(1405,1,'Acceso a Cuentas pendientes','2025-05-24 21:06:49','','Cuentas pendientes'),(1406,1,'Acceso a Contabilidad','2025-05-24 21:06:51','','Contabilidad'),(1407,1,'Acceso a Gastos','2025-05-24 21:06:53','','Gastos'),(1408,1,'Acceso a Cuentas pendientes','2025-05-24 21:06:53','','Cuentas pendientes'),(1409,1,'Acceso a Conciliación bancaria','2025-05-24 21:06:59','','Conciliación bancaria'),(1410,1,'Acceso a Caja','2025-05-24 21:08:33','','Caja'),(1411,1,'Acceso a Gastos','2025-05-24 21:08:36','','Gastos'),(1412,1,'Acceso a Cuentas pendientes','2025-05-24 21:08:39','','Cuentas pendientes'),(1413,1,'Registro de pago parcial','2025-05-24 21:08:45','Compra #7 - Monto: 10.00','Cuentas por Pagar - Pago emitido'),(1414,1,'Registro de pago completo','2025-05-24 21:09:21','Venta #15 - Monto: 66.00','Cuentas por Cobrar - Pago recibido'),(1415,1,'Acceso a Productos','2025-05-24 21:13:09','','Productos'),(1416,1,'Acceso a Compras','2025-05-24 21:13:12','','Compras'),(1417,1,'Acceso a Ventas','2025-05-24 21:15:19','','Ventas'),(1418,1,'Acceso a Gastos','2025-05-24 21:15:30','','Gastos'),(1419,1,'Acceso a Cuentas pendientes','2025-05-24 21:15:31','','Cuentas pendientes'),(1420,1,'Acceso a Reporte De proveedores','2025-05-24 21:15:45','','Reporte De proveedores'),(1421,1,'Acceso a Contabilidad','2025-05-24 21:15:51','','Contabilidad'),(1422,1,'Acceso a Contabilidad','2025-05-24 21:15:52','','Contabilidad'),(1423,1,'Acceso a Reporte De Inventario','2025-05-24 21:15:58','','Reporte De Inventario'),(1424,1,'Acceso a Cuentas pendientes','2025-05-24 21:17:36','','Cuentas pendientes'),(1425,1,'Acceso a Reporte De Gastos','2025-05-24 21:17:40','','Reporte De Gastos'),(1426,1,'Acceso a Gastos','2025-05-24 21:28:01','','Gastos'),(1427,1,'Acceso a Reporte De Cuentas Pendientes','2025-05-24 21:28:05','','Reporte De Cuentas Pendientes'),(1428,1,'Acceso a Contabilidad','2025-05-24 21:28:33','','Contabilidad'),(1429,1,'Acceso a Catálogo de cuentas','2025-05-24 21:28:33','','Catálogo de cuentas'),(1430,1,'Eliminar cuenta contable con el código','2025-05-24 21:35:59','7','Contabilidad'),(1431,1,'Eliminar cuenta contable con el código','2025-05-24 21:36:11','4','Contabilidad'),(1432,1,'Eliminar cuenta contable con el código','2025-05-24 21:36:16','2','Contabilidad'),(1433,1,'Acceso a Finanzas','2025-05-24 21:37:26','','Finanzas'),(1434,1,'Acceso a Finanzas','2025-05-24 21:37:26','','Finanzas'),(1435,1,'Acceso a Caja','2025-05-24 21:37:30','','Caja'),(1436,1,'Registro de caja','2025-05-24 21:38:03',NULL,'Caja'),(1437,1,'Cierre de Caja','2025-05-24 21:38:15','Cierre de caja Caja $','Caja'),(1438,1,'Editar Caja','2025-05-24 21:38:27','Caja ','Caja'),(1439,1,'Editar Caja','2025-05-24 21:38:30','Caja ','Caja'),(1440,1,'Acceso a Gastos','2025-05-24 21:41:25','','Gastos'),(1441,1,'Acceso a Cuentas pendientes','2025-05-24 21:41:26','','Cuentas pendientes'),(1442,1,'Acceso a Productos','2025-05-24 21:41:29','','Productos'),(1443,1,'Acceso a Ajuste de Inventario','2025-05-24 21:41:31','','Ajuste de Inventario'),(1444,1,'Acceso a Descarga de productos','2025-05-24 21:41:31','','Descarga de productos'),(1445,1,'Acceso a Ventas','2025-05-24 21:41:33','','Ventas'),(1446,1,'Acceso a Marcas','2025-05-24 21:41:37','','Marcas'),(1447,1,'Acceso a Gastos','2025-05-24 21:41:40','','Gastos'),(1448,1,'Acceso a Ajuste de Inventario','2025-05-24 23:02:14','','Ajuste de Inventario'),(1449,1,'Acceso a Ajuste de Inventario','2025-05-24 23:02:15','','Ajuste de Inventario'),(1450,1,'Acceso a Finanzas','2025-05-24 23:02:16','','Finanzas'),(1451,1,'Acceso a Finanzas','2025-05-24 23:02:16','','Finanzas'),(1452,1,'Acceso a Caja','2025-05-24 23:04:26','','Caja'),(1453,1,'Acceso a Cuenta Bancaria','2025-05-24 23:05:25','','Cuenta Bancaria'),(1454,1,'Acceso a Contabilidad','2025-05-24 23:05:33','','Contabilidad'),(1455,1,'Acceso a Contabilidad','2025-05-24 23:05:33','','Contabilidad'),(1456,1,'Acceso a Cuentas pendientes','2025-05-24 23:05:35','','Cuentas pendientes'),(1457,1,'Acceso a Cuentas pendientes','2025-05-24 23:05:42','','Cuentas pendientes'),(1458,1,'Acceso a Gastos','2025-05-24 23:05:44','','Gastos'),(1459,1,'Acceso a Reporte De compras','2025-05-24 23:05:51','','Reporte De compras'),(1460,1,'Acceso a Ventas','2025-05-24 23:10:33','','Ventas'),(1461,1,'Acceso a Caja','2025-05-24 23:10:37','','Caja'),(1462,1,'Acceso a Cuenta Bancaria','2025-05-24 23:10:40','','Cuenta Bancaria'),(1463,1,'Acceso a Cuenta Bancaria','2025-05-24 23:12:10','','Cuenta Bancaria'),(1464,1,'Acceso a Caja','2025-05-25 01:57:18','','Caja'),(1465,1,'Acceso a Conciliación bancaria','2025-05-25 01:57:42','','Conciliación bancaria'),(1466,1,'Acceso a Cuentas pendientes','2025-05-25 01:57:59','','Cuentas pendientes'),(1467,1,'Acceso a Gastos','2025-05-25 01:58:17','','Gastos'),(1468,1,'Acceso a Banco','2025-05-25 01:58:20','','Banco'),(1469,1,'Eliminar Banco','2025-05-25 01:58:30','Eliminado el banco con el código 1','Banco'),(1470,1,'Acceso a Finanzas','2025-05-25 01:58:46','','Finanzas'),(1471,1,'Acceso a Finanzas','2025-05-25 01:58:46','','Finanzas'),(1472,1,'Acceso a Finanzas','2025-05-25 01:58:47','','Finanzas'),(1473,1,'Acceso a Finanzas','2025-05-25 01:58:47','','Finanzas'),(1474,1,'Acceso a Cuenta Bancaria','2025-05-25 01:58:49','','Cuenta Bancaria'),(1475,1,'Acceso a Finanzas','2025-05-25 02:09:14','','Finanzas'),(1476,1,'Acceso a Finanzas','2025-05-25 02:09:15','','Finanzas'),(1477,1,'Acceso a Gastos','2025-05-25 02:14:47','','Gastos'),(1478,1,'Acceso a Contabilidad','2025-05-25 02:15:14','','Contabilidad'),(1479,1,'Acceso a Catálogo de cuentas','2025-05-25 02:15:14','','Catálogo de cuentas'),(1480,1,'Acceso a Finanzas','2025-05-25 02:15:15','','Finanzas'),(1481,1,'Acceso a Finanzas','2025-05-25 02:15:16','','Finanzas'),(1482,1,'Acceso a Gastos','2025-05-25 02:18:04','','Gastos'),(1483,1,'Acceso a Finanzas','2025-05-25 02:20:29','','Finanzas'),(1484,1,'Acceso a Finanzas','2025-05-25 02:20:29','','Finanzas'),(1485,1,'Acceso a Contabilidad','2025-05-25 02:25:17','','Contabilidad'),(1486,1,'Acceso a Catálogo de cuentas','2025-05-25 02:25:18','','Catálogo de cuentas'),(1487,1,'Acceso a Cuentas pendientes','2025-05-25 02:27:09','','Cuentas pendientes'),(1488,1,'Acceso a Finanzas','2025-05-25 02:27:10','','Finanzas'),(1489,1,'Acceso a Finanzas','2025-05-25 02:27:10','','Finanzas'),(1490,1,'Acceso a Finanzas','2025-05-25 02:27:58','','Finanzas'),(1491,1,'Acceso a Finanzas','2025-05-25 02:27:59','','Finanzas'),(1492,1,'Acceso a Contabilidad','2025-05-25 02:28:27','','Contabilidad'),(1493,1,'Acceso a Catálogo de cuentas','2025-05-25 02:28:28','','Catálogo de cuentas'),(1494,1,'Acceso a Finanzas','2025-05-25 02:29:00','','Finanzas'),(1495,1,'Acceso a Finanzas','2025-05-25 02:29:00','','Finanzas'),(1496,1,'Acceso a Productos','2025-05-25 02:36:05','','Productos'),(1497,1,'Acceso a Finanzas','2025-05-25 02:36:08','','Finanzas'),(1498,1,'Acceso a Finanzas','2025-05-25 02:36:08','','Finanzas'),(1499,1,'Acceso a Cuentas pendientes','2025-05-25 02:38:55','','Cuentas pendientes'),(1500,1,'Acceso a Contabilidad','2025-05-25 02:47:03','','Contabilidad'),(1501,1,'Acceso a Catálogo de cuentas','2025-05-25 02:47:04','','Catálogo de cuentas'),(1502,1,'Acceso a Contabilidad','2025-05-25 02:48:05','','Contabilidad'),(1503,1,'Acceso a Gestionar asientos','2025-05-25 02:48:10','','Gestionar asientos'),(1504,1,'Acceso a Compras','2025-05-25 02:51:51','','Compras'),(1505,1,'Acceso a Ventas','2025-05-25 02:51:57','','Ventas'),(1506,1,'Acceso a Compras','2025-05-25 02:52:03','','Compras'),(1507,1,'Acceso a Ventas','2025-05-25 02:52:04','','Ventas'),(1508,1,'Acceso a Clientes','2025-05-25 02:52:07','','Clientes'),(1509,1,'Acceso a Compras','2025-05-25 02:52:10','','Compras'),(1510,1,'Acceso a Proveedores','2025-05-25 02:52:11','','Proveedores'),(1511,1,'Acceso a Ajuste de Inventario','2025-05-25 02:52:14','','Ajuste de Inventario'),(1512,1,'Acceso a Productos','2025-05-25 02:52:17','','Productos'),(1513,1,'Acceso a Ajuste de Inventario','2025-05-25 02:52:18','','Ajuste de Inventario'),(1514,1,'Acceso a Ajuste de Inventario','2025-05-25 02:52:18','','Ajuste de Inventario'),(1515,1,'Acceso a Ajuste de Inventario','2025-05-25 02:52:19','','Ajuste de Inventario'),(1516,1,'Acceso a Carga de productos','2025-05-25 02:52:19','','Carga de productos'),(1517,1,'Acceso a Productos','2025-05-25 02:52:23','','Productos'),(1518,1,'Acceso a Compras','2025-05-25 02:53:10','','Compras'),(1519,1,'Acceso a Ventas','2025-05-25 02:53:11','','Ventas'),(1520,1,'Acceso a Ventas','2025-05-25 02:58:54','','Ventas'),(1521,1,'Registro de pago parcial','2025-05-25 02:59:06','Venta #36 - Monto: 10.00','Ventas - Pago recibido'),(1522,1,'Acceso a Cuentas pendientes','2025-05-25 02:59:10','','Cuentas pendientes'),(1523,1,'Registro de pago completo','2025-05-25 02:59:32','Venta #16 - Monto: 2.00','Cuentas por Cobrar - Pago recibido'),(1524,1,'Acceso a Reporte De proveedores','2025-05-25 03:12:34','','Reporte De proveedores'),(1525,1,'Acceso a Reporte De Inventario','2025-05-25 03:12:51','','Reporte De Inventario'),(1526,1,'Acceso a Reporte De Inventario','2025-05-25 03:13:53','','Reporte De Inventario'),(1527,1,'Acceso a Reporte De Cuentas Pendientes','2025-05-25 03:14:37','','Reporte De Cuentas Pendientes'),(1528,1,'Acceso a Cuentas pendientes','2025-05-25 03:15:01','','Cuentas pendientes'),(1529,1,'Acceso a Reporte De Gastos','2025-05-25 03:15:04','','Reporte De Gastos'),(1530,1,'Acceso a Caja','2025-05-25 03:17:11','','Caja'),(1531,1,'Registro de caja','2025-05-25 03:20:07',NULL,'Caja'),(1532,1,'Registro de caja','2025-05-25 03:20:37',NULL,'Caja'),(1533,1,'Cierre de Caja','2025-05-25 03:21:06','Cierre de caja caja dos','Caja'),(1534,1,'Editar Caja','2025-05-25 03:22:22','caja dos','Caja'),(1535,1,'Editar Caja','2025-05-25 03:22:36','caja dos','Caja'),(1536,1,'Editar Caja','2025-05-25 03:23:01','Caja ','Caja'),(1537,1,'Acceso a Cuenta Bancaria','2025-05-25 03:25:54','','Cuenta Bancaria'),(1538,1,'Acceso a Caja','2025-05-25 03:27:57','','Caja'),(1539,1,'Editar Caja','2025-05-25 03:28:39','caja dosss ','Caja'),(1540,1,'Acceso a Cuenta Bancaria','2025-05-25 03:30:06','','Cuenta Bancaria'),(1541,1,'Acceso a Cuenta Bancaria','2025-05-25 03:31:19','','Cuenta Bancaria'),(1542,1,'Acceso a Banco','2025-05-25 03:31:26','','Banco'),(1543,1,'Eliminar Banco','2025-05-25 03:31:30','Eliminado el banco con el código 4','Banco'),(1544,1,'Acceso a Cuenta Bancaria','2025-05-25 03:31:35','','Cuenta Bancaria'),(1545,1,'Acceso a Contabilidad','2025-05-25 03:32:32','','Contabilidad'),(1546,1,'Acceso a Conciliación bancaria','2025-05-25 03:34:26','','Conciliación bancaria'),(1547,1,'Acceso a Caja','2025-05-25 03:35:52','','Caja'),(1548,1,'Acceso a Ajuste Empresa','2025-05-25 03:36:57','','Ajuste Empresa'),(1549,1,'Acceso a Ventas','2025-05-25 03:38:25','','Ventas'),(1550,1,'Registro de pago parcial','2025-05-25 03:38:37','Venta #36 - Monto: 805.00','Ventas - Pago recibido'),(1551,1,'Acceso a Contabilidad','2025-05-25 03:39:25','','Contabilidad'),(1552,1,'Acceso a Gestionar asientos','2025-05-25 03:39:30','','Gestionar asientos'),(1553,1,'Acceso a Caja','2025-05-25 03:39:50','','Caja'),(1554,1,'Acceso a Cuenta Bancaria','2025-05-25 03:39:51','','Cuenta Bancaria'),(1555,1,'Acceso a Conciliación bancaria','2025-05-25 03:39:53','','Conciliación bancaria'),(1556,1,'Acceso a Cuenta Bancaria','2025-05-25 03:39:55','','Cuenta Bancaria'),(1557,1,'Acceso a Caja','2025-05-25 03:39:59','','Caja'),(1558,1,'Acceso a Productos','2025-05-25 03:43:31','','Productos'),(1559,1,'Acceso a Ventas','2025-05-25 03:56:17','','Ventas'),(1560,1,'Acceso a Cuentas pendientes','2025-05-25 03:57:10','','Cuentas pendientes'),(1561,1,'Acceso a Cuenta Bancaria','2025-05-25 03:57:13','','Cuenta Bancaria'),(1562,1,'Registro de Cuenta','2025-05-25 03:57:21','01050000000000000000','Cuenta Bancaria'),(1563,1,'Acceso a Ventas','2025-05-25 03:57:40','','Ventas'),(1564,1,'Acceso a Tipos de pago','2025-05-25 03:57:50','','Tipos de pago'),(1565,1,'Registro de tipo de pago','2025-05-25 03:58:16','4','Tipo de pago'),(1566,1,'Editar tipo de pago','2025-05-25 03:58:32','Pago Movil','Tipo de pago'),(1567,1,'Registro de tipo de pago','2025-05-25 03:58:45','3','Tipo de pago'),(1568,1,'Acceso a Ventas','2025-05-25 03:58:52','','Ventas'),(1569,1,'Registro de pago completo','2025-05-25 03:59:03','Venta #36 - Monto: 6.94','Ventas - Pago recibido'),(1570,1,'Acceso a Cuentas pendientes','2025-05-25 04:10:51','','Cuentas pendientes'),(1571,1,'Acceso a Cuentas pendientes','2025-05-25 04:11:46','','Cuentas pendientes'),(1572,1,'Acceso a Reporte De Cuentas Pendientes','2025-05-25 04:11:52','','Reporte De Cuentas Pendientes'),(1573,1,'Acceso a Reporte De proveedores','2025-05-25 04:13:08','','Reporte De proveedores'),(1574,1,'Acceso a Reporte De Cuentas Pendientes','2025-05-25 04:13:18','','Reporte De Cuentas Pendientes'),(1575,1,'Acceso a Reporte De Clientes','2025-05-25 04:13:57','','Reporte De Clientes'),(1576,1,'Acceso a Caja','2025-05-25 04:13:59','','Caja'),(1577,1,'Acceso a Reporte De proveedores','2025-05-25 04:14:02','','Reporte De proveedores'),(1578,1,'Acceso a Reporte De Inventario','2025-05-25 04:14:05','','Reporte De Inventario'),(1579,1,'Acceso a Reporte De Cuentas Pendientes','2025-05-25 04:15:49','','Reporte De Cuentas Pendientes'),(1580,1,'Acceso a Reporte De Inventario','2025-05-25 04:55:22','','Reporte De Inventario'),(1581,1,'Acceso a Reporte De Gastos','2025-05-25 05:08:01','','Reporte De Gastos'),(1582,1,'Acceso a Reporte De Inventario','2025-05-25 05:11:39','','Reporte De Inventario'),(1583,1,'Acceso a Cuentas pendientes','2025-05-25 05:59:01','','Cuentas pendientes'),(1584,1,'Acceso a Reporte De Cuentas Pendientes','2025-05-25 05:59:06','','Reporte De Cuentas Pendientes'),(1585,1,'Acceso a Reporte De Inventario','2025-05-25 05:59:34','','Reporte De Inventario'),(1586,1,'Acceso a Reporte De Inventario','2025-05-25 06:05:20','','Reporte De Inventario'),(1587,1,'Acceso a Reporte De Inventario','2025-05-25 06:09:37','','Reporte De Inventario'),(1588,1,'Acceso a Reporte De Inventario','2025-05-25 06:12:56','','Reporte De Inventario'),(1589,1,'Acceso a Reporte De Inventario','2025-05-25 06:18:48','','Reporte De Inventario'),(1590,1,'Acceso a Ajuste de Inventario','2025-05-25 06:31:50','','Ajuste de Inventario'),(1591,1,'Acceso a Carga de productos','2025-05-25 06:31:51','','Carga de productos'),(1592,1,'Registro de carga','2025-05-25 06:32:15','Charcuteria','Carga'),(1593,1,'Registro de carga','2025-05-25 06:32:32','Quesos','Carga'),(1594,1,'Acceso a Reporte De Inventario','2025-05-25 06:32:46','','Reporte De Inventario'),(1595,1,'Acceso a Reporte De Cuentas Pendientes','2025-05-25 06:35:54','','Reporte De Cuentas Pendientes'),(1596,1,'Acceso a Ajuste de Inventario','2025-05-25 07:27:23','','Ajuste de Inventario'),(1597,1,'Acceso a Carga de productos','2025-05-25 07:27:24','','Carga de productos'),(1598,1,'Registro de carga','2025-05-25 07:27:42','sin factura','Carga'),(1599,1,'Acceso a Reporte De Cuentas Pendientes','2025-05-25 07:34:39','','Reporte De Cuentas Pendientes'),(1600,1,'Acceso a Ajuste de Inventario','2025-05-25 08:13:53','','Ajuste de Inventario'),(1601,1,'Acceso a Carga de productos','2025-05-25 08:13:54','','Carga de productos'),(1602,1,'Acceso a Reporte De Cuentas Pendientes','2025-05-25 08:14:18','','Reporte De Cuentas Pendientes'),(1603,1,'Acceso a Ajuste de Inventario','2025-05-25 08:15:17','','Ajuste de Inventario'),(1604,1,'Acceso a Descarga de productos','2025-05-25 08:15:17','','Descarga de productos'),(1605,1,'Acceso a Ajuste de Inventario','2025-05-25 08:15:18','','Ajuste de Inventario'),(1606,1,'Acceso a Carga de productos','2025-05-25 08:15:19','','Carga de productos'),(1607,1,'Registro de carga','2025-05-25 08:16:11','Comercio','Carga'),(1608,1,'Acceso a Cuentas pendientes','2025-05-25 08:19:39','','Cuentas pendientes'),(1609,1,'Acceso a Reporte De Cuentas Pendientes','2025-05-25 08:19:46','','Reporte De Cuentas Pendientes'),(1610,1,'Acceso a Reporte De proveedores','2025-05-25 08:59:25','','Reporte De proveedores'),(1611,1,'Acceso a Cuentas pendientes','2025-05-25 08:59:28','','Cuentas pendientes'),(1612,1,'Acceso a Reporte De Inventario','2025-05-25 08:59:31','','Reporte De Inventario'),(1613,1,'Acceso a Cuentas pendientes','2025-05-25 09:00:33','','Cuentas pendientes'),(1614,1,'Acceso a Reporte De Cuentas Pendientes','2025-05-25 09:00:36','','Reporte De Cuentas Pendientes'),(1615,1,'Acceso a Reporte De proveedores','2025-05-25 09:11:22','','Reporte De proveedores'),(1616,1,'Acceso a Cuentas pendientes','2025-05-25 09:11:24','','Cuentas pendientes'),(1617,1,'Acceso a Reporte De Inventario','2025-05-25 09:11:27','','Reporte De Inventario'),(1618,1,'Acceso a Reporte De Cuentas Pendientes','2025-05-25 09:11:42','','Reporte De Cuentas Pendientes'),(1619,1,'Acceso a Reporte De proveedores','2025-05-25 17:23:48','','Reporte De proveedores'),(1620,1,'Acceso a Reporte De Inventario','2025-05-25 17:23:51','','Reporte De Inventario'),(1621,1,'Acceso a Reporte De Cuentas Pendientes','2025-05-25 17:31:14','','Reporte De Cuentas Pendientes'),(1622,1,'Acceso a Reporte De proveedores','2025-05-25 18:13:43','','Reporte De proveedores'),(1623,1,'Acceso a Cuentas pendientes','2025-05-25 18:13:48','','Cuentas pendientes'),(1624,1,'Acceso a Productos','2025-05-25 18:14:02','','Productos'),(1625,1,'Acceso a Cuentas pendientes','2025-05-25 18:26:17','','Cuentas pendientes'),(1626,1,'Acceso a Cuentas pendientes','2025-05-25 18:29:21','','Cuentas pendientes'),(1627,1,'Registro de pago completo','2025-05-25 18:47:00','Venta #26 - Monto: 989.21','Cuentas por Cobrar - Pago recibido'),(1628,1,'Acceso a Productos','2025-05-25 18:50:05','','Productos'),(1629,1,'Acceso a Ventas','2025-05-25 18:50:06','','Ventas'),(1630,1,'Acceso a Marcas','2025-05-25 18:50:14','','Marcas'),(1631,1,'Acceso a Cuentas pendientes','2025-05-25 18:50:17','','Cuentas pendientes'),(1632,1,'Acceso a Productos','2025-05-25 18:50:21','','Productos'),(1633,1,'Acceso a Caja','2025-05-25 18:59:43','','Caja'),(1634,1,'Acceso a Productos','2025-05-25 18:59:45','','Productos'),(1635,1,'Acceso a Compras','2025-05-25 18:59:49','','Compras'),(1636,1,'Acceso a Contabilidad','2025-05-25 18:59:50','','Contabilidad'),(1637,1,'Acceso a Catálogo de cuentas','2025-05-25 18:59:51','','Catálogo de cuentas'),(1638,1,'Acceso a Cuentas pendientes','2025-05-25 19:01:09','','Cuentas pendientes'),(1639,19,'Acceso al sistema','2025-05-25 22:48:52','manuel','Inicio'),(1640,19,'Acceso a Contabilidad','2025-05-25 22:48:58','','Contabilidad'),(1641,19,'Acceso a Catálogo de cuentas','2025-05-25 22:49:03','','Catálogo de cuentas'),(1642,19,'Acceso a Cuentas pendientes','2025-05-25 22:49:28','','Cuentas pendientes'),(1643,1,'Acceso al sistema','2025-05-25 22:50:15','admin','Inicio'),(1644,1,'Registro de rol','2025-05-25 22:57:21','Rol de prueba manuela','Roles'),(1645,1,'Acceso a Usuarios','2025-05-25 22:57:32','','Usuarios'),(1646,1,'Editar usuario','2025-05-25 22:57:39','manuel','Usuarios'),(1647,19,'Acceso al sistema','2025-05-25 22:57:55','manuel','Inicio'),(1648,19,'Acceso a Usuarios','2025-05-25 22:58:20','','Usuarios'),(1649,19,'Acceso a Usuarios','2025-05-25 23:00:07','','Usuarios'),(1650,19,'Acceso a Cuentas pendientes','2025-05-25 23:00:16','','Cuentas pendientes'),(1651,19,'Acceso a Contabilidad','2025-05-25 23:01:43','','Contabilidad'),(1652,19,'Acceso a Catálogo de cuentas','2025-05-25 23:01:45','','Catálogo de cuentas'),(1653,19,'Acceso a Ventas','2025-05-25 23:02:57','','Ventas'),(1654,19,'Acceso a Contabilidad','2025-05-25 23:03:33','','Contabilidad'),(1655,19,'Acceso a Contabilidad','2025-05-25 23:03:33','','Contabilidad'),(1656,1,'Acceso al sistema','2025-05-25 23:04:02','admin','Inicio'),(1657,1,'Acceso a Contabilidad','2025-05-25 23:05:34','','Contabilidad'),(1658,1,'Acceso a Catálogo de cuentas','2025-05-25 23:05:36','','Catálogo de cuentas'),(1659,1,'Registro de cuenta contable','2025-05-25 23:41:17','Prueba cuenta','Contabilidad'),(1660,1,'Acceso a Productos','2025-05-25 23:43:33','','Productos'),(1661,1,'Acceso a Contabilidad','2025-05-25 23:44:26','','Contabilidad'),(1662,1,'Acceso a Catálogo de cuentas','2025-05-25 23:44:27','','Catálogo de cuentas'),(1663,1,'Registro de cuenta contable','2025-05-26 00:19:43','Prueba cuenta','Contabilidad'),(1664,1,'Registro de cuenta contable','2025-05-26 00:21:20','prueba uno','Contabilidad'),(1665,1,'Registro de cuenta contable','2025-05-26 00:21:33','prueba dos','Contabilidad'),(1666,1,'Registro de cuenta contable','2025-05-26 00:21:49','prueba tres','Contabilidad'),(1667,1,'Registro de cuenta contable','2025-05-26 00:22:10','prueba cuatro','Contabilidad'),(1668,1,'Registro de cuenta contable','2025-05-26 00:22:29','prueba cinco','Contabilidad'),(1669,1,'Edición de cuenta contable con el código','2025-05-26 00:25:28','1','Contabilidad'),(1670,1,'Edición de cuenta contable con el código','2025-05-26 00:25:58','13','Contabilidad'),(1671,1,'Edición de cuenta contable con el código','2025-05-26 00:26:31','21','Contabilidad'),(1672,1,'Edición de cuenta contable con el código','2025-05-26 00:27:12','35','Contabilidad'),(1673,1,'Eliminar cuenta contable con el código','2025-05-26 00:27:26','5','Contabilidad'),(1674,1,'Eliminar cuenta contable con el código','2025-05-26 00:27:31','3','Contabilidad'),(1675,1,'Eliminar cuenta contable con el código','2025-05-26 00:27:34','8','Contabilidad'),(1676,1,'Eliminar cuenta contable con el código','2025-05-26 00:33:18','11','Contabilidad'),(1677,1,'Eliminar cuenta contable con el código','2025-05-26 00:33:37','9','Contabilidad'),(1678,1,'Eliminar cuenta contable con el código','2025-05-26 00:40:24','6','Contabilidad'),(1679,1,'Eliminar cuenta contable con el código','2025-05-26 00:40:28','10','Contabilidad'),(1680,1,'Eliminar cuenta contable con el código','2025-05-26 00:40:32','12','Contabilidad'),(1681,1,'Eliminar cuenta contable con el código','2025-05-26 00:43:19','14','Contabilidad'),(1682,1,'Eliminar cuenta contable con el código','2025-05-26 00:43:40','15','Contabilidad'),(1683,1,'Eliminar cuenta contable con el código','2025-05-26 00:44:50','28','Contabilidad'),(1684,1,'Acceso a Contabilidad','2025-05-26 00:45:05','','Contabilidad'),(1685,1,'Acceso a Catálogo de cuentas','2025-05-26 00:45:06','','Catálogo de cuentas'),(1686,1,'Acceso a Contabilidad','2025-05-26 00:45:07','','Contabilidad'),(1687,1,'Acceso a Gestionar asientos','2025-05-26 00:45:07','','Gestionar asientos'),(1688,1,'Acceso a Ventas','2025-05-26 00:46:26','','Ventas'),(1689,1,'Acceso a Contabilidad','2025-05-26 00:46:27','','Contabilidad'),(1690,1,'Acceso a Gestionar asientos','2025-05-26 00:46:28','','Gestionar asientos'),(1691,1,'Acceso a Contabilidad','2025-05-26 00:46:29','','Contabilidad'),(1692,1,'Acceso a Catálogo de cuentas','2025-05-26 00:46:29','','Catálogo de cuentas'),(1693,1,'Acceso a Productos','2025-05-26 02:18:06','','Productos'),(1694,1,'Registro de producto','2025-05-26 02:18:16','Manuela ','Productos'),(1695,1,'Acceso a Finanzas','2025-05-26 02:19:48','','Finanzas'),(1696,1,'Acceso a Finanzas','2025-05-26 02:19:48','','Finanzas'),(1697,1,'Acceso a Finanzas','2025-05-26 03:02:21','','Finanzas'),(1698,1,'Acceso a Productos','2025-05-26 03:02:24','','Productos'),(1699,1,'Acceso a Cuentas pendientes','2025-05-26 03:02:27','','Cuentas pendientes'),(1700,1,'Acceso a Productos','2025-05-26 03:02:40','','Productos'),(1701,1,'Acceso a Ajuste de Inventario','2025-05-26 03:02:41','','Ajuste de Inventario'),(1702,1,'Acceso a Carga de productos','2025-05-26 03:02:42','','Carga de productos'),(1703,1,'Acceso a Ajuste de Inventario','2025-05-26 03:02:43','','Ajuste de Inventario'),(1704,1,'Acceso a Compras','2025-05-26 03:02:43','','Compras'),(1705,1,'Acceso a Contabilidad','2025-05-26 03:03:02','','Contabilidad'),(1706,1,'Acceso a Catálogo de cuentas','2025-05-26 03:03:03','','Catálogo de cuentas'),(1707,1,'Acceso a Categorías','2025-05-26 03:08:16','','Categorías'),(1708,1,'Acceso a Contabilidad','2025-05-26 03:09:52','','Contabilidad'),(1709,1,'Acceso a Catálogo de cuentas','2025-05-26 03:09:52','','Catálogo de cuentas'),(1710,1,'Acceso a Productos','2025-05-26 03:09:58','','Productos'),(1711,1,'Acceso a Contabilidad','2025-05-26 03:10:02','','Contabilidad'),(1712,1,'Acceso a Catálogo de cuentas','2025-05-26 03:10:03','','Catálogo de cuentas'),(1713,1,'Registro de cuenta contable','2025-05-26 03:18:15','Prueba cuenta','Contabilidad'),(1714,1,'Registro de cuenta contable','2025-05-26 03:26:51','Prueba cuenta','Contabilidad'),(1715,19,'Acceso al sistema','2025-05-26 04:18:50','manuel','Inicio'),(1716,19,'Acceso a Contabilidad','2025-05-26 04:18:53','','Contabilidad'),(1717,19,'Acceso a Gestionar asientos','2025-05-26 04:18:57','','Gestionar asientos'),(1718,19,'Acceso a Contabilidad','2025-05-26 04:18:59','','Contabilidad'),(1719,19,'Acceso a Catálogo de cuentas','2025-05-26 04:18:59','','Catálogo de cuentas'),(1720,19,'Acceso a Ventas','2025-05-26 04:19:10','','Ventas'),(1721,19,'Acceso a Cuentas pendientes','2025-05-26 04:19:12','','Cuentas pendientes'),(1722,1,'Acceso al sistema','2025-05-26 04:20:00','admin','Inicio'),(1723,1,'Acceso a Cuentas pendientes','2025-05-26 04:20:08','','Cuentas pendientes'),(1724,19,'Acceso al sistema','2025-05-26 04:44:52','manuel','Inicio'),(1725,19,'Acceso a Cuentas pendientes','2025-05-26 04:44:56','','Cuentas pendientes'),(1726,19,'Acceso al sistema','2025-05-26 04:45:54','manuel','Inicio'),(1727,19,'Acceso a Usuarios','2025-05-26 04:46:00','','Usuarios'),(1728,19,'Acceso a Contabilidad','2025-05-26 04:46:11','','Contabilidad'),(1729,19,'Acceso a Catálogo de cuentas','2025-05-26 04:46:12','','Catálogo de cuentas'),(1730,1,'Acceso al sistema','2025-05-26 04:52:54','admin','Inicio'),(1731,1,'Acceso a Ventas','2025-05-26 04:52:57','','Ventas'),(1732,1,'Acceso a Cuentas pendientes','2025-05-26 04:53:02','','Cuentas pendientes'),(1733,1,'Acceso a Ventas','2025-05-26 05:14:11','','Ventas'),(1734,1,'Acceso a Cuentas pendientes','2025-05-26 05:18:39','','Cuentas pendientes'),(1735,1,'Acceso a Reporte De Cuentas Pendientes','2025-05-26 05:18:48','','Reporte De Cuentas Pendientes'),(1736,1,'Acceso a Cuentas pendientes','2025-05-26 05:24:28','','Cuentas pendientes'),(1737,1,'Acceso a Productos','2025-05-26 05:24:31','','Productos'),(1738,1,'Acceso a Divisas','2025-05-26 05:26:54','','Divisas'),(1739,1,'Acceso a Caja','2025-05-26 05:27:09','','Caja'),(1740,1,'Acceso a Divisas','2025-05-26 05:27:25','','Divisas'),(1741,1,'Acceso a Usuarios','2025-05-26 05:31:48','','Usuarios'),(1742,1,'Registro de rol','2025-05-26 05:33:25','administrador dos','Roles'),(1743,1,'Acceso a Usuarios','2025-05-26 05:33:31','','Usuarios'),(1744,1,'Editar usuario','2025-05-26 05:33:39','manuela','Usuarios'),(1745,16,'Acceso al sistema','2025-05-26 05:33:59','manuela','Inicio'),(1746,16,'Acceso a Divisas','2025-05-26 05:34:08','','Divisas'),(1747,16,'Buscar divisa','2025-05-26 05:37:28','','Divisas'),(1748,16,'Buscar divisa','2025-05-26 05:37:46','Euro','Divisas'),(1749,16,'Eliminar divisa','2025-05-26 05:37:54','Eliminada la Divisa con Codigo 2','Divisas'),(1750,16,'Acceso a Caja','2025-05-26 05:38:02','','Caja'),(1751,16,'Acceso a Divisas','2025-05-26 05:38:09','','Divisas'),(1752,16,'Eliminar divisa','2025-05-26 05:38:14','Eliminada la Divisa con Codigo 4','Divisas'),(1753,16,'Acceso a Caja','2025-05-26 05:38:26','','Caja'),(1754,16,'Acceso a Tipos de pago','2025-05-26 05:38:56','','Tipos de pago'),(1755,16,'Acceso a Gastos','2025-05-26 05:40:39','','Gastos'),(1756,16,'Acceso a Productos','2025-05-26 05:40:42','','Productos'),(1757,16,'Acceso a Productos','2025-05-26 05:40:44','','Productos'),(1758,16,'Acceso a Reporte De Inventario','2025-05-26 05:59:01','','Reporte De Inventario'),(1759,16,'Acceso a Reporte De Cuentas Pendientes','2025-05-26 05:59:16','','Reporte De Cuentas Pendientes');
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_usuario`
--

LOCK TABLES `tipo_usuario` WRITE;
/*!40000 ALTER TABLE `tipo_usuario` DISABLE KEYS */;
INSERT INTO `tipo_usuario` VALUES (1,'Administrador',1),(2,'pruebaaaaaa',1),(3,'pruebaone',1),(6,'prueba',1),(7,'pruebatwo',1),(8,'manuela',0),(9,'Contador',1),(10,'Rol de prueba manuela',1),(11,'administrador dos',1);
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
INSERT INTO `tpu_permisos` VALUES (2,1,1),(2,1,3),(2,2,3),(2,3,1),(2,3,3),(2,4,3),(2,5,1),(2,5,3),(2,5,4),(2,7,3),(2,9,3),(1,1,1),(1,1,3),(1,1,4),(1,2,1),(1,2,3),(1,2,4),(1,3,1),(1,3,3),(1,3,4),(1,4,1),(1,4,3),(1,4,4),(1,5,1),(1,5,3),(1,5,4),(1,6,1),(1,6,3),(1,6,4),(1,7,1),(1,7,3),(1,7,4),(1,8,1),(1,8,3),(1,8,4),(1,9,1),(1,9,3),(1,9,4),(1,10,1),(1,10,3),(1,10,4),(1,11,1),(1,11,3),(1,11,4),(1,12,1),(1,12,3),(1,12,4),(1,13,1),(1,13,3),(1,13,4),(1,14,1),(1,14,3),(1,14,4),(1,15,1),(1,15,3),(1,15,4),(3,1,1),(3,1,3),(3,2,1),(3,4,1),(3,5,1),(3,6,3),(3,6,4),(3,14,1),(6,1,2),(6,3,2),(6,5,2),(6,7,2),(7,5,2),(8,1,2),(8,2,2),(8,3,2),(9,3,1),(9,3,3),(9,3,4),(9,3,2),(9,12,1),(9,12,3),(9,12,4),(9,12,2),(10,3,3),(10,3,2),(10,5,1),(10,5,2),(10,12,2),(10,13,3),(10,13,4),(10,13,2),(11,1,1),(11,1,3),(11,1,4),(11,1,2),(11,2,1),(11,2,3),(11,2,4),(11,2,2),(11,3,1),(11,3,3),(11,3,4),(11,3,2),(11,4,1),(11,4,3),(11,4,4),(11,4,2),(11,5,1),(11,5,3),(11,5,4),(11,5,2),(11,6,1),(11,6,3),(11,6,4),(11,6,2),(11,7,1),(11,7,3),(11,7,4),(11,7,2),(11,8,1),(11,8,3),(11,8,4),(11,8,2),(11,9,1),(11,9,3),(11,9,4),(11,9,2),(11,10,1),(11,10,3),(11,10,4),(11,10,2),(11,11,1),(11,11,3),(11,11,4),(11,11,2),(11,12,1),(11,12,3),(11,12,4),(11,12,2),(11,13,1),(11,13,3),(11,13,4),(11,13,2),(11,14,1),(11,14,3),(11,14,4),(11,14,2),(11,15,1),(11,15,3),(11,15,4),(11,15,2);
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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Administrador','admin','$2y$10$.nbh0vwGWNkBgsVzkBSoYurftn9Mg.TLYkxmK32KhMKOzaTjaRS3.',1,1),(2,'jorges','jorge','$2y$10$wRFU5jEfVEpp/jXR0OQ0YuycA5JvHQilBkXSwfHBds164nz1doz3e',1,1),(16,'Manuela ','manuela','$2y$10$gZ84jSgivO442KA.ltxwheZpFPGce9c21apbK4AiYc.xHV8/XNDyq',11,1),(19,'Manuel Antonio','manuel','$2y$10$ujcHj3nn9QMSVD8rQO/2s.Qi9lCpBHamaBawgjjqG9q10TY.y83iu',10,1);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Current Database: `savycplus`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `savycplus` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci */;

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
  KEY `fk_analisis_rentabilidad_productos` (`cod_producto`),
  CONSTRAINT `fk_analisis_rentabilidad_productos` FOREIGN KEY (`cod_producto`) REFERENCES `productos` (`cod_producto`)
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
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asientos_contables`
--

LOCK TABLES `asientos_contables` WRITE;
/*!40000 ALTER TABLE `asientos_contables` DISABLE KEYS */;
INSERT INTO `asientos_contables` VALUES (3,23,'2025-05-19 16:31:42','venta al contado #33',426.42,1),(4,23,'2025-05-19 16:31:42','venta al contado #33',571.40,1),(5,18,'2025-05-19 18:22:42','venta al contado #32',497.55,1),(6,18,'2025-05-19 18:22:42','venta al contado #32',589.92,1),(7,15,'2025-05-19 20:46:20','venta a credito #30',81.29,1),(19,16,'2025-05-19 22:11:29','venta a credito #31',85.28,1),(20,16,'2025-05-19 22:11:29','venta a credito #31',98.92,1),(23,24,'2025-05-19 23:17:31','venta al contado #34',540.13,1),(24,24,'2025-05-19 23:17:31','venta al contado #34',660.10,1),(25,19,'2025-05-20 02:48:27','venta al contado #10',213.21,1),(26,19,'2025-05-20 02:48:27','venta al contado #10',10.00,1),(27,12,'2025-05-20 02:49:08','venta a credito #27',677.43,1),(28,12,'2025-05-20 02:49:08','venta a credito #27',907.76,1),(29,10,'2025-05-20 02:49:46','venta al contado #25',270.97,1),(30,10,'2025-05-20 02:49:46','venta al contado #25',5.09,1),(31,13,'2025-05-20 02:54:37','venta a credito #28',852.84,1),(32,13,'2025-05-20 02:54:37','venta a credito #28',989.21,1),(33,25,'2025-05-20 10:06:03','venta al contado #35',852.84,1),(34,25,'2025-05-20 10:06:03','venta al contado #35',1142.81,1),(35,26,'2025-05-20 11:36:36','venta a credito #36',613.39,1),(36,26,'2025-05-20 11:36:36','venta a credito #36',821.94,1),(41,27,'2025-05-21 00:34:18','venta al contado #37',65.00,1),(42,27,'2025-05-21 00:34:18','venta al contado #37',100.00,1);
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
  `nombre_banco` varchar(20) NOT NULL,
  PRIMARY KEY (`cod_banco`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banco`
--

LOCK TABLES `banco` WRITE;
/*!40000 ALTER TABLE `banco` DISABLE KEYS */;
INSERT INTO `banco` VALUES (2,'Banco Mercanti');
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
  `nombre` varchar(20) NOT NULL,
  `saldo` decimal(10,2) NOT NULL,
  `cod_divisas` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_caja`),
  KEY `cod_divisas` (`cod_divisas`),
  CONSTRAINT `caja_ibfk_1` FOREIGN KEY (`cod_divisas`) REFERENCES `divisas` (`cod_divisa`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caja`
--

LOCK TABLES `caja` WRITE;
/*!40000 ALTER TABLE `caja` DISABLE KEYS */;
INSERT INTO `caja` VALUES (7,'caja prueba',500.00,3,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cambio_divisa`
--

LOCK TABLES `cambio_divisa` WRITE;
/*!40000 ALTER TABLE `cambio_divisa` DISABLE KEYS */;
INSERT INTO `cambio_divisa` VALUES (1,1,1.00,'0000-00-00'),(11,3,10.00,'2025-04-07'),(13,3,95.00,'2025-04-07'),(15,5,105.00,'2025-04-29'),(17,3,10.00,'2025-04-07'),(18,3,95.00,'2025-04-07'),(20,5,105.00,'2025-04-29'),(22,3,95.00,'2025-04-07'),(24,5,105.00,'2025-04-29'),(26,3,95.00,'2025-04-07'),(28,5,105.00,'2025-04-29'),(30,3,95.00,'2025-04-07'),(32,5,105.00,'2025-04-29');
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carga`
--

LOCK TABLES `carga` WRITE;
/*!40000 ALTER TABLE `carga` DISABLE KEYS */;
INSERT INTO `carga` VALUES (1,'2025-05-25 02:31:52','Charcuteria',0.00,1),(2,'2025-05-25 02:32:17','Quesos',0.00,1),(3,'2025-05-25 03:27:25','sin factura',0.00,1),(4,'2025-05-25 04:15:20','Comercio',0.00,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoria_gasto`
--

LOCK TABLES `categoria_gasto` WRITE;
/*!40000 ALTER TABLE `categoria_gasto` DISABLE KEYS */;
INSERT INTO `categoria_gasto` VALUES (3,4,4,1,'inter','2025-05-21',1);
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'queso',1),(2,'Jamones',1),(4,'viveres',1);
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,'generico','perez','12345678','','','',1),(2,'daniel','rojas','26779660','04245645108','danielrojas1901@gmail.com','av.florencio jimenez, parque residencial araguaney',1),(3,'Manuela','Mujica','28516209','12453145','manuelaalejandra.mujica@gmail.com','asdasda',1);
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compras`
--

LOCK TABLES `compras` WRITE;
/*!40000 ALTER TABLE `compras` DISABLE KEYS */;
INSERT INTO `compras` VALUES (5,1,'contado',NULL,55.00,55.00,0.00,'2025-04-29',NULL,1),(6,1,'contado',NULL,96.90,96.90,0.00,'2025-05-15',NULL,3),(7,1,'credito','2025-05-24',12193.70,12193.70,0.00,'2025-05-17',NULL,2),(8,1,'contado',NULL,20847.20,20847.20,0.00,'2025-05-18',NULL,1),(9,1,'contado',NULL,20847.20,20847.20,0.00,'2025-05-18',NULL,1),(10,1,'contado',NULL,20847.20,20847.20,0.00,'2025-05-18',NULL,3),(11,1,'credito','2025-05-20',4775.90,4775.90,0.00,'2025-05-19',NULL,1),(12,1,'contado',NULL,1215.00,1215.00,0.00,'2025-05-20',NULL,1);
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
INSERT INTO `condicion_pagoe` VALUES (1,'prepago'),(2,'pospago'),(3,'credito'),(4,'contado');
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `control`
--

LOCK TABLES `control` WRITE;
/*!40000 ALTER TABLE `control` DISABLE KEYS */;
INSERT INTO `control` VALUES (2,'2025-05-20 12:28:00',NULL,10.00,NULL,7,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuenta_bancaria`
--

LOCK TABLES `cuenta_bancaria` WRITE;
/*!40000 ALTER TABLE `cuenta_bancaria` DISABLE KEYS */;
INSERT INTO `cuenta_bancaria` VALUES (4,2,1,'01050000000000000000',1076.15,1,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuentas_contables`
--

LOCK TABLES `cuentas_contables` WRITE;
/*!40000 ALTER TABLE `cuentas_contables` DISABLE KEYS */;
INSERT INTO `cuentas_contables` VALUES (1,'1','ACTIVO','deudora',NULL,1,0.00,1),(13,'2','PASIVO','deudora',NULL,1,0.00,1),(16,'2.1.1.01','PROVEEDORES POR COMPRAS','deudora',13,4,0.00,1),(17,'2.1.1.02','PROVEEDORES POR GASTOS','deudora',13,4,0.00,1),(18,'2.1.1.03','PROVEEDORES INTERNACIONALES','deudora',13,4,0.00,1),(19,'2.2','PASIVO NO CORRIENTE','deudora',13,2,0.00,1),(20,'2.2.1','PRÉSTAMOS A LARGO PLAZO','deudora',13,3,0.00,1),(21,'3','PATRIMONIO','deudora',NULL,1,0.00,1),(22,'3.1','CAPITAL SOCIAL','deudora',21,2,0.00,1),(23,'5','GASTOS','deudora',NULL,1,0.00,1),(24,'5.1','COSTO DE VENTAS','deudora',23,2,0.00,1),(25,'5.2','GASTOS DE OPERACIÓN','deudora',23,2,0.00,1),(26,'5.3','GASTOS FINANCIEROS','deudora',23,2,0.00,1),(27,'4','INGRESOS','acreedora',NULL,1,0.00,1),(29,'4.2','SERVICIOS PRESTADOS','acreedora',27,2,0.00,1),(30,'4.3','OTROS INGRESOS','acreedora',27,2,0.00,1),(32,'6','INGRESOS','acreedora',NULL,1,0.00,2),(33,'6.1','Prueba cuenta','acreedora',32,2,0.00,2),(34,'7','Prueba cuenta','deudora',NULL,1,0.00,2),(35,'8','prueba uno','deudora',NULL,1,0.00,2),(36,'8.1','prueba dos','deudora',35,2,0.00,2),(37,'8.1.1','prueba tres','deudora',36,3,0.00,2),(38,'8.1.1.01','prueba cuatro','deudora',37,4,0.00,2),(39,'8.1.1.01.01','prueba cinco','deudora',38,5,0.00,2),(40,'9','Prueba cuenta','deudora',NULL,1,0.00,2),(41,'4.4','Prueba cuenta','acreedora',27,2,0.00,2);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `descarga`
--

LOCK TABLES `descarga` WRITE;
/*!40000 ALTER TABLE `descarga` DISABLE KEYS */;
INSERT INTO `descarga` VALUES (1,'2025-04-29 23:49:15','ajuste stock prueba',0.00,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_asientos`
--

LOCK TABLES `detalle_asientos` WRITE;
/*!40000 ALTER TABLE `detalle_asientos` DISABLE KEYS */;
INSERT INTO `detalle_asientos` VALUES (1,3,24,426.42,'Debe'),(9,5,24,497.55,'Debe'),(13,7,24,81.29,'Debe'),(43,19,24,85.28,'Debe'),(50,23,24,540.13,'Debe'),(55,25,24,213.21,'Debe'),(59,27,24,677.43,'Debe'),(63,29,24,270.97,'Debe'),(67,31,24,852.84,'Debe'),(71,33,24,852.84,'Debe'),(76,35,24,613.39,'Debe'),(88,41,24,65.00,'Debe');
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_carga`
--

LOCK TABLES `detalle_carga` WRITE;
/*!40000 ALTER TABLE `detalle_carga` DISABLE KEYS */;
INSERT INTO `detalle_carga` VALUES (5,5,1,25),(6,7,2,23),(7,10,2,10),(8,7,3,1),(9,5,4,15),(10,10,4,20);
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_compras`
--

LOCK TABLES `detalle_compras` WRITE;
/*!40000 ALTER TABLE `detalle_compras` DISABLE KEYS */;
INSERT INTO `detalle_compras` VALUES (5,5,6,5.00,11.00),(6,6,7,10.20,9.50),(7,7,8,9.50,677.43),(8,7,9,7.80,738.22),(9,8,10,55.00,379.04),(10,9,11,55.00,379.04),(11,10,12,55.00,379.04),(12,11,13,5.60,852.84),(13,12,14,1.00,65.00),(14,12,15,1.00,1150.00);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_descarga`
--

LOCK TABLES `detalle_descarga` WRITE;
/*!40000 ALTER TABLE `detalle_descarga` DISABLE KEYS */;
INSERT INTO `detalle_descarga` VALUES (1,5,1,0.2);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_operacion`
--

LOCK TABLES `detalle_operacion` WRITE;
/*!40000 ALTER TABLE `detalle_operacion` DISABLE KEYS */;
INSERT INTO `detalle_operacion` VALUES (1,'al contado'),(2,'a credito');
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_pago_emitido`
--

LOCK TABLES `detalle_pago_emitido` WRITE;
/*!40000 ALTER TABLE `detalle_pago_emitido` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_pago_recibido`
--

LOCK TABLES `detalle_pago_recibido` WRITE;
/*!40000 ALTER TABLE `detalle_pago_recibido` DISABLE KEYS */;
INSERT INTO `detalle_pago_recibido` VALUES (51,36,11,6.94),(52,37,11,989.21);
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_productos`
--

LOCK TABLES `detalle_productos` WRITE;
/*!40000 ALTER TABLE `detalle_productos` DISABLE KEYS */;
INSERT INTO `detalle_productos` VALUES (1,1,0,'0000-00-00',''),(2,1,8,'0000-00-00',''),(3,1,67,'0000-00-00',''),(4,1,23.5,'0000-00-00',''),(5,2,40,'2025-04-29','26-12'),(6,2,2.808,'2025-08-21',''),(7,3,30.799,'0000-00-00',''),(8,3,9.5,'0000-00-00',''),(9,2,7.8,'0000-00-00',''),(10,4,83.8,'0000-00-00',''),(11,4,55,'0000-00-00',''),(12,4,55,'0000-00-00',''),(13,2,5.6,'0000-00-00',''),(14,2,1,'0000-00-00','1234'),(15,3,1,'0000-00-00','0890809');
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_tipo_pago`
--

LOCK TABLES `detalle_tipo_pago` WRITE;
/*!40000 ALTER TABLE `detalle_tipo_pago` DISABLE KEYS */;
INSERT INTO `detalle_tipo_pago` VALUES (11,4,'digital',4,NULL,1),(12,3,'digital',4,NULL,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_ventas`
--

LOCK TABLES `detalle_ventas` WRITE;
/*!40000 ALTER TABLE `detalle_ventas` DISABLE KEYS */;
INSERT INTO `detalle_ventas` VALUES (1,7,1,70.00,0.00,10.000),(2,7,2,28.00,0.00,4.000),(3,8,5,4.31,0.00,0.322),(4,9,5,7.37,0.00,0.500),(5,10,5,3.69,0.00,0.250),(6,11,5,2.21,0.00,0.150),(7,12,5,3.39,0.00,0.230),(8,13,5,14.74,0.00,1.000),(9,14,5,10.98,0.00,0.745),(10,15,5,14.74,0.00,1.000),(11,16,5,6.38,0.00,0.433),(12,17,5,3.69,0.00,0.250),(13,18,5,2.95,0.00,0.200),(14,19,5,6.37,0.00,0.432),(15,20,5,4.25,0.00,0.288),(16,20,6,17.13,0.00,1.162),(17,21,6,3.14,0.00,0.213),(18,22,6,9.58,0.00,0.650),(19,23,5,7.37,0.00,0.500),(20,23,7,19.10,0.00,1.500),(21,24,5,14.74,0.00,1.000),(22,25,7,5.09,0.00,0.400),(23,26,5,989.21,0.00,1.000),(24,27,7,907.76,0.00,1.000),(25,28,5,989.21,0.00,1.000),(26,29,5,956.57,0.00,0.967),(27,30,7,108.93,0.00,0.120),(28,31,5,98.92,0.00,0.100),(29,32,5,494.61,0.00,0.500),(30,32,7,95.31,0.00,0.105),(31,33,5,571.41,0.00,0.500),(32,34,10,545.82,0.00,1.200),(33,34,5,114.28,0.00,0.100),(34,35,5,1142.81,0.00,1.000),(35,36,5,571.41,0.00,0.500),(36,36,7,250.54,0.00,0.276),(37,37,5,72.55,0.00,0.833),(38,37,6,14.55,0.00,0.167);
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_vueltoe`
--

LOCK TABLES `detalle_vueltoe` WRITE;
/*!40000 ALTER TABLE `detalle_vueltoe` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `divisas`
--

LOCK TABLES `divisas` WRITE;
/*!40000 ALTER TABLE `divisas` DISABLE KEYS */;
INSERT INTO `divisas` VALUES (1,'Bolívares','Bs',1),(3,'Euro','EUR',1),(5,'libra','Lb',1);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gasto`
--

LOCK TABLES `gasto` WRITE;
/*!40000 ALTER TABLE `gasto` DISABLE KEYS */;
INSERT INTO `gasto` VALUES (2,3,2,'pago inter',100.00,'2025-05-21',NULL,1);
/*!40000 ALTER TABLE `gasto` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movimientos`
--

LOCK TABLES `movimientos` WRITE;
/*!40000 ALTER TABLE `movimientos` DISABLE KEYS */;
INSERT INTO `movimientos` VALUES (4,19,1,2,'2025-05-13',1),(5,20,1,1,'2025-05-13',1),(6,21,1,1,'2025-05-14',1),(7,22,1,1,'2025-05-15',1),(8,23,1,1,'2025-05-15',1),(9,24,1,2,'2025-05-16',1),(10,25,1,1,'2025-05-16',2),(11,26,1,2,'2025-05-17',1),(12,27,1,2,'2025-05-17',2),(13,28,1,2,'2025-05-17',2),(14,29,1,2,'2025-05-17',1),(15,30,1,2,'2025-05-18',2),(16,31,1,2,'2025-05-18',2),(17,6,1,1,'2025-05-15',1),(18,32,1,1,'2025-05-18',2),(19,10,1,1,'2025-05-18',2),(20,6,2,1,'2025-05-15',1),(21,10,2,1,'2025-05-18',1),(22,11,2,2,'2025-05-19',1),(23,33,1,1,'2025-05-19',2),(24,34,1,1,'2025-05-19',2),(25,35,1,1,'2025-05-20',2),(26,36,1,2,'2025-05-20',2),(27,37,1,1,'2025-05-21',2),(28,15,1,1,'2025-05-12',1),(29,16,1,1,'2025-05-12',1);
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
  `fecha` date NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pago_emitido`
--

LOCK TABLES `pago_emitido` WRITE;
/*!40000 ALTER TABLE `pago_emitido` DISABLE KEYS */;
INSERT INTO `pago_emitido` VALUES (1,'compra',1,'2025-05-16',6,NULL,100.00),(6,'compra',NULL,'2025-05-19',10,NULL,20847.20),(7,'compra',NULL,'2025-05-24',7,NULL,10.00);
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
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pago_recibido`
--

LOCK TABLES `pago_recibido` WRITE;
/*!40000 ALTER TABLE `pago_recibido` DISABLE KEYS */;
INSERT INTO `pago_recibido` VALUES (1,11,NULL,'2025-05-12 13:47:17',2.21),(2,10,NULL,'2025-05-12 18:09:31',10.00),(3,9,NULL,'2025-05-12 18:20:54',10.00),(4,8,NULL,'2025-05-12 18:43:39',5.00),(5,13,1,'2025-05-12 19:40:11',20.00),(6,12,2,'2025-05-12 19:41:34',10.00),(7,14,NULL,'2025-05-12 21:47:07',5.00),(8,15,NULL,'2025-05-12 22:02:05',5.00),(9,16,NULL,'2025-05-12 22:46:41',5.00),(10,14,3,'2025-05-12 22:47:03',10.00),(11,17,4,'2025-05-13 09:31:17',10.00),(12,16,NULL,'2025-05-13 09:32:59',1.00),(13,20,5,'2025-05-13 23:40:46',25.00),(14,19,NULL,'2025-05-13 23:56:17',5.00),(15,21,6,'2025-05-14 17:53:43',4.00),(16,22,NULL,'2025-05-15 02:32:09',9.58),(17,23,NULL,'2025-05-15 12:38:10',26.46),(18,24,NULL,'2025-05-16 23:49:59',14.74),(19,25,NULL,'2025-05-16 23:51:50',5.09),(25,32,NULL,'2025-05-18 22:58:01',589.92),(26,31,NULL,'2025-05-19 12:49:20',98.92),(27,33,NULL,'2025-05-19 13:39:02',494.76),(28,33,NULL,'2025-05-19 13:40:19',76.64),(29,34,NULL,'2025-05-19 23:10:28',660.10),(30,35,NULL,'2025-05-20 10:05:20',1142.81),(31,37,7,'2025-05-21 00:25:06',100.00),(32,15,8,'2025-05-24 17:09:10',66.00),(33,36,NULL,'2025-05-24 22:58:56',10.00),(34,16,9,'2025-05-24 22:59:14',2.00),(35,36,NULL,'2025-05-24 23:38:27',805.00),(36,36,NULL,'2025-05-24 23:58:54',6.94),(37,26,NULL,'2025-05-25 14:46:09',989.21);
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presentacion_producto`
--

LOCK TABLES `presentacion_producto` WRITE;
/*!40000 ALTER TABLE `presentacion_producto` DISABLE KEYS */;
INSERT INTO `presentacion_producto` VALUES (1,1,1,'pieza','10',7.00,0,1),(2,1,2,'pieza','4.5',65.00,34,1),(3,1,3,'pieza','3.5',1150.00,34,1),(4,1,4,'Pieza','10',379.04,20,1),(5,3,5,NULL,NULL,125.00,30,1),(6,3,2,NULL,NULL,0.00,0,1),(7,3,6,NULL,NULL,0.00,0,2);
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
INSERT INTO `presupuestos` VALUES (126,3,800.00,'2025-05-01','Presupuesto de prueba para mayo hola','2025-05-25 02:17:42');
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,1,NULL,'Queso Duro',NULL),(2,2,6,'jamon de piernaa','vista/dist/img/productos/ImgThumb2.jpg'),(3,1,7,'Queso amarillo','vista/dist/img/productos/default.png'),(4,1,7,'queso blanco','vista/dist/img/productos/default.png'),(5,4,8,'mantequilla','vista/dist/img/productos/default.png'),(6,2,NULL,'Manuela ','vista/dist/img/productos/default.png');
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
  `cod_stock` int(11) NOT NULL AUTO_INCREMENT,
  `cod_detallep` int(11) NOT NULL,
  `mes` varchar(20) NOT NULL,
  `stock_inicial` decimal(10,2) DEFAULT NULL,
  `stock_final` decimal(10,2) DEFAULT NULL,
  `ventas_cantidad` decimal(10,2) DEFAULT NULL,
  `rotacion` decimal(8,2) DEFAULT NULL,
  `dias_rotacion` decimal(8,2) DEFAULT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`cod_stock`),
  KEY `fk_stock_mensual_detalle_productos` (`cod_detallep`),
  CONSTRAINT `fk_stock_mensual_detalle_productos` FOREIGN KEY (`cod_detallep`) REFERENCES `detalle_productos` (`cod_detallep`)
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_operacion`
--

LOCK TABLES `tipo_operacion` WRITE;
/*!40000 ALTER TABLE `tipo_operacion` DISABLE KEYS */;
INSERT INTO `tipo_operacion` VALUES (1,'venta'),(2,'compra'),(3,'gasto');
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
  `status` int(11) NOT NULL,
  PRIMARY KEY (`cod_metodo`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_pago`
--

LOCK TABLES `tipo_pago` WRITE;
/*!40000 ALTER TABLE `tipo_pago` DISABLE KEYS */;
INSERT INTO `tipo_pago` VALUES (1,'Efectivo',1),(2,'Efectivo USD',1),(3,'Punto de Venta',1),(4,'Pago Movil',1),(5,'Transferencia',1),(7,'biopago',1);
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unidades_medida`
--

LOCK TABLES `unidades_medida` WRITE;
/*!40000 ALTER TABLE `unidades_medida` DISABLE KEYS */;
INSERT INTO `unidades_medida` VALUES (1,'kg',1),(3,'UND',1);
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
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ventas`
--

LOCK TABLES `ventas` WRITE;
/*!40000 ALTER TABLE `ventas` DISABLE KEYS */;
INSERT INTO `ventas` VALUES (7,1,'contado',NULL,98.00,'2025-04-08 20:03:36',3),(8,2,'contado',NULL,4.31,'2025-04-29 22:32:39',3),(9,2,'credito','2025-05-19',7.37,'2025-05-12 01:23:22',3),(10,2,'contado','0000-00-00',3.69,'2025-05-12 01:49:17',3),(11,2,'credito','2025-05-19',2.21,'2025-05-12 13:15:38',3),(12,2,'contado','0000-00-00',3.39,'2025-05-12 19:08:33',3),(13,3,'contado','0000-00-00',14.74,'2025-05-12 19:33:50',3),(14,3,'contado','0000-00-00',10.98,'2025-05-12 19:45:45',3),(15,2,'contado','0000-00-00',14.74,'2025-05-12 21:47:13',3),(16,2,'contado','0000-00-00',6.38,'2025-05-12 22:35:56',3),(17,2,'credito','2025-05-21',3.69,'2025-05-12 22:47:43',3),(18,3,'contado','0000-00-00',2.95,'2025-05-13 20:34:34',1),(19,2,'credito','2025-05-20',6.37,'2025-05-13 23:35:37',2),(20,3,'contado','0000-00-00',21.37,'2025-05-13 23:36:53',3),(21,2,'contado','0000-00-00',3.14,'2025-05-14 17:52:50',3),(22,3,'contado','0000-00-00',9.58,'2025-05-15 02:28:56',3),(23,2,'contado','0000-00-00',26.46,'2025-05-15 12:36:49',3),(24,2,'credito','2025-05-23',14.74,'2025-05-16 14:43:01',3),(25,3,'contado','0000-00-00',5.09,'2025-05-16 23:50:54',3),(26,2,'credito','2025-05-22',989.21,'2025-05-17 22:55:53',3),(27,3,'credito','2025-05-19',907.76,'2025-05-17 22:56:42',1),(28,3,'credito','2025-05-19',989.21,'2025-05-17 22:57:42',1),(29,2,'credito','2025-05-24',956.57,'2025-05-17 23:52:03',1),(30,3,'credito','2025-05-20',108.93,'2025-05-18 00:13:18',1),(31,3,'credito','2025-05-25',98.92,'2025-05-18 00:23:09',3),(32,2,'contado','0000-00-00',589.92,'2025-05-18 22:47:35',3),(33,3,'contado','0000-00-00',571.40,'2025-05-19 13:36:36',3),(34,2,'contado','0000-00-00',660.10,'2025-05-19 23:09:09',3),(35,2,'contado','0000-00-00',1142.81,'2025-05-20 10:04:57',3),(36,2,'credito','2025-05-27',821.94,'2025-05-20 11:35:21',3),(37,2,'contado','0000-00-00',87.10,'2025-05-21 00:16:39',3);
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vuelto_emitido`
--

LOCK TABLES `vuelto_emitido` WRITE;
/*!40000 ALTER TABLE `vuelto_emitido` DISABLE KEYS */;
INSERT INTO `vuelto_emitido` VALUES (1,5.26),(2,6.61),(3,4.02),(4,6.31),(5,3.63),(6,0.86),(7,12.90),(8,56.26),(9,1.62);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vuelto_recibido`
--

LOCK TABLES `vuelto_recibido` WRITE;
/*!40000 ALTER TABLE `vuelto_recibido` DISABLE KEYS */;
INSERT INTO `vuelto_recibido` VALUES (1,3.10),(2,0.20);
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

-- Dump completed on 2025-05-26  2:01:42
