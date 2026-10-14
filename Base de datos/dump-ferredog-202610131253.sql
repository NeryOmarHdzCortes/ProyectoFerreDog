/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-12.0.2-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: ferredog
-- ------------------------------------------------------
-- Server version	12.0.2-MariaDB-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `categoria`
--

DROP TABLE IF EXISTS `categoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `categoria` (
  `Id_Categoria` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) NOT NULL,
  `Descripcion` varchar(200) DEFAULT NULL,
  `Fecha_Creacion` date DEFAULT curdate(),
  `Estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`Id_Categoria`),
  CONSTRAINT `chk_cat_estado` CHECK (`Estado` in (0,1))
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoria`
--

LOCK TABLES `categoria` WRITE;
/*!40000 ALTER TABLE `categoria` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `categoria` VALUES
(1,'Herramientas Manuales','Martillos, desarmadores, llaves y pinzas','2025-02-10',1),
(2,'Herramientas Eléctricas','Taladros, rotomartillos, sierras eléctricas','2025-01-08',1),
(3,'Tornillería y Fijaciones','Tornillos, tuercas, pijas, anclas','2025-01-02',1),
(4,'Pintura y Acabados','Pinturas, barnices, brochas y rodillos','2025-02-17',1),
(5,'Plomería','Tubería, conexiones, llaves de agua','2025-01-18',1),
(6,'Electricidad','Cables, contactos, apagadores, focos','2025-01-16',1),
(7,'Cerrajería','Chapas, candados, llaves y bisagras','2025-01-15',1),
(8,'Jardinería','Herramientas y productos para jardín','2025-01-09',1),
(9,'Seguridad Industrial','Equipo de protección personal','2025-02-17',1),
(10,'Adhesivos y Selladores','Pegamentos, siliconas y cintas','2025-01-07',1),
(11,'Materiales de Construcción','Cemento, arena, block, varilla','2025-02-13',1),
(12,'Instrumentos de Medición','Cintas métricas, niveles, flexómetros','2025-02-17',1),
(13,'Abrasivos','Lijas, discos de corte y desbaste','2025-02-27',1),
(14,'Iluminación','Lámparas, focos LED y luminarias','2025-02-04',1),
(15,'Limpieza y Mantenimiento','Productos de limpieza industrial','2025-01-06',1);
/*!40000 ALTER TABLE `categoria` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `cliente`
--

DROP TABLE IF EXISTS `cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cliente` (
  `Id_Cliente` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(50) NOT NULL,
  `Apellidos` varchar(100) NOT NULL,
  `Telefono` varchar(15) DEFAULT NULL,
  `Correo` varchar(100) DEFAULT NULL,
  `Direccion` varchar(200) DEFAULT NULL,
  `Fecha_Alta` date DEFAULT curdate(),
  `Estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`Id_Cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cliente`
--

