-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: localhost    Database: control_escolar
-- ------------------------------------------------------
-- Server version	8.0.44

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `achievements`
--

DROP TABLE IF EXISTS `achievements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `achievements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `clave` varchar(100) DEFAULT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `descripcion` text,
  `icono` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clave` (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `achievements`
--

LOCK TABLES `achievements` WRITE;
/*!40000 ALTER TABLE `achievements` DISABLE KEYS */;
/*!40000 ALTER TABLE `achievements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `actividades`
--

DROP TABLE IF EXISTS `actividades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `actividades` (
  `ID_actividad` int NOT NULL AUTO_INCREMENT,
  `ID_curso` int NOT NULL,
  `titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_limite` datetime DEFAULT NULL,
  `ponderacion` decimal(5,2) NOT NULL,
  `tipo` enum('Tarea','Examen','Proyecto','Participacion') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Tarea',
  PRIMARY KEY (`ID_actividad`),
  KEY `ID_curso` (`ID_curso`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actividades`
--

LOCK TABLES `actividades` WRITE;
/*!40000 ALTER TABLE `actividades` DISABLE KEYS */;
INSERT INTO `actividades` VALUES (11,1,'Tarea 1: HTML',NULL,NULL,10.00,'Tarea'),(12,1,'Examen Parcial 1',NULL,NULL,30.00,'Examen'),(13,1,'Proyecto Final',NULL,NULL,40.00,'Proyecto'),(14,2,'Tarea 1: Modelo ER',NULL,NULL,20.00,'Tarea'),(15,2,'Examen 1',NULL,NULL,30.00,'Examen'),(16,3,'Tarea 1: SQL',NULL,NULL,15.00,'Tarea'),(17,3,'Proyecto BD',NULL,NULL,35.00,'Proyecto'),(18,4,'Tarea 1: Límites',NULL,NULL,10.00,'Tarea'),(19,5,'Práctica 1: Laboratorio',NULL,NULL,25.00,'Proyecto'),(20,6,'Ensayo 1',NULL,NULL,50.00,'Tarea');
/*!40000 ALTER TABLE `actividades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `administradores`
--

DROP TABLE IF EXISTS `administradores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `administradores` (
  `id_admin` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido` varchar(50) DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `id_domicilio` int DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_admin`),
  KEY `id_domicilio` (`id_domicilio`),
  CONSTRAINT `administradores_ibfk_1` FOREIGN KEY (`id_domicilio`) REFERENCES `domicilio` (`id_domicilio`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `administradores`
--

LOCK TABLES `administradores` WRITE;
/*!40000 ALTER TABLE `administradores` DISABLE KEYS */;
INSERT INTO `administradores` VALUES (1,'Ana','Ramos','5551010101','aramos@escuela.com','Directora',1,NULL),(2,'Jorge','Navarro','5552020202','jnavarro@escuela.com','Subdirector',2,NULL),(3,'Elisa','Mendoza','5553030303','emendoza@escuela.com','Coordinadora',3,NULL),(4,'Raúl','Santos','5554040404','rsantos@escuela.com','Contador',4,NULL),(5,'Sandra','Molina','5555050505','smolina@escuela.com','Secretaria',5,NULL),(6,'Iván','Pérez','5556060606','iperez@escuela.com','Administrador',6,NULL),(7,'Mónica','Suárez','5557070707','msuarez@escuela.com','Sistemas',7,NULL),(8,'César','Bravo','5558080808','cbravo@escuela.com','Mantenimiento',8,NULL),(9,'Ruth','Díaz','5559090909','rdiaz@escuela.com','Bibliotecaria',9,NULL),(10,'Héctor','Ramírez','5550009999','hramirez@escuela.com','Recepcionista',10,NULL);
/*!40000 ALTER TABLE `administradores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alumnos`
--

DROP TABLE IF EXISTS `alumnos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alumnos` (
  `id_alumno` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido` varchar(50) DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `id_padre` int DEFAULT NULL,
  `id_domicilio` int DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_alumno`),
  KEY `id_padre` (`id_padre`),
  KEY `id_domicilio` (`id_domicilio`),
  CONSTRAINT `alumnos_ibfk_1` FOREIGN KEY (`id_padre`) REFERENCES `padres` (`ID_padre`) ON DELETE CASCADE,
  CONSTRAINT `alumnos_ibfk_2` FOREIGN KEY (`id_domicilio`) REFERENCES `domicilio` (`id_domicilio`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alumnos`
--

LOCK TABLES `alumnos` WRITE;
/*!40000 ALTER TABLE `alumnos` DISABLE KEYS */;
INSERT INTO `alumnos` VALUES (1,'Sofía','García','5551110001','sofia.g@escuela.com',1,1,NULL),(2,'Miguel','Pérez','5552220002','miguel.p@escuela.com',2,2,NULL),(3,'Valeria','Torres','5553330003','valeria.t@escuela.com',3,3,NULL),(4,'Daniel','Luna','5554440004','daniel.l@escuela.com',4,4,NULL),(5,'Lucía','Flores','5555550005','lucia.f@escuela.com',5,5,NULL),(6,'Mateo','Sosa','5556660006','mateo.s@escuela.com',6,6,NULL),(7,'Isabella','Reyes','5557770007','isa.r@escuela.com',7,7,NULL),(8,'Emilio','Castro','5558880008','emilio.c@escuela.com',8,8,NULL),(9,'Camila','Ruiz','5559990009','camila.r@escuela.com',9,9,NULL),(10,'Santiago','Ortiz','5550000010','santi.o@escuela.com',10,10,NULL);
/*!40000 ALTER TABLE `alumnos` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = cp850 */ ;
/*!50003 SET character_set_results = cp850 */ ;
/*!50003 SET collation_connection  = cp850_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `eliminar_padre_alumno` AFTER DELETE ON `alumnos` FOR EACH ROW BEGIN

    DELETE FROM padres WHERE id_padre = OLD.id_padre;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `auditoria_calificaciones`
--

DROP TABLE IF EXISTS `auditoria_calificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auditoria_calificaciones` (
  `ID_auditoria` int NOT NULL AUTO_INCREMENT,
  `ID_calificacion_modificada` int NOT NULL,
  `calificacion_anterior` decimal(5,2) DEFAULT NULL,
  `calificacion_nueva` decimal(5,2) DEFAULT NULL,
  `fecha_cambio` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ID_profesor_actor` int DEFAULT NULL,
  PRIMARY KEY (`ID_auditoria`),
  KEY `idx_calificacion_modificada` (`ID_calificacion_modificada`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auditoria_calificaciones`
--

LOCK TABLES `auditoria_calificaciones` WRITE;
/*!40000 ALTER TABLE `auditoria_calificaciones` DISABLE KEYS */;
INSERT INTO `auditoria_calificaciones` VALUES (1,1,85.00,90.00,'2025-09-15 16:30:00',2),(2,5,7.00,70.00,'2025-09-16 18:00:00',2),(3,7,80.00,85.00,'2025-10-05 15:45:00',1),(4,10,0.00,88.00,'2025-10-20 20:20:00',4),(5,12,85.00,90.00,'2025-11-01 17:15:00',2),(6,2,75.00,80.00,'2025-09-18 20:00:00',2),(7,3,95.00,100.00,'2025-09-20 14:30:00',2),(8,4,0.00,100.00,'2025-09-22 22:45:00',2),(9,6,60.00,80.00,'2025-10-10 17:20:00',1),(10,8,90.00,95.00,'2025-11-05 15:15:00',1);
/*!40000 ALTER TABLE `auditoria_calificaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calificaciones`
--

DROP TABLE IF EXISTS `calificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calificaciones` (
  `ID_calificacion` int NOT NULL AUTO_INCREMENT,
  `ID_inscripcion` int NOT NULL,
  `ID_actividad` int NOT NULL,
  `calificacion_obtenida` decimal(5,2) NOT NULL,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ID_profesor_que_califica` int NOT NULL,
  `comentarios` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`ID_calificacion`),
  UNIQUE KEY `idx_inscripcion_actividad` (`ID_inscripcion`,`ID_actividad`),
  KEY `ID_actividad` (`ID_actividad`),
  KEY `ID_profesor_que_califica` (`ID_profesor_que_califica`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calificaciones`
--

LOCK TABLES `calificaciones` WRITE;
/*!40000 ALTER TABLE `calificaciones` DISABLE KEYS */;
INSERT INTO `calificaciones` VALUES (13,1,1,90.00,'2025-11-24 15:46:55',2,NULL),(14,1,2,80.00,'2025-11-24 15:46:55',2,NULL),(15,1,3,100.00,'2025-11-24 15:46:55',2,NULL),(16,4,1,100.00,'2025-11-24 15:46:55',2,NULL),(17,4,2,70.00,'2025-11-24 15:46:55',2,NULL),(18,2,4,80.00,'2025-11-24 15:46:55',1,NULL),(19,2,5,85.00,'2025-11-24 15:46:55',1,NULL),(20,6,6,95.00,'2025-11-24 15:46:55',1,NULL),(21,7,8,70.00,'2025-11-24 15:46:55',3,NULL),(22,5,9,88.00,'2025-11-24 15:46:55',4,NULL),(23,9,10,75.00,'2025-11-24 15:46:55',8,NULL),(24,4,3,90.00,'2025-11-24 15:46:55',2,NULL);
/*!40000 ALTER TABLE `calificaciones` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_AuditarCalificacion` AFTER UPDATE ON `calificaciones` FOR EACH ROW BEGIN
    IF OLD.calificacion_obtenida <> NEW.calificacion_obtenida THEN
        INSERT INTO `auditoria_calificaciones`
            (`ID_calificacion_modificada`, `calificacion_anterior`, `calificacion_nueva`, `ID_profesor_actor`)
        VALUES
            (NEW.ID_calificacion, OLD.calificacion_obtenida, NEW.calificacion_obtenida, NEW.ID_profesor_que_califica);
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `calle`
--

DROP TABLE IF EXISTS `calle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calle` (
  `id_calle` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `id_colonia` int DEFAULT NULL,
  PRIMARY KEY (`id_calle`),
  KEY `id_colonia` (`id_colonia`),
  CONSTRAINT `calle_ibfk_1` FOREIGN KEY (`id_colonia`) REFERENCES `colonia` (`id_colonia`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calle`
--

LOCK TABLES `calle` WRITE;
/*!40000 ALTER TABLE `calle` DISABLE KEYS */;
INSERT INTO `calle` VALUES (1,'Av. Reforma',1),(2,'Primavera',2),(3,'Los Olivos',3),(4,'Calle Sol',4),(5,'Calle Luna',5),(6,'Río Verde',6),(7,'Niebla',7),(8,'Encino',8),(9,'Cedro',9),(10,'Tulipán',10);
/*!40000 ALTER TABLE `calle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `colonia`
--

DROP TABLE IF EXISTS `colonia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `colonia` (
  `id_colonia` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `id_municipio` int DEFAULT NULL,
  PRIMARY KEY (`id_colonia`),
  KEY `id_municipio` (`id_municipio`),
  CONSTRAINT `colonia_ibfk_1` FOREIGN KEY (`id_municipio`) REFERENCES `municipio` (`id_municipio`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `colonia`
--

LOCK TABLES `colonia` WRITE;
/*!40000 ALTER TABLE `colonia` DISABLE KEYS */;
INSERT INTO `colonia` VALUES (1,'Centro',1),(2,'Las Flores',2),(3,'Los Pinos',3),(4,'San Rafael',4),(5,'La Esperanza',5),(6,'Mirador',6),(7,'Vista Hermosa',7),(8,'Lomas Altas',8),(9,'El Rosario',9),(10,'Aurora',10);
/*!40000 ALTER TABLE `colonia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contratos`
--

DROP TABLE IF EXISTS `contratos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contratos` (
  `id_contrato` int NOT NULL AUTO_INCREMENT,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `puesto` varchar(100) DEFAULT NULL,
  `poliza_seguro` varchar(50) DEFAULT NULL,
  `id_profesor` int DEFAULT NULL,
  PRIMARY KEY (`id_contrato`),
  KEY `id_profesor` (`id_profesor`),
  CONSTRAINT `contratos_ibfk_1` FOREIGN KEY (`id_profesor`) REFERENCES `profesores` (`ID_profesor`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contratos`
--

LOCK TABLES `contratos` WRITE;
/*!40000 ALTER TABLE `contratos` DISABLE KEYS */;
INSERT INTO `contratos` VALUES (21,'2020-08-15','2030-08-15','Profesor Titular C','GNP-99887711',1),(22,'2024-01-10','2025-12-31','Profesor de Asignatura B','AXA-11223344',2),(23,'2018-05-20',NULL,'Profesor Investigador Titular','MET-55667788',3),(24,'2022-09-01','2026-08-31','Técnico Académico Asociado','MAP-99001122',4),(25,'2023-02-01','2025-01-31','Profesor de Medio Tiempo','BBV-33445566',5),(26,'2025-01-15','2025-07-15','Profesor Invitado','GNP-12345678',6),(27,'2019-01-10',NULL,'Coordinador de Academia','AXA-87654321',7),(28,'2021-08-15','2025-08-15','Profesor de Asignatura A','MET-11229988',8),(29,'2024-08-01','2026-07-31','Profesor Asociado B','MAP-44556677',9),(30,'2025-01-01','2025-06-30','Profesor Interino','BBV-88990011',10);
/*!40000 ALTER TABLE `contratos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cursos`
--

DROP TABLE IF EXISTS `cursos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cursos` (
  `id_curso` int NOT NULL AUTO_INCREMENT,
  `nombre_curso` varchar(100) DEFAULT NULL,
  `id_materia` int DEFAULT NULL,
  `id_profesor` int DEFAULT NULL,
  PRIMARY KEY (`id_curso`),
  KEY `id_profesor` (`id_profesor`),
  KEY `id_materia` (`id_materia`),
  CONSTRAINT `cursos_ibfk_1` FOREIGN KEY (`id_profesor`) REFERENCES `profesores` (`ID_profesor`),
  CONSTRAINT `cursos_ibfk_2` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id_materia`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cursos`
--

LOCK TABLES `cursos` WRITE;
/*!40000 ALTER TABLE `cursos` DISABLE KEYS */;
INSERT INTO `cursos` VALUES (1,'Desarrollo Web Fullstack',1,2),(2,'Administración de Bases de Datos',2,1),(3,'Fundamentos de Sistemas Operativos',3,1),(4,'Cálculo Diferencial e Integral',4,3),(5,'Química Orgánica',5,4),(6,'Introducción a la Filosofía',6,8),(7,'Física Clásica',7,5),(8,'Arquitectura de Redes',8,7),(9,'Ética para Ingenieros',9,8),(10,'Matemáticas Avanzadas',10,3),(11,'Literatura',9,6),(12,'Ciencias Sociales',10,10),(13,'Biología',3,9);
/*!40000 ALTER TABLE `cursos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departamento`
--

DROP TABLE IF EXISTS `departamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departamento` (
  `id_departamento` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_departamento`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departamento`
--

LOCK TABLES `departamento` WRITE;
/*!40000 ALTER TABLE `departamento` DISABLE KEYS */;
INSERT INTO `departamento` VALUES (1,'Matemáticas'),(2,'Ciencias'),(3,'Lenguas'),(4,'Educación Física'),(5,'Tecnología'),(6,'Arte'),(7,'Historia'),(8,'Música'),(9,'Informática'),(10,'Orientación');
/*!40000 ALTER TABLE `departamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `domicilio`
--

DROP TABLE IF EXISTS `domicilio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `domicilio` (
  `id_domicilio` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) DEFAULT NULL,
  `id_calle` int DEFAULT NULL,
  PRIMARY KEY (`id_domicilio`),
  KEY `id_calle` (`id_calle`),
  CONSTRAINT `domicilio_ibfk_1` FOREIGN KEY (`id_calle`) REFERENCES `calle` (`id_calle`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `domicilio`
--

LOCK TABLES `domicilio` WRITE;
/*!40000 ALTER TABLE `domicilio` DISABLE KEYS */;
INSERT INTO `domicilio` VALUES (1,'Casa azul',1),(2,'Depto 3B',2),(3,'Número 12',3),(4,'Casa blanca',4),(5,'Casa 45',5),(6,'Depto A',6),(7,'Casa amarilla',7),(8,'Casa 100',8),(9,'Casa gris',9),(10,'Depto B',10);
/*!40000 ALTER TABLE `domicilio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `examenes`
--

DROP TABLE IF EXISTS `examenes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `examenes` (
  `id_examen` int NOT NULL AUTO_INCREMENT,
  `nombre_examen` varchar(100) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `id_curso` int DEFAULT NULL,
  PRIMARY KEY (`id_examen`),
  KEY `id_curso` (`id_curso`),
  CONSTRAINT `examenes_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `examenes`
--

LOCK TABLES `examenes` WRITE;
/*!40000 ALTER TABLE `examenes` DISABLE KEYS */;
INSERT INTO `examenes` VALUES (1,'Examen Parcial 1: Frontend','2025-09-15',1),(2,'Examen Parcial 2: Backend','2025-11-10',1),(3,'Examen Teórico: Normalización','2025-09-20',2),(4,'Examen Final: SQL Avanzado','2025-12-01',2),(5,'Parcial 1: Gestión de Procesos','2025-10-05',3),(6,'Primer Parcial: Derivadas','2025-09-25',4),(7,'Segundo Parcial: Integrales','2025-11-15',4),(8,'Examen de Nomenclatura','2025-10-10',5),(9,'Examen de Lógica Formal','2025-10-20',6),(10,'Examen Práctico: Subnetting','2025-11-05',8);
/*!40000 ALTER TABLE `examenes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historial_academico`
--

DROP TABLE IF EXISTS `historial_academico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historial_academico` (
  `id_historial` int NOT NULL AUTO_INCREMENT,
  `id_alumno` int NOT NULL,
  `id_materia` int NOT NULL,
  `id_profesor` int NOT NULL,
  `ciclo_escolar` varchar(20) NOT NULL,
  `promedio` decimal(4,2) NOT NULL,
  `observaciones` text,
  PRIMARY KEY (`id_historial`),
  KEY `id_alumno` (`id_alumno`),
  KEY `id_materia` (`id_materia`),
  KEY `id_profesor` (`id_profesor`),
  CONSTRAINT `historial_academico_ibfk_1` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `historial_academico_ibfk_2` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id_materia`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `historial_academico_ibfk_3` FOREIGN KEY (`id_profesor`) REFERENCES `profesores` (`ID_profesor`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_academico`
--

LOCK TABLES `historial_academico` WRITE;
/*!40000 ALTER TABLE `historial_academico` DISABLE KEYS */;
INSERT INTO `historial_academico` VALUES (1,1,1,1,'2024-2025',9.50,'Excelente desempeño'),(2,2,2,2,'2024-2025',8.00,'Debe reforzar práctica'),(3,3,3,3,'2024-2025',9.00,'Participa activamente'),(4,4,4,4,'2024-2025',7.80,'Asistencia irregular'),(5,5,5,5,'2024-2025',9.20,'Muy buena actitud'),(6,6,6,6,'2024-2025',8.60,'Buen aprovechamiento'),(7,7,7,7,'2024-2025',10.00,'Sobresaliente'),(8,8,8,8,'2024-2025',8.30,'Cumple objetivos mínimos'),(9,9,9,9,'2024-2025',7.50,'Puede mejorar redacción'),(10,10,10,10,'2024-2025',9.80,'Excelente dominio de tema');
/*!40000 ALTER TABLE `historial_academico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inscripciones`
--

DROP TABLE IF EXISTS `inscripciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inscripciones` (
  `ID_inscripcion` int NOT NULL AUTO_INCREMENT,
  `ID_alumno` int NOT NULL,
  `ID_curso` int NOT NULL,
  `estado` enum('Pendiente','Aprobado','Rechazado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pendiente',
  `fecha_solicitud` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_aprobacion` timestamp NULL DEFAULT NULL,
  `ID_profesor_que_aprueba` int DEFAULT NULL,
  PRIMARY KEY (`ID_inscripcion`),
  UNIQUE KEY `idx_alumno_curso` (`ID_alumno`,`ID_curso`),
  KEY `ID_curso` (`ID_curso`),
  KEY `ID_profesor_que_aprueba` (`ID_profesor_que_aprueba`),
  CONSTRAINT `inscripciones_ibfk_1` FOREIGN KEY (`ID_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE,
  CONSTRAINT `inscripciones_ibfk_2` FOREIGN KEY (`ID_curso`) REFERENCES `cursos` (`id_curso`) ON DELETE CASCADE,
  CONSTRAINT `inscripciones_ibfk_3` FOREIGN KEY (`ID_profesor_que_aprueba`) REFERENCES `profesores` (`id_profesor`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inscripciones`
--

LOCK TABLES `inscripciones` WRITE;
/*!40000 ALTER TABLE `inscripciones` DISABLE KEYS */;
INSERT INTO `inscripciones` VALUES (1,1,1,'Aprobado','2025-11-24 22:15:43',NULL,NULL),(2,1,3,'Aprobado','2025-11-24 22:15:43',NULL,NULL),(3,1,4,'Pendiente','2025-11-24 22:15:43',NULL,NULL),(4,2,1,'Aprobado','2025-11-24 22:15:43',NULL,NULL),(5,2,5,'Aprobado','2025-11-24 22:15:43',NULL,NULL),(6,3,2,'Aprobado','2025-11-24 22:15:43',NULL,NULL),(7,3,4,'Aprobado','2025-11-24 22:15:43',NULL,NULL),(8,4,1,'Rechazado','2025-11-24 22:15:43',NULL,NULL),(9,4,6,'Aprobado','2025-11-24 22:15:43',NULL,NULL),(10,5,1,'Pendiente','2025-11-24 22:15:43',NULL,NULL),(11,6,1,'Pendiente','2025-12-01 06:32:01',NULL,NULL),(12,6,2,'Aprobado','2025-12-01 06:32:02','2025-12-01 02:23:05',1),(13,6,3,'Aprobado','2025-12-01 06:32:03','2025-12-01 02:23:44',1),(14,6,4,'Pendiente','2025-12-01 06:32:04',NULL,NULL),(15,6,5,'Pendiente','2025-12-01 06:32:04',NULL,NULL),(16,6,6,'Pendiente','2025-12-01 06:32:05',NULL,NULL),(17,6,7,'Pendiente','2025-12-01 06:32:05',NULL,NULL),(18,6,8,'Pendiente','2025-12-01 06:32:06',NULL,NULL),(19,6,9,'Pendiente','2025-12-01 06:32:07',NULL,NULL),(20,6,10,'Pendiente','2025-12-01 06:32:07',NULL,NULL),(21,5,2,'Pendiente','2025-12-01 04:00:42',NULL,NULL),(22,5,3,'Pendiente','2025-12-01 04:00:43',NULL,NULL),(23,5,13,'Pendiente','2025-12-01 04:00:44',NULL,NULL),(24,5,4,'Pendiente','2025-12-01 04:00:45',NULL,NULL),(25,5,5,'Pendiente','2025-12-01 04:00:46',NULL,NULL),(26,5,6,'Pendiente','2025-12-01 04:00:46',NULL,NULL),(27,5,7,'Pendiente','2025-12-01 04:00:47',NULL,NULL),(28,5,8,'Pendiente','2025-12-01 04:00:48',NULL,NULL);
/*!40000 ALTER TABLE `inscripciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `materia`
--

DROP TABLE IF EXISTS `materia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `materia` (
  `id_materia` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `creditos` int NOT NULL,
  `id_departamento` int NOT NULL,
  PRIMARY KEY (`id_materia`),
  KEY `id_departamento` (`id_departamento`),
  CONSTRAINT `materia_ibfk_1` FOREIGN KEY (`id_departamento`) REFERENCES `departamento` (`id_departamento`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `materia`
--

LOCK TABLES `materia` WRITE;
/*!40000 ALTER TABLE `materia` DISABLE KEYS */;
INSERT INTO `materia` VALUES (1,'Desarrollo Web Fullstack','Fundamentos de álgebra y aritmética',5,1),(2,'Administración de Bases de Datos','Introducción a la mecánica y movimiento',5,2),(3,'Biología','Estructura y funciones de los seres vivos',4,2),(4,'Cálculo Diferencial e Integral','Competencias básicas de inglés',4,3),(5,'Química Orgánica','Condición física y salud',2,4),(6,'Introducción a la Filosofía','Geometría y trigonometría',5,1),(7,'Física Clásica','Estructura de la materia y reacciones químicas',4,2),(8,'Arquitectura de Redes','Comprensión y expresión avanzada',4,3),(9,'Literatura','Análisis de obras literarias',3,3),(10,'Ciencias Sociales','Historia y sociedad contemporánea',3,2);
/*!40000 ALTER TABLE `materia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `municipio`
--

DROP TABLE IF EXISTS `municipio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `municipio` (
  `id_municipio` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_municipio`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `municipio`
--

LOCK TABLES `municipio` WRITE;
/*!40000 ALTER TABLE `municipio` DISABLE KEYS */;
INSERT INTO `municipio` VALUES (1,'Ciudad Central'),(2,'Villa del Sol'),(3,'Monteverde'),(4,'Puerto Azul'),(5,'Valle Verde'),(6,'Sierra Alta'),(7,'Costa Dorada'),(8,'Nueva Esperanza'),(9,'Pueblo Nuevo'),(10,'San Miguel');
/*!40000 ALTER TABLE `municipio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notificaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `tipo` varchar(50) NOT NULL DEFAULT 'sistema',
  `titulo` varchar(255) NOT NULL,
  `mensaje` text,
  `referencia_tipo` varchar(50) DEFAULT NULL,
  `referencia_id` int DEFAULT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT '0',
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  KEY `leido` (`leido`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificaciones`
--

LOCK TABLES `notificaciones` WRITE;
/*!40000 ALTER TABLE `notificaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `notificaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `padres`
--

DROP TABLE IF EXISTS `padres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `padres` (
  `id_padre` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido` varchar(50) DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `id_domicilio` int DEFAULT NULL,
  PRIMARY KEY (`id_padre`),
  KEY `id_domicilio` (`id_domicilio`),
  CONSTRAINT `padres_ibfk_1` FOREIGN KEY (`id_domicilio`) REFERENCES `domicilio` (`id_domicilio`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `padres`
--

LOCK TABLES `padres` WRITE;
/*!40000 ALTER TABLE `padres` DISABLE KEYS */;
INSERT INTO `padres` VALUES (1,'Carlos','García','5551112233','cgarcia@correo.com',1),(2,'Lucía','Pérez','5552223344','lperez@correo.com',2),(3,'Andrés','Torres','5553334455','atorres@correo.com',3),(4,'María','Luna','5554445566','mluna@correo.com',4),(5,'Roberto','Flores','5555556677','rflores@correo.com',5),(6,'Laura','Sosa','5556667788','lsosa@correo.com',6),(7,'Daniel','Reyes','5557778899','dreyes@correo.com',7),(8,'Patricia','Castro','5558889900','pcastro@correo.com',8),(9,'Julio','Ruiz','5559990011','jruiz@correo.com',9),(10,'Silvia','Ortiz','5550001122','sortiz@correo.com',10);
/*!40000 ALTER TABLE `padres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `padres_alumnos`
--

DROP TABLE IF EXISTS `padres_alumnos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `padres_alumnos` (
  `ID_alumno` int NOT NULL,
  `parentesco` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `padres_idpadres` int NOT NULL,
  PRIMARY KEY (`padres_idpadres`),
  KEY `ID_alumno` (`ID_alumno`),
  KEY `fk_padres_alumnos_padres1_idx` (`padres_idpadres`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `padres_alumnos`
--

LOCK TABLES `padres_alumnos` WRITE;
/*!40000 ALTER TABLE `padres_alumnos` DISABLE KEYS */;
INSERT INTO `padres_alumnos` VALUES (1,'Padre',1),(2,'Madre',2),(3,'Padre',3),(4,'Madre',4),(5,'Padre',5),(6,'Padre',6),(7,'Madre',7),(8,'Padre',8),(9,'Padre',9),(10,'Madre',10);
/*!40000 ALTER TABLE `padres_alumnos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profesores`
--

DROP TABLE IF EXISTS `profesores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profesores` (
  `id_profesor` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido` varchar(50) DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `es_tutor` tinyint(1) DEFAULT NULL,
  `tipo_contrato` enum('Completo','Parcial') DEFAULT NULL,
  `id_departamento` int DEFAULT NULL,
  `id_domicilio` int DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_profesor`),
  KEY `id_departamento` (`id_departamento`),
  KEY `id_domicilio` (`id_domicilio`),
  CONSTRAINT `profesores_ibfk_1` FOREIGN KEY (`id_departamento`) REFERENCES `departamento` (`id_departamento`),
  CONSTRAINT `profesores_ibfk_2` FOREIGN KEY (`id_domicilio`) REFERENCES `domicilio` (`id_domicilio`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profesores`
--

LOCK TABLES `profesores` WRITE;
/*!40000 ALTER TABLE `profesores` DISABLE KEYS */;
INSERT INTO `profesores` VALUES (1,'María','López','5551111111','mlopez@escuela.com',1,'Completo',1,1,NULL),(2,'José','Martínez','5552222222','jmartinez@escuela.com',0,'Parcial',2,2,NULL),(3,'Elena','Ramírez','5553333333','eramirez@escuela.com',1,'Completo',3,3,NULL),(4,'Pedro','Santos','5554444444','psantos@escuela.com',0,'Parcial',4,4,NULL),(5,'Lucía','Torres','5555555555','ltorres@escuela.com',1,'Completo',5,5,NULL),(6,'Andrés','Gómez','5556666666','agomez@escuela.com',0,'Parcial',6,6,NULL),(7,'Marta','Jiménez','5557777777','mjimenez@escuela.com',1,'Completo',7,7,NULL),(8,'Carlos','Rivas','5558888888','crivas@escuela.com',0,'Parcial',8,8,NULL),(9,'Rosa','Hernández','5559999999','rhernandez@escuela.com',1,'Completo',9,9,NULL),(10,'Luis','Domínguez','5550000000','ldominguez@escuela.com',0,'Parcial',10,10,NULL);
/*!40000 ALTER TABLE `profesores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resultados`
--

DROP TABLE IF EXISTS `resultados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resultados` (
  `id_resultado` int NOT NULL AUTO_INCREMENT,
  `calificacion` decimal(4,2) DEFAULT NULL,
  `id_examen` int DEFAULT NULL,
  `id_alumno` int DEFAULT NULL,
  PRIMARY KEY (`id_resultado`),
  KEY `id_examen` (`id_examen`),
  KEY `id_alumno` (`id_alumno`),
  CONSTRAINT `resultados_ibfk_1` FOREIGN KEY (`id_examen`) REFERENCES `examenes` (`id_examen`),
  CONSTRAINT `resultados_ibfk_2` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resultados`
--

LOCK TABLES `resultados` WRITE;
/*!40000 ALTER TABLE `resultados` DISABLE KEYS */;
INSERT INTO `resultados` VALUES (21,90.00,1,1),(22,85.50,1,2),(23,70.00,1,4),(24,95.00,3,3),(25,99.99,6,3),(26,75.00,6,1),(27,92.00,8,2),(28,88.00,9,4),(29,80.00,5,5),(30,78.50,10,7);
/*!40000 ALTER TABLE `resultados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_achievements`
--

DROP TABLE IF EXISTS `user_achievements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_achievements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_achievement` int NOT NULL,
  `otorgado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_achievement` (`id_usuario`,`id_achievement`),
  KEY `idx_id_usuario` (`id_usuario`),
  KEY `idx_id_achievement` (`id_achievement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_achievements`
--

LOCK TABLES `user_achievements` WRITE;
/*!40000 ALTER TABLE `user_achievements` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_achievements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `rol` enum('Alumno','Profesor','Padre','Administrador') DEFAULT NULL,
  `id_alumno` int DEFAULT NULL,
  `id_profesor` int DEFAULT NULL,
  `id_padre` int DEFAULT NULL,
  `id_admin` int DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_usuario`),
  KEY `id_alumno` (`id_alumno`),
  KEY `id_profesor` (`id_profesor`),
  KEY `id_padre` (`id_padre`),
  KEY `id_admin` (`id_admin`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`) ON DELETE CASCADE,
  CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`id_profesor`) REFERENCES `profesores` (`id_profesor`) ON DELETE CASCADE,
  CONSTRAINT `usuarios_ibfk_3` FOREIGN KEY (`id_padre`) REFERENCES `padres` (`id_padre`) ON DELETE CASCADE,
  CONSTRAINT `usuarios_ibfk_4` FOREIGN KEY (`id_admin`) REFERENCES `administradores` (`id_admin`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'sofiag','pass1','Alumno',1,NULL,NULL,NULL,NULL,NULL,1),(2,'miguelp','pass2','Alumno',2,NULL,NULL,NULL,NULL,NULL,1),(3,'valeriat','pass3','Alumno',3,NULL,NULL,NULL,NULL,NULL,1),(4,'daniell','pass4','Alumno',4,NULL,NULL,NULL,NULL,NULL,1),(5,'luciaf','$2y$10$fhF9yI/zOaEXPo/Nsqw7LuW1SIQHhQWNT8WxPOkM9PAy2sGN.YVkS','Alumno',5,NULL,NULL,NULL,NULL,NULL,1),(6,'mateos','$2y$10$QVoH/UkXGAUDvBI2c9A3mOlOPClOcexJSCzRMK6tqf6x3.9Mf2qUm','Alumno',6,NULL,NULL,NULL,NULL,NULL,1),(7,'isareyes','pass7','Alumno',7,NULL,NULL,NULL,NULL,NULL,1),(8,'emilioc','pass8','Alumno',8,NULL,NULL,NULL,NULL,NULL,1),(9,'camilar','pass9','Alumno',9,NULL,NULL,NULL,NULL,NULL,1),(10,'santiortiz','pass10','Alumno',10,NULL,NULL,NULL,NULL,NULL,1),(11,'mlopez','$2y$10$zaytKe5Kd6jOZSwaSII/5e8uHNLaG6DV.eaD9O5mWDJ.Q2H5UEXsW','Profesor',NULL,1,NULL,NULL,NULL,NULL,1),(12,'jmartinez','pass12','Profesor',NULL,2,NULL,NULL,NULL,NULL,1),(13,'eramirez','pass13','Profesor',NULL,3,NULL,NULL,NULL,NULL,1),(14,'psantos','pass14','Profesor',NULL,4,NULL,NULL,NULL,NULL,1),(15,'ltorres','pass15','Profesor',NULL,5,NULL,NULL,NULL,NULL,1),(16,'agomez','pass16','Profesor',NULL,6,NULL,NULL,NULL,NULL,1),(17,'mjimenez','pass17','Profesor',NULL,7,NULL,NULL,NULL,NULL,1),(18,'crivas','pass18','Profesor',NULL,8,NULL,NULL,NULL,NULL,1),(19,'rhernandez','$2y$10$FLhO0Rhsm2HRK5TatKWgaOEOGm6OsmQttT5eKGIOrSEiY4eicC3am','Profesor',NULL,9,NULL,NULL,NULL,NULL,1),(20,'ldominguez','pass20','Profesor',NULL,10,NULL,NULL,NULL,NULL,1),(21,'cgarcia','pass21','Padre',NULL,NULL,1,NULL,NULL,NULL,1),(22,'lperez','pass22','Padre',NULL,NULL,2,NULL,NULL,NULL,1),(23,'atorres','pass23','Padre',NULL,NULL,3,NULL,NULL,NULL,1),(24,'mluna','pass24','Padre',NULL,NULL,4,NULL,NULL,NULL,1),(25,'rflores','pass25','Padre',NULL,NULL,5,NULL,NULL,NULL,1),(26,'lsosa','pass26','Padre',NULL,NULL,6,NULL,NULL,NULL,1),(27,'dreyes','pass27','Padre',NULL,NULL,7,NULL,NULL,NULL,1),(28,'pcastro','pass28','Padre',NULL,NULL,8,NULL,NULL,NULL,1),(29,'jruiz','pass29','Padre',NULL,NULL,9,NULL,NULL,NULL,1),(30,'sortiz','pass30','Padre',NULL,NULL,10,NULL,NULL,NULL,1),(31,'aramos','$2y$10$XxZ0vPAyiAgySjQ//REKd.JBiJVYq1UeFKRoBFQ5znUIPTjnjT9Fe','Administrador',NULL,NULL,NULL,1,NULL,NULL,1),(32,'jnavarro','pass32','Administrador',NULL,NULL,NULL,2,NULL,NULL,1),(33,'emendoza','pass33','Administrador',NULL,NULL,NULL,3,NULL,NULL,1),(34,'rsantos','pass34','Administrador',NULL,NULL,NULL,4,NULL,NULL,1),(35,'smolina','pass35','Administrador',NULL,NULL,NULL,5,NULL,NULL,1),(36,'iperez','pass36','Administrador',NULL,NULL,NULL,6,NULL,NULL,1),(37,'msuarez','pass37','Administrador',NULL,NULL,NULL,7,NULL,NULL,1),(38,'cbravo','pass38','Administrador',NULL,NULL,NULL,8,NULL,NULL,1),(39,'rdiaz','pass39','Administrador',NULL,NULL,NULL,9,NULL,NULL,1),(40,'hramirez','pass40','Administrador',NULL,NULL,NULL,10,NULL,NULL,1);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `vista_compleja_alumnos`
--

DROP TABLE IF EXISTS `vista_compleja_alumnos`;
/*!50001 DROP VIEW IF EXISTS `vista_compleja_alumnos`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_compleja_alumnos` AS SELECT 
 1 AS `id_alumno`,
 1 AS `alumno`,
 1 AS `padre`,
 1 AS `nombre_curso`,
 1 AS `profesor`,
 1 AS `promedio`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vista_profesores_simple`
--

DROP TABLE IF EXISTS `vista_profesores_simple`;
/*!50001 DROP VIEW IF EXISTS `vista_profesores_simple`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_profesores_simple` AS SELECT 
 1 AS `id_profesor`,
 1 AS `nombre`,
 1 AS `apellido`,
 1 AS `es_tutor`,
 1 AS `tipo_contrato`,
 1 AS `departamento`*/;