LOCK TABLES `cliente` WRITE;
/*!40000 ALTER TABLE `cliente` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `cliente` VALUES
(1,'Norma','Rodríguez Salazar','9514475523','norma661@gmail.com','Calle Zaragoza #231, Col. Centro, Oaxaca de Juárez, Oax.','2026-08-02',1),
(2,'Martha','Castillo Villanueva','9518062546',NULL,'Av. Revolución #126, Col. Centro, Oaxaca de Juárez, Oax.','2025-02-23',1),
(3,'Brenda','Contreras Villanueva','9512355439','brenda463@gmail.com','Calle Independencia #632, Col. Centro, Oaxaca de Juárez, Oax.','2026-03-31',1),
(4,'Roberto','Ruiz Santiago','9519901266','roberto333@gmail.com','Av. Hidalgo #474, Col. Centro, Oaxaca de Juárez, Oax.','2026-01-26',1),
(5,'Marisol','Rodríguez Cabrera','9512432176',NULL,'Calle Reforma #452, Col. Centro, Oaxaca de Juárez, Oax.','2026-05-01',1),
(6,'Emilio','Ramírez Núñez','9512815603',NULL,'Calle Independencia #875, Col. Centro, Oaxaca de Juárez, Oax.','2026-05-09',1),
(7,'Alejandra','Bautista Ruiz','9517724045','alejandra866@gmail.com','Calle Reforma #155, Col. Centro, Oaxaca de Juárez, Oax.','2025-07-05',1),
(8,'Guadalupe','Pérez Mejía','9516228407','guadalupe465@gmail.com','Calle Niños Héroes #342, Col. Centro, Oaxaca de Juárez, Oax.','2025-12-16',1),
(9,'Vanessa','Delgado Pérez','9515565128','vanessa773@gmail.com','Calle 5 de Mayo #687, Col. Centro, Oaxaca de Juárez, Oax.','2025-02-20',1),
(10,'Óscar','Contreras Chávez','9511129686','óscar117@gmail.com','Calle 5 de Mayo #774, Col. Centro, Oaxaca de Juárez, Oax.','2025-10-24',1),
(11,'Roberto','Ramos Herrera','9511709714','roberto854@gmail.com','Av. Universidad #29, Col. Centro, Oaxaca de Juárez, Oax.','2025-12-29',1),
(12,'José','Vázquez Campos','9517795314','josé656@gmail.com','Av. Insurgentes #999, Col. Centro, Oaxaca de Juárez, Oax.','2026-03-04',1),
(13,'Tomás','Guzmán Cabrera','9517718433','tomás865@gmail.com','Av. Revolución #90, Col. Centro, Oaxaca de Juárez, Oax.','2025-04-19',1),
(14,'Arturo','Mejía Castillo','9519289908','arturo79@gmail.com','Calle 5 de Mayo #782, Col. Centro, Oaxaca de Juárez, Oax.','2025-03-19',1),
(15,'Ximena','Contreras Salazar','9512287332','ximena651@gmail.com','Av. Oaxaca #894, Col. Centro, Oaxaca de Juárez, Oax.','2026-01-13',1),
(16,'Patricia','Santiago Delgado','9514158134','patricia568@gmail.com','Calle Independencia #629, Col. Centro, Oaxaca de Juárez, Oax.','2025-02-12',1),
(17,'Fernando','López Contreras','9516614484',NULL,'Calle Reforma #347, Col. Centro, Oaxaca de Juárez, Oax.','2026-09-05',1),
(18,'Daniel','Mendoza Álvarez','9519831414','daniel929@gmail.com','Calle Reforma #671, Col. Centro, Oaxaca de Juárez, Oax.','2025-12-03',1),
(19,'Julia','Moreno Hernández','9519158272','julia220@gmail.com','Calle Morelos #241, Col. Centro, Oaxaca de Juárez, Oax.','2025-12-16',1),
(20,'Rosa','González Mendoza','9519833734','rosa790@gmail.com','Av. Insurgentes #687, Col. Centro, Oaxaca de Juárez, Oax.','2026-07-07',1),
(21,'Tomás','Ramírez Delgado','9511733037','tomás852@gmail.com','Av. Universidad #383, Col. Centro, Oaxaca de Juárez, Oax.','2025-06-19',1),
(22,'Teresa','Contreras Santiago','9513321942','teresa460@gmail.com','Calle Allende #266, Col. Centro, Oaxaca de Juárez, Oax.','2025-06-15',1),
(23,'Adrián','Moreno Núñez','9517478792','adrián529@gmail.com','Calle Reforma #393, Col. Centro, Oaxaca de Juárez, Oax.','2026-05-30',1),
(24,'Gerardo','Rodríguez Fuentes','9515833542','gerardo141@gmail.com','Calle Morelos #318, Col. Centro, Oaxaca de Juárez, Oax.','2026-03-10',1),
(25,'Raúl','Chávez Luna','9511275739','raúl114@gmail.com','Av. Oaxaca #537, Col. Centro, Oaxaca de Juárez, Oax.','2026-05-27',1),
(26,'Rosa','Mendoza Aguilar','9512436363',NULL,'Av. Revolución #379, Col. Centro, Oaxaca de Juárez, Oax.','2026-08-08',1),
(27,'Alfonso','Herrera Medina','9511730386','alfonso21@gmail.com','Av. Oaxaca #406, Col. Centro, Oaxaca de Juárez, Oax.','2026-02-04',1),
(28,'Beatriz','Mejía Hernández','9516578736','beatriz247@gmail.com','Av. Juárez #55, Col. Centro, Oaxaca de Juárez, Oax.','2025-04-17',1),
(29,'Salvador','Peña Gómez','9514206183','salvador713@gmail.com','Av. Hidalgo #375, Col. Centro, Oaxaca de Juárez, Oax.','2026-04-30',1),
(30,'Ana','Peña Castillo','9516389033','ana448@gmail.com','Calle Reforma #91, Col. Centro, Oaxaca de Juárez, Oax.','2025-07-04',1),
(31,'Iván','Ortiz Contreras','9511710601','iván707@gmail.com','Calle Niños Héroes #900, Col. Centro, Oaxaca de Juárez, Oax.','2026-03-31',1),
(32,'Itzel','Aguilar Ruiz','9511543335','itzel59@gmail.com','Calle Zaragoza #167, Col. Centro, Oaxaca de Juárez, Oax.','2026-01-28',1),
(33,'Norma','Moreno Contreras','9513277144','norma543@gmail.com','Calle Allende #85, Col. Centro, Oaxaca de Juárez, Oax.','2026-05-18',1),
(34,'Diego','Pérez Salazar','9517189302','diego684@gmail.com','Calle 5 de Mayo #832, Col. Centro, Oaxaca de Juárez, Oax.','2026-05-21',0),
(35,'Gabriela','Santiago Pérez','9517869154',NULL,'Calle Allende #598, Col. Centro, Oaxaca de Juárez, Oax.','2026-05-06',1),
(36,'Gerardo','Peña González','9519705660','gerardo353@gmail.com','Av. Símbolos Patrios #300, Col. Centro, Oaxaca de Juárez, Oax.','2025-08-19',1),
(37,'Beatriz','Díaz Luna','9512567468','beatriz641@gmail.com','Av. Revolución #666, Col. Centro, Oaxaca de Juárez, Oax.','2025-11-07',1),
(38,'Ismael','Cruz Flores','9516068298','ismael620@gmail.com','Calle 5 de Mayo #260, Col. Centro, Oaxaca de Juárez, Oax.','2026-07-13',1),
(39,'Paola','Ortiz Guzmán','9518792046',NULL,'Calle Morelos #358, Col. Centro, Oaxaca de Juárez, Oax.','2026-03-27',1),
(40,'Mónica','Fuentes Vázquez','9516246485','mónica999@gmail.com','Calle Morelos #791, Col. Centro, Oaxaca de Juárez, Oax.','2025-06-13',1),
(41,'Vicente','Guzmán Rojas','9515038756','vicente93@gmail.com','Calle Morelos #607, Col. Centro, Oaxaca de Juárez, Oax.','2026-02-07',1),
(42,'Luis','Mendoza Rojas','9515692934','luis689@gmail.com','Calle Allende #648, Col. Centro, Oaxaca de Juárez, Oax.','2026-06-01',1),
(43,'Alejandra','Ramírez Rodríguez','9517592683','alejandra348@gmail.com','Calle Zaragoza #767, Col. Centro, Oaxaca de Juárez, Oax.','2026-09-20',1),
(44,'Ximena','Castillo Medina','9511279121','ximena726@gmail.com','Calle Independencia #349, Col. Centro, Oaxaca de Juárez, Oax.','2026-07-31',1),
(45,'Lorena','Cortés Ortiz','9512843740','lorena705@gmail.com','Calle Morelos #29, Col. Centro, Oaxaca de Juárez, Oax.','2025-09-06',0),
(46,'Enrique','Cruz Chávez','9512044906','enrique794@gmail.com','Calle Niños Héroes #151, Col. Centro, Oaxaca de Juárez, Oax.','2026-09-19',1),
(47,'Susana','Vázquez Rivas','9519944387','susana980@gmail.com','Calle Independencia #569, Col. Centro, Oaxaca de Juárez, Oax.','2025-06-27',1),
(48,'Eduardo','Santiago Vargas','9515993143','eduardo572@gmail.com','Av. Constitución #305, Col. Centro, Oaxaca de Juárez, Oax.','2025-11-02',1),
(49,'Ivonne','Vázquez Vargas','9514590102','ivonne815@gmail.com','Calle Morelos #857, Col. Centro, Oaxaca de Juárez, Oax.','2025-09-20',1),
(50,'Susana','Cabrera Ruiz','9518980915',NULL,'Calle 5 de Mayo #676, Col. Centro, Oaxaca de Juárez, Oax.','2026-02-21',1),
(51,'Iván','Vázquez Mendoza','9511155477','iván92@gmail.com','Av. Hidalgo #591, Col. Centro, Oaxaca de Juárez, Oax.','2025-10-24',1),
(52,'Adriana','Mendoza Guzmán','9518852085','adriana506@gmail.com','Av. Hidalgo #207, Col. Centro, Oaxaca de Juárez, Oax.','2025-05-18',1),
(53,'Adriana','Flores Escobar','9515949685','adriana191@gmail.com','Calle Zaragoza #700, Col. Centro, Oaxaca de Juárez, Oax.','2025-01-15',1),
(54,'Leticia','Santiago Luna','9514964499','leticia528@gmail.com','Av. Insurgentes #693, Col. Centro, Oaxaca de Juárez, Oax.','2025-09-25',1),
(55,'Arturo','Flores Gutiérrez','9514664338','arturo910@gmail.com','Av. Constitución #263, Col. Centro, Oaxaca de Juárez, Oax.','2026-04-28',1),
(56,'Yesenia','Pérez Silva','9518607331','yesenia80@gmail.com','Av. Juárez #535, Col. Centro, Oaxaca de Juárez, Oax.','2026-04-02',1),
(57,'Gonzalo','López Núñez','9518052503','gonzalo741@gmail.com','Calle Independencia #922, Col. Centro, Oaxaca de Juárez, Oax.','2025-07-04',1),
(58,'Daniel','Ramos Pérez','9515152562','daniel553@gmail.com','Calle 5 de Mayo #364, Col. Centro, Oaxaca de Juárez, Oax.','2026-01-24',1),
(59,'Manuel','Escobar Bautista','9511946973',NULL,'Av. Juárez #525, Col. Centro, Oaxaca de Juárez, Oax.','2025-08-11',1),
(60,'Martha','Escobar Campos','9517394713','martha689@gmail.com','Av. Constitución #547, Col. Centro, Oaxaca de Juárez, Oax.','2025-02-18',1),
(61,'Enrique','Chávez Cruz','9514780654','enrique551@gmail.com','Av. Hidalgo #888, Col. Centro, Oaxaca de Juárez, Oax.','2026-04-30',1),
(62,'Araceli','López Núñez','9515278117','araceli543@gmail.com','Calle Morelos #348, Col. Centro, Oaxaca de Juárez, Oax.','2025-05-18',1),
(63,'Fabiola','Moreno Pérez','9512968239','fabiola53@gmail.com','Calle Niños Héroes #782, Col. Centro, Oaxaca de Juárez, Oax.','2025-07-14',1),
(64,'Raúl','Cabrera Delgado','9515748945','raúl246@gmail.com','Av. Universidad #487, Col. Centro, Oaxaca de Juárez, Oax.','2026-07-20',1),
(65,'Cecilia','Ortiz Álvarez','9514291551','cecilia478@gmail.com','Calle Independencia #245, Col. Centro, Oaxaca de Juárez, Oax.','2026-03-04',1),
(66,'Raúl','Rojas Núñez','9512247057','raúl264@gmail.com','Calle Allende #694, Col. Centro, Oaxaca de Juárez, Oax.','2026-06-21',0),
(67,'Juan','Chávez Ramírez','9519484790','juan725@gmail.com','Calle 5 de Mayo #392, Col. Centro, Oaxaca de Juárez, Oax.','2025-06-04',0),
(68,'Tomás','Vargas Rivas','9519882154','tomás565@gmail.com','Calle Niños Héroes #291, Col. Centro, Oaxaca de Juárez, Oax.','2026-02-26',0),
(69,'Diana','Hernández Medina','9512352527','diana826@gmail.com','Av. Oaxaca #888, Col. Centro, Oaxaca de Juárez, Oax.','2025-08-20',1),
(70,'Verónica','Pérez Gutiérrez','9511781180','verónica58@gmail.com','Av. Símbolos Patrios #86, Col. Centro, Oaxaca de Juárez, Oax.','2026-08-04',1),
(71,'Yolanda','Herrera Campos','9514475536','yolanda871@gmail.com','Calle Zaragoza #157, Col. Centro, Oaxaca de Juárez, Oax.','2025-11-19',0),
(72,'Paola','González Ortiz','9516565503','paola855@gmail.com','Av. Universidad #783, Col. Centro, Oaxaca de Juárez, Oax.','2026-02-03',1),
(73,'Patricia','Medina Mejía','9516714198','patricia448@gmail.com','Calle Zaragoza #797, Col. Centro, Oaxaca de Juárez, Oax.','2025-08-26',1),
(74,'Yesenia','Gutiérrez Villanueva','9516312979','yesenia803@gmail.com','Calle Independencia #119, Col. Centro, Oaxaca de Juárez, Oax.','2025-06-07',1),
(75,'Arturo','Contreras Moreno','9519597330','arturo90@gmail.com','Av. Universidad #115, Col. Centro, Oaxaca de Juárez, Oax.','2025-07-11',1),
(76,'Rodrigo','Núñez Pérez','9515629871','rodrigo885@gmail.com','Av. Universidad #316, Col. Centro, Oaxaca de Juárez, Oax.','2025-03-06',1),
(77,'José','Moreno Álvarez','9514369728','josé508@gmail.com','Calle 5 de Mayo #331, Col. Centro, Oaxaca de Juárez, Oax.','2025-05-20',1),
(78,'Pablo','Vázquez Mendoza','9515887726','pablo991@gmail.com','Calle Allende #719, Col. Centro, Oaxaca de Juárez, Oax.','2025-08-03',1),
(79,'Enrique','Salazar Rojas','9518098909',NULL,'Av. Constitución #396, Col. Centro, Oaxaca de Juárez, Oax.','2025-09-16',1),
(80,'Óscar','Vargas Cortés','9517031903','óscar943@gmail.com','Calle Niños Héroes #926, Col. Centro, Oaxaca de Juárez, Oax.','2026-03-05',1),
(81,'Yesenia','Luna Pérez','9511743175',NULL,'Calle Zaragoza #670, Col. Centro, Oaxaca de Juárez, Oax.','2025-12-30',1),
(82,'Paola','Bautista Castillo','9519567725','paola11@gmail.com','Calle Niños Héroes #541, Col. Centro, Oaxaca de Juárez, Oax.','2025-04-11',1),
(83,'Vanessa','Morales Contreras','9513529080','vanessa696@gmail.com','Calle Independencia #35, Col. Centro, Oaxaca de Juárez, Oax.','2026-08-26',1),
(84,'Iván','Chávez Pérez','9513757404',NULL,'Av. Símbolos Patrios #768, Col. Centro, Oaxaca de Juárez, Oax.','2025-04-22',1),
(85,'Antonio','Núñez Flores','9514094587','antonio474@gmail.com','Av. Juárez #665, Col. Centro, Oaxaca de Juárez, Oax.','2025-08-26',1),
(86,'Paola','Hernández Mejía','9514603680','paola607@gmail.com','Calle Morelos #295, Col. Centro, Oaxaca de Juárez, Oax.','2026-03-16',1),
(87,'Laura','Campos Rodríguez','9512911847','laura375@gmail.com','Av. Hidalgo #696, Col. Centro, Oaxaca de Juárez, Oax.','2025-11-30',1),
(88,'Nancy','Luna Vázquez','9518016887','nancy602@gmail.com','Calle Zaragoza #151, Col. Centro, Oaxaca de Juárez, Oax.','2025-05-25',1),
(89,'Marisol','Gómez Rojas','9519249791','marisol661@gmail.com','Av. Universidad #737, Col. Centro, Oaxaca de Juárez, Oax.','2026-05-22',1),
(90,'Ivonne','Flores Pérez','9518404902','ivonne226@gmail.com','Av. Revolución #233, Col. Centro, Oaxaca de Juárez, Oax.','2025-02-12',1),
(91,'Claudia','Mejía Peña','9512459332','claudia34@gmail.com','Calle 5 de Mayo #896, Col. Centro, Oaxaca de Juárez, Oax.','2026-04-06',1),
(92,'Teresa','Reyes Mejía','9513784709','teresa728@gmail.com','Calle Zaragoza #152, Col. Centro, Oaxaca de Juárez, Oax.','2026-09-17',1),
(93,'Elena','López Campos','9515909595','elena552@gmail.com','Av. Hidalgo #596, Col. Centro, Oaxaca de Juárez, Oax.','2025-06-22',1),
(94,'Vicente','Salazar Rodríguez','9512266952',NULL,'Av. Oaxaca #823, Col. Centro, Oaxaca de Juárez, Oax.','2026-03-01',1),
(95,'Patricia','Gutiérrez Medina','9519299939','patricia899@gmail.com','Av. Hidalgo #340, Col. Centro, Oaxaca de Juárez, Oax.','2026-03-16',1),
(96,'Adriana','Núñez Campos','9516064462',NULL,'Av. Juárez #905, Col. Centro, Oaxaca de Juárez, Oax.','2025-05-16',1),
(97,'Yolanda','Pérez Peña','9516125594','yolanda681@gmail.com','Calle Niños Héroes #726, Col. Centro, Oaxaca de Juárez, Oax.','2025-06-19',1),
(98,'Marisol','Peña Pérez','9514794852','marisol489@gmail.com','Av. Revolución #706, Col. Centro, Oaxaca de Juárez, Oax.','2025-03-21',1),
(99,'Juan','Cabrera Castillo','9511142182','juan433@gmail.com','Calle Zaragoza #675, Col. Centro, Oaxaca de Juárez, Oax.','2025-03-12',1),
(100,'Rodrigo','Escobar Reyes','9512454383','rodrigo422@gmail.com','Av. Símbolos Patrios #112, Col. Centro, Oaxaca de Juárez, Oax.','2026-03-30',1),
(101,'Ricardo','Cortés Escobar','9515958372','ricardo323@gmail.com','Av. Oaxaca #65, Col. Centro, Oaxaca de Juárez, Oax.','2025-07-13',1),
(102,'Teresa','Rojas Ramos','9511202450',NULL,'Calle Independencia #966, Col. Centro, Oaxaca de Juárez, Oax.','2026-09-01',1),
(103,'Daniel','Campos Chávez','9513561552','daniel782@gmail.com','Av. Hidalgo #95, Col. Centro, Oaxaca de Juárez, Oax.','2026-02-16',1),
(104,'Vicente','Reyes Cruz','9518339797','vicente650@gmail.com','Calle Niños Héroes #986, Col. Centro, Oaxaca de Juárez, Oax.','2025-03-09',1),
(105,'Ana','Jiménez Pérez','9513946849','ana627@gmail.com','Calle Reforma #22, Col. Centro, Oaxaca de Juárez, Oax.','2026-09-16',1),
(106,'María','Flores Rojas','9513129542','maría802@gmail.com','Calle Niños Héroes #809, Col. Centro, Oaxaca de Juárez, Oax.','2025-09-23',1),
(107,'Alejandra','Villanueva Hernández','9512293020','alejandra537@gmail.com','Calle 5 de Mayo #476, Col. Centro, Oaxaca de Juárez, Oax.','2025-03-07',1),
(108,'Ana','Rojas Campos','9512934976','ana792@gmail.com','Calle Morelos #768, Col. Centro, Oaxaca de Juárez, Oax.','2025-04-16',1),
(109,'Verónica','García Jiménez','9511593302',NULL,'Av. Revolución #832, Col. Centro, Oaxaca de Juárez, Oax.','2025-06-03',1),
(110,'Lorena','Morales Vázquez','9518662929','lorena328@gmail.com','Av. Insurgentes #387, Col. Centro, Oaxaca de Juárez, Oax.','2025-12-02',1),
(111,'Gonzalo','Ortiz Mejía','9514278644','gonzalo606@gmail.com','Calle Niños Héroes #932, Col. Centro, Oaxaca de Juárez, Oax.','2026-05-21',1),
(112,'Teresa','Cruz García','9511160974','teresa555@gmail.com','Calle Independencia #11, Col. Centro, Oaxaca de Juárez, Oax.','2025-12-21',1),
(113,'Angélica','Mejía Escobar','9512004872','angélica244@gmail.com','Av. Juárez #738, Col. Centro, Oaxaca de Juárez, Oax.','2026-03-06',1),
(114,'Miguel','Morales Silva','9515562662','miguel470@gmail.com','Calle Morelos #86, Col. Centro, Oaxaca de Juárez, Oax.','2025-12-01',1),
(115,'Gabriela','Villanueva Villanueva','9517820148','gabriela169@gmail.com','Calle Reforma #558, Col. Centro, Oaxaca de Juárez, Oax.','2026-04-06',1),
(116,'Pedro','Contreras Ortiz','9517784575','pedro93@gmail.com','Calle Allende #473, Col. Centro, Oaxaca de Juárez, Oax.','2025-06-28',0),
(117,'Susana','Jiménez Rodríguez','9518439543','susana144@gmail.com','Av. Constitución #141, Col. Centro, Oaxaca de Juárez, Oax.','2025-07-22',1),
(118,'Salvador','Sánchez Martínez','9517626978','salvador947@gmail.com','Calle 5 de Mayo #40, Col. Centro, Oaxaca de Juárez, Oax.','2026-08-11',1),
(119,'Marco','Gómez Torres','9511581321','marco287@gmail.com','Calle Zaragoza #644, Col. Centro, Oaxaca de Juárez, Oax.','2026-04-02',1),
(120,'Diana','Rojas Chávez','9518033593','diana90@gmail.com','Calle Morelos #668, Col. Centro, Oaxaca de Juárez, Oax.','2025-10-14',1);
/*!40000 ALTER TABLE `cliente` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `compras`
--

DROP TABLE IF EXISTS `compras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `compras` (
  `Id_Compras` int(11) NOT NULL AUTO_INCREMENT,
  `Fecha_Compra` date DEFAULT curdate(),
  `Total` decimal(10,2) NOT NULL,
  `Estado` enum('Pendiente','Recibida','Cancelada') DEFAULT 'Pendiente',
  `Id_Proveedor` int(11) NOT NULL,
  `Id_Empleado` int(11) NOT NULL,
  PRIMARY KEY (`Id_Compras`),
  KEY `Id_Proveedor` (`Id_Proveedor`),
  KEY `Id_Empleado` (`Id_Empleado`),
  KEY `idx_compras_fecha` (`Fecha_Compra`),
  CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`Id_Proveedor`) REFERENCES `proveedor` (`Id_Proveedor`) ON UPDATE CASCADE,
  CONSTRAINT `compras_ibfk_2` FOREIGN KEY (`Id_Empleado`) REFERENCES `empleados` (`Id_Empleado`) ON UPDATE CASCADE,
  CONSTRAINT `CONSTRAINT_1` CHECK (`Total` >= 0)
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compras`
--

LOCK TABLES `compras` WRITE;
/*!40000 ALTER TABLE `compras` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `compras` VALUES
(1,'2026-09-02',70439.68,'Recibida',12,3),
(2,'2025-07-08',23993.97,'Recibida',13,10),
(3,'2026-08-21',61163.96,'Recibida',8,20),
(4,'2025-10-18',170009.47,'Recibida',26,4),
(5,'2025-03-09',136617.70,'Recibida',13,4),
(6,'2025-11-05',85437.00,'Recibida',13,19),
(7,'2025-08-29',288271.77,'Recibida',12,3),
(8,'2026-07-10',175826.44,'Recibida',25,9),
(9,'2025-06-02',121534.24,'Recibida',26,13),
(10,'2025-04-22',355550.64,'Cancelada',2,7),
(11,'2026-02-20',162456.25,'Recibida',8,9),
(12,'2026-07-24',129694.01,'Recibida',2,8),
(13,'2026-09-15',132656.56,'Recibida',24,17),
(14,'2025-10-28',71375.10,'Recibida',21,14),
(15,'2025-01-17',85606.47,'Recibida',12,10),
(16,'2026-09-15',1397.37,'Recibida',8,18),
(17,'2026-02-23',12531.75,'Recibida',21,5),
(18,'2026-05-24',225531.70,'Recibida',9,13),
(19,'2025-08-27',38438.14,'Recibida',10,10),
(20,'2026-05-18',623236.74,'Recibida',20,16),
(21,'2025-08-31',197323.14,'Recibida',14,5),
(22,'2025-02-14',12732.16,'Pendiente',11,20),
(23,'2025-12-01',54914.02,'Recibida',4,17),
(24,'2025-10-13',303052.80,'Recibida',12,16),
(25,'2025-06-01',228188.62,'Recibida',8,11),
(26,'2025-12-25',20238.70,'Cancelada',7,6),
(27,'2025-09-09',90002.88,'Recibida',22,16),
(28,'2025-03-31',72636.20,'Recibida',5,10),
(29,'2025-01-23',56567.60,'Recibida',5,8),
(30,'2025-09-01',361760.45,'Recibida',11,16),
(31,'2026-02-10',130362.75,'Recibida',19,12),
(32,'2026-01-11',65360.87,'Cancelada',3,8),
(33,'2025-03-24',121175.01,'Recibida',28,6),
(34,'2026-02-03',161538.24,'Recibida',12,10),
(35,'2025-10-06',351851.85,'Recibida',13,1),
(36,'2025-07-12',75979.12,'Recibida',9,17),
(37,'2025-11-02',40925.60,'Cancelada',16,20),
(38,'2026-02-04',136647.24,'Recibida',9,2),
(39,'2025-11-13',12898.80,'Recibida',10,2),
(40,'2025-07-14',223686.00,'Recibida',20,2),
(41,'2026-08-19',375672.17,'Recibida',5,14),
(42,'2026-06-10',76413.14,'Pendiente',30,6),
(43,'2025-12-05',187543.62,'Recibida',26,11),
(44,'2025-06-06',479726.00,'Pendiente',11,5),
(45,'2026-03-01',56107.80,'Recibida',7,18),
(46,'2026-08-14',18744.32,'Recibida',2,14),
(47,'2026-07-27',205479.23,'Recibida',9,4),
(48,'2025-12-31',175209.88,'Recibida',20,14),
(49,'2025-05-05',310155.60,'Recibida',19,5),
(50,'2026-08-06',16934.25,'Cancelada',17,14),
(51,'2026-06-19',210298.13,'Pendiente',16,6),
(52,'2026-02-11',381672.98,'Recibida',7,10),
(53,'2025-12-10',131002.99,'Recibida',13,6),
(54,'2025-12-09',129262.32,'Recibida',11,4),
(55,'2025-05-18',286958.32,'Cancelada',25,5),
(56,'2025-07-27',203043.39,'Pendiente',25,16),
(57,'2026-03-02',115003.24,'Recibida',12,1),
(58,'2026-04-27',193918.40,'Recibida',16,19),
(59,'2026-03-05',163784.60,'Recibida',10,3),
(60,'2026-02-07',285298.49,'Cancelada',4,14),
(61,'2026-01-10',141926.50,'Recibida',23,10),
(62,'2025-05-15',161898.28,'Recibida',16,15),
(63,'2026-09-22',60083.01,'Recibida',17,14),
(64,'2026-06-03',21997.82,'Cancelada',10,5),
(65,'2025-05-05',289707.17,'Recibida',22,19),
(66,'2025-04-19',35112.87,'Recibida',29,20),
(67,'2026-06-12',101737.25,'Cancelada',21,7),
(68,'2025-08-21',116348.07,'Recibida',21,4),
(69,'2025-03-12',166054.92,'Recibida',18,13),
(70,'2025-08-05',111557.70,'Recibida',1,5),
(71,'2025-02-11',384534.32,'Recibida',10,5),
(72,'2025-07-28',295677.00,'Pendiente',11,14),
(73,'2026-01-19',138058.56,'Recibida',20,13),
(74,'2026-02-07',46659.90,'Cancelada',30,9),
(75,'2026-09-19',243550.56,'Recibida',27,14),
(76,'2025-05-17',135144.60,'Recibida',28,17),
(77,'2025-01-04',61764.09,'Recibida',7,4),
(78,'2025-12-27',46119.25,'Recibida',4,7),
(79,'2026-03-29',232223.60,'Recibida',2,12),
(80,'2025-11-16',268288.00,'Recibida',11,19),
(81,'2025-11-26',304440.47,'Recibida',1,19),
(82,'2026-03-13',93937.94,'Recibida',6,11),
(83,'2026-09-07',22445.64,'Recibida',24,7),
(84,'2026-05-29',32481.60,'Cancelada',16,10),
(85,'2026-08-23',234567.90,'Recibida',5,7),
(86,'2025-05-14',26495.72,'Pendiente',29,20),
(87,'2025-08-26',188922.24,'Recibida',27,9),
(88,'2025-06-23',23114.05,'Cancelada',24,10),
(89,'2025-12-02',84029.00,'Cancelada',6,6),
(90,'2026-07-12',297218.85,'Recibida',5,10),
(91,'2025-09-13',407043.56,'Recibida',21,13),
(92,'2025-08-29',136095.19,'Cancelada',15,4),
(93,'2025-08-07',31826.52,'Recibida',12,15),
(94,'2025-07-05',108237.30,'Recibida',7,12),
(95,'2026-01-24',73189.81,'Recibida',19,8),
(96,'2025-04-17',302934.60,'Recibida',15,15),
(97,'2025-10-22',1174.36,'Recibida',5,18),
(98,'2025-05-22',256759.50,'Pendiente',27,7),
(99,'2025-09-28',36458.88,'Recibida',16,5),
(100,'2025-04-22',122726.87,'Recibida',20,7),
(101,'2026-07-06',72187.16,'Recibida',20,16),
(102,'2025-04-05',214685.68,'Recibida',30,7),
(103,'2025-03-17',153907.98,'Recibida',15,1),
(104,'2025-01-26',35112.87,'Recibida',23,7),
(105,'2026-01-30',81278.26,'Recibida',16,16),
(106,'2025-09-29',323375.23,'Recibida',14,1),
(107,'2026-02-13',216476.48,'Pendiente',16,14),
(108,'2026-09-18',199275.55,'Recibida',21,15),
(109,'2025-02-20',149559.01,'Recibida',30,3),
(110,'2026-04-27',47659.50,'Recibida',8,14),
(111,'2026-02-17',87127.34,'Pendiente',25,8),
(112,'2025-06-20',142738.75,'Recibida',9,3),
(113,'2025-12-26',46857.14,'Pendiente',7,17),
(114,'2026-09-08',298036.19,'Recibida',16,14),
(115,'2025-06-17',8055.60,'Pendiente',4,12),
(116,'2025-05-12',95121.89,'Recibida',4,12),
(117,'2025-08-15',62289.20,'Recibida',30,19),
(118,'2026-08-23',112575.49,'Recibida',30,8),
(119,'2026-07-25',126506.94,'Cancelada',20,15),
(120,'2025-06-14',369693.40,'Recibida',29,14);
/*!40000 ALTER TABLE `compras` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `detalle_compras`
--

DROP TABLE IF EXISTS `detalle_compras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_compras` (
  `Id_Detalle` int(11) NOT NULL AUTO_INCREMENT,
  `Cantidad` int(11) NOT NULL,
  `Precio_Unitario` decimal(10,2) NOT NULL,
  `Total` decimal(10,2) NOT NULL,
  `Id_Compras` int(11) NOT NULL,
  `Id_Producto` int(11) NOT NULL,
  PRIMARY KEY (`Id_Detalle`),
  KEY `Id_Compras` (`Id_Compras`),
  KEY `Id_Producto` (`Id_Producto`),
  CONSTRAINT `detalle_compras_ibfk_1` FOREIGN KEY (`Id_Compras`) REFERENCES `compras` (`Id_Compras`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_compras_ibfk_2` FOREIGN KEY (`Id_Producto`) REFERENCES `productos` (`Id_Producto`) ON UPDATE CASCADE,
  CONSTRAINT `CONSTRAINT_1` CHECK (`Cantidad` > 0)
) ENGINE=InnoDB AUTO_INCREMENT=181 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_compras`
--

LOCK TABLES `detalle_compras` WRITE;
/*!40000 ALTER TABLE `detalle_compras` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `detalle_compras` VALUES
(1,113,623.36,70439.68,1,118),
(2,39,615.23,23993.97,2,53),
(3,26,2352.46,61163.96,3,81),
(4,53,718.09,38058.77,4,47),
(5,148,71.67,10607.16,4,94),
(6,58,2092.13,121343.54,4,136),
(7,10,1177.63,11776.30,5,87),
(8,57,2190.20,124841.40,5,45),
(9,18,1195.50,21519.00,6,6),
(10,60,1065.30,63918.00,6,133),
(11,44,861.59,37909.96,7,113),
(12,107,2339.83,250361.81,7,46),
(13,106,1658.74,175826.44,8,121),
(14,107,1082.72,115851.04,9,98),
(15,20,284.16,5683.20,9,34),
(16,140,2112.18,295705.20,10,97),
(17,90,145.72,13114.80,10,82),
(18,24,1947.11,46730.64,10,71),
(19,85,1911.25,162456.25,11,79),
(20,79,1620.71,128036.09,12,85),
(21,24,69.08,1657.92,12,101),
(22,94,1411.24,132656.56,13,16),
(23,47,1359.20,63882.40,14,4),
(24,62,120.85,7492.70,14,20),
(25,111,167.13,18551.43,15,1),
(26,32,2095.47,67055.04,15,5),
(27,13,107.49,1397.37,16,95),
(28,49,255.75,12531.75,17,88),
(29,56,915.70,51279.20,18,74),
(30,94,1853.75,174252.50,18,31),
(31,31,1239.94,38438.14,19,92),
(32,145,2065.22,299456.90,20,62),
(33,120,1337.48,160497.60,20,107),
(34,56,2436.48,136442.88,20,124),
(35,32,838.73,26839.36,20,109),
(36,118,1672.23,197323.14,21,17),
(37,58,219.52,12732.16,22,18),
(38,62,885.71,54914.02,23,148),
(39,128,2367.60,303052.80,24,111),
(40,97,2352.46,228188.62,25,81),
(41,10,2023.87,20238.70,26,90),
(42,96,937.53,90002.88,27,40),
(43,70,1037.66,72636.20,28,116),
(44,40,1372.35,54894.00,29,54),
(45,10,167.36,1673.60,29,93),
(46,139,2408.20,334739.80,30,149),
(47,83,325.55,27020.65,30,100),
(48,93,1401.75,130362.75,31,27),
(49,37,1766.51,65360.87,32,44),
(50,99,1223.99,121175.01,33,76),
(51,96,1682.69,161538.24,34,33),
(52,147,2393.55,351851.85,35,99),
(53,140,69.08,9671.20,36,101),
(54,116,571.62,66307.92,36,132),
(55,20,2046.28,40925.60,37,65),
(56,24,668.01,16032.24,38,39),
(57,55,2193.00,120615.00,38,83),
(58,120,107.49,12898.80,39,95),
(59,102,2193.00,223686.00,40,83),
(60,105,257.50,27037.50,41,104),
(61,149,2339.83,348634.67,41,46),
(62,37,2065.22,76413.14,42,62),
(63,114,606.33,69121.62,43,48),
(64,54,2193.00,118422.00,43,83),
(65,130,2422.08,314870.40,44,91),
(66,140,1177.54,164855.60,44,138),
(67,73,768.60,56107.80,45,37),
(68,112,167.36,18744.32,46,93),
(69,33,565.81,18671.73,47,145),
(70,125,1494.46,186807.50,47,123),
(71,131,1337.48,175209.88,48,107),
(72,131,2367.60,310155.60,49,111),
(73,67,252.75,16934.25,50,15),
(74,102,1604.14,163622.28,51,57),
(75,65,718.09,46675.85,51,47),
(76,139,1076.62,149650.18,52,29),
(77,136,1706.05,232022.80,52,144),
(78,61,2147.59,131002.99,53,72),
(79,72,1795.31,129262.32,54,131),
(80,134,2141.48,286958.32,55,128),
(81,91,1795.31,163373.21,56,131),
(82,17,2333.54,39670.18,56,120),
(83,42,890.12,37385.04,57,51),
(84,55,1411.24,77618.20,57,16),
(85,130,1491.68,193918.40,58,42),
(86,59,2141.48,126347.32,59,128),
(87,16,2339.83,37437.28,59,46),
(88,41,167.36,6861.76,60,93),
(89,143,1947.11,278436.73,60,71),
(90,115,789.30,90769.50,61,139),
(91,25,2046.28,51157.00,61,65),
(92,64,2083.51,133344.64,62,127),
(93,111,257.24,28553.64,62,141),
(94,39,1540.59,60083.01,63,7),
(95,38,578.89,21997.82,64,70),
(96,121,1254.26,151765.46,65,10),
(97,141,978.31,137941.71,65,11),
(98,117,300.11,35112.87,66,130),
(99,69,1235.81,85270.89,67,22),
(100,113,145.72,16466.36,67,82),
(101,23,2095.47,48195.81,68,5),
(102,33,2065.22,68152.26,68,62),
(103,24,1649.21,39581.04,69,50),
(104,102,1239.94,126473.88,69,92),
(105,10,1682.69,16826.90,70,33),
(106,85,1114.48,94730.80,70,23),
(107,138,2190.20,302247.60,71,45),
(108,76,1082.72,82286.72,71,98),
(109,135,2190.20,295677.00,72,45),
(110,57,2422.08,138058.56,73,91),
(111,34,1372.35,46659.90,74,54),
(112,116,1301.39,150961.24,75,36),
(113,86,1076.62,92589.32,75,29),
(114,45,218.27,9822.15,76,108),
(115,23,255.75,5882.25,76,88),
(116,60,1990.67,119440.20,76,143),
(117,93,664.13,61764.09,77,103),
(118,25,1844.77,46119.25,78,69),
(119,140,1658.74,232223.60,79,121),
(120,92,1405.31,129288.52,80,55),
(121,122,1139.34,138999.48,80,38),
(122,37,2393.55,88561.35,81,99),
(123,137,1575.76,215879.12,81,86),
(124,130,623.36,81036.80,82,118),
(125,42,307.17,12901.14,82,66),
(126,18,1246.98,22445.64,83,32),
(127,30,1082.72,32481.60,84,98),
(128,98,2393.55,234567.90,85,99),
(129,103,257.24,26495.72,86,141),
(130,78,2422.08,188922.24,87,91),
(131,71,325.55,23114.05,88,100),
(132,110,763.90,84029.00,89,12),
(133,68,951.18,64680.24,90,3),
(134,141,1649.21,232538.61,90,50),
(135,146,1411.24,206041.04,91,16),
(136,92,2184.81,201002.52,91,89),
(137,34,1923.64,65403.76,92,24),
(138,35,1649.21,57722.35,92,50),
(139,89,145.72,12969.08,92,82),
(140,132,241.11,31826.52,93,60),
(141,95,1139.34,108237.30,94,38),
(142,47,1557.23,73189.81,95,84),
(143,144,1372.35,197618.40,96,54),
(144,44,2393.55,105316.20,96,99),
(145,17,69.08,1174.36,97,101),
(146,95,2112.18,200657.10,98,97),
(147,90,623.36,56102.40,98,118),
(148,32,1139.34,36458.88,99,38),
(149,14,307.17,4300.38,100,66),
(150,91,1301.39,118426.49,100,36),
(151,52,1195.50,62166.00,101,6),
(152,17,589.48,10021.16,101,25),
(153,92,2333.54,214685.68,102,120),
(154,76,145.72,11074.72,103,82),
(155,146,978.31,142833.26,103,11),
(156,117,300.11,35112.87,104,130),
(157,49,1658.74,81278.26,105,121),
(158,132,2333.54,308027.28,106,120),
(159,127,120.85,15347.95,106,20),
(160,116,1795.31,208255.96,107,131),
(161,119,69.08,8220.52,107,101),
(162,97,1706.05,165486.85,108,144),
(163,110,307.17,33788.70,108,66),
(164,127,1177.63,149559.01,109,87),
(165,34,1401.75,47659.50,110,27),
(166,53,937.53,49689.09,111,40),
(167,115,325.55,37438.25,111,100),
(168,77,1853.75,142738.75,112,31),
(169,22,2129.87,46857.14,113,112),
(170,143,1083.01,154870.43,114,52),
(171,57,2185.97,124600.29,114,21),
(172,77,241.11,18565.47,114,60),
(173,28,287.70,8055.60,115,41),
(174,47,2023.87,95121.89,116,90),
(175,40,1557.23,62289.20,117,84),
(176,11,368.39,4052.29,118,129),
(177,96,1130.45,108523.20,118,2),
(178,133,951.18,126506.94,119,3),
(179,144,2190.20,315388.80,120,45),
(180,140,387.89,54304.60,120,43);
/*!40000 ALTER TABLE `detalle_compras` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `detalle_ventas`
--

DROP TABLE IF EXISTS `detalle_ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_ventas` (
  `Id_Detalle` int(11) NOT NULL AUTO_INCREMENT,
  `Cantidad` int(11) NOT NULL,
  `Precio_Unitario` decimal(10,2) NOT NULL,
  `Descuento` decimal(5,2) DEFAULT 0.00,
  `Subtotal` decimal(10,2) NOT NULL,
  `Id_Ventas` int(11) NOT NULL,
  `Id_Producto` int(11) NOT NULL,
  PRIMARY KEY (`Id_Detalle`),
  KEY `Id_Ventas` (`Id_Ventas`),
  KEY `Id_Producto` (`Id_Producto`),
  CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`Id_Ventas`) REFERENCES `ventas` (`Id_Ventas`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_ventas_ibfk_2` FOREIGN KEY (`Id_Producto`) REFERENCES `productos` (`Id_Producto`) ON UPDATE CASCADE,
  CONSTRAINT `CONSTRAINT_1` CHECK (`Cantidad` > 0)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_ventas`
--

LOCK TABLES `detalle_ventas` WRITE;
/*!40000 ALTER TABLE `detalle_ventas` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `detalle_ventas` VALUES
(1,1,477.24,5.00,453.38,1,66),
(2,4,1209.73,5.00,4596.97,2,109),
(3,7,1931.37,5.00,12843.61,2,76),
(4,5,924.72,0.00,4623.60,3,132),
(5,5,3838.78,0.00,19193.90,3,8),
(6,3,3519.14,0.00,10557.42,3,140),
(7,6,1356.66,10.00,7325.96,4,148),
(8,1,1492.18,0.00,1492.18,5,133),
(9,1,1733.61,10.00,1560.25,5,58),
(10,5,2663.16,5.00,12650.01,5,127),
(11,7,490.38,10.00,3089.39,6,34),
(12,7,3022.97,5.00,20102.75,7,72),
(13,6,2292.86,0.00,13757.16,8,107),
(14,8,330.74,10.00,2381.33,8,60),
(15,1,764.55,0.00,764.55,9,9),
(16,4,3240.39,0.00,12961.56,9,120),
(17,7,3676.81,10.00,23163.90,10,81),
(18,1,1672.42,15.00,1421.56,10,116),
(19,7,3045.71,15.00,18121.97,10,135),
(20,2,267.86,10.00,482.15,11,93),
(21,3,2518.45,5.00,7177.58,11,69),
(22,2,310.73,15.00,528.24,12,18),
(23,3,3339.10,15.00,8514.70,12,105),
(24,6,481.89,5.00,2746.77,13,142),
(25,5,1849.03,5.00,8782.89,13,38),
(26,7,2679.53,10.00,16881.04,14,13),
(27,6,1801.89,0.00,10811.34,15,4),
(28,8,2292.86,10.00,16508.59,15,107),
(29,2,3355.13,0.00,6710.26,16,136),
(30,7,3240.39,0.00,22682.73,16,120),
(31,8,1387.99,15.00,9438.33,17,134),
(32,2,3063.26,0.00,6126.52,18,91),
(33,8,1639.61,10.00,11805.19,18,10),
(34,3,984.21,10.00,2657.37,19,147),
(35,5,3312.79,0.00,16563.95,19,49),
(36,5,2988.39,5.00,14194.85,20,45),
(37,2,1821.16,0.00,3642.32,20,87),
(38,5,3022.97,0.00,15114.85,21,72),
(39,7,1883.39,10.00,11865.36,22,114),
(40,5,2512.59,0.00,12562.95,23,24),
(41,3,350.17,0.00,1050.51,23,77),
(42,7,211.19,0.00,1478.33,24,1),
(43,8,1957.32,15.00,13309.78,25,92),
(44,2,2183.84,15.00,3712.53,26,137),
(45,4,1430.24,0.00,5720.96,27,96),
(46,3,2710.26,5.00,7724.24,28,33),
(47,5,211.19,5.00,1003.15,28,1),
(48,6,3151.66,0.00,18909.96,29,56),
(49,4,2822.79,0.00,11291.16,30,131),
(50,1,1492.18,0.00,1492.18,31,133),
(51,5,336.06,0.00,1680.30,32,88),
(52,4,3498.03,10.00,12592.91,33,78),
(53,3,1012.75,10.00,2734.43,33,139),
(54,3,2988.39,5.00,8516.91,33,45),
(55,5,3283.18,10.00,14774.31,34,97),
(56,8,1883.39,15.00,12807.05,35,114),
(57,1,3519.14,5.00,3343.18,35,140),
(58,4,2183.84,0.00,8735.36,35,137),
(59,4,1821.16,15.00,6191.94,36,87),
(60,7,3312.79,0.00,23189.53,37,49),
(61,8,3300.88,0.00,26407.04,37,65),
(62,4,2688.56,0.00,10754.24,38,90),
(63,8,2710.26,5.00,20597.98,39,33),
(64,2,3403.76,0.00,6807.52,39,30),
(65,3,2582.80,0.00,7748.40,40,62),
(66,7,310.73,0.00,2175.11,40,18),
(67,2,2988.39,0.00,5976.78,41,45),
(68,8,1481.33,0.00,11850.64,41,98),
(69,1,2822.79,0.00,2822.79,42,131),
(70,1,965.09,5.00,916.84,42,70),
(71,5,1647.53,0.00,8237.65,43,138),
(72,6,2679.53,15.00,13665.60,44,13),
(73,1,1672.42,0.00,1672.42,44,116),
(74,4,3283.18,0.00,13132.72,45,97),
(75,8,63.53,10.00,457.42,46,26),
(76,1,2303.84,0.00,2303.84,46,57),
(77,3,1899.02,15.00,4842.50,47,22),
(78,8,3355.13,15.00,22814.88,48,136),
(79,1,481.89,0.00,481.89,49,142),
(80,3,1647.53,5.00,4695.46,50,138),
(81,5,3174.79,15.00,13492.86,51,63),
(82,7,764.55,10.00,4816.66,52,9),
(83,4,2650.67,0.00,10602.68,53,119),
(84,1,468.07,0.00,468.07,53,130),
(85,5,2147.00,0.00,10735.00,53,16),
(86,3,211.19,0.00,633.57,54,1),
(87,5,1356.66,5.00,6444.14,54,148),
(88,3,1707.44,0.00,5122.32,55,11),
(89,1,477.24,5.00,453.38,55,66),
(90,6,1672.42,0.00,10034.52,56,116),
(91,2,2292.86,0.00,4585.72,57,107),
(92,1,3227.36,10.00,2904.62,58,89),
(93,1,3566.49,0.00,3566.49,59,149),
(94,7,1801.89,0.00,12613.23,60,4),
(95,7,797.86,0.00,5585.02,61,48),
(96,2,3227.36,10.00,5809.25,62,89),
(97,2,3797.67,5.00,7215.57,62,106),
(98,7,3022.97,15.00,17986.67,63,72),
(99,4,3451.59,0.00,13806.36,63,5),
(100,1,1329.28,5.00,1262.82,64,113),
(101,8,1239.15,0.00,9913.20,65,3),
(102,8,1209.73,0.00,9677.84,66,109),
(103,8,3179.02,0.00,25432.16,67,125),
(104,5,2183.84,0.00,10919.20,68,137),
(105,7,3151.66,15.00,18752.38,69,56),
(106,2,3300.88,15.00,5611.50,70,65),
(107,6,2918.45,0.00,17510.70,71,128),
(108,5,1877.16,0.00,9385.80,72,6),
(109,4,2582.80,0.00,10331.20,73,62),
(110,4,1872.49,0.00,7489.96,74,32),
(111,2,1849.03,10.00,3328.25,75,38),
(112,6,2056.34,0.00,12338.04,76,73),
(113,5,2292.86,0.00,11464.30,77,107),
(114,1,1050.46,15.00,892.89,78,28),
(115,7,110.77,10.00,697.85,79,94),
(116,4,310.73,0.00,1242.92,79,18),
(117,1,2918.45,5.00,2772.53,80,128),
(118,8,2479.39,10.00,17851.61,80,71),
(119,2,3179.02,0.00,6358.04,81,125),
(120,1,1381.70,0.00,1381.70,82,40),
(121,4,1844.47,0.00,7377.88,83,146),
(122,5,2219.71,5.00,10543.62,84,44),
(123,4,2322.28,0.00,9289.12,84,122),
(124,1,3451.59,5.00,3279.01,85,5),
(125,1,267.86,10.00,241.07,86,93),
(126,7,101.54,10.00,639.70,87,101),
(127,4,2518.45,0.00,10073.80,88,69),
(128,7,3283.18,10.00,20684.03,89,97),
(129,2,1699.94,15.00,2889.90,90,67),
(130,6,388.07,0.00,2328.42,90,15);
/*!40000 ALTER TABLE `detalle_ventas` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `empleados`
--

DROP TABLE IF EXISTS `empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleados` (
  `Id_Empleado` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(50) NOT NULL,
  `Apellidos` varchar(100) NOT NULL,
  `Correo` varchar(100) DEFAULT NULL,
  `Telefono` varchar(15) DEFAULT NULL,
  `Puesto` varchar(50) DEFAULT NULL,
  `Fecha_Alta` date DEFAULT curdate(),
  `Estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`Id_Empleado`),
  UNIQUE KEY `Correo` (`Correo`),
  KEY `idx_empleados_nombre` (`Nombre`,`Apellidos`),
  CONSTRAINT `chk_emp_estado` CHECK (`Estado` in (0,1)),
  CONSTRAINT `chk_emp_nombre` CHECK (`Nombre` regexp '^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]+$' and `Apellidos` regexp '^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]+$')
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleados`
--

LOCK TABLES `empleados` WRITE;
/*!40000 ALTER TABLE `empleados` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `empleados` VALUES
(1,'Verónica','Hernández Pérez','TU_CORREO@gmail.com','9511445199','Gerente','2025-08-12',1),
(2,'Perla','Chávez Morales','perla.chavez@ferredog.com','9511109031','Admin Sistema','2026-04-05',1),
(3,'Silvia','Cabrera Aguilar','silvia.cabrera@ferredog.com','9516647119','Vendedor','2025-10-12',1),
(4,'Daniel','Rodríguez Torres','daniel.rodriguez@ferredog.com','9518707870','Vendedor','2025-09-28',1),
(5,'Angélica','Pérez Núñez','angelica.perez@ferredog.com','9512166941','Almacenista','2026-01-06',1),
(6,'Sergio','Villanueva Mendoza','sergio.villanueva@ferredog.com','9517377459','Cajero','2025-08-27',1),
(7,'Emilio','Flores Moreno','emilio.flores@ferredog.com','9512197935','Vendedor','2025-08-03',1),
(8,'Silvia','Herrera Escobar','silvia.herrera@ferredog.com','9515528972','Almacenista','2025-06-17',1),
(9,'Susana','Morales Salazar','susana.morales@ferredog.com','9511538552','Vendedor','2025-02-27',1),
(10,'Paola','Ruiz González','paola.ruiz@ferredog.com','9519375710','Almacenista','2025-11-19',1),
(11,'Andrés','Cruz Álvarez','andres.cruz@ferredog.com','9515408072','Cajero','2025-09-10',1),
(12,'Marisol','Cortés Gutiérrez','marisol.cortes@ferredog.com','9513320821','Vendedor','2025-08-13',1),
(13,'Patricia','Bautista López','patricia.bautista@ferredog.com','9518082668','Cajero','2025-06-06',1),
(14,'Angélica','Ramos Delgado','angelica.ramos@ferredog.com','9511192619','Supervisor','2025-09-15',1),
(15,'Ale','Salazar Herrera','alejandra.salazar@ferredog.com','9518294150','Almacenista','2025-12-15',1),
(16,'Juan','Escobar Escobar','juan.escobar@ferredog.com','9519517485','Almacenista','2026-05-28',1),
(17,'Karla','Luna Medina','karla.luna@ferredog.com','9513710343','Almacenista','2025-06-06',1),
(18,'Nancy','García Delgado','nancy.garcia@ferredog.com','9517089806','Vendedor','2026-05-16',0),
(19,'Karla','Jiménez López','karla.jimenez@ferredog.com','9519153566','Almacenista','2025-03-22',1),
(20,'Perla','Villanueva Ramírez','perla.villanueva@ferredog.com','9513770370','Cajero','2026-05-02',1),
(21,'Nery Omar','Gonzales Cortes','hernandezcortesneryomar@gmail.com',NULL,'Almacenista','2026-10-12',1),
(67,'Gabriel Policarpo','Policarpo Sanchez','carloszan584@gmail.com',NULL,'Almacenista','2026-10-13',1),
(68,'erik omars','hernandez','h@gmail.com',NULL,'Administrador','2026-10-13',1),
(69,'Alex','Jimenez','L24920206v@oaxaca.tecnm.com',NULL,'Administrador','2026-10-13',1);
/*!40000 ALTER TABLE `empleados` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `factura_cliente`
--

DROP TABLE IF EXISTS `factura_cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `factura_cliente` (
  `Id_Factura_Cliente` int(11) NOT NULL AUTO_INCREMENT,
  `Fecha` date DEFAULT curdate(),
  `Subtotal` decimal(10,2) NOT NULL,
  `Total` decimal(10,2) NOT NULL,
  `Id_Venta` int(11) NOT NULL,
  PRIMARY KEY (`Id_Factura_Cliente`),
  UNIQUE KEY `Id_Venta` (`Id_Venta`),
  CONSTRAINT `factura_cliente_ibfk_1` FOREIGN KEY (`Id_Venta`) REFERENCES `ventas` (`Id_Ventas`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factura_cliente`
--

LOCK TABLES `factura_cliente` WRITE;
/*!40000 ALTER TABLE `factura_cliente` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `factura_cliente` VALUES
(1,'2025-11-27',346.88,22814.88,48),
(2,'2026-08-15',134.87,9677.84,66),
(3,'2025-10-24',226.00,16138.49,8),
(4,'2025-02-01',426.12,3089.39,6),
(5,'2026-05-03',33.25,241.07,86),
(6,'2026-05-12',111.41,13132.72,45),
(7,'2026-05-16',136.23,8237.65,43),
(8,'2025-10-06',139.76,12613.23,60),
(9,'2026-08-15',265.85,15702.44,5),
(10,'2025-07-13',273.34,17931.71,18),
(11,'2025-11-06',647.65,4695.46,50),
(12,'2025-02-21',190.58,1381.70,82),
(13,'2025-02-09',62.54,453.38,1),
(14,'2026-05-12',156.51,7659.73,11),
(15,'2026-08-27',157.40,11291.16,30),
(16,'2025-01-20',719.77,5218.32,90),
(17,'2025-01-26',789.10,5720.96,27),
(18,'2026-07-10',454.21,29392.99,16),
(19,'2025-09-10',167.34,9913.20,65),
(20,'2025-08-28',196.53,13024.82,62),
(21,'2025-12-30',103.10,7489.96,74),
(22,'2026-05-26',183.26,13726.11,9),
(23,'2025-06-09',123.16,892.89,78),
(24,'2025-06-30',459.07,3328.25,75),
(25,'2025-10-04',976.24,7077.71,54),
(26,'2025-10-03',6840.91,49596.57,37),
(27,'2026-05-28',2735.55,19832.74,84),
(28,'2025-12-23',515.81,3739.63,42),
(29,'2025-11-01',2037.84,14774.31,34),
(30,'2026-06-18',66.47,481.89,49);
/*!40000 ALTER TABLE `factura_cliente` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `factura_proveedor`
--

DROP TABLE IF EXISTS `factura_proveedor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `factura_proveedor` (
  `Id_Factura_Proveedor` int(11) NOT NULL AUTO_INCREMENT,
  `Fecha` date DEFAULT curdate(),
  `Subtotal` decimal(10,2) NOT NULL,
  `Total` decimal(10,2) NOT NULL,
  `Id_Compras` int(11) NOT NULL,
  PRIMARY KEY (`Id_Factura_Proveedor`),
  UNIQUE KEY `Id_Compras` (`Id_Compras`),
  CONSTRAINT `factura_proveedor_ibfk_1` FOREIGN KEY (`Id_Compras`) REFERENCES `compras` (`Id_Compras`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factura_proveedor`
--

LOCK TABLES `factura_proveedor` WRITE;
/*!40000 ALTER TABLE `factura_proveedor` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `factura_proveedor` VALUES
(1,'2026-09-04',60723.86,70439.68,1),
(2,'2025-07-09',20684.46,23993.97,2),
(3,'2026-08-22',52727.55,61163.96,3),
(4,'2025-10-20',146559.89,170009.47,4),
(5,'2025-03-09',117773.88,136617.70,5),
(6,'2025-11-10',73652.59,85437.00,6),
(7,'2025-09-01',248510.15,288271.77,7),
(8,'2026-07-13',151574.52,175826.44,8),
(9,'2025-06-06',104770.90,121534.24,9),
(10,'2026-02-23',140048.49,162456.25,11),
(11,'2026-07-26',111805.18,129694.01,12),
(12,'2026-09-17',114359.10,132656.56,13),
(13,'2025-10-29',61530.26,71375.10,14),
(14,'2025-01-21',73798.68,85606.47,15),
(15,'2026-09-18',1204.63,1397.37,16),
(16,'2026-02-24',10803.23,12531.75,17),
(17,'2026-05-29',194423.88,225531.70,18),
(18,'2025-08-31',33136.33,38438.14,19),
(19,'2026-05-18',537273.05,623236.74,20),
(20,'2025-08-31',170106.16,197323.14,21),
(21,'2025-12-05',47339.67,54914.02,23),
(22,'2025-10-17',261252.41,303052.80,24),
(23,'2025-06-04',196714.33,228188.62,25),
(24,'2025-09-12',77588.69,90002.88,27),
(25,'2025-04-02',62617.41,72636.20,28),
(26,'2025-01-24',48765.17,56567.60,29),
(27,'2025-09-02',311862.46,361760.45,30),
(28,'2026-02-10',112381.68,130362.75,31),
(29,'2025-03-24',104461.22,121175.01,33),
(30,'2026-02-04',139257.10,161538.24,34),
(31,'2025-10-06',303320.56,351851.85,35),
(32,'2025-07-13',65499.24,75979.12,36),
(33,'2026-02-08',117799.34,136647.24,38),
(34,'2025-11-18',11119.66,12898.80,39),
(35,'2025-07-19',192832.76,223686.00,40),
(36,'2026-08-23',323855.32,375672.17,41),
(37,'2025-12-10',161675.53,187543.62,43),
(38,'2026-03-02',48368.79,56107.80,45),
(39,'2026-08-16',16158.90,18744.32,46),
(40,'2026-07-27',177137.27,205479.23,47),
(41,'2026-01-01',151043.00,175209.88,48),
(42,'2025-05-07',267375.52,310155.60,49),
(43,'2026-02-13',329028.43,381672.98,52),
(44,'2025-12-14',112933.61,131002.99,53),
(45,'2025-12-10',111433.03,129262.32,54),
(46,'2026-03-07',99140.72,115003.24,57),
(47,'2026-04-28',167171.03,193918.40,58),
(48,'2026-03-07',141193.62,163784.60,59),
(49,'2026-01-14',122350.43,141926.50,61),
(50,'2025-05-19',139567.48,161898.28,62),
(51,'2026-09-22',51795.70,60083.01,63),
(52,'2025-05-06',249747.56,289707.17,65),
(53,'2025-04-21',30269.72,35112.87,66),
(54,'2025-08-26',100300.06,116348.07,68),
(55,'2025-03-16',143150.79,166054.92,69),
(56,'2025-08-08',96170.43,111557.70,70),
(57,'2025-02-16',331495.10,384534.32,71),
(58,'2026-01-19',119016.00,138058.56,73),
(59,'2026-09-22',209957.38,243550.56,75),
(60,'2025-05-20',116503.97,135144.60,76),
(61,'2025-01-07',53244.91,61764.09,77),
(62,'2026-01-01',39757.97,46119.25,78),
(63,'2026-03-30',200192.76,232223.60,79),
(64,'2025-11-19',231282.76,268288.00,80),
(65,'2025-11-26',262448.68,304440.47,81),
(66,'2026-03-13',80980.98,93937.94,82),
(67,'2026-09-08',19349.69,22445.64,83),
(68,'2026-08-24',202213.71,234567.90,85),
(69,'2025-08-31',162864.00,188922.24,87),
(70,'2026-07-16',256223.15,297218.85,90),
(71,'2025-09-13',350899.62,407043.56,91),
(72,'2025-08-07',27436.66,31826.52,93),
(73,'2025-07-08',93308.02,108237.30,94),
(74,'2026-01-26',63094.66,73189.81,95),
(75,'2025-04-18',261150.52,302934.60,96),
(76,'2025-10-25',1012.38,1174.36,97),
(77,'2025-10-01',31430.07,36458.88,99),
(78,'2025-04-23',105799.03,122726.87,100),
(79,'2026-07-08',62230.31,72187.16,101),
(80,'2025-04-08',185073.86,214685.68,102),
(81,'2025-03-20',132679.29,153907.98,103),
(82,'2025-01-30',30269.72,35112.87,104),
(83,'2026-02-03',70067.47,81278.26,105),
(84,'2025-10-02',278771.75,323375.23,106),
(85,'2026-09-20',171789.27,199275.55,108),
(86,'2025-02-21',128930.18,149559.01,109),
(87,'2026-04-28',41085.78,47659.50,110),
(88,'2025-06-22',123050.65,142738.75,112),
(89,'2026-09-13',256927.75,298036.19,114),
(90,'2025-05-14',82001.63,95121.89,116),
(91,'2025-08-20',53697.59,62289.20,117),
(92,'2026-08-28',97047.84,112575.49,118),
(93,'2025-06-19',318701.21,369693.40,120);
/*!40000 ALTER TABLE `factura_proveedor` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `inventario`
--

DROP TABLE IF EXISTS `inventario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventario` (
  `Id_Inventario` int(11) NOT NULL AUTO_INCREMENT,
  `Existencias` int(11) NOT NULL DEFAULT 0,
  `Fecha_Entrada` date DEFAULT NULL,
  `Fecha_Salida` date DEFAULT NULL,
  `Id_Producto` int(11) NOT NULL,
  PRIMARY KEY (`Id_Inventario`),
  UNIQUE KEY `Id_Producto` (`Id_Producto`),
  CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`Id_Producto`) REFERENCES `productos` (`Id_Producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `CONSTRAINT_1` CHECK (`Existencias` >= 0),
  CONSTRAINT `chk_inv_existencias` CHECK (`Existencias` >= 0)
) ENGINE=InnoDB AUTO_INCREMENT=368 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventario`
--

LOCK TABLES `inventario` WRITE;
/*!40000 ALTER TABLE `inventario` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `inventario` VALUES
(1,36,'2025-05-27',NULL,1),
(2,30,'2025-10-02',NULL,2),
(3,89,'2025-06-07',NULL,5),
(4,28,'2025-12-05',NULL,6),
(5,51,'2025-02-16','2025-09-09',7),
(6,26,'2026-02-04',NULL,8),
(7,10,'2025-05-02',NULL,9),
(8,46,'2025-01-27','2025-09-05',10),
(9,73,'2025-07-15','2026-06-17',15),
(10,100,'2025-09-01',NULL,17),
(11,7,'2025-06-03',NULL,20),
(12,184,'2026-05-30',NULL,22),
(13,64,'2025-08-26',NULL,23),
(14,15,'2025-04-13','2026-08-15',24),
(15,17,'2026-07-16','2026-08-18',25),
(16,60,'2026-07-17',NULL,27),
(17,29,'2026-02-22',NULL,28),
(18,116,'2026-06-13','2026-08-03',29),
(19,40,'2026-02-21',NULL,30),
(20,16,'2025-11-23',NULL,32),
(21,6,'2025-12-23','2026-03-03',35),
(22,41,'2025-05-20','2026-03-07',36),
(23,18,'2025-07-05','2026-02-04',37),
(24,20,'2026-03-19',NULL,38),
(25,68,'2026-05-20','2026-07-17',39),
(26,12,'2025-12-04',NULL,40),
(27,14,'2026-05-08','2026-09-04',41),
(28,38,'2025-06-17','2025-09-23',43),
(29,22,'2025-05-19',NULL,45),
(30,13,'2025-03-03',NULL,47),
(31,21,'2026-03-21','2026-05-05',48),
(32,36,'2026-01-04',NULL,49),
(33,16,'2026-06-07',NULL,50),
(34,5,'2025-01-11',NULL,52),
(35,61,'2025-03-14',NULL,53),
(36,50,'2025-01-11',NULL,56),
(37,139,'2025-08-27',NULL,57),
(38,107,'2025-07-02',NULL,58),
(39,63,'2025-03-18',NULL,59),
(40,15,'2025-03-31',NULL,60),
(41,31,'2025-08-27',NULL,61),
(42,107,'2026-01-08','2026-02-24',65),
(43,24,'2026-01-05',NULL,66),
(44,57,'2025-07-01',NULL,67),
(45,71,'2025-11-24',NULL,68),
(46,39,'2025-06-10','2026-07-13',69),
(47,25,'2026-06-26',NULL,72),
(48,18,'2025-10-01',NULL,73),
(49,7,'2025-11-08',NULL,74),
(50,53,'2025-03-19',NULL,76),
(51,55,'2025-02-07',NULL,77),
(52,25,'2025-10-06','2026-07-28',79),
(53,20,'2025-06-02',NULL,81),
(54,25,'2025-07-19',NULL,82),
(55,37,'2025-09-22',NULL,83),
(56,19,'2026-05-16','2026-07-23',85),
(57,17,'2025-06-13',NULL,86),
(58,70,'2026-06-12',NULL,87),
(59,84,'2026-04-24',NULL,88),
(60,26,'2025-10-04','2026-09-07',90),
(61,37,'2025-01-11',NULL,91),
(62,20,'2026-04-30',NULL,92),
(63,31,'2025-03-11',NULL,93),
(64,17,'2026-05-26','2026-06-07',94),
(65,24,'2025-12-26',NULL,97),
(66,20,'2026-01-29',NULL,99),
(67,60,'2025-02-10',NULL,100),
(68,147,'2026-04-12','2026-08-27',101),
(69,66,'2025-01-05','2025-06-14',103),
(70,123,'2026-07-04','2026-09-17',105),
(71,33,'2025-08-31','2025-10-31',106),
(72,45,'2025-11-04',NULL,107),
(73,26,'2025-03-04',NULL,108),
(74,55,'2025-10-20','2026-08-11',109),
(75,15,'2025-01-02','2025-01-31',110),
(76,21,'2025-12-01','2026-08-31',111),
(77,57,'2025-04-11','2026-08-05',112),
(78,103,'2026-06-21',NULL,113),
(79,28,'2025-03-06','2025-05-30',115),
(80,56,'2025-06-19',NULL,116),
(81,102,'2025-12-01','2026-09-19',118),
(82,124,'2025-10-24',NULL,121),
(83,10,'2026-07-22','2026-09-21',122),
(84,25,'2026-06-03','2026-08-28',123),
(85,50,'2026-01-17',NULL,127),
(86,91,'2025-03-04',NULL,128),
(87,48,'2025-03-20',NULL,129),
(88,31,'2026-01-24','2026-02-05',130),
(89,22,'2025-01-14',NULL,133),
(90,11,'2025-07-17',NULL,134),
(91,160,'2025-05-01',NULL,135),
(92,12,'2026-06-12',NULL,137),
(93,7,'2026-05-12',NULL,139),
(94,48,'2025-02-06',NULL,140),
(95,36,'2026-05-08',NULL,142),
(96,70,'2026-07-16','2026-08-17',144),
(97,98,'2025-11-05','2026-06-25',145),
(98,55,'2026-01-04','2026-03-20',147),
(99,27,'2025-06-28',NULL,148),
(100,4,'2025-10-01',NULL,150),
(201,32,'2026-10-12',NULL,151),
(202,10,'2026-10-12',NULL,152),
(293,666,'2026-10-13',NULL,243),
(294,34,'2026-10-13',NULL,244),
(295,10,'2026-10-13',NULL,245);
/*!40000 ALTER TABLE `inventario` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `marcas`
--

DROP TABLE IF EXISTS `marcas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `marcas` (
  `Id_Marca` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) NOT NULL,
  `Descripcion` varchar(200) DEFAULT NULL,
  `Estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`Id_Marca`),
  UNIQUE KEY `uq_marcas_nombre` (`Nombre`),
  CONSTRAINT `chk_marca_estado` CHECK (`Estado` in (0,1))
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marcas`
--

LOCK TABLES `marcas` WRITE;
/*!40000 ALTER TABLE `marcas` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `marcas` VALUES
(1,'Truper',NULL,1),
(2,'Pretul',NULL,1),
(3,'Urrea',NULL,1),
(4,'Comex',NULL,1),
(5,'Condumex',NULL,1),
(6,'Fiero',NULL,1),
(7,'Perfect',NULL,1),
(8,'Cemex',NULL,1),
(9,'Volteck',NULL,1),
(10,'Foset',NULL,1);
/*!40000 ALTER TABLE `marcas` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `metodo_pago`
--

DROP TABLE IF EXISTS `metodo_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `metodo_pago` (
  `Id_Metodo_Pago` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`Id_Metodo_Pago`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metodo_pago`
--

LOCK TABLES `metodo_pago` WRITE;
/*!40000 ALTER TABLE `metodo_pago` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `metodo_pago` VALUES
(1,'Efectivo'),
(2,'Tarjeta'),
(3,'Transferencia');
/*!40000 ALTER TABLE `metodo_pago` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `movimiento_inventario`
--

DROP TABLE IF EXISTS `movimiento_inventario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `movimiento_inventario` (
  `Id_Movimiento` int(11) NOT NULL AUTO_INCREMENT,
  `Tipo` enum('Entrada','Salida','Ajuste','Devolucion') NOT NULL,
  `Cantidad` int(11) NOT NULL,
  `Stock_Antes` int(11) NOT NULL,
  `Stock_Despues` int(11) NOT NULL,
  `Motivo` varchar(200) DEFAULT NULL,
  `Fecha` datetime DEFAULT current_timestamp(),
  `Id_Producto` int(11) NOT NULL,
  `Id_Empleado` int(11) NOT NULL,
  PRIMARY KEY (`Id_Movimiento`),
  KEY `Id_Empleado` (`Id_Empleado`),
  KEY `idx_movinv_fecha` (`Fecha`),
  KEY `idx_movinv_producto` (`Id_Producto`),
  CONSTRAINT `movimiento_inventario_ibfk_1` FOREIGN KEY (`Id_Producto`) REFERENCES `productos` (`Id_Producto`) ON UPDATE CASCADE,
  CONSTRAINT `movimiento_inventario_ibfk_2` FOREIGN KEY (`Id_Empleado`) REFERENCES `empleados` (`Id_Empleado`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=401 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movimiento_inventario`
--

LOCK TABLES `movimiento_inventario` WRITE;
/*!40000 ALTER TABLE `movimiento_inventario` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `movimiento_inventario` VALUES
(1,'Entrada',54,3,57,'Ingreso por compra a proveedor','2025-11-21 20:46:52',75,20),
(2,'Entrada',132,10,142,'Reabastecimiento por compra','2025-09-23 19:57:33',127,7),
(3,'Entrada',195,34,229,'Reabastecimiento por compra','2026-03-04 08:53:16',26,20),
(4,'Entrada',60,14,74,'Recepción de mercancía','2026-04-25 14:28:49',106,17),
(5,'Salida',5,75,70,'Salida por venta','2025-11-05 20:20:29',94,4),
(6,'Entrada',105,62,167,'Ingreso por compra a proveedor','2025-04-15 08:58:29',142,13),
(7,'Salida',10,26,16,'Salida por consumo interno','2026-07-19 12:41:05',145,12),
(8,'Entrada',68,0,68,'Conteo inicial de inventario','2025-01-11 16:51:31',39,3),
(9,'Entrada',118,53,171,'Recepción de mercancía','2025-01-06 12:14:45',74,15),
(10,'Salida',19,132,113,'Venta a cliente','2025-07-15 20:21:46',93,17),
(11,'Entrada',45,0,45,'Conteo inicial de inventario','2025-01-16 08:02:00',107,13),
(12,'Salida',8,143,135,'Salida por venta','2026-03-10 10:03:42',77,13),
(13,'Entrada',53,61,114,'Recepción de mercancía','2025-08-15 14:09:40',42,14),
(14,'Entrada',185,23,208,'Reabastecimiento por compra','2025-03-16 15:16:28',135,6),
(15,'Entrada',116,38,154,'Recepción de mercancía','2025-02-17 15:40:37',58,17),
(16,'Entrada',121,69,190,'Reposición de stock','2025-01-21 09:29:41',106,3),
(17,'Entrada',110,22,132,'Recepción de mercancía','2026-09-19 19:13:22',8,12),
(18,'Entrada',177,78,255,'Reposición de stock','2025-11-30 10:37:40',55,13),
(19,'Salida',5,138,133,'Salida por consumo interno','2025-07-05 16:55:42',28,16),
(20,'Entrada',173,8,181,'Reabastecimiento por compra','2025-07-28 19:50:11',2,12),
(21,'Entrada',65,15,80,'Reabastecimiento por compra','2025-06-06 09:24:23',139,5),
(22,'Salida',7,92,85,'Salida por consumo interno','2025-02-18 10:02:15',109,15),
(23,'Salida',15,33,18,'Salida por venta','2026-07-28 09:37:11',107,20),
(24,'Devolucion',5,29,34,'Devolución por garantía','2025-01-25 19:09:56',83,4),
(25,'Entrada',51,58,109,'Recepción de mercancía','2026-02-06 16:18:26',63,8),
(26,'Entrada',66,0,66,'Conteo inicial de inventario','2025-01-02 08:35:49',103,18),
(27,'Salida',13,110,97,'Salida por venta','2025-09-02 15:08:35',96,4),
(28,'Entrada',116,28,144,'Reabastecimiento por compra','2026-04-29 08:39:20',48,5),
(29,'Salida',1,132,131,'Venta a cliente','2025-06-06 20:21:08',124,19),
(30,'Entrada',30,0,30,'Conteo inicial de inventario','2025-01-06 14:14:18',2,1),
(31,'Entrada',26,0,26,'Conteo inicial de inventario','2025-01-01 19:35:40',108,3),
(32,'Devolucion',4,98,102,'Devolución por garantía','2026-07-09 09:02:20',73,4),
(33,'Entrada',79,13,92,'Ingreso por compra a proveedor','2025-04-11 11:57:37',131,19),
(34,'Devolucion',2,70,72,'Devolución de cliente','2025-03-26 13:56:44',106,9),
(35,'Entrada',187,11,198,'Reabastecimiento por compra','2025-02-16 10:08:08',13,9),
(36,'Salida',10,18,8,'Salida por venta','2025-04-09 19:38:30',61,2),
(37,'Devolucion',3,6,9,'Producto defectuoso devuelto','2025-08-19 08:58:10',37,6),
(38,'Entrada',18,0,18,'Conteo inicial de inventario','2025-01-06 16:07:54',73,6),
(39,'Entrada',100,0,100,'Conteo inicial de inventario','2025-01-16 11:12:08',17,9),
(40,'Entrada',184,0,184,'Conteo inicial de inventario','2025-01-02 19:45:44',22,11),
(41,'Entrada',50,0,50,'Conteo inicial de inventario','2025-01-02 14:20:29',127,18),
(42,'Entrada',15,0,15,'Conteo inicial de inventario','2025-01-01 13:19:00',60,18),
(43,'Entrada',154,55,209,'Reposición de stock','2025-05-23 11:03:18',143,11),
(44,'Entrada',48,0,48,'Conteo inicial de inventario','2025-01-16 20:31:45',129,20),
(45,'Devolucion',3,76,79,'Devolución de cliente','2025-01-07 15:16:21',114,13),
(46,'Salida',17,148,131,'Salida por consumo interno','2026-05-02 14:36:51',67,16),
(47,'Entrada',116,7,123,'Reposición de stock','2025-03-23 13:13:20',56,2),
(48,'Salida',2,34,32,'Salida por consumo interno','2025-10-11 12:02:15',13,4),
(49,'Entrada',57,0,57,'Conteo inicial de inventario','2025-01-05 12:58:52',67,6),
(50,'Devolucion',2,79,81,'Devolución por garantía','2025-12-24 11:53:42',141,19),
(51,'Entrada',55,0,55,'Conteo inicial de inventario','2025-01-04 20:38:49',109,8),
(52,'Salida',7,13,6,'Salida por consumo interno','2025-06-18 13:42:33',86,12),
(53,'Entrada',39,0,39,'Conteo inicial de inventario','2025-01-10 08:02:50',69,7),
(54,'Entrada',177,27,204,'Ingreso por compra a proveedor','2025-02-14 20:01:39',43,11),
(55,'Salida',16,93,77,'Venta en mostrador','2026-05-01 19:08:14',132,9),
(56,'Entrada',129,76,205,'Ingreso por compra a proveedor','2026-04-29 12:04:59',89,8),
(57,'Entrada',22,0,22,'Conteo inicial de inventario','2025-01-07 15:00:39',133,3),
(58,'Salida',3,57,54,'Venta en mostrador','2025-07-28 10:26:22',10,16),
(59,'Entrada',82,57,139,'Recepción de mercancía','2026-04-03 11:04:07',13,15),
(60,'Salida',10,82,72,'Venta a cliente','2025-01-29 14:05:37',109,6),
(61,'Entrada',145,14,159,'Reabastecimiento por compra','2026-07-31 15:05:54',106,10),
(62,'Entrada',166,35,201,'Recepción de mercancía','2026-04-17 13:34:03',28,12),
(63,'Entrada',120,54,174,'Reabastecimiento por compra','2025-10-19 14:33:05',90,10),
(64,'Entrada',143,49,192,'Ingreso por compra a proveedor','2025-02-20 17:50:15',76,20),
(65,'Salida',1,10,9,'Salida por venta','2025-02-06 19:08:30',38,14),
(66,'Devolucion',5,56,61,'Devolución por garantía','2025-05-07 13:28:47',143,1),
(67,'Salida',6,108,102,'Venta a cliente','2026-05-24 18:29:49',145,9),
(68,'Salida',13,16,3,'Venta en mostrador','2025-07-14 08:03:29',40,12),
(69,'Entrada',40,39,79,'Recepción de mercancía','2025-11-06 18:56:40',84,11),
(70,'Salida',5,146,141,'Salida por venta','2026-06-10 11:54:54',30,18),
(71,'Entrada',105,16,121,'Recepción de mercancía','2025-03-25 15:02:03',93,13),
(72,'Entrada',89,51,140,'Recepción de mercancía','2025-08-18 16:32:30',42,7),
(73,'Salida',2,77,75,'Salida por venta','2026-02-09 16:12:54',132,17),
(74,'Entrada',25,0,25,'Conteo inicial de inventario','2025-01-06 15:11:18',79,15),
(75,'Entrada',20,0,20,'Conteo inicial de inventario','2025-01-03 16:48:55',38,20),
(76,'Salida',18,119,101,'Salida por venta','2025-11-23 11:41:14',147,20),
(77,'Salida',17,42,25,'Salida por consumo interno','2025-12-23 20:43:50',31,20),
(78,'Entrada',192,66,258,'Reposición de stock','2026-02-04 08:23:54',87,15),
(79,'Entrada',37,0,37,'Conteo inicial de inventario','2025-01-04 20:44:17',83,19),
(80,'Entrada',63,0,63,'Conteo inicial de inventario','2025-01-15 11:14:10',59,5),
(81,'Salida',5,47,42,'Salida por consumo interno','2025-05-06 09:21:17',23,6),
(82,'Salida',17,80,63,'Salida por consumo interno','2025-04-11 17:44:48',35,9),
(83,'Entrada',144,46,190,'Ingreso por compra a proveedor','2026-06-09 11:34:30',56,10),
(84,'Entrada',159,4,163,'Ingreso por compra a proveedor','2026-07-19 17:49:00',83,8),
(85,'Entrada',33,0,33,'Conteo inicial de inventario','2025-01-11 15:14:55',106,18),
(86,'Entrada',133,41,174,'Ingreso por compra a proveedor','2026-07-17 11:16:20',1,17),
(87,'Entrada',70,0,70,'Conteo inicial de inventario','2025-01-13 13:33:02',144,12),
(88,'Salida',17,83,66,'Venta en mostrador','2026-01-31 14:37:08',20,18),
(89,'Salida',14,131,117,'Venta en mostrador','2025-11-04 17:44:09',31,7),
(90,'Devolucion',4,64,68,'Producto defectuoso devuelto','2025-09-09 10:07:46',87,2),
(91,'Entrada',165,15,180,'Ingreso por compra a proveedor','2025-04-25 17:05:54',104,7),
(92,'Entrada',73,0,73,'Conteo inicial de inventario','2025-01-02 13:24:06',15,16),
(93,'Salida',9,127,118,'Venta a cliente','2025-08-12 10:28:15',92,20),
(94,'Entrada',111,45,156,'Ingreso por compra a proveedor','2025-12-07 15:38:20',39,2),
(95,'Salida',1,9,8,'Venta a cliente','2026-03-27 15:06:54',8,1),
(96,'Entrada',81,22,103,'Reabastecimiento por compra','2025-08-18 12:50:01',102,16),
(97,'Devolucion',1,11,12,'Devolución por garantía','2026-06-11 08:34:53',31,16),
(98,'Entrada',60,15,75,'Reabastecimiento por compra','2025-12-27 08:15:18',53,19),
(99,'Salida',14,65,51,'Salida por venta','2025-06-16 10:59:25',79,15),
(100,'Entrada',25,0,25,'Conteo inicial de inventario','2025-01-11 14:21:26',123,11),
(101,'Salida',10,87,77,'Venta en mostrador','2026-05-28 17:56:12',142,20),
(102,'Entrada',196,4,200,'Ingreso por compra a proveedor','2025-08-03 09:35:42',69,1),
(103,'Entrada',133,42,175,'Ingreso por compra a proveedor','2025-04-10 09:38:55',15,17),
(104,'Salida',8,112,104,'Venta en mostrador','2025-09-15 12:37:11',53,2),
(105,'Salida',4,12,8,'Salida por consumo interno','2025-09-28 10:07:59',112,1),
(106,'Salida',15,119,104,'Venta en mostrador','2026-01-10 10:03:33',37,18),
(107,'Entrada',84,54,138,'Recepción de mercancía','2026-04-28 14:55:51',68,4),
(108,'Entrada',88,44,132,'Recepción de mercancía','2026-05-21 10:33:16',92,10),
(109,'Entrada',152,61,213,'Recepción de mercancía','2025-05-10 12:22:18',8,9),
(110,'Entrada',13,15,28,'Ingreso por compra a proveedor','2025-05-21 09:59:33',77,15),
(111,'Entrada',15,0,15,'Conteo inicial de inventario','2025-01-14 16:44:03',110,13),
(112,'Entrada',36,0,36,'Conteo inicial de inventario','2025-01-14 16:34:44',1,4),
(113,'Salida',14,73,59,'Venta a cliente','2026-07-05 13:37:58',103,18),
(114,'Salida',7,53,46,'Salida por consumo interno','2025-06-12 16:59:14',52,11),
(115,'Salida',7,49,42,'Venta a cliente','2026-01-30 14:10:09',124,19),
(116,'Entrada',70,21,91,'Reposición de stock','2025-11-20 17:49:51',49,2),
(117,'Entrada',24,0,24,'Conteo inicial de inventario','2025-01-02 20:42:30',97,2),
(118,'Salida',17,34,17,'Venta a cliente','2025-10-30 16:59:45',13,15),
(119,'Entrada',99,39,138,'Recepción de mercancía','2026-09-11 13:13:46',73,7),
(120,'Devolucion',1,46,47,'Devolución de cliente','2026-01-29 12:39:57',81,14),
(121,'Entrada',29,79,108,'Recepción de mercancía','2025-10-03 18:14:35',45,14),
(122,'Salida',17,52,35,'Salida por venta','2026-03-08 10:59:09',62,16),
(123,'Salida',13,83,70,'Salida por venta','2025-03-23 16:46:49',139,11),
(124,'Entrada',36,58,94,'Recepción de mercancía','2025-09-20 11:19:01',48,2),
(125,'Entrada',97,5,102,'Reabastecimiento por compra','2025-09-12 15:33:58',143,16),
(126,'Salida',11,64,53,'Venta a cliente','2025-07-11 08:40:06',150,9),
(127,'Entrada',97,7,104,'Recepción de mercancía','2025-07-22 16:39:01',97,18),
(128,'Entrada',94,36,130,'Reposición de stock','2025-02-06 08:16:16',49,18),
(129,'Entrada',176,25,201,'Reposición de stock','2026-08-17 18:29:51',136,3),
(130,'Salida',9,34,25,'Venta a cliente','2026-03-24 08:46:58',143,3),
(131,'Salida',18,117,99,'Venta a cliente','2026-05-24 13:08:40',101,6),
(132,'Entrada',91,0,91,'Conteo inicial de inventario','2025-01-09 17:23:10',128,2),
(133,'Entrada',172,73,245,'Ingreso por compra a proveedor','2026-06-13 14:02:27',93,2),
(134,'Entrada',196,46,242,'Recepción de mercancía','2026-03-21 20:14:49',79,10),
(135,'Salida',4,94,90,'Venta a cliente','2025-04-03 13:15:34',73,18),
(136,'Entrada',102,0,102,'Conteo inicial de inventario','2025-01-01 13:12:26',118,1),
(137,'Entrada',71,0,71,'Conteo inicial de inventario','2025-01-03 13:34:37',68,11),
(138,'Devolucion',1,17,18,'Devolución por garantía','2025-11-17 18:18:33',101,11),
(139,'Entrada',146,36,182,'Reabastecimiento por compra','2026-05-09 20:52:41',50,8),
(140,'Entrada',170,64,234,'Recepción de mercancía','2025-09-03 09:55:29',90,16),
(141,'Salida',14,109,95,'Venta a cliente','2025-01-20 13:06:26',128,12),
(142,'Devolucion',5,38,43,'Devolución de cliente','2026-07-04 11:56:18',130,8),
(143,'Entrada',123,0,123,'Conteo inicial de inventario','2025-01-11 18:31:07',105,18),
(144,'Entrada',62,12,74,'Reposición de stock','2025-03-04 15:52:53',132,4),
(145,'Salida',9,20,11,'Salida por venta','2026-06-15 16:07:49',17,20),
(146,'Entrada',31,0,31,'Conteo inicial de inventario','2025-01-07 17:08:10',61,7),
(147,'Entrada',56,0,56,'Conteo inicial de inventario','2025-01-11 09:39:32',116,13),
(148,'Devolucion',5,68,73,'Producto defectuoso devuelto','2025-11-26 11:08:43',49,6),
(149,'Entrada',198,51,249,'Recepción de mercancía','2026-07-22 18:26:54',150,6),
(150,'Salida',18,71,53,'Venta a cliente','2025-10-04 10:37:46',116,16),
(151,'Salida',1,35,34,'Salida por venta','2026-06-21 10:23:49',37,2),
(152,'Entrada',12,0,12,'Conteo inicial de inventario','2025-01-01 16:05:26',137,16),
(153,'Entrada',61,0,61,'Conteo inicial de inventario','2025-01-12 16:07:20',53,11),
(154,'Entrada',172,66,238,'Recepción de mercancía','2026-07-03 08:11:15',88,9),
(155,'Entrada',103,0,103,'Conteo inicial de inventario','2025-01-05 11:06:16',113,9),
(156,'Entrada',31,0,31,'Conteo inicial de inventario','2025-01-02 12:10:41',93,8),
(157,'Salida',13,100,87,'Venta a cliente','2025-07-26 18:00:44',28,3),
(158,'Entrada',70,0,70,'Conteo inicial de inventario','2025-01-14 14:19:02',87,9),
(159,'Entrada',110,22,132,'Reabastecimiento por compra','2025-12-18 10:30:22',95,3),
(160,'Salida',4,45,41,'Venta en mostrador','2026-04-25 20:01:18',27,6),
(161,'Salida',15,91,76,'Salida por venta','2025-10-23 19:15:23',60,11),
(162,'Entrada',25,0,25,'Conteo inicial de inventario','2025-01-04 11:01:10',72,3),
(163,'Salida',20,106,86,'Venta en mostrador','2025-05-18 20:20:02',132,13),
(164,'Entrada',174,67,241,'Reabastecimiento por compra','2025-02-11 08:59:16',119,6),
(165,'Entrada',93,0,93,'Reposición de stock','2025-11-17 09:11:42',137,4),
(166,'Salida',20,76,56,'Salida por consumo interno','2026-04-17 18:46:37',148,16),
(167,'Entrada',120,52,172,'Recepción de mercancía','2026-04-23 13:43:30',11,4),
(168,'Entrada',147,0,147,'Conteo inicial de inventario','2025-01-01 13:04:51',101,2),
(169,'Devolucion',2,68,70,'Devolución por garantía','2026-03-13 13:37:49',47,14),
(170,'Entrada',39,80,119,'Recepción de mercancía','2025-09-24 12:59:46',41,3),
(171,'Salida',12,143,131,'Salida por consumo interno','2026-02-17 10:24:21',23,20),
(172,'Entrada',55,0,55,'Conteo inicial de inventario','2025-01-10 12:49:25',77,6),
(173,'Salida',17,35,18,'Salida por venta','2025-05-15 11:57:58',121,19),
(174,'Entrada',155,1,156,'Reabastecimiento por compra','2025-05-20 15:46:00',24,7),
(175,'Entrada',112,37,149,'Reposición de stock','2025-06-26 12:31:47',129,4),
(176,'Salida',13,13,0,'Venta en mostrador','2026-04-23 15:32:54',23,11),
(177,'Entrada',135,14,149,'Ingreso por compra a proveedor','2025-08-01 20:08:11',41,5),
(178,'Entrada',47,30,77,'Ingreso por compra a proveedor','2025-01-28 12:35:21',102,1),
(179,'Entrada',77,69,146,'Reabastecimiento por compra','2025-03-11 08:20:24',9,9),
(180,'Salida',3,77,74,'Salida por venta','2026-01-14 12:52:46',74,16),
(181,'Salida',4,93,89,'Venta en mostrador','2026-09-10 18:00:59',82,18),
(182,'Entrada',138,52,190,'Reabastecimiento por compra','2026-09-01 10:00:13',148,15),
(183,'Entrada',10,72,82,'Recepción de mercancía','2025-02-13 08:12:30',87,18),
(184,'Entrada',53,36,89,'Reposición de stock','2026-01-24 17:57:35',21,13),
(185,'Entrada',91,10,101,'Ingreso por compra a proveedor','2025-11-18 17:22:06',36,19),
(186,'Entrada',53,76,129,'Reposición de stock','2025-07-08 10:30:59',13,4),
(187,'Devolucion',2,67,69,'Devolución por garantía','2025-08-21 18:07:03',116,3),
(188,'Entrada',154,24,178,'Ingreso por compra a proveedor','2025-07-26 10:58:15',17,6),
(189,'Entrada',17,0,17,'Conteo inicial de inventario','2025-01-11 18:00:22',94,6),
(190,'Salida',13,37,24,'Salida por venta','2025-09-03 12:36:43',120,15),
(191,'Entrada',11,0,11,'Conteo inicial de inventario','2025-01-07 15:19:15',134,17),
(192,'Entrada',50,0,50,'Conteo inicial de inventario','2025-01-16 10:58:00',56,3),
(193,'Entrada',28,0,28,'Conteo inicial de inventario','2025-01-09 11:51:51',115,18),
(194,'Devolucion',3,10,13,'Devolución de cliente','2025-11-14 12:35:30',143,16),
(195,'Entrada',186,46,232,'Reposición de stock','2026-03-02 13:43:49',70,15),
(196,'Entrada',70,24,94,'Reabastecimiento por compra','2025-04-25 11:32:19',6,14),
(197,'Salida',17,79,62,'Salida por consumo interno','2025-06-15 10:39:48',122,11),
(198,'Salida',3,23,20,'Venta en mostrador','2026-01-05 20:09:41',20,7),
(199,'Entrada',19,64,83,'Ingreso por compra a proveedor','2025-01-12 12:37:23',63,1),
(200,'Devolucion',4,8,12,'Devolución de cliente','2026-02-23 10:54:38',41,4),
(201,'Entrada',16,0,16,'Conteo inicial de inventario','2025-01-16 09:32:17',50,14),
(202,'Entrada',13,0,13,'Conteo inicial de inventario','2025-01-15 09:17:57',47,19),
(203,'Entrada',77,5,82,'Reposición de stock','2026-03-09 18:55:07',5,20),
(204,'Entrada',40,0,40,'Conteo inicial de inventario','2025-01-02 14:48:03',30,20),
(205,'Entrada',4,0,4,'Conteo inicial de inventario','2025-01-06 12:31:46',150,14),
(206,'Salida',9,45,36,'Venta en mostrador','2025-12-30 10:54:36',74,9),
(207,'Entrada',158,34,192,'Reabastecimiento por compra','2026-06-28 20:41:25',119,10),
(208,'Salida',2,139,137,'Salida por consumo interno','2026-02-22 18:11:14',98,13),
(209,'Entrada',124,78,202,'Reabastecimiento por compra','2025-12-19 12:55:00',135,18),
(210,'Entrada',48,0,48,'Conteo inicial de inventario','2025-01-07 15:58:19',140,19),
(211,'Salida',2,6,4,'Venta a cliente','2026-06-10 19:05:35',48,12),
(212,'Salida',18,33,15,'Venta a cliente','2025-02-17 10:55:54',7,5),
(213,'Entrada',16,68,84,'Reabastecimiento por compra','2025-09-29 10:50:49',86,14),
(214,'Entrada',14,4,18,'Recepción de mercancía','2025-06-25 09:29:23',111,15),
(215,'Salida',7,22,15,'Venta en mostrador','2025-09-28 14:52:28',122,5),
(216,'Salida',1,84,83,'Venta a cliente','2025-07-15 08:26:29',42,14),
(217,'Salida',8,24,16,'Venta en mostrador','2026-07-31 17:07:46',136,19),
(218,'Entrada',33,43,76,'Reposición de stock','2025-01-05 08:08:28',38,9),
(219,'Devolucion',2,2,4,'Devolución de cliente','2025-10-27 18:08:06',46,13),
(220,'Entrada',68,50,118,'Reabastecimiento por compra','2025-02-19 14:54:40',92,12),
(221,'Entrada',26,0,26,'Conteo inicial de inventario','2025-01-12 09:21:53',8,10),
(222,'Entrada',127,38,165,'Reposición de stock','2025-01-25 13:35:42',23,17),
(223,'Entrada',128,21,149,'Reabastecimiento por compra','2025-07-14 15:18:42',136,2),
(224,'Salida',7,23,16,'Venta en mostrador','2025-05-25 20:51:15',29,10),
(225,'Salida',17,50,33,'Salida por venta','2025-07-27 18:20:46',41,1),
(226,'Entrada',111,70,181,'Recepción de mercancía','2026-09-17 11:22:13',106,4),
(227,'Devolucion',5,97,102,'Devolución de cliente','2026-06-30 10:03:12',9,8),
(228,'Salida',16,62,46,'Salida por consumo interno','2025-07-25 17:14:04',130,8),
(229,'Entrada',67,78,145,'Recepción de mercancía','2025-02-24 12:20:36',115,13),
(230,'Entrada',21,71,92,'Reabastecimiento por compra','2025-03-30 19:10:13',59,12),
(231,'Entrada',51,0,51,'Conteo inicial de inventario','2025-01-13 17:25:49',7,11),
(232,'Entrada',69,18,87,'Reabastecimiento por compra','2025-06-01 12:43:26',70,20),
(233,'Devolucion',1,2,3,'Devolución por garantía','2026-08-06 11:09:46',57,11),
(234,'Salida',17,138,121,'Venta a cliente','2026-04-20 18:04:22',119,9),
(235,'Entrada',20,0,20,'Conteo inicial de inventario','2025-01-16 15:18:50',81,10),
(236,'Entrada',64,0,64,'Conteo inicial de inventario','2025-01-12 15:40:29',23,19),
(237,'Entrada',155,51,206,'Recepción de mercancía','2025-03-27 17:57:20',118,11),
(238,'Salida',6,93,87,'Venta en mostrador','2025-08-17 12:23:12',103,7),
(239,'Devolucion',2,15,17,'Devolución de cliente','2025-05-07 20:39:06',132,5),
(240,'Entrada',139,0,139,'Conteo inicial de inventario','2025-01-13 18:43:44',57,13),
(241,'Salida',12,72,60,'Venta en mostrador','2026-06-05 18:51:18',70,18),
(242,'Salida',4,48,44,'Salida por consumo interno','2025-04-03 10:13:21',135,8),
(243,'Entrada',23,34,57,'Recepción de mercancía','2025-02-28 16:16:18',82,11),
(244,'Salida',10,142,132,'Venta en mostrador','2025-10-27 09:14:29',138,11),
(245,'Entrada',57,0,57,'Conteo inicial de inventario','2025-01-03 16:24:07',112,17),
(246,'Entrada',7,0,7,'Conteo inicial de inventario','2025-01-08 20:03:02',20,14),
(247,'Salida',9,105,96,'Venta a cliente','2026-02-05 14:12:46',131,15),
(248,'Entrada',128,53,181,'Ingreso por compra a proveedor','2025-10-26 09:17:04',133,2),
(249,'Entrada',145,22,167,'Reposición de stock','2025-02-06 10:31:33',38,20),
(250,'Salida',12,68,56,'Venta en mostrador','2025-09-29 16:05:59',8,12),
(251,'Salida',13,56,43,'Salida por venta','2026-05-04 12:03:34',1,17),
(252,'Devolucion',3,94,97,'Devolución de cliente','2025-08-29 16:44:24',141,8),
(253,'Entrada',119,73,192,'Ingreso por compra a proveedor','2026-03-20 14:34:57',112,20),
(254,'Entrada',129,5,134,'Reabastecimiento por compra','2026-07-21 14:54:07',73,3),
(255,'Entrada',92,79,171,'Reposición de stock','2025-02-28 20:00:58',138,9),
(256,'Salida',5,8,3,'Salida por consumo interno','2026-02-07 14:37:55',12,7),
(257,'Entrada',20,0,20,'Conteo inicial de inventario','2025-01-03 12:24:07',99,6),
(258,'Entrada',18,0,18,'Conteo inicial de inventario','2025-01-07 08:17:30',37,9),
(259,'Entrada',185,19,204,'Recepción de mercancía','2026-04-07 14:03:18',55,9),
(260,'Salida',2,8,6,'Salida por consumo interno','2025-12-26 11:55:12',96,16),
(261,'Entrada',31,0,31,'Conteo inicial de inventario','2025-01-04 12:19:15',130,10),
(262,'Salida',14,143,129,'Venta a cliente','2026-08-25 10:26:02',39,7),
(263,'Entrada',24,49,73,'Ingreso por compra a proveedor','2026-08-07 20:39:53',41,18),
(264,'Entrada',60,0,60,'Conteo inicial de inventario','2025-01-01 17:48:38',27,18),
(265,'Devolucion',1,22,23,'Producto defectuoso devuelto','2025-12-22 18:28:30',139,5),
(266,'Entrada',25,0,25,'Conteo inicial de inventario','2025-01-15 15:24:30',82,15),
(267,'Salida',9,129,120,'Venta a cliente','2025-07-20 11:01:21',84,2),
(268,'Entrada',31,5,36,'Ingreso por compra a proveedor','2026-03-02 12:45:42',63,14),
(269,'Entrada',16,0,16,'Conteo inicial de inventario','2025-01-16 09:09:11',32,1),
(270,'Entrada',26,0,26,'Conteo inicial de inventario','2025-01-10 08:11:49',90,7),
(271,'Salida',1,92,91,'Salida por consumo interno','2026-09-08 20:22:18',148,14),
(272,'Salida',1,88,87,'Venta a cliente','2026-05-01 09:30:18',96,8),
(273,'Devolucion',3,18,21,'Devolución por garantía','2025-11-01 17:25:21',59,15),
(274,'Salida',14,134,120,'Salida por consumo interno','2025-04-17 20:55:00',114,20),
(275,'Salida',19,26,7,'Salida por consumo interno','2025-09-03 18:46:17',30,10),
(276,'Entrada',17,0,17,'Conteo inicial de inventario','2025-01-06 13:02:23',86,8),
(277,'Entrada',182,39,221,'Reabastecimiento por compra','2025-05-04 16:58:03',31,5),
(278,'Entrada',12,0,12,'Conteo inicial de inventario','2025-01-10 15:19:25',40,13),
(279,'Salida',11,142,131,'Salida por venta','2025-01-01 17:42:24',88,14),
(280,'Salida',15,132,117,'Venta en mostrador','2025-08-20 20:58:26',103,16),
(281,'Entrada',190,52,242,'Reabastecimiento por compra','2026-07-02 19:07:01',103,20),
(282,'Entrada',89,0,89,'Conteo inicial de inventario','2025-01-08 09:36:00',5,4),
(283,'Entrada',183,16,199,'Ingreso por compra a proveedor','2025-06-02 11:11:37',22,9),
(284,'Salida',6,66,60,'Salida por consumo interno','2025-02-07 17:20:43',30,14),
(285,'Salida',20,146,126,'Salida por venta','2026-09-10 12:35:25',135,8),
(286,'Salida',16,85,69,'Salida por consumo interno','2026-02-08 19:35:00',2,2),
(287,'Entrada',129,77,206,'Reposición de stock','2025-11-18 10:43:13',81,14),
(288,'Salida',4,30,26,'Venta a cliente','2025-10-23 11:09:38',5,5),
(289,'Entrada',153,46,199,'Recepción de mercancía','2025-05-10 18:23:38',122,19),
(290,'Entrada',36,0,36,'Conteo inicial de inventario','2025-01-11 10:58:25',142,10),
(291,'Entrada',36,58,94,'Recepción de mercancía','2026-04-30 17:08:29',93,17),
(292,'Entrada',130,10,140,'Recepción de mercancía','2025-10-13 09:16:49',13,9),
(293,'Entrada',91,47,138,'Ingreso por compra a proveedor','2026-02-03 20:01:09',50,11),
(294,'Entrada',177,44,221,'Reposición de stock','2025-01-14 18:59:21',7,5),
(295,'Devolucion',2,3,5,'Devolución de cliente','2025-09-24 10:04:03',102,4),
(296,'Entrada',60,0,60,'Conteo inicial de inventario','2025-01-15 12:03:56',100,8),
(297,'Salida',7,86,79,'Venta en mostrador','2026-07-29 11:59:04',5,3),
(298,'Entrada',7,0,7,'Conteo inicial de inventario','2025-01-08 17:53:14',139,18),
(299,'Entrada',73,76,149,'Ingreso por compra a proveedor','2025-10-25 20:19:31',137,6),
(300,'Entrada',180,59,239,'Recepción de mercancía','2025-06-24 12:26:08',109,13),
(301,'Salida',14,43,29,'Salida por consumo interno','2025-07-27 19:29:16',145,16),
(302,'Entrada',10,0,10,'Conteo inicial de inventario','2025-01-08 11:12:48',122,19),
(303,'Entrada',107,0,107,'Conteo inicial de inventario','2025-01-01 09:07:06',65,13),
(304,'Entrada',184,33,217,'Reposición de stock','2025-09-08 16:42:31',60,14),
(305,'Salida',18,101,83,'Salida por venta','2026-07-24 18:05:44',105,12),
(306,'Entrada',151,76,227,'Reabastecimiento por compra','2026-06-19 13:40:04',50,16),
(307,'Entrada',20,0,20,'Conteo inicial de inventario','2025-01-15 15:00:36',92,19),
(308,'Entrada',99,79,178,'Reabastecimiento por compra','2026-07-31 13:09:24',11,19),
(309,'Salida',6,41,35,'Venta en mostrador','2025-04-26 20:31:04',67,18),
(310,'Entrada',136,5,141,'Ingreso por compra a proveedor','2026-06-09 16:53:16',19,19),
(311,'Entrada',176,65,241,'Ingreso por compra a proveedor','2026-09-09 09:34:57',144,18),
(312,'Entrada',17,0,17,'Conteo inicial de inventario','2025-01-02 12:10:00',25,8),
(313,'Entrada',146,37,183,'Recepción de mercancía','2025-07-13 14:49:22',101,8),
(314,'Entrada',116,31,147,'Recepción de mercancía','2025-10-03 17:53:29',82,16),
(315,'Entrada',21,0,21,'Conteo inicial de inventario','2025-01-16 17:33:43',111,7),
(316,'Devolucion',1,63,64,'Devolución de cliente','2025-01-09 19:04:40',102,10),
(317,'Entrada',160,0,160,'Conteo inicial de inventario','2025-01-08 13:10:17',135,1),
(318,'Salida',16,83,67,'Salida por venta','2025-12-18 10:22:08',10,9),
(319,'Salida',20,92,72,'Salida por consumo interno','2026-04-11 16:58:00',124,4),
(320,'Salida',14,48,34,'Salida por consumo interno','2026-07-25 11:49:25',50,17),
(321,'Entrada',38,0,38,'Conteo inicial de inventario','2025-01-11 12:08:08',43,5),
(322,'Entrada',29,0,29,'Conteo inicial de inventario','2025-01-10 13:36:12',28,13),
(323,'Entrada',194,29,223,'Ingreso por compra a proveedor','2025-04-24 13:21:48',50,13),
(324,'Entrada',153,73,226,'Recepción de mercancía','2026-04-26 12:41:04',21,3),
(325,'Entrada',22,0,22,'Conteo inicial de inventario','2025-01-03 15:25:41',45,12),
(326,'Devolucion',1,66,67,'Devolución de cliente','2026-08-06 11:00:38',70,15),
(327,'Entrada',5,0,5,'Conteo inicial de inventario','2025-01-05 15:16:08',52,8),
(328,'Entrada',186,66,252,'Ingreso por compra a proveedor','2025-08-03 08:01:40',147,16),
(329,'Salida',2,129,127,'Salida por consumo interno','2025-12-12 16:58:45',132,2),
(330,'Entrada',185,5,190,'Reabastecimiento por compra','2026-07-14 12:04:59',135,6),
(331,'Entrada',28,0,28,'Conteo inicial de inventario','2025-01-06 20:32:44',6,10),
(332,'Entrada',159,45,204,'Ingreso por compra a proveedor','2025-10-12 10:40:04',78,10),
(333,'Entrada',84,0,84,'Conteo inicial de inventario','2025-01-14 09:00:14',88,18),
(334,'Entrada',172,31,203,'Reposición de stock','2025-03-01 12:25:44',10,19),
(335,'Salida',18,49,31,'Venta a cliente','2025-04-03 10:43:11',57,3),
(336,'Entrada',90,28,118,'Reabastecimiento por compra','2025-10-06 18:19:06',35,7),
(337,'Entrada',43,28,71,'Recepción de mercancía','2025-08-26 16:48:51',120,19),
(338,'Salida',4,57,53,'Salida por venta','2026-05-07 17:55:18',43,10),
(339,'Entrada',124,0,124,'Conteo inicial de inventario','2025-01-15 14:08:00',121,13),
(340,'Entrada',19,0,19,'Conteo inicial de inventario','2025-01-07 12:53:14',85,16),
(341,'Entrada',7,0,7,'Conteo inicial de inventario','2025-01-14 19:17:56',74,13),
(342,'Entrada',37,0,37,'Conteo inicial de inventario','2025-01-11 18:45:40',91,11),
(343,'Entrada',46,0,46,'Conteo inicial de inventario','2025-01-04 12:20:06',10,10),
(344,'Salida',9,141,132,'Salida por consumo interno','2026-01-10 14:07:12',15,6),
(345,'Devolucion',1,47,48,'Producto defectuoso devuelto','2025-09-27 11:34:29',13,5),
(346,'Entrada',107,0,107,'Conteo inicial de inventario','2025-01-13 17:40:47',58,17),
(347,'Entrada',55,0,55,'Conteo inicial de inventario','2025-01-12 16:01:48',147,13),
(348,'Entrada',102,76,178,'Ingreso por compra a proveedor','2026-02-15 20:18:48',93,10),
(349,'Entrada',116,0,116,'Conteo inicial de inventario','2025-01-04 13:21:07',29,8),
(350,'Entrada',14,65,79,'Ingreso por compra a proveedor','2026-03-17 09:21:56',73,6),
(351,'Entrada',98,0,98,'Conteo inicial de inventario','2025-01-09 09:02:49',145,3),
(352,'Salida',1,67,66,'Venta a cliente','2026-04-18 09:24:58',17,4),
(353,'Entrada',14,0,14,'Conteo inicial de inventario','2025-01-10 16:56:28',41,3),
(354,'Entrada',172,62,234,'Ingreso por compra a proveedor','2026-03-09 14:27:26',116,8),
(355,'Salida',11,99,88,'Salida por consumo interno','2025-09-16 20:40:27',146,3),
(356,'Entrada',196,75,271,'Ingreso por compra a proveedor','2025-07-27 12:25:33',103,1),
(357,'Salida',15,71,56,'Salida por consumo interno','2026-05-04 17:56:03',52,1),
(358,'Entrada',129,19,148,'Reposición de stock','2026-01-10 17:10:20',7,5),
(359,'Entrada',162,0,162,'Reabastecimiento por compra','2026-06-29 08:06:31',73,2),
(360,'Entrada',43,4,47,'Recepción de mercancía','2025-07-16 16:47:10',30,8),
(361,'Salida',20,55,35,'Venta en mostrador','2025-04-23 12:01:39',69,6),
(362,'Entrada',154,45,199,'Reabastecimiento por compra','2026-01-31 17:44:45',35,12),
(363,'Salida',14,138,124,'Salida por consumo interno','2025-09-17 09:56:11',148,19),
(364,'Entrada',133,66,199,'Reabastecimiento por compra','2025-05-17 13:20:12',125,15),
(365,'Entrada',110,1,111,'Reabastecimiento por compra','2026-08-08 15:54:26',148,6),
(366,'Salida',5,12,7,'Venta en mostrador','2026-06-11 13:45:05',38,18),
(367,'Devolucion',1,98,99,'Producto defectuoso devuelto','2025-04-09 17:09:50',51,2),
(368,'Entrada',188,74,262,'Reabastecimiento por compra','2025-11-13 09:00:57',15,1),
(369,'Entrada',53,0,53,'Conteo inicial de inventario','2025-01-12 11:37:17',76,5),
(370,'Entrada',188,39,227,'Recepción de mercancía','2026-03-09 18:51:02',124,1),
(371,'Entrada',10,0,10,'Conteo inicial de inventario','2025-01-06 09:32:04',9,19),
(372,'Entrada',24,0,24,'Conteo inicial de inventario','2025-01-02 18:56:19',66,13),
(373,'Entrada',14,73,87,'Reposición de stock','2026-03-12 14:14:16',141,16),
(374,'Salida',17,149,132,'Venta a cliente','2025-10-28 16:29:52',45,1),
(375,'Entrada',155,1,156,'Recepción de mercancía','2026-04-21 08:15:42',32,7),
(376,'Salida',16,70,54,'Salida por consumo interno','2025-05-30 19:14:36',16,6),
(377,'Entrada',10,46,56,'Recepción de mercancía','2026-06-05 11:05:40',100,2),
(378,'Salida',4,5,1,'Venta a cliente','2025-02-12 19:41:28',75,9),
(379,'Salida',5,5,0,'Salida por venta','2026-06-29 17:32:35',6,11),
(380,'Entrada',6,0,6,'Conteo inicial de inventario','2025-01-10 18:04:17',35,3),
(381,'Salida',3,129,126,'Salida por consumo interno','2026-01-28 15:13:59',102,7),
(382,'Salida',3,72,69,'Salida por consumo interno','2025-01-28 16:27:21',118,5),
(383,'Entrada',41,0,41,'Conteo inicial de inventario','2025-01-01 14:17:11',36,17),
(384,'Entrada',27,0,27,'Conteo inicial de inventario','2025-01-16 13:43:10',148,1),
(385,'Salida',8,119,111,'Salida por venta','2025-01-27 11:50:57',36,17),
(386,'Entrada',15,0,15,'Conteo inicial de inventario','2025-01-05 11:30:29',24,12),
(387,'Entrada',36,0,36,'Conteo inicial de inventario','2025-01-12 11:02:44',49,8),
(388,'Entrada',138,25,163,'Reabastecimiento por compra','2026-02-14 13:45:30',36,19),
(389,'Entrada',11,41,52,'Recepción de mercancía','2025-01-04 13:07:16',77,11),
(390,'Entrada',173,66,239,'Reabastecimiento por compra','2025-01-28 11:38:57',125,7),
(391,'Entrada',160,52,212,'Ingreso por compra a proveedor','2026-03-21 08:25:08',75,3),
(392,'Entrada',194,68,262,'Ingreso por compra a proveedor','2026-06-18 12:55:00',18,4),
(393,'Salida',14,149,135,'Salida por consumo interno','2026-04-15 10:01:01',27,15),
(394,'Entrada',64,12,76,'Reposición de stock','2025-04-23 08:23:31',111,3),
(395,'Salida',20,28,8,'Salida por venta','2025-11-02 13:23:27',35,17),
(396,'Entrada',21,0,21,'Conteo inicial de inventario','2025-01-05 12:47:34',48,17),
(397,'Devolucion',4,10,14,'Devolución por garantía','2026-03-26 08:04:01',74,4),
(398,'Salida',14,63,49,'Venta en mostrador','2026-07-27 11:40:58',23,12),
(399,'Entrada',115,65,180,'Ingreso por compra a proveedor','2026-09-05 13:13:12',67,4),
(400,'Entrada',36,67,103,'Recepción de mercancía','2026-03-03 08:25:06',36,10);
/*!40000 ALTER TABLE `movimiento_inventario` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `producto_promocion`
--

DROP TABLE IF EXISTS `producto_promocion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `producto_promocion` (
  `Id_Producto` int(11) NOT NULL,
  `Id_Promocion` int(11) NOT NULL,
  PRIMARY KEY (`Id_Producto`,`Id_Promocion`),
  KEY `Id_Promocion` (`Id_Promocion`),
  CONSTRAINT `producto_promocion_ibfk_1` FOREIGN KEY (`Id_Producto`) REFERENCES `productos` (`Id_Producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `producto_promocion_ibfk_2` FOREIGN KEY (`Id_Promocion`) REFERENCES `promociones` (`Id_Promocion`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `producto_promocion`
--

LOCK TABLES `producto_promocion` WRITE;
/*!40000 ALTER TABLE `producto_promocion` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `producto_promocion` VALUES
(8,1),
(130,1),
(104,2),
(119,2),
(123,3),
(75,4),
(68,5),
(90,5),
(140,5),
(106,7),
(113,7),
(130,7),
(140,7),
(26,8),
(93,8),
(31,9),
(56,9),
(60,9),
(68,9),
(93,9),
(1,10),
(19,10),
(24,10),
(66,10),
(118,10),
(2,11),
(28,11),
(42,11),
(14,12),
(46,12),
(56,12),
(3,13),
(4,13),
(34,14),
(44,15),
(33,16),
(55,16),
(91,16),
(143,16),
(17,17),
(102,17),
(67,18),
(139,18),
(16,19),
(81,19),
(95,19),
(101,19),
(135,19),
(2,20),
(119,20);
/*!40000 ALTER TABLE `producto_promocion` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `Id_Producto` int(11) NOT NULL AUTO_INCREMENT,
  `Codigo_Barras` varchar(50) DEFAULT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Descripcion` varchar(200) DEFAULT NULL,
  `Precio_Venta` decimal(10,2) DEFAULT NULL,
  `Precio_Compra` decimal(10,2) DEFAULT NULL,
  `Stock_Minimo` int(11) DEFAULT 5,
  `Stock_Maximo` int(11) DEFAULT 100,
  `Fecha_Registro` date DEFAULT curdate(),
  `Estado` tinyint(1) DEFAULT 1,
  `Id_Categoria` int(11) NOT NULL,
  `Id_Unidad` int(11) NOT NULL,
  `Id_Proveedor` int(11) DEFAULT NULL,
  `Id_Marca` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id_Producto`),
  UNIQUE KEY `Codigo_Barras` (`Codigo_Barras`),
  KEY `Id_Unidad` (`Id_Unidad`),
  KEY `Id_Proveedor` (`Id_Proveedor`),
  KEY `idx_productos_categoria` (`Id_Categoria`),
  KEY `idx_productos_estado` (`Estado`),
  KEY `fk_productos_marca` (`Id_Marca`),
  KEY `idx_productos_nombre` (`Nombre`),
  CONSTRAINT `fk_productos_marca` FOREIGN KEY (`Id_Marca`) REFERENCES `marcas` (`Id_Marca`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`Id_Categoria`) REFERENCES `categoria` (`Id_Categoria`) ON UPDATE CASCADE,
  CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`Id_Unidad`) REFERENCES `unidad_medida` (`Id_Unidad`) ON UPDATE CASCADE,
  CONSTRAINT `productos_ibfk_3` FOREIGN KEY (`Id_Proveedor`) REFERENCES `proveedor` (`Id_Proveedor`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `CONSTRAINT_1` CHECK (`Precio_Venta` >= 0 and `Precio_Compra` >= 0),
  CONSTRAINT `CONSTRAINT_2` CHECK (`Stock_Minimo` <= `Stock_Maximo`),
  CONSTRAINT `chk_prod_precio_venta` CHECK (`Precio_Venta` is null or `Precio_Venta` > 0),
  CONSTRAINT `chk_prod_precio_compra` CHECK (`Precio_Compra` is null or `Precio_Compra` >= 0),
  CONSTRAINT `chk_prod_estado` CHECK (`Estado` in (0,1)),
  CONSTRAINT `chk_prod_stock` CHECK (`Stock_Minimo` is null or `Stock_Minimo` >= 0),
  CONSTRAINT `chk_prod_stock_maximo` CHECK (`Stock_Maximo` is null or `Stock_Minimo` is null or `Stock_Maximo` >= `Stock_Minimo`),
  CONSTRAINT `chk_prod_texto` CHECK (trim(`Nombre`) <> '')
) ENGINE=InnoDB AUTO_INCREMENT=318 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `productos` VALUES
(1,'750100000000','Charola para Pintura','Charola para Pintura - Categoría 4',211.19,167.13,10,80,'2025-12-20',1,4,1,5,NULL),
(2,'7502784537981','Foco Ahorrador Espiral 20W','Foco Ahorrador Espiral 20W - Categoría 14',1634.12,1130.45,5,40,'2025-04-22',1,14,1,4,NULL),
(3,'7503311595947','Desarmador de Cruz #2','Desarmador de Cruz #2 - Categoría 1',1239.15,951.18,5,75,'2025-04-30',1,1,1,9,NULL),
(4,'7509314894275','Perno Hexagonal 3/8x2\" (Caja 50)','Perno Hexagonal 3/8x2\" (Caja 50) - Categoría 3',1801.89,1359.20,5,50,'2025-08-31',1,3,5,9,NULL),
(5,'7504133648196','Lámpara de Mano Recargable','Lámpara de Mano Recargable - Categoría 14',3451.59,2095.47,10,120,'2025-05-26',1,14,1,1,NULL),
(6,'7501302743100','Cloro Industrial 1L','Cloro Industrial 1L - Categoría 15',1877.16,1195.50,5,75,'2025-11-03',1,15,3,15,NULL),
(7,'7504004954167','Cúter Profesional 18mm','Cúter Profesional 18mm - Categoría 1',2464.58,1540.59,15,150,'2025-01-26',1,1,1,26,NULL),
(8,'7505554002894','Tuerca Hexagonal 1/4\" (Caja 100)','Tuerca Hexagonal 1/4\" (Caja 100) - Categoría 3',3838.78,2262.43,5,50,'2025-07-22',1,3,5,24,NULL),
(9,'7508275716844','Foco LED 9W','Foco LED 9W - Categoría 6',764.55,437.13,10,120,'2025-03-07',1,6,1,15,NULL),
(10,'750100000001','Cinta Aislante','Cinta Aislante - Categoría 6',1639.61,1254.26,5,60,'2025-12-07',1,6,1,26,NULL),
(11,'7506077328932','Calibrador Vernier','Calibrador Vernier - Categoría 12',1707.44,978.31,5,60,'2025-08-04',1,12,1,15,NULL),
(12,'7504804995586','Llave de Paso 1/2\"','Llave de Paso 1/2\" - Categoría 5',1120.84,763.90,10,120,'2025-02-19',1,5,1,10,NULL),
(13,'7504786723780','Tubo PVC Hidráulico 1/2\" 6m','Tubo PVC Hidráulico 1/2\" 6m - Categoría 5',2679.53,1974.94,5,50,'2025-09-25',1,5,1,13,NULL),
(14,'7509529980028','Martillo de Goma','Martillo de Goma - Categoría 1',3175.79,2106.38,5,40,'2026-03-23',1,1,1,25,NULL),
(15,'7508007537041','Apagador Sencillo','Apagador Sencillo - Categoría 6',388.07,252.75,10,150,'2025-02-22',1,6,1,15,NULL),
(16,'7502233703564','Aspersor Oscilante','Aspersor Oscilante - Categoría 8',2147.00,1411.24,3,24,'2026-05-14',1,8,1,3,NULL),
(17,'7502540640961','Pintura Vinílica Blanca 4L','Pintura Vinílica Blanca 4L - Categoría 4',2262.18,1672.23,15,120,'2026-03-13',1,4,3,6,NULL),
(18,'7502530756453','Flexómetro 5m','Flexómetro 5m - Categoría 12',310.73,219.52,3,24,'2025-11-26',1,12,1,21,NULL),
(19,'7501908357275','Taladro Percutor 1/2\" 750W','Taladro Percutor 1/2\" 750W - Categoría 2',1079.30,823.00,10,80,'2025-01-12',1,2,1,3,NULL),
(20,'750100000002','Lentes de Seguridad','Lentes de Seguridad - Categoría 9',175.05,120.85,3,24,'2025-11-08',0,9,1,20,NULL),
(21,'7508351627246','Foco LED 12W','Foco LED 12W - Categoría 6',3462.88,2185.97,15,150,'2025-09-23',1,6,1,20,NULL),
(22,'7504170543077','Taquete de Plástico #8 (Caja 100)','Taquete de Plástico #8 (Caja 100) - Categoría 3',1899.02,1235.81,15,225,'2025-12-03',1,3,5,19,NULL),
(23,'7503484983995','Compresor de Aire 1HP','Compresor de Aire 1HP - Categoría 2',1747.66,1114.48,10,80,'2025-06-08',1,2,1,29,NULL),
(24,'7507547276750','Tornillo Autorroscante 1\" (Caja 100)','Tornillo Autorroscante 1\" (Caja 100) - Categoría 3',2512.59,1923.64,10,80,'2026-04-18',1,3,5,1,NULL),
(25,'7501369558506','Impermeabilizante Acrílico 19L','Impermeabilizante Acrílico 19L - Categoría 4',961.81,589.48,3,30,'2025-11-17',1,4,3,13,NULL),
(26,'7503821321366','Tee PVC 1/2\"','Tee PVC 1/2\" - Categoría 5',63.53,42.03,3,24,'2026-03-19',0,5,1,3,NULL),
(27,'7506672781625','Pintura en Spray Negro Mate','Pintura en Spray Negro Mate - Categoría 4',2400.08,1401.75,10,80,'2026-01-01',1,4,3,19,NULL),
(28,'7501252966666','Manguera para Jardín 15m','Manguera para Jardín 15m - Categoría 8',1050.46,718.13,3,45,'2025-04-01',0,8,1,1,NULL),
(29,'7501139133719','Barniz Marino 1L','Barniz Marino 1L - Categoría 4',1481.81,1076.62,10,120,'2025-01-27',1,4,3,10,NULL),
(30,'750100000003','Reflector LED 50W','Reflector LED 50W - Categoría 14',3403.76,2349.29,5,60,'2025-03-25',1,14,1,26,NULL),
(31,'7504961331390','Manguera Flexible para WC','Manguera Flexible para WC - Categoría 5',2819.85,1853.75,10,120,'2026-01-04',1,5,1,10,NULL),
(32,'7501745267574','Rotomartillo Demoledor 1500W','Rotomartillo Demoledor 1500W - Categoría 2',1872.49,1246.98,5,40,'2026-04-01',1,2,1,16,NULL),
(33,'7506670967626','Centro de Carga 8 Espacios','Centro de Carga 8 Espacios - Categoría 6',2710.26,1682.69,5,75,'2026-02-21',1,6,1,11,NULL),
(34,'7504978245719','Tornillo para Madera 2\" (Caja 100)','Tornillo para Madera 2\" (Caja 100) - Categoría 3',490.38,284.16,10,150,'2026-05-09',1,3,5,25,NULL),
(35,'7502374416237','Candado de Bronce 40mm','Candado de Bronce 40mm - Categoría 7',108.05,66.17,5,60,'2025-07-14',1,7,1,28,NULL),
(36,'7507683757414','Chapa para Puerta Interior','Chapa para Puerta Interior - Categoría 7',1960.34,1301.39,10,80,'2025-10-04',1,7,1,8,NULL),
(37,'7505254458207','Martillo de Bola 8 oz','Martillo de Bola 8 oz - Categoría 1',1239.13,768.60,3,30,'2025-03-04',1,1,1,28,NULL),
(38,'7505687669776','Cerrojo de Sobreponer','Cerrojo de Sobreponer - Categoría 7',1849.03,1139.34,5,50,'2025-08-24',0,7,1,26,NULL),
(39,'7505255950560','Rastrillo de Jardín','Rastrillo de Jardín - Categoría 8',1026.51,668.01,10,80,'2025-06-20',1,8,1,10,NULL),
(40,'750100000004','Pegamento Epóxico','Pegamento Epóxico - Categoría 10',1381.70,937.53,5,40,'2026-05-08',1,10,1,25,NULL),
(41,'7509563414931','Martillo de Uña 16 oz','Martillo de Uña 16 oz - Categoría 1',377.98,287.70,10,120,'2025-12-23',1,1,1,13,NULL),
(42,'7503492861360','Sellador para Concreto 4L','Sellador para Concreto 4L - Categoría 4',2379.93,1491.68,10,120,'2025-11-19',1,4,3,23,NULL),
(43,'7509353021300','Desarmador Plano 1/4x6\"','Desarmador Plano 1/4x6\" - Categoría 1',532.25,387.89,5,60,'2025-04-05',1,1,1,27,NULL),
(44,'7507527395548','Chaleco Reflejante','Chaleco Reflejante - Categoría 9',2219.71,1766.51,5,60,'2025-06-14',1,9,1,14,NULL),
(45,'7506529046749','Router para Madera','Router para Madera - Categoría 2',2988.39,2190.20,5,60,'2025-08-24',1,2,1,3,NULL),
(46,'7502405677173','Atornillador Eléctrico Inalámbrico','Atornillador Eléctrico Inalámbrico - Categoría 2',3950.87,2339.83,15,180,'2025-05-08',1,2,1,1,NULL),
(47,'7504587222980','Adhesivo de Contacto 1L','Adhesivo de Contacto 1L - Categoría 10',1037.73,718.09,3,36,'2025-03-01',0,10,1,13,NULL),
(48,'7502067712266','Esmalte Alquidálico 1L','Esmalte Alquidálico 1L - Categoría 4',797.86,606.33,3,45,'2025-12-23',1,4,3,17,NULL),
(49,'7508839311511','Carretilla de Jardín','Carretilla de Jardín - Categoría 8',3312.79,2002.92,10,80,'2025-08-21',1,8,1,29,NULL),
(50,'750100000005','Nivel Láser','Nivel Láser - Categoría 12',2848.71,1649.21,3,45,'2025-11-16',1,12,1,30,NULL),
(51,'7502261902852','Jabón Líquido para Manos 1L','Jabón Líquido para Manos 1L - Categoría 15',1298.68,890.12,10,80,'2025-02-18',1,15,3,13,NULL),
(52,'7505406266787','Apagador de Escalera','Apagador de Escalera - Categoría 6',1002.00,1083.01,5,40,'2025-12-26',0,7,9,6,4),
(53,'7506053253526','Escoba de Plástico','Escoba de Plástico - Categoría 15',774.16,615.23,5,75,'2025-11-29',1,15,1,14,NULL),
(54,'7508148520088','Candado de Acero 50mm','Candado de Acero 50mm - Categoría 7',1795.99,1372.35,15,120,'2026-02-28',0,7,1,23,NULL),
(55,'7506269507001','Pasador de Seguridad','Pasador de Seguridad - Categoría 7',2204.09,1405.31,10,120,'2025-05-23',1,7,1,11,NULL),
(56,'7509195177360','Grapa Galvanizada (Caja 1kg)','Grapa Galvanizada (Caja 1kg) - Categoría 3',3151.66,2186.41,10,120,'2026-01-29',1,3,5,29,NULL),
(57,'7503926358808','Multímetro Digital','Multímetro Digital - Categoría 12',2303.84,1604.14,10,150,'2025-01-15',1,12,1,6,NULL),
(58,'7505432262668','Disco de Desbaste 4 1/2\"','Disco de Desbaste 4 1/2\" - Categoría 13',1733.61,1385.92,10,120,'2025-04-23',1,13,1,17,NULL),
(59,'7503250555414','Cemento Gris 50kg','Cemento Gris 50kg - Categoría 11',3213.52,2218.58,10,100,'2025-06-04',1,11,1,15,NULL),
(60,'750100000006','Banda para Lijadora','Banda para Lijadora - Categoría 13',330.74,241.11,5,40,'2025-04-06',1,13,1,30,NULL),
(61,'7502506156119','Contacto Doble Polarizado','Contacto Doble Polarizado - Categoría 6',1242.46,783.45,10,120,'2025-01-13',1,6,1,2,NULL),
(62,'7501130386114','Pistola de Calor 2000W','Pistola de Calor 2000W - Categoría 2',2582.80,2065.22,5,40,'2025-08-20',1,2,1,1,NULL),
(63,'7502442582082','Cemento para PVC 1/4L','Cemento para PVC 1/4L - Categoría 5',3174.79,2236.16,10,150,'2025-07-20',1,5,3,27,NULL),
(64,'7506479829094','Coples PVC 1/2\"','Coples PVC 1/2\" - Categoría 5',1062.91,709.61,3,30,'2025-12-08',1,5,1,7,NULL),
(65,'7506426028487','Pinzas de Corte Diagonal 8\"','Pinzas de Corte Diagonal 8\" - Categoría 1',3300.88,2046.28,10,150,'2025-02-04',1,1,1,15,NULL),
(66,'7504549897138','Tubo PVC Hidráulico 3/4\" 6m','Tubo PVC Hidráulico 3/4\" 6m - Categoría 5',477.24,307.17,3,45,'2026-03-01',1,5,1,30,NULL),
(67,'7501104989571','Brocha 3\"','Brocha 3\" - Categoría 4',1699.94,1019.81,10,100,'2026-01-12',1,4,1,6,NULL),
(68,'7503939393063','Malla Electrosoldada 6x6','Malla Electrosoldada 6x6 - Categoría 11',351.33,206.68,10,80,'2025-01-16',1,11,1,25,NULL),
(69,'7508626000926','Tornillo para Tablaroca (Caja 100)','Tornillo para Tablaroca (Caja 100) - Categoría 3',2518.45,1844.77,3,45,'2025-03-04',1,3,5,13,NULL),
(70,'750100000007','Alicate Universal 8\"','Alicate Universal 8\" - Categoría 1',965.09,578.89,15,150,'2025-10-08',0,1,1,8,NULL),
(71,'7505164698004','Lámpara LED de Techo 18W','Lámpara LED de Techo 18W - Categoría 14',2479.39,1947.11,5,75,'2025-04-29',0,14,1,7,NULL),
(72,'7503159727424','Tira LED 5m','Tira LED 5m - Categoría 14',3022.97,2147.59,10,100,'2026-05-29',1,14,1,17,NULL),
(73,'7509111580696','Brocha 2\"','Brocha 2\" - Categoría 4',2056.34,1278.27,5,40,'2025-11-01',1,4,1,8,NULL),
(74,'7507406668819','Lija para Metal #80 (Caja 10)','Lija para Metal #80 (Caja 10) - Categoría 13',1506.70,915.70,3,24,'2026-04-02',0,13,5,5,NULL),
(75,'7509734999650','Silicón para Vidrio','Silicón para Vidrio - Categoría 10',1123.44,658.13,5,75,'2026-03-18',1,10,1,10,NULL),
(76,'7503533385514','Tornillo Hexagonal 5/16\" (Caja 50)','Tornillo Hexagonal 5/16\" (Caja 50) - Categoría 3',1931.37,1223.99,15,120,'2026-03-05',1,3,5,29,NULL),
(77,'7506792077757','Cincel para Concreto 1/2\"','Cincel para Concreto 1/2\" - Categoría 1',350.17,262.05,10,120,'2025-08-14',1,1,1,17,NULL),
(78,'7503942723411','Grava (Bolsa 40kg)','Grava (Bolsa 40kg) - Categoría 11',3498.03,2199.07,3,24,'2025-06-10',1,11,10,13,NULL),
(79,'7508315163339','Varilla Corrugada 3/8\" 12m','Varilla Corrugada 3/8\" 12m - Categoría 11',3041.48,1911.25,5,40,'2025-02-18',0,11,1,5,NULL),
(80,'750100000008','Codo PVC 1/2\" 90°','Codo PVC 1/2\" 90° - Categoría 5',1837.46,1422.77,3,36,'2025-07-30',1,5,1,27,NULL),
(81,'7508848253850','Cable THW Calibre 10 (Rollo 100m)','Cable THW Calibre 10 (Rollo 100m) - Categoría 6',3676.81,2352.46,15,120,'2025-01-13',1,6,1,18,NULL),
(82,'7509663562499','Tijeras para Podar','Tijeras para Podar - Categoría 8',232.18,145.72,5,40,'2026-03-09',1,8,1,1,NULL),
(83,'7504698652048','Pintura Vinílica Blanca 19L','Pintura Vinílica Blanca 19L - Categoría 4',2872.66,2193.00,5,40,'2025-03-27',1,4,3,15,NULL),
(84,'7505525068415','Bisagra de Piso 3\"','Bisagra de Piso 3\" - Categoría 7',2568.55,1557.23,3,24,'2025-02-09',1,7,7,6,NULL),
(85,'7503730026306','Llave de Nariz 1/2\"','Llave de Nariz 1/2\" - Categoría 5',2271.54,1620.71,10,120,'2025-08-08',1,5,1,26,NULL),
(86,'7506774348000','Guantes de Jardinería','Guantes de Jardinería - Categoría 8',2493.81,1575.76,5,75,'2026-01-21',1,8,1,27,NULL),
(87,'7506487419902','Jalador para Piso','Jalador para Piso - Categoría 15',1821.16,1177.63,10,120,'2026-02-16',1,15,1,30,NULL),
(88,'7509487814627','Arnés de Seguridad','Arnés de Seguridad - Categoría 9',336.06,255.75,10,120,'2025-02-16',1,9,1,10,NULL),
(89,'7502463854004','Pala Recta para Jardín','Pala Recta para Jardín - Categoría 8',3227.36,2184.81,10,80,'2026-04-06',1,8,1,11,NULL),
(90,'750100000009','Fertilizante Multiusos 1kg','Fertilizante Multiusos 1kg - Categoría 8',2688.56,2023.87,5,40,'2025-12-01',1,8,10,10,NULL),
(91,'7507130875418','Cilindro para Chapa','Cilindro para Chapa - Categoría 7',3063.26,2422.08,5,50,'2025-01-23',1,7,1,27,NULL),
(92,'7503608006020','Pegamento Blanco 1L','Pegamento Blanco 1L - Categoría 10',1957.32,1239.94,10,80,'2025-08-08',0,10,1,4,NULL),
(93,'7505021532489','Lija para Madera #120 (Caja 10)','Lija para Madera #120 (Caja 10) - Categoría 13',267.86,167.36,5,75,'2025-01-01',1,13,5,29,NULL),
(94,'7503901449055','Chapa para Puerta Exterior','Chapa para Puerta Exterior - Categoría 7',110.77,71.67,3,36,'2025-11-20',1,7,1,3,NULL),
(95,'7506583852861','Cable THW Calibre 12 (Rollo 100m)','Cable THW Calibre 12 (Rollo 100m) - Categoría 6',149.20,107.49,3,36,'2025-03-25',1,6,1,14,NULL),
(96,'7502969046109','Desengrasante Industrial 1L','Desengrasante Industrial 1L - Categoría 15',1430.24,875.90,15,225,'2026-03-31',1,15,3,11,NULL),
(97,'1263112313','Ancla de Expansión 1/4\" (Caja 50)','Ancla de Expansión 1/4\" (Caja 50) - Categoría 3',2488.22,2112.18,12,50,'2025-12-06',1,3,4,9,4),
(98,'7502372110355','Clavo de Acero 2\" (Caja 1kg)','Clavo de Acero 2\" (Caja 1kg) - Categoría 3',1481.33,1082.72,10,100,'2025-06-17',1,3,5,27,NULL),
(99,'7506095589288','Esmeriladora Angular 4 1/2\"','Esmeriladora Angular 4 1/2\" - Categoría 2',3484.31,2393.55,3,24,'2025-11-06',1,2,1,23,NULL),
(100,'750100000010','Botas de Seguridad con Casquillo','Botas de Seguridad con Casquillo - Categoría 9',526.72,325.55,10,80,'2025-09-17',1,9,1,22,NULL),
(101,'7503849721586','Yeso para Construcción 20kg','Yeso para Construcción 20kg - Categoría 11',101.54,69.08,15,150,'2025-12-21',1,11,1,8,NULL),
(102,'7502398853463','Disco de Corte para Metal 7\"','Disco de Corte para Metal 7\" - Categoría 13',1715.43,1107.05,10,150,'2026-02-23',1,13,1,16,NULL),
(103,'7501482609515','Multicontacto 6 Salidas','Multicontacto 6 Salidas - Categoría 6',1057.05,664.13,10,80,'2025-01-03',0,6,1,10,NULL),
(104,'7507272996077','Pinzas de Presión 10\"','Pinzas de Presión 10\" - Categoría 1',378.81,257.50,5,75,'2025-09-19',1,1,1,22,NULL),
(105,'7507047536149','Llave Ajustable 12\"','Llave Ajustable 12\" - Categoría 1',3339.10,2372.69,10,150,'2025-02-24',1,1,1,22,NULL),
(106,'7505283417744','Escuadra de Acero 12\"','Escuadra de Acero 12\" - Categoría 12',3797.67,2199.07,5,40,'2025-03-11',1,12,1,3,NULL),
(107,'7505684380142','Caladora Eléctrica 650W','Caladora Eléctrica 650W - Categoría 2',2292.86,1337.48,10,150,'2025-02-18',1,2,1,22,NULL),
(108,'7506261891335','Cubeta de Plástico 19L','Cubeta de Plástico 19L - Categoría 15',301.75,218.27,5,60,'2025-05-24',1,15,1,28,NULL),
(109,'7507048871061','Pija Galvanizada 1 1/2\" (Caja 100)','Pija Galvanizada 1 1/2\" (Caja 100) - Categoría 3',1209.73,838.73,15,150,'2025-06-01',1,3,5,7,NULL),
(110,'750100000011','Flotador para Tinaco','Flotador para Tinaco - Categoría 5',2477.36,1956.82,3,24,'2026-04-10',1,5,1,26,NULL),
(111,'7507968048084','Pastilla Termomagnética 20A','Pastilla Termomagnética 20A - Categoría 6',4087.73,2367.60,3,30,'2026-03-10',1,6,1,16,NULL),
(112,'7506022147552','Thinner Estándar 1L','Thinner Estándar 1L - Categoría 4',3490.19,2129.87,5,75,'2026-04-18',1,4,3,23,NULL),
(113,'7507102794674','Tapones Auditivos (Par)','Tapones Auditivos (Par) - Categoría 9',1329.28,861.59,15,120,'2025-01-21',1,9,1,30,NULL),
(114,'7505739516058','Llave Stilson 14\"','Llave Stilson 14\" - Categoría 1',1883.39,1336.46,5,60,'2026-03-18',1,1,1,1,NULL),
(115,'7504501206738','Regadera para Baño','Regadera para Baño - Categoría 5',3427.94,2042.77,3,45,'2025-03-02',1,5,1,9,NULL),
(116,'7508886817440','Cinta Canela 2\"','Cinta Canela 2\" - Categoría 10',1672.42,1037.66,15,150,'2025-07-06',1,10,6,10,NULL),
(117,'7502673017006','Cinta Teflón','Cinta Teflón - Categoría 5',1839.70,1132.35,15,150,'2025-11-11',1,5,1,19,NULL),
(118,'7504482554108','Sierra Circular 7 1/4\"','Sierra Circular 7 1/4\" - Categoría 2',817.29,623.36,15,225,'2025-07-29',1,2,1,7,NULL),
(119,'7505896126187','Serrucho para Madera 20\"','Serrucho para Madera 20\" - Categoría 1',2650.67,1591.39,5,60,'2025-10-06',1,1,1,7,NULL),
(120,'750100000012','Rotomartillo SDS Plus 800W','Rotomartillo SDS Plus 800W - Categoría 2',3240.39,2333.54,5,75,'2025-03-28',1,2,1,17,NULL),
(121,'7501272543150','Rodillo para Pintura 9\"','Rodillo para Pintura 9\" - Categoría 4',2334.32,1658.74,15,180,'2025-03-25',1,4,1,12,NULL),
(122,'7508188326406','Segueta para Metal','Segueta para Metal - Categoría 1',2322.28,1682.76,5,60,'2025-10-19',1,1,1,19,NULL),
(123,'7503316422964','Cinta Doble Cara','Cinta Doble Cara - Categoría 10',2347.21,1494.46,3,30,'2025-11-19',1,10,6,19,NULL),
(124,'7504551376159','Nivel de Burbuja 60cm','Nivel de Burbuja 60cm - Categoría 1',3473.79,2436.48,5,60,'2026-04-15',1,1,1,10,NULL),
(125,'7503407003322','Sellador de Poliuretano','Sellador de Poliuretano - Categoría 10',3179.02,2205.78,5,40,'2026-01-20',1,10,1,7,NULL),
(126,'7503831022790','Llave Ajustable 10\"','Llave Ajustable 10\" - Categoría 1',1946.38,1264.81,10,100,'2025-05-13',1,1,1,23,NULL),
(127,'7505378426603','Pulidora Orbital 6\"','Pulidora Orbital 6\" - Categoría 2',2663.16,2083.51,10,80,'2025-10-10',1,2,1,23,NULL),
(128,'7503330026074','Mascarilla Antipolvo','Mascarilla Antipolvo - Categoría 9',2918.45,2141.48,15,180,'2026-05-24',1,9,1,26,NULL),
(129,'7505050795239','Block de Concreto 15x20x40','Block de Concreto 15x20x40 - Categoría 11',575.32,368.39,10,100,'2025-04-12',1,11,1,14,NULL),
(130,'750100000013','Guantes de Carnaza','Guantes de Carnaza - Categoría 9',468.07,300.11,3,45,'2025-06-20',1,9,7,25,NULL),
(131,'7501786760942','Arena de Río (Bolsa 40kg)','Arena de Río (Bolsa 40kg) - Categoría 11',2822.79,1795.31,5,40,'2025-04-11',1,11,10,6,NULL),
(132,'7503580866325','Lámpara de Emergencia','Lámpara de Emergencia - Categoría 14',924.72,571.62,10,150,'2025-12-21',1,14,1,21,NULL),
(133,'7503264672371','Juego de Desarmadores 6 pzas','Juego de Desarmadores 6 pzas - Categoría 1',1492.18,1065.30,3,30,'2026-04-06',1,1,8,22,NULL),
(134,'7509668770700','Disco de Diamante 4\"','Disco de Diamante 4\" - Categoría 13',1387.99,951.38,5,60,'2026-03-30',1,13,1,23,NULL),
(135,'7507050135064','Ladrillo Rojo Recocido','Ladrillo Rojo Recocido - Categoría 11',3045.71,2287.92,15,225,'2025-08-20',1,11,1,16,NULL),
(136,'7509564483911','Taladro Inalámbrico 20V','Taladro Inalámbrico 20V - Categoría 2',3355.13,2092.13,10,150,'2025-11-19',1,2,1,14,NULL),
(137,'7509076158959','Casco de Seguridad','Casco de Seguridad - Categoría 9',2183.84,1384.56,5,75,'2026-04-16',1,9,1,26,NULL),
(138,'7503609502981','Flexómetro 8m','Flexómetro 8m - Categoría 12',1647.53,1177.54,5,75,'2026-05-08',1,12,1,29,NULL),
(139,'7505920943322','Rondana Plana 1/4\" (Caja 100)','Rondana Plana 1/4\" (Caja 100) - Categoría 3',1012.75,789.30,3,30,'2025-07-31',0,3,5,6,NULL),
(140,'750100000014','Extensión Eléctrica 5m','Extensión Eléctrica 5m - Categoría 6',3519.14,2018.23,5,75,'2026-01-12',1,6,1,28,NULL),
(141,'7508638548674','Juego de Llaves Allen 9 pzas','Juego de Llaves Allen 9 pzas - Categoría 1',325.83,257.24,5,75,'2025-11-04',1,1,8,13,NULL),
(142,'7506462581651','Pala Recta para Jardín (2)','Pala Recta para Jardín (2) - Categoría 8',481.89,347.45,10,80,'2025-09-13',1,8,1,13,NULL),
(143,'7502519761040','Carretilla de mecanico','Carretilla de Jardín (2) - Categoría 8',10000.00,1990.67,100,120,'2025-07-11',1,8,5,13,8),
(144,'7507262424331','Grapa Galvanizada (Caja 1kg) (2)','Grapa Galvanizada (Caja 1kg) (2) - Categoría 3',2469.21,1706.05,10,80,'2025-08-16',1,3,5,14,NULL),
(145,'7502644511332','Esmeriladora Angular 4 1/2\" (2)','Esmeriladora Angular 4 1/2\" (2) - Categoría 2',786.22,565.81,10,100,'2025-03-26',1,2,1,28,NULL),
(146,'7505789701653','Botas de Seguridad con Casquillo (2)','Botas de Seguridad con Casquillo (2) - Categoría 9',1844.47,1181.06,10,150,'2026-01-02',1,9,1,23,NULL),
(147,'7501111659297','Disco de Desbaste 4 1/2\" (2)','Disco de Desbaste 4 1/2\" (2) - Categoría 13',984.21,730.31,10,80,'2026-04-19',1,13,1,28,NULL),
(148,'7508032890101','Aspersor Oscilante (2)','Aspersor Oscilante (2) - Categoría 8',1356.66,885.71,5,75,'2026-04-28',1,8,1,2,NULL),
(149,'7508763061452','Sierra Circular 7 1/4\" (2)','Sierra Circular 7 1/4\" (2) - Categoría 2',3566.49,2408.20,10,80,'2025-04-11',1,2,1,22,NULL),
(150,'750100000015','Block de Concreto 15x20x40 (2)','Block de Concreto 15x20x40 (2) - Categoría 11',688.05,492.60,3,24,'2025-07-04',1,11,1,25,NULL),
(151,'7676767677676','GambitoPremiun',NULL,10000.00,NULL,10,100,'2026-10-12',1,2,3,22,5),
(152,'12345667','tenazas',NULL,NULL,NULL,2,100,'2026-10-12',1,7,5,11,10),
(243,'13123132','nery',NULL,NULL,NULL,0,100,'2026-10-13',1,14,3,33,5),
(244,'12345678','pinzas B',NULL,1000.00,NULL,33,100,'2026-10-13',1,5,6,7,2),
(245,'123','Ventilador',NULL,223.23,NULL,4,100,'2026-10-13',1,4,1,27,9);
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `promociones`
--

DROP TABLE IF EXISTS `promociones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `promociones` (
  `Id_Promocion` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) NOT NULL,
  `Descripcion` varchar(200) DEFAULT NULL,
  `Descuento` decimal(5,2) NOT NULL,
  `Fecha_Inicio` date NOT NULL,
  `Fecha_Fin` date NOT NULL,
  `Estado` tinyint(1) DEFAULT 1,
  `Id_Empleado` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id_Promocion`),
  KEY `Id_Empleado` (`Id_Empleado`),
  CONSTRAINT `promociones_ibfk_1` FOREIGN KEY (`Id_Empleado`) REFERENCES `empleados` (`Id_Empleado`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `CONSTRAINT_1` CHECK (`Fecha_Fin` >= `Fecha_Inicio`),
  CONSTRAINT `CONSTRAINT_2` CHECK (`Descuento` > 0 and `Descuento` <= 100)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promociones`
--

LOCK TABLES `promociones` WRITE;
/*!40000 ALTER TABLE `promociones` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `promociones` VALUES
(1,'Descuento de Temporada Ferretera','Promoción especial: 15.26% de descuento por tiempo limitado',15.26,'2025-10-03','2025-11-16',0,11),
(2,'Promo Herramientas Eléctricas','Promoción especial: 23.79% de descuento por tiempo limitado',23.79,'2025-01-29','2025-03-06',0,6),
(3,'Oferta Tornillería al Mayoreo','Promoción especial: 24.16% de descuento por tiempo limitado',24.16,'2025-11-19','2025-12-04',0,1),
(4,'Descuento en Pinturas','Promoción especial: 21.52% de descuento por tiempo limitado',21.52,'2026-06-21','2026-07-10',0,17),
(5,'Promo Día del Albañil','Promoción especial: 15.33% de descuento por tiempo limitado',15.33,'2026-05-27','2026-06-05',0,2),
(6,'Rebaja en Plomería','Promoción especial: 11.83% de descuento por tiempo limitado',11.83,'2025-11-29','2026-01-12',0,11),
(7,'Oferta Electricidad Básica','Promoción especial: 7.9% de descuento por tiempo limitado',7.90,'2025-07-06','2025-07-27',0,19),
(8,'Descuento Cerrajería','Promoción especial: 23.32% de descuento por tiempo limitado',23.32,'2025-03-05','2025-03-15',0,18),
(9,'Promo Jardinería de Primavera','Promoción especial: 23.51% de descuento por tiempo limitado',23.51,'2025-10-29','2025-11-11',0,8),
(10,'Oferta Seguridad Industrial','Promoción especial: 9.17% de descuento por tiempo limitado',9.17,'2025-05-14','2025-06-05',0,1),
(11,'Descuento Adhesivos y Selladores','Promoción especial: 11.21% de descuento por tiempo limitado',11.21,'2026-02-05','2026-03-17',0,18),
(12,'Promo Materiales de Construcción','Promoción especial: 25.15% de descuento por tiempo limitado',25.15,'2025-01-26','2025-03-10',0,7),
(13,'Oferta Instrumentos de Medición','Promoción especial: 22.28% de descuento por tiempo limitado',22.28,'2025-01-14','2025-02-15',0,12),
(14,'Descuento Abrasivos','Promoción especial: 21.7% de descuento por tiempo limitado',21.70,'2025-12-06','2026-01-15',0,2),
(15,'Promo Iluminación LED','Promoción especial: 5.6% de descuento por tiempo limitado',5.60,'2026-02-09','2026-02-19',0,14),
(16,'Oferta Limpieza Industrial','Promoción especial: 14.53% de descuento por tiempo limitado',14.53,'2026-03-20','2026-04-01',0,10),
(17,'Venta Nocturna Ferredog','Promoción especial: 9.6% de descuento por tiempo limitado',9.60,'2026-01-26','2026-02-10',0,10),
(18,'Promo Fin de Mes','Promoción especial: 10.21% de descuento por tiempo limitado',10.21,'2025-01-03','2025-02-03',0,4),
(19,'Descuento Cliente Frecuente','Promoción especial: 22.31% de descuento por tiempo limitado',22.31,'2025-04-28','2025-05-14',0,7),
(20,'Oferta Relámpago Ferretera','Promoción especial: 5.82% de descuento por tiempo limitado',5.82,'2026-05-21','2026-06-06',0,12);
/*!40000 ALTER TABLE `promociones` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `proveedor`
--

DROP TABLE IF EXISTS `proveedor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `proveedor` (
  `Id_Proveedor` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) NOT NULL,
  `Contacto` varchar(100) DEFAULT NULL,
  `Telefono` varchar(15) DEFAULT NULL,
  `Correo` varchar(50) DEFAULT NULL,
  `Direccion` varchar(200) DEFAULT NULL,
  `RFC` varchar(10) DEFAULT NULL,
  `Tipo` varchar(100) DEFAULT NULL,
  `Estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`Id_Proveedor`),
  UNIQUE KEY `uq_proveedor_nombre` (`Nombre`),
  UNIQUE KEY `uq_proveedor_correo` (`Correo`),
  KEY `idx_proveedor_contacto` (`Contacto`),
  CONSTRAINT `chk_prov_estado` CHECK (`Estado` in (0,1)),
  CONSTRAINT `chk_prov_texto` CHECK (trim(`Nombre`) <> '')
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedor`
--

LOCK TABLES `proveedor` WRITE;
/*!40000 ALTER TABLE `proveedor` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `proveedor` VALUES
(1,'Tornillos y Fijaciones del Sur','Nancy Jiménez Luna','9515537157','contacto@tornillos.com','Av. Juárez #36, Col. Centro, Oaxaca de Juárez, Oax.','ZOA374735','Nacional',1),
(2,'Distribuidora Ferretera Oaxaca','Guadalupe Villanueva Ruiz','9511937991','contacto@distribuidora.com','Av. Constitución #980, Col. Centro, Oaxaca de Juárez, Oax.','BZN799162','Nacional',1),
(3,'Pinturas y Recubrimientos MX','Angélica Torres Gómez','9514158036','contacto@pinturas.com','Calle Allende #839, Col. Centro, Oaxaca de Juárez, Oax.','EPU690795','Importador',1),
(4,'Grupo Eléctrico Industrial','Leticia Rojas Martínez','9516428919','contacto@grupo.com','Av. Insurgentes #32, Col. Centro, Oaxaca de Juárez, Oax.','CGF509217','Nacional',1),
(5,'Plomería Total S.A.','Rosa Villanueva Sánchez','9518329435','contacto@plomería.com','Av. Constitución #664, Col. Centro, Oaxaca de Juárez, Oax.','WWY393131','Importador',1),
(6,'Aceros y Materiales del Istmo','Leticia Fuentes Núñez','9517569981','contacto@aceros.com','Calle Allende #799, Col. Centro, Oaxaca de Juárez, Oax.','SYU251361','Regional',1),
(7,'Herramientas Profesionales GH','Óscar Vázquez Santiago','9516632941','contacto@herramientas.com','Calle 5 de Mayo #21, Col. Centro, Oaxaca de Juárez, Oax.','FOY172918','Nacional',1),
(8,'Comercializadora Cerrajera','José Torres Álvarez','9519044494','contacto@comercializadora.com','Av. Constitución #894, Col. Centro, Oaxaca de Juárez, Oax.','USB441483','Regional',1),
(9,'Suministros Industriales Pérez','Martha Flores Contreras','9512562037','contacto@suministros.com','Calle Independencia #618, Col. Centro, Oaxaca de Juárez, Oax.','BVH390137','Regional',1),
(10,'Importadora de Herramientas Nacional','Guadalupe Aguilar Peña','9515723173','contacto@importadora.com','Av. Hidalgo #253, Col. Centro, Oaxaca de Juárez, Oax.','EXE121677','Nacional',1),
(11,'Cementos y Agregados del Valle','Gonzalo Núñez Rojas','9512374659','contacto@cementos.com','Calle 5 de Mayo #874, Col. Centro, Oaxaca de Juárez, Oax.','DJL266101','Nacional',1),
(12,'Distribuidora de Iluminación LED','Pablo Vargas Rojas','9515505722','12.contacto@distribuidora.com','Calle Zaragoza #73, Col. Centro, Oaxaca de Juárez, Oax.','XGQ333104','Nacional',1),
(13,'Grupo Adhesivos Mexicanos','Claudia Pérez Rivas','9518966231','13.contacto@grupo.com','Calle Zaragoza #912, Col. Centro, Oaxaca de Juárez, Oax.','IOZ558845','Nacional',1),
(14,'Ferretería Mayorista del Pacífico','Alejandro Gutiérrez Fuentes','9514554926','contacto@ferretería.com','Av. Constitución #207, Col. Centro, Oaxaca de Juárez, Oax.','AON591949','Nacional',1),
(15,'Equipos de Seguridad Industrial SA','Alejandro Salazar Aguilar','9515633880','contacto@equipos.com','Av. Revolución #479, Col. Centro, Oaxaca de Juárez, Oax.','FVD537805','Nacional',1),
(16,'Abrasivos y Discos MX','Itzel Cortés Silva','9512986234','contacto@abrasivos.com','Av. Símbolos Patrios #362, Col. Centro, Oaxaca de Juárez, Oax.','UCD559117','Distribuidor',0),
(17,'Jardín y Vivero Suministros','Rubén Campos Delgado','9518972838','contacto@jardín.com','Av. Símbolos Patrios #285, Col. Centro, Oaxaca de Juárez, Oax.','MEK664386','Nacional',0),
(18,'Grupo Constructor Zapoteca','Arturo Ortiz Ruiz','9512497967','18.contacto@grupo.com','Calle Morelos #462, Col. Centro, Oaxaca de Juárez, Oax.','VWR923773','Fabricante',1),
(19,'Materiales Eléctricos del Sureste','Lorena Mejía Rojas','9511485669','contacto@materiales.com','Av. Juárez #207, Col. Centro, Oaxaca de Juárez, Oax.','TWV513518','Nacional',1),
(20,'Tuberías y Conexiones Hidráulicas','Teresa Martínez Guzmán','9518484373','contacto@tuberías.com','Av. Juárez #993, Col. Centro, Oaxaca de Juárez, Oax.','BJF343962','Importador',1),
(21,'Distribuidora Nacional de Tornillos','Cecilia Torres Flores','9514776753','21.contacto@distribuidora.com','Av. Oaxaca #221, Col. Centro, Oaxaca de Juárez, Oax.','VGV000872','Nacional',1),
(22,'Pinturas Vinci de México','Julia Hernández Villanueva','9515620085','22.contacto@pinturas.com','Av. Universidad #926, Col. Centro, Oaxaca de Juárez, Oax.','ZRM118269','Importador',1),
(23,'Herrajes y Candados Continental','Araceli Álvarez Reyes','9515742531','contacto@herrajes.com','Av. Hidalgo #710, Col. Centro, Oaxaca de Juárez, Oax.','MRV315683','Nacional',1),
(24,'Proveedora Industrial Monte Albán','Salvador Ruiz Luna','9517779646','contacto@proveedora.com','Av. Oaxaca #337, Col. Centro, Oaxaca de Juárez, Oax.','IHR961520','Nacional',1),
(25,'Aceros Oaxaca S.A. de C.V.','Ivonne Mendoza Álvarez','9518981512','25.contacto@aceros.com','Av. Símbolos Patrios #973, Col. Centro, Oaxaca de Juárez, Oax.','XKT598376','Nacional',1),
(26,'Suministros de Limpieza Profesional','Silvia Rivas Núñez','9516960666','26.contacto@suministros.com','Calle Morelos #772, Col. Centro, Oaxaca de Juárez, Oax.','ECD287163','Nacional',1),
(27,'Grupo Ferretero del Istmo','Salvador Moreno Álvarez','9517403346','27.contacto@grupo.com','Av. Revolución #315, Col. Centro, Oaxaca de Juárez, Oax.','PMD065641','Importador',1),
(28,'Comercial Herramientas Total','Fernando Ramírez Díaz','9516258215','contacto@comercial.com','Av. Oaxaca #69, Col. Centro, Oaxaca de Juárez, Oax.','IZX918103','Nacional',1),
(29,'Distribuidora de Cables y Contactos','Pedro Bautista Villanueva','9511632925','29.contacto@distribuidora.com','Av. Revolución #214, Col. Centro, Oaxaca de Juárez, Oax.','ETM706649','Nacional',1),
(30,'Maderas y Construcción del Sur','Mónica Bautista Silva','9511767026','contacto@maderas.com','Av. Hidalgo #981, Col. Centro, Oaxaca de Juárez, Oax.','ERQ263135','Nacional',1),
(31,'Nery Surte','Nery Omar Hdz cortes','9516386310','hnery7744@gmail.com','Intento',NULL,'Distribuidor',0),
(32,'Nery Surte1','Jorge Hernandez','9516386311','h1nery7744@gmail.com','',NULL,'Mayorista',0),
(33,'Nery Surte654321','Nery Omar Hdz cortes','9516386310','hwewnery7744@gmail.com','Av. Símbolos Patrios #362, Col. Centro, Oaxaca de Juárez, Oax.',NULL,'Fabricante',1),
(54,'erahin','Jorge Hernandez','dkkdd','hernandezcortesneryomar@gmail.com.xd','ssjjsjs',NULL,'Distribuidor',1);
/*!40000 ALTER TABLE `proveedor` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `rol`
--

DROP TABLE IF EXISTS `rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rol` (
  `Id_Rol` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(20) NOT NULL,
  `Descripcion` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`Id_Rol`),
  UNIQUE KEY `uq_rol_nombre` (`Nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rol`
--

LOCK TABLES `rol` WRITE;
/*!40000 ALTER TABLE `rol` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `rol` VALUES
(1,'Administrador','Configura el sistema, usuarios, productos, proveedores y pedidos'),
(2,'Vendedor','Registra ventas y consulta productos'),
(3,'Almacenista','Registra entradas/salidas de inventario y recibe mercancía');
/*!40000 ALTER TABLE `rol` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `unidad_medida`
--

DROP TABLE IF EXISTS `unidad_medida`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `unidad_medida` (
  `Id_Unidad` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(50) NOT NULL,
  `Abreviatura` varchar(10) NOT NULL,
  PRIMARY KEY (`Id_Unidad`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unidad_medida`
--

LOCK TABLES `unidad_medida` WRITE;
/*!40000 ALTER TABLE `unidad_medida` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `unidad_medida` VALUES
(1,'Pieza','pz'),
(2,'Metro','m'),
(3,'Litro','L'),
(4,'Kilogramo','kg'),
(5,'Caja','cja'),
(6,'Rollo','rll'),
(7,'Par','par'),
(8,'Juego','jgo'),
(9,'Galón','gal'),
(10,'Bolsa','bls'),
(11,'Metro Cuadrado','m2'),
(12,'Tonelada','ton'),
(13,'Gramo','g'),
(14,'Mililitro','ml');
/*!40000 ALTER TABLE `unidad_medida` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `Id_Usuario` int(11) NOT NULL AUTO_INCREMENT,
  `Usuario` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Rol` varchar(20) NOT NULL,
  `Fecha_Alta` datetime DEFAULT current_timestamp(),
  `Estado` tinyint(1) DEFAULT 1,
  `Intentos_Fallidos` int(11) NOT NULL DEFAULT 0,
  `Fecha_Bloqueo` datetime DEFAULT NULL,
  `Codigo_Recuperacion` varchar(255) DEFAULT NULL,
  `Codigo_Expiracion` datetime DEFAULT NULL,
  `Id_Empleado` int(11) NOT NULL,
  PRIMARY KEY (`Id_Usuario`),
  UNIQUE KEY `Usuario` (`Usuario`),
  UNIQUE KEY `Id_Empleado` (`Id_Empleado`),
  KEY `fk_usuario_rol` (`Rol`),
  CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`Rol`) REFERENCES `rol` (`Nombre`) ON UPDATE CASCADE,
  CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`Id_Empleado`) REFERENCES `empleados` (`Id_Empleado`) ON UPDATE CASCADE,
  CONSTRAINT `chk_usuario_estado` CHECK (`Estado` in (0,1)),
  CONSTRAINT `chk_usuario_intentos` CHECK (`Intentos_Fallidos` >= 0)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `usuario` VALUES
(1,'Verónicahe','$2y$10$ZAjUnLvRgxRk.SeJftgGDODzgqhlHqTlzkV9gQFmfGlv8odnlJb7q','Administrador','2025-04-11 08:25:46',1,0,NULL,NULL,NULL,1),
(2,'pchavez','$2y$10$ZAjUnLvRgxRk.SeJftgGDODzgqhlHqTlzkV9gQFmfGlv8odnlJb7q','Administrador','2026-05-04 16:58:54',1,0,NULL,NULL,NULL,2),
(3,'scabrera','$2y$10$ZAjUnLvRgxRk.SeJftgGDODzgqhlHqTlzkV9gQFmfGlv8odnlJb7q','Vendedor','2025-01-09 18:52:35',1,0,NULL,NULL,NULL,3),
(4,'drodriguez','$2y$10$ZAjUnLvRgxRk.SeJftgGDODzgqhlHqTlzkV9gQFmfGlv8odnlJb7q','Vendedor','2025-08-04 18:15:42',1,0,NULL,NULL,NULL,4),
(5,'aperez','$2y$10$ZAjUnLvRgxRk.SeJftgGDODzgqhlHqTlzkV9gQFmfGlv8odnlJb7q','Almacenista','2025-04-29 12:11:37',0,0,NULL,NULL,NULL,5),
(6,'svillanueva','$2y$10$ZAjUnLvRgxRk.SeJftgGDODzgqhlHqTlzkV9gQFmfGlv8odnlJb7q','Vendedor','2026-03-17 20:37:38',1,0,NULL,NULL,NULL,6),
(7,'eflores','$2y$10$ZAjUnLvRgxRk.SeJftgGDODzgqhlHqTlzkV9gQFmfGlv8odnlJb7q','Vendedor','2026-04-13 10:51:29',1,0,NULL,NULL,NULL,7),
(8,'sherrera','$2y$10$ZAjUnLvRgxRk.SeJftgGDODzgqhlHqTlzkV9gQFmfGlv8odnlJb7q','Almacenista','2026-01-25 15:00:22',1,0,NULL,NULL,NULL,8),
(9,'smorales','$2y$10$ZAjUnLvRgxRk.SeJftgGDODzgqhlHqTlzkV9gQFmfGlv8odnlJb7q','Vendedor','2025-03-12 20:41:02',1,0,NULL,NULL,NULL,9),
(10,'pruiz','$2y$10$ZAjUnLvRgxRk.SeJftgGDODzgqhlHqTlzkV9gQFmfGlv8odnlJb7q','Almacenista','2026-05-17 09:27:23',1,0,NULL,NULL,NULL,10),
(11,'acruz','$2y$10$ZAjUnLvRgxRk.SeJftgGDODzgqhlHqTlzkV9gQFmfGlv8odnlJb7q','Vendedor','2025-04-06 12:14:25',1,0,NULL,NULL,NULL,11),
(12,'mcortes','$2y$10$ZAjUnLvRgxRk.SeJftgGDODzgqhlHqTlzkV9gQFmfGlv8odnlJb7q','Vendedor','2025-07-08 08:16:55',0,0,NULL,NULL,NULL,12),
(13,'pbautista','$2y$10$ZAjUnLvRgxRk.SeJftgGDODzgqhlHqTlzkV9gQFmfGlv8odnlJb7q','Vendedor','2025-09-28 20:50:40',1,0,NULL,NULL,NULL,13),
(14,'aramos','$2y$10$ZAjUnLvRgxRk.SeJftgGDODzgqhlHqTlzkV9gQFmfGlv8odnlJb7q','Administrador','2025-12-23 14:21:20',1,0,NULL,NULL,NULL,14),
(15,'asalazar','$2y$10$ZAjUnLvRgxRk.SeJftgGDODzgqhlHqTlzkV9gQFmfGlv8odnlJb7q','Almacenista','2025-06-07 15:59:45',1,0,NULL,NULL,NULL,15),
(16,'jescobar','$2y$10$ZAjUnLvRgxRk.SeJftgGDODzgqhlHqTlzkV9gQFmfGlv8odnlJb7q','Almacenista','2025-06-05 15:02:08',1,0,NULL,NULL,NULL,16),
(17,'kluna','$2y$10$ZAjUnLvRgxRk.SeJftgGDODzgqhlHqTlzkV9gQFmfGlv8odnlJb7q','Almacenista','2026-01-19 14:09:15',1,0,NULL,NULL,NULL,17),
(18,'ngarcia','$2y$10$ZAjUnLvRgxRk.SeJftgGDODzgqhlHqTlzkV9gQFmfGlv8odnlJb7q','Vendedor','2026-05-24 12:49:51',0,0,NULL,NULL,NULL,18),
(19,'kjimenez','$2y$10$ZAjUnLvRgxRk.SeJftgGDODzgqhlHqTlzkV9gQFmfGlv8odnlJb7q','Almacenista','2026-01-10 09:50:25',1,0,NULL,NULL,NULL,19),
(20,'pvillanueva','$2y$10$ZAjUnLvRgxRk.SeJftgGDODzgqhlHqTlzkV9gQFmfGlv8odnlJb7q','Vendedor','2026-01-15 11:28:28',1,0,NULL,NULL,NULL,20),
(21,'NOmar','$2y$10$iBM/rYyICW0ZyW5vXYpVQumJqikPJLDmMjYYxI.doFv3o/LWN65xm','Almacenista','2026-10-12 22:18:47',1,0,NULL,NULL,NULL,21),
(67,'Gambito','$2y$10$Cp05fxcheaoHEN/Dv/WlCeGhH8xWosnUP0zIruVsKt7QmBsya81U6','Almacenista','2026-10-13 10:33:55',1,0,NULL,NULL,NULL,67),
(68,'GMama','$2y$10$ErDZ97E7PKA6cZWYB2D2De6ahD6d9czXA9dNbny8cCxYua5NnRRjq','Administrador','2026-10-13 10:46:58',1,0,NULL,NULL,NULL,68),
(69,'alexPo','$2y$10$hsHpRXtZmCL3h/6PToYhruo.5K5cfr2MfROrHKPgMwLIW8a1Q.kwq','Administrador','2026-10-13 10:56:57',1,0,NULL,NULL,NULL,69);
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `ventas`
--

DROP TABLE IF EXISTS `ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ventas` (
  `Id_Ventas` int(11) NOT NULL AUTO_INCREMENT,
  `Fecha` date DEFAULT curdate(),
  `Descripcion` varchar(100) DEFAULT NULL,
  `Total` decimal(10,2) NOT NULL,
  `Estado` enum('Completada','Cancelada','Pendiente') DEFAULT 'Completada',
  `Id_Metodo_Pago` int(11) NOT NULL,
  `Id_Cliente` int(11) DEFAULT NULL,
  `Id_Empleado` int(11) NOT NULL,
  PRIMARY KEY (`Id_Ventas`),
  KEY `Id_Metodo_Pago` (`Id_Metodo_Pago`),
  KEY `Id_Cliente` (`Id_Cliente`),
  KEY `Id_Empleado` (`Id_Empleado`),
  KEY `idx_ventas_fecha` (`Fecha`),
  KEY `idx_ventas_estado` (`Estado`),
  CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`Id_Metodo_Pago`) REFERENCES `metodo_pago` (`Id_Metodo_Pago`) ON UPDATE CASCADE,
  CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`Id_Cliente`) REFERENCES `cliente` (`Id_Cliente`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `ventas_ibfk_3` FOREIGN KEY (`Id_Empleado`) REFERENCES `empleados` (`Id_Empleado`) ON UPDATE CASCADE,
  CONSTRAINT `CONSTRAINT_1` CHECK (`Total` >= 0)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ventas`
--

LOCK TABLES `ventas` WRITE;
/*!40000 ALTER TABLE `ventas` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `ventas` VALUES
(1,'2025-12-03','Venta rápida',453.38,'Completada',3,50,17),
(2,'2025-08-03','Venta con factura',17440.58,'Completada',1,81,1),
(3,'2026-08-22','Venta mayoreo',34374.92,'Completada',2,12,18),
(4,'2025-10-14','Venta rápida',7325.96,'Completada',1,NULL,4),
(5,'2026-04-11','Venta de mostrador',15702.44,'Completada',1,43,9),
(6,'2025-06-29','Venta con factura',3089.39,'Completada',1,101,3),
(7,'2025-03-26','Pedido especial',20102.75,'Completada',2,85,15),
(8,'2025-06-16','Venta con factura',16138.49,'Completada',1,114,1),
(9,'2026-02-06','Venta de mostrador',13726.11,'Completada',3,69,1),
(10,'2025-02-20','Pedido especial',42707.43,'Completada',3,NULL,12),
(11,'2026-01-22','Venta rápida',7659.73,'Completada',2,50,8),
(12,'2025-08-14','Venta con factura',9042.94,'Completada',3,NULL,11),
(13,'2025-03-26','Venta con factura',11529.66,'Completada',2,17,10),
(14,'2025-01-20','Venta rápida',16881.04,'Completada',1,13,18),
(15,'2025-12-05','Venta rápida',27319.93,'Completada',2,81,17),
(16,'2025-08-30','Venta con factura',29392.99,'Completada',3,101,6),
(17,'2026-06-17','Venta con factura',9438.33,'Completada',2,120,5),
(18,'2026-02-27','Pedido especial',17931.71,'Completada',3,23,9),
(19,'2025-12-22','Venta rápida',19221.32,'Completada',2,NULL,2),
(20,'2026-05-02','Venta con factura',17837.17,'Completada',3,82,17),
(21,'2025-04-20','Venta con factura',15114.85,'Cancelada',3,51,13),
(22,'2026-03-03','Venta mayoreo',11865.36,'Completada',3,98,5),
(23,'2026-01-26','Venta mayoreo',13613.46,'Completada',2,NULL,4),
(24,'2025-07-06','Pedido especial',1478.33,'Completada',1,37,1),
(25,'2026-09-03','Venta de mostrador',13309.78,'Completada',3,120,17),
(26,'2025-11-05','Venta mayoreo',3712.53,'Cancelada',2,106,14),
(27,'2025-06-24','Pedido especial',5720.96,'Completada',2,101,11),
(28,'2025-07-14','Venta mayoreo',8727.39,'Completada',1,105,8),
(29,'2026-09-20','Pedido especial',18909.96,'Completada',1,111,9),
(30,'2025-09-21','Venta rápida',11291.16,'Completada',1,56,19),
(31,'2025-09-15','Pedido especial',1492.18,'Completada',1,119,12),
(32,'2026-05-31','Venta con factura',1680.30,'Completada',3,NULL,7),
(33,'2026-05-14','Venta con factura',23844.25,'Completada',2,91,19),
(34,'2025-08-25','Venta mayoreo',14774.31,'Completada',2,78,16),
(35,'2026-02-28','Venta mayoreo',24885.59,'Completada',2,107,13),
(36,'2026-06-22','Venta de mostrador',6191.94,'Completada',3,54,6),
(37,'2026-03-03','Venta rápida',49596.57,'Pendiente',1,14,17),
(38,'2026-07-23','Venta rápida',10754.24,'Completada',1,61,9),
(39,'2025-11-22','Venta mayoreo',27405.50,'Completada',1,82,18),
(40,'2026-08-25','Venta con factura',9923.51,'Completada',3,31,1),
(41,'2025-05-20','Venta mayoreo',17827.42,'Completada',1,93,18),
(42,'2026-01-18','Venta mayoreo',3739.63,'Completada',1,50,19),
(43,'2026-07-12','Venta mayoreo',8237.65,'Completada',3,110,6),
(44,'2025-11-30','Venta rápida',15338.02,'Completada',3,26,1),
(45,'2025-07-20','Pedido especial',13132.72,'Completada',3,84,12),
(46,'2025-01-18','Venta mayoreo',2761.26,'Completada',2,118,7),
(47,'2025-08-16','Venta con factura',4842.50,'Completada',2,NULL,11),
(48,'2026-01-08','Venta con factura',22814.88,'Completada',3,14,6),
(49,'2025-07-03','Venta mayoreo',481.89,'Pendiente',1,30,18),
(50,'2026-01-31','Pedido especial',4695.46,'Completada',2,88,6),
(51,'2026-04-02','Venta rápida',13492.86,'Completada',3,93,5),
(52,'2025-09-20','Venta de mostrador',4816.66,'Pendiente',1,14,13),
(53,'2025-03-06','Venta con factura',21805.75,'Completada',1,NULL,8),
(54,'2025-10-08','Venta mayoreo',7077.71,'Completada',3,22,7),
(55,'2025-12-31','Pedido especial',5575.70,'Completada',2,106,3),
(56,'2025-04-08','Venta con factura',10034.52,'Completada',1,28,2),
(57,'2026-02-16','Venta mayoreo',4585.72,'Completada',3,75,14),
(58,'2026-08-28','Venta rápida',2904.62,'Completada',2,101,8),
(59,'2025-07-06','Venta rápida',3566.49,'Cancelada',1,36,20),
(60,'2025-12-28','Venta con factura',12613.23,'Pendiente',1,22,7),
(61,'2025-08-02','Venta rápida',5585.02,'Completada',2,26,8),
(62,'2025-05-05','Venta rápida',13024.82,'Pendiente',2,6,6),
(63,'2025-07-23','Venta con factura',31793.03,'Completada',3,66,7),
(64,'2026-03-03','Venta mayoreo',1262.82,'Completada',1,NULL,1),
(65,'2025-12-29','Venta con factura',9913.20,'Completada',2,55,19),
(66,'2025-07-31','Venta de mostrador',9677.84,'Completada',1,78,18),
(67,'2025-09-29','Venta de mostrador',25432.16,'Completada',1,115,3),
(68,'2026-05-31','Pedido especial',10919.20,'Completada',3,89,18),
(69,'2026-03-25','Venta de mostrador',18752.38,'Completada',3,78,2),
(70,'2026-01-13','Venta con factura',5611.50,'Completada',1,NULL,7),
(71,'2026-04-01','Venta mayoreo',17510.70,'Completada',2,23,12),
(72,'2026-09-18','Venta con factura',9385.80,'Completada',2,39,4),
(73,'2025-08-30','Venta con factura',10331.20,'Completada',3,79,3),
(74,'2026-02-24','Pedido especial',7489.96,'Completada',3,65,14),
(75,'2025-06-24','Venta rápida',3328.25,'Completada',3,45,7),
(76,'2026-03-09','Pedido especial',12338.04,'Completada',1,62,20),
(77,'2025-01-11','Venta de mostrador',11464.30,'Completada',3,86,7),
(78,'2025-04-10','Venta con factura',892.89,'Completada',2,29,6),
(79,'2026-09-14','Venta de mostrador',1940.77,'Cancelada',1,8,15),
(80,'2026-03-13','Pedido especial',20624.14,'Completada',3,NULL,15),
(81,'2025-01-04','Venta de mostrador',6358.04,'Completada',3,74,14),
(82,'2026-06-05','Venta con factura',1381.70,'Completada',3,17,11),
(83,'2025-10-23','Venta con factura',7377.88,'Completada',2,106,3),
(84,'2025-08-15','Venta de mostrador',19832.74,'Completada',2,65,13),
(85,'2025-06-13','Venta mayoreo',3279.01,'Completada',3,NULL,7),
(86,'2025-02-27','Venta de mostrador',241.07,'Completada',2,66,3),
(87,'2025-11-09','Venta rápida',639.70,'Completada',2,13,16),
(88,'2025-02-25','Pedido especial',10073.80,'Completada',1,NULL,9),
(89,'2026-06-21','Venta con factura',20684.03,'Completada',2,118,16),
(90,'2025-12-13','Venta de mostrador',5218.32,'Completada',3,60,7);
/*!40000 ALTER TABLE `ventas` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Temporary table structure for view `vw_catalogo_productos`
--

DROP TABLE IF EXISTS `vw_catalogo_productos`;
/*!50001 DROP VIEW IF EXISTS `vw_catalogo_productos`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `vw_catalogo_productos` AS SELECT
 1 AS `Id_Producto`,
  1 AS `Codigo`,
  1 AS `Producto`,
  1 AS `Marca`,
  1 AS `Id_Categoria`,
  1 AS `Categoria`,
  1 AS `Id_Proveedor`,
  1 AS `Proveedor`,
  1 AS `Unidad`,
  1 AS `Precio_Venta`,
  1 AS `Sin_Precio`,
  1 AS `Estado`,
  1 AS `Stock`,
  1 AS `Stock_Minimo`,
  1 AS `Stock_Critico` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_usuarios`
--

DROP TABLE IF EXISTS `vw_usuarios`;
/*!50001 DROP VIEW IF EXISTS `vw_usuarios`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `vw_usuarios` AS SELECT
 1 AS `Id_Usuario`,
  1 AS `Id_Empleado`,
  1 AS `Nombre_Completo`,
  1 AS `Usuario`,
  1 AS `Correo`,
  1 AS `Id_Rol`,
  1 AS `Rol`,
  1 AS `Estado` */;
SET character_set_client = @saved_cs_client;

--
-- Dumping routines for database 'ferredog'
--

--
-- Final view structure for view `vw_catalogo_productos`
--

/*!50001 DROP VIEW IF EXISTS `vw_catalogo_productos`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_uca1400_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_catalogo_productos` AS select `p`.`Id_Producto` AS `Id_Producto`,`p`.`Codigo_Barras` AS `Codigo`,`p`.`Nombre` AS `Producto`,`m`.`Nombre` AS `Marca`,`c`.`Id_Categoria` AS `Id_Categoria`,`c`.`Nombre` AS `Categoria`,`pr`.`Id_Proveedor` AS `Id_Proveedor`,`pr`.`Nombre` AS `Proveedor`,`um`.`Abreviatura` AS `Unidad`,`p`.`Precio_Venta` AS `Precio_Venta`,`p`.`Precio_Venta` is null AS `Sin_Precio`,case when `p`.`Estado` = 1 then 'Activo' else 'Inactivo' end AS `Estado`,coalesce(`i`.`Existencias`,0) AS `Stock`,`p`.`Stock_Minimo` AS `Stock_Minimo`,coalesce(`i`.`Existencias`,0) <= `p`.`Stock_Minimo` AS `Stock_Critico` from (((((`productos` `p` left join `marcas` `m` on(`m`.`Id_Marca` = `p`.`Id_Marca`)) left join `categoria` `c` on(`c`.`Id_Categoria` = `p`.`Id_Categoria`)) left join `proveedor` `pr` on(`pr`.`Id_Proveedor` = `p`.`Id_Proveedor`)) left join `unidad_medida` `um` on(`um`.`Id_Unidad` = `p`.`Id_Unidad`)) left join `inventario` `i` on(`i`.`Id_Producto` = `p`.`Id_Producto`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_usuarios`
--

/*!50001 DROP VIEW IF EXISTS `vw_usuarios`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_uca1400_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_usuarios` AS select `u`.`Id_Usuario` AS `Id_Usuario`,`e`.`Id_Empleado` AS `Id_Empleado`,concat(`e`.`Nombre`,' ',`e`.`Apellidos`) AS `Nombre_Completo`,`u`.`Usuario` AS `Usuario`,`e`.`Correo` AS `Correo`,`r`.`Id_Rol` AS `Id_Rol`,`u`.`Rol` AS `Rol`,case when `u`.`Estado` = 1 then 'Activo' else 'Inactivo' end AS `Estado` from ((`usuario` `u` join `empleados` `e` on(`e`.`Id_Empleado` = `u`.`Id_Empleado`)) left join `rol` `r` on(`r`.`Nombre` = `u`.`Rol`)) */;
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
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-10-13 12:53:20