SET character_set_client = @saved_cs_client;

--
-- Dumping routines for database 'control_escolar'
--
/*!50003 DROP FUNCTION IF EXISTS `fn_CalcularPromedioActual` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_CalcularPromedioActual`(
    p_ID_inscripcion INT
) RETURNS decimal(5,2)
    READS SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE v_promedio DECIMAL(5, 2);

    SELECT 
        COALESCE(SUM(cal.calificacion_obtenida * act.ponderacion / 100), 0.00)
    INTO 
        v_promedio
    FROM 
        `calificaciones` AS cal
    JOIN 
        `actividades` AS act ON cal.ID_actividad = act.ID_actividad
    WHERE 
        cal.ID_inscripcion = p_ID_inscripcion;

    RETURN COALESCE(v_promedio, 0.00);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_CalcularPromedioFinal` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_CalcularPromedioFinal`(
    p_ID_inscripcion INT
) RETURNS decimal(5,2)
    READS SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE v_promedio_final DECIMAL(5, 2);

    SELECT
        COALESCE(SUM(cal.calificacion_obtenida * act.ponderacion / 100), 0.00)
    INTO
        v_promedio_final
    FROM
        `calificaciones` AS cal
    JOIN
        `actividades` AS act ON cal.ID_actividad = act.ID_actividad
    WHERE
        cal.ID_inscripcion = p_ID_inscripcion;

    RETURN COALESCE(v_promedio_final, 0.00);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_ObtenerRolUsuario` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_ObtenerRolUsuario`(
    p_ID_usuario INT
) RETURNS varchar(20) CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci
    READS SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE v_rol VARCHAR(20) DEFAULT 'Desconocido';

    SELECT
        CASE
            WHEN u.id_admin IS NOT NULL THEN 'admin'
            WHEN u.id_profesor IS NOT NULL THEN 'profesor'
            WHEN u.id_alumno IS NOT NULL THEN 'alumno'
            WHEN u.id_padre IS NOT NULL THEN 'padre'
            ELSE 'Desconocido'
        END
    INTO v_rol
    FROM usuarios u
    WHERE u.id_usuario = p_ID_usuario;

    RETURN v_rol;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_ActualizarCalificacion` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ActualizarCalificacion`(
    IN p_ID_usuario_actor INT,
    IN p_ID_calificacion INT,
    IN p_nueva_calificacion DECIMAL(5, 2),
    IN p_comentarios TEXT
)
BEGIN
    DECLARE v_es_admin INT DEFAULT 0;
    DECLARE v_es_profesor INT DEFAULT 0;
    DECLARE v_ID_profesor_actor INT DEFAULT NULL;

    
    SELECT COUNT(*) INTO v_es_admin
    FROM usuarios
    WHERE id_usuario = p_ID_usuario_actor AND rol = 'Administrador';

    SELECT id_profesor INTO v_ID_profesor_actor
    FROM usuarios
    WHERE id_usuario = p_ID_usuario_actor AND id_profesor IS NOT NULL
    LIMIT 1;

    SET v_es_profesor = IF(v_ID_profesor_actor IS NULL, 0, 1);

    IF v_es_admin > 0 OR v_es_profesor > 0 THEN
        UPDATE calificaciones
        SET
            calificacion_obtenida = p_nueva_calificacion,
            comentarios = p_comentarios,
            fecha_registro = CURRENT_TIMESTAMP,
            ID_profesor_que_califica = IF(v_es_profesor > 0, v_ID_profesor_actor, NULL)
        WHERE ID_calificacion = p_ID_calificacion;

        SELECT 'Calificación actualizada correctamente.' AS mensaje;
    ELSE
        SELECT 'Permiso denegado. Solo profesores o administradores pueden modificar calificaciones.' AS mensaje;
    END IF;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_ActualizarUsuario` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ActualizarUsuario`(
    IN p_id_usuario INT,
    IN p_username VARCHAR(50),
    IN p_password VARCHAR(255),
    IN p_rol ENUM('Alumno','Profesor','Padre','Administrador'),
    OUT p_resultado VARCHAR(255)
)
    READS SQL DATA
BEGIN
    DECLARE v_existe INT;
    
    
    SELECT COUNT(*) INTO v_existe FROM usuarios WHERE id_usuario = p_id_usuario;
    
    IF v_existe = 0 THEN
        SET p_resultado = 'Error: El usuario no existe.';
    ELSE
        
        UPDATE usuarios
        SET username = p_username,
            password = p_password,
            rol = p_rol
        WHERE id_usuario = p_id_usuario;
        
        SET p_resultado = 'Usuario actualizado exitosamente.';
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_AgregarUsuario` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_AgregarUsuario`(
    IN p_username VARCHAR(50),
    IN p_password VARCHAR(255),
    IN p_rol ENUM('Alumno','Profesor','Padre','Administrador'),
    IN p_id_alumno INT,
    IN p_id_profesor INT,
    IN p_id_padre INT,
    IN p_id_admin INT,
    OUT p_resultado VARCHAR(255)
)
    READS SQL DATA
BEGIN
    DECLARE v_existe INT;
    
    
    SELECT COUNT(*) INTO v_existe FROM usuarios WHERE username = p_username;
    
    IF v_existe > 0 THEN
        SET p_resultado = 'Error: El usuario ya existe.';
    ELSE
        
        INSERT INTO usuarios (username, password, rol, id_alumno, id_profesor, id_padre, id_admin)
        VALUES (p_username, p_password, p_rol, p_id_alumno, p_id_profesor, p_id_padre, p_id_admin);
        
        SET p_resultado = 'Usuario creado exitosamente.';
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_AprobarInscripcion` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_AprobarInscripcion`(
    IN p_ID_inscripcion INT,
    IN p_ID_profesor_actor INT
)
BEGIN
    UPDATE inscripciones
    SET
        estado = 'Aprobado',
        fecha_aprobacion = CURRENT_TIMESTAMP,
        ID_profesor_que_aprueba = p_ID_profesor_actor
    WHERE
        ID_inscripcion = p_ID_inscripcion
        AND estado = 'Pendiente';
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_CerrarCurso` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_CerrarCurso`(
    IN p_ID_curso INT
)
BEGIN
    DECLARE v_done INT DEFAULT FALSE;
    DECLARE v_ID_inscripcion INT;
    DECLARE v_ID_alumno INT;
    DECLARE v_calificacion_final DECIMAL(5, 2);
    DECLARE v_estado_materia ENUM('Aprobada', 'Reprobada', 'En Curso');

    DECLARE cur_inscripciones CURSOR FOR
        SELECT ID_inscripcion, ID_alumno
        FROM `inscripciones`
        WHERE ID_curso = p_ID_curso AND estado = 'Aprobado';

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = TRUE;

    OPEN cur_inscripciones;

    loop_alumnos: LOOP
        FETCH cur_inscripciones INTO v_ID_inscripcion, v_ID_alumno;
        IF v_done THEN
            LEAVE loop_alumnos;
        END IF;

        SET v_calificacion_final = fn_CalcularPromedioFinal(v_ID_inscripcion);

        IF v_calificacion_final >= 60.00 THEN
            SET v_estado_materia = 'Aprobada';
        ELSE
            SET v_estado_materia = 'Reprobada';
        END IF;

        UPDATE historial_academico
        SET
            calificacion_final = v_calificacion_final,
            estado_materia = v_estado_materia
        WHERE
            ID_alumno = v_ID_alumno AND ID_curso = p_ID_curso;

    END LOOP loop_alumnos;

    CLOSE cur_inscripciones;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_RegistrarUsuarioPorRol` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_RegistrarUsuarioPorRol`(
    IN p_email VARCHAR(100),
    IN p_password_hash VARCHAR(255),
    IN p_nombre VARCHAR(100),
    IN p_apellido_p VARCHAR(100),
    IN p_apellido_m VARCHAR(100),
    IN p_ID_domicilio INT,
    IN p_rol VARCHAR(20),
    IN p_matricula VARCHAR(20),
    IN p_cubiculo VARCHAR(50),
    IN p_especialidad VARCHAR(100),
    IN p_nivel_acceso INT,
    IN p_telefono_contacto VARCHAR(20)
)
BEGIN
    DECLARE v_ID_usuario INT;
    DECLARE v_id_profile INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'Error: No se pudo registrar. Verifique los datos.' AS mensaje;
    END;

    START TRANSACTION;

    
    INSERT INTO usuarios (username, password, rol)
    VALUES (p_email, p_password_hash, p_rol);

    SET v_ID_usuario = LAST_INSERT_ID();

    CASE p_rol
        WHEN 'Alumno' THEN
            IF p_matricula IS NULL OR p_matricula = '' THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La matrícula es obligatoria para el rol Alumno.';
            END IF;
            INSERT INTO alumnos (nombre, apellido, telefono, correo, id_domicilio)
            VALUES (p_nombre, p_apellido_p, p_telefono_contacto, p_email, p_ID_domicilio);
            SET v_id_profile = LAST_INSERT_ID();
            UPDATE usuarios SET id_alumno = v_id_profile WHERE id_usuario = v_ID_usuario;

        WHEN 'Profesor' THEN
            INSERT INTO profesores (nombre, apellido, telefono, correo, id_departamento, id_domicilio)
            VALUES (p_nombre, p_apellido_p, p_telefono_contacto, p_email, NULL, p_ID_domicilio);
            SET v_id_profile = LAST_INSERT_ID();
            UPDATE usuarios SET id_profesor = v_id_profile WHERE id_usuario = v_ID_usuario;

        WHEN 'Administrador' THEN
            INSERT INTO administradores (nombre, apellido, telefono, correo, cargo, id_domicilio)
            VALUES (p_nombre, p_apellido_p, p_telefono_contacto, p_email, 'Administrador', p_ID_domicilio);
            SET v_id_profile = LAST_INSERT_ID();
            UPDATE usuarios SET id_admin = v_id_profile WHERE id_usuario = v_ID_usuario;

        WHEN 'Padre' THEN
            INSERT INTO padres (nombre, apellido, telefono, correo, id_domicilio)
            VALUES (p_nombre, p_apellido_p, p_telefono_contacto, p_email, p_ID_domicilio);
            SET v_id_profile = LAST_INSERT_ID();
            UPDATE usuarios SET id_padre = v_id_profile WHERE id_usuario = v_ID_usuario;

        ELSE
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Rol no válido. No se insertó en tabla de rol.';
    END CASE;

    COMMIT;
    SELECT 'Usuario registrado exitosamente.' AS mensaje;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_SolicitarInscripcion` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_SolicitarInscripcion`(
    IN p_ID_alumno INT,
    IN p_ID_curso INT
)
BEGIN
    DECLARE v_conteo INT;
    
    SELECT COUNT(*) INTO v_conteo
    FROM inscripciones
    WHERE ID_alumno = p_ID_alumno AND ID_curso = p_ID_curso;
    
    IF v_conteo = 0 THEN
        INSERT INTO inscripciones (ID_alumno, ID_curso, estado)
        VALUES (p_ID_alumno, p_ID_curso, 'Pendiente');
        SELECT 'Solicitud de inscripción enviada.' AS `mensaje`;
    ELSE
        SELECT 'Error: Ya tienes una solicitud para este curso.' AS `mensaje`;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `vista_compleja_alumnos`
--

/*!50001 DROP VIEW IF EXISTS `vista_compleja_alumnos`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = cp850 */;
/*!50001 SET character_set_results     = cp850 */;
/*!50001 SET collation_connection      = cp850_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_compleja_alumnos` AS select `a`.`id_alumno` AS `id_alumno`,`a`.`nombre` AS `alumno`,`pa`.`nombre` AS `padre`,`c`.`nombre_curso` AS `nombre_curso`,`pr`.`nombre` AS `profesor`,avg(`r`.`calificacion`) AS `promedio` from (((((`alumnos` `a` join `padres` `pa` on((`a`.`id_padre` = `pa`.`id_padre`))) join `resultados` `r` on((`a`.`id_alumno` = `r`.`id_alumno`))) join `examenes` `e` on((`r`.`id_examen` = `e`.`id_examen`))) join `cursos` `c` on((`e`.`id_curso` = `c`.`id_curso`))) join `profesores` `pr` on((`c`.`id_profesor` = `pr`.`id_profesor`))) group by `a`.`id_alumno`,`c`.`id_curso` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_profesores_simple`
--

/*!50001 DROP VIEW IF EXISTS `vista_profesores_simple`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = cp850 */;
/*!50001 SET character_set_results     = cp850 */;
/*!50001 SET collation_connection      = cp850_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_profesores_simple` AS select `p`.`id_profesor` AS `id_profesor`,`p`.`nombre` AS `nombre`,`p`.`apellido` AS `apellido`,`p`.`es_tutor` AS `es_tutor`,`p`.`tipo_contrato` AS `tipo_contrato`,`d`.`nombre` AS `departamento` from (`profesores` `p` join `departamento` `d` on((`p`.`id_departamento` = `d`.`id_departamento`))) */;
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

-- Dump completed on 2025-11-30 22:16:27
