-- =========================================================================
-- JBL ERP — Esquema y datos de ejemplo de la tienda
-- MySQL 8 / MariaDB 10.4 o superior
--
-- Equivale a ejecutar "php artisan migrate --seed" dentro de erp/.
-- Sirve para revisar el modelo o para cargar la base sin Laravel:
--
--   mysql -u root -p < database/schema.sql
--
-- OJO: borra y vuelve a crear las tablas de la base jbl_erp.
--
-- Tablas del negocio:  users, categorias, productos, pedidos, pedido_items,
--                      movimientos_inventario, mensajes, bitacora
-- Tablas de Laravel:   migrations, password_reset_tokens, sessions, cache,
--                      cache_locks, jobs, job_batches, failed_jobs
--
-- Datos de ejemplo: 3 usuarios (uno por rol, contraseña "admin123"),
-- 5 categorías, 26 productos, 22 pedidos con su detalle, los movimientos de
-- inventario que generaron, 5 mensajes y la bitácora. Los nombres, correos,
-- precios y stock son inventados para la demostración.
--
-- Se genera con mysqldump a partir de una base recién migrada y sembrada;
-- no conviene editarlo a mano.
-- =========================================================================

CREATE DATABASE IF NOT EXISTS jbl_erp
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE jbl_erp;

/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `bitacora`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bitacora` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `usuario` varchar(80) NOT NULL,
  `accion` varchar(20) NOT NULL,
  `modulo` varchar(30) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bitacora_user_id_foreign` (`user_id`),
  KEY `bitacora_accion_index` (`accion`),
  KEY `bitacora_modulo_index` (`modulo`),
  KEY `bitacora_created_at_index` (`created_at`),
  CONSTRAINT `bitacora_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `bitacora` VALUES (1,2,'Editor de catálogo','estado','pedidos','Pasó el pedido JBL-000001 a Entregado','127.0.0.1','2026-11-03 02:38:00');
INSERT INTO `bitacora` VALUES (2,2,'Editor de catálogo','estado','pedidos','Pasó el pedido JBL-000002 a Entregado','127.0.0.1','2026-11-02 21:40:00');
INSERT INTO `bitacora` VALUES (3,2,'Editor de catálogo','estado','pedidos','Pasó el pedido JBL-000003 a Entregado','127.0.0.1','2026-11-03 21:38:00');
INSERT INTO `bitacora` VALUES (4,2,'Editor de catálogo','estado','pedidos','Pasó el pedido JBL-000004 a Entregado','127.0.0.1','2026-11-03 17:55:00');
INSERT INTO `bitacora` VALUES (5,2,'Editor de catálogo','estado','pedidos','Pasó el pedido JBL-000005 a Cancelado','127.0.0.1','2026-11-04 17:39:00');
INSERT INTO `bitacora` VALUES (6,2,'Editor de catálogo','estado','pedidos','Pasó el pedido JBL-000006 a Entregado','127.0.0.1','2026-11-05 22:23:00');
INSERT INTO `bitacora` VALUES (7,2,'Editor de catálogo','estado','pedidos','Pasó el pedido JBL-000007 a Entregado','127.0.0.1','2026-11-06 03:55:00');
INSERT INTO `bitacora` VALUES (8,2,'Editor de catálogo','estado','pedidos','Pasó el pedido JBL-000008 a Entregado','127.0.0.1','2026-11-06 19:24:00');
INSERT INTO `bitacora` VALUES (9,2,'Editor de catálogo','estado','pedidos','Pasó el pedido JBL-000009 a Entregado','127.0.0.1','2026-11-07 20:21:00');
INSERT INTO `bitacora` VALUES (10,2,'Editor de catálogo','estado','pedidos','Pasó el pedido JBL-000010 a Entregado','127.0.0.1','2026-11-08 05:10:00');
INSERT INTO `bitacora` VALUES (11,2,'Editor de catálogo','estado','pedidos','Pasó el pedido JBL-000011 a Entregado','127.0.0.1','2026-11-08 17:21:00');
INSERT INTO `bitacora` VALUES (12,2,'Editor de catálogo','estado','pedidos','Pasó el pedido JBL-000012 a Entregado','127.0.0.1','2026-11-09 21:18:00');
INSERT INTO `bitacora` VALUES (13,2,'Editor de catálogo','estado','pedidos','Pasó el pedido JBL-000013 a Entregado','127.0.0.1','2026-11-09 22:35:00');
INSERT INTO `bitacora` VALUES (14,2,'Editor de catálogo','estado','pedidos','Pasó el pedido JBL-000014 a Cancelado','127.0.0.1','2026-11-10 21:35:00');
INSERT INTO `bitacora` VALUES (15,2,'Editor de catálogo','estado','pedidos','Pasó el pedido JBL-000015 a Enviado','127.0.0.1','2026-11-10 20:13:00');
INSERT INTO `bitacora` VALUES (16,2,'Editor de catálogo','estado','pedidos','Pasó el pedido JBL-000016 a Enviado','127.0.0.1','2026-11-11 22:51:00');
INSERT INTO `bitacora` VALUES (17,2,'Editor de catálogo','estado','pedidos','Pasó el pedido JBL-000017 a Enviado','127.0.0.1','2026-11-12 23:36:00');
INSERT INTO `bitacora` VALUES (18,2,'Editor de catálogo','estado','pedidos','Pasó el pedido JBL-000018 a Enviado','127.0.0.1','2026-11-12 22:38:00');
INSERT INTO `bitacora` VALUES (19,2,'Editor de catálogo','estado','pedidos','Pasó el pedido JBL-000019 a Confirmado','127.0.0.1','2026-11-13 23:50:00');
INSERT INTO `bitacora` VALUES (20,2,'Editor de catálogo','estado','pedidos','Pasó el pedido JBL-000020 a Confirmado','127.0.0.1','2026-11-15 01:21:00');
INSERT INTO `bitacora` VALUES (21,2,'Editor de catálogo','estado','pedidos','Pasó el pedido JBL-000021 a Confirmado','127.0.0.1','2026-11-14 23:36:00');
INSERT INTO `bitacora` VALUES (22,1,'Administrador JBL','crear','usuarios','Creó la cuenta de Editor de catálogo','127.0.0.1','2026-11-02 22:33:18');
INSERT INTO `bitacora` VALUES (23,1,'Administrador JBL','crear','usuarios','Creó la cuenta de Consulta','127.0.0.1','2026-11-02 22:33:18');
INSERT INTO `bitacora` VALUES (24,2,'Editor de catálogo','ingresar','acceso','Ingresó al panel','127.0.0.1','2026-11-13 14:12:00');
INSERT INTO `bitacora` VALUES (25,2,'Editor de catálogo','editar','productos','Actualizó el precio de oferta del JBL Charge 4','127.0.0.1','2026-11-13 14:30:00');
INSERT INTO `bitacora` VALUES (26,1,'Administrador JBL','ingresar','acceso','Ingresó al panel','127.0.0.1','2026-11-14 13:47:00');
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(60) NOT NULL,
  `slug` varchar(70) NOT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `orden` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categorias_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `categorias` VALUES (1,'Parlantes portátiles','parlantes-portatiles','Bluetooth, batería para todo el día y carcasa resistente al agua.',1,1,'2026-11-15 22:33:16','2026-11-15 22:33:16');
INSERT INTO `categorias` VALUES (2,'Parlantes de fiesta','parlantes-de-fiesta','Potencia para llenar una azotea, con luces y asa para llevarlos a donde sea.',1,2,'2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `categorias` VALUES (3,'Audífonos','audifonos','De diadema o deportivos, con Bluetooth y batería para varios días.',1,3,'2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `categorias` VALUES (4,'Audio para casa','audio-para-casa','Parlantes de sala para escuchar sentado, con el sonido clásico de la marca.',1,4,'2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `categorias` VALUES (5,'Audio profesional','audio-profesional','Monitores de estudio y sistemas para escenario.',1,5,'2026-11-15 22:33:17','2026-11-15 22:33:17');
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `mensajes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mensajes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) NOT NULL,
  `correo` varchar(120) NOT NULL,
  `mensaje` text NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `mensajes` VALUES (1,'Ana Torres Vega','ana.torres@example.com','Hola, quisiera saber si el Boombox 3 viene con cargador incluido y cuánto demora el envío a Lince.',1,'2026-11-09 16:33:18','2026-11-09 16:33:18');
INSERT INTO `mensajes` VALUES (2,'Luis Paredes Soto','luis.paredes@example.com','¿El Charge 4 puede cargar un celular mientras reproduce música? Gracias.',1,'2026-11-11 18:33:18','2026-11-11 18:33:18');
INSERT INTO `mensajes` VALUES (3,'María Quispe Huamán','maria.quispe@example.com','Me interesa el Flip 3 para la playa. ¿La resistencia IPX5 aguanta la arena y el agua salada?',0,'2026-11-13 19:33:18','2026-11-13 19:33:18');
INSERT INTO `mensajes` VALUES (4,'Jorge Salazar León','jorge.salazar@example.com','Hice un pedido ayer con recojo en tienda. ¿En qué horario puedo pasar a recogerlo?',0,'2026-11-14 21:33:18','2026-11-14 21:33:18');
INSERT INTO `mensajes` VALUES (5,'Lucía Ramírez Díaz','lucia.ramirez@example.com','Buenas tardes, ¿emiten factura a nombre de empresa? Necesito tres parlantes para una oficina.',0,'2026-11-15 16:33:18','2026-11-15 16:33:18');
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` VALUES (4,'2026_10_18_000001_create_parlantes_table',1);
INSERT INTO `migrations` VALUES (5,'2026_10_18_000002_create_mensajes_table',1);
INSERT INTO `migrations` VALUES (6,'2026_11_15_000001_create_categorias_table',1);
INSERT INTO `migrations` VALUES (7,'2026_11_15_000002_convierte_parlantes_en_productos',1);
INSERT INTO `migrations` VALUES (8,'2026_11_15_000003_create_pedidos_tables',1);
INSERT INTO `migrations` VALUES (9,'2026_11_15_000004_create_movimientos_inventario_table',1);
INSERT INTO `migrations` VALUES (10,'2026_11_15_000005_agrega_roles_y_bitacora',1);
DROP TABLE IF EXISTS `movimientos_inventario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `movimientos_inventario` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `pedido_id` bigint(20) unsigned DEFAULT NULL,
  `tipo` varchar(20) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `stock_resultante` int(10) unsigned NOT NULL,
  `motivo` varchar(160) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `movimientos_inventario_producto_id_foreign` (`producto_id`),
  KEY `movimientos_inventario_user_id_foreign` (`user_id`),
  KEY `movimientos_inventario_pedido_id_foreign` (`pedido_id`),
  KEY `movimientos_inventario_tipo_index` (`tipo`),
  CONSTRAINT `movimientos_inventario_pedido_id_foreign` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `movimientos_inventario_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `movimientos_inventario_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `movimientos_inventario` VALUES (1,1,NULL,NULL,'entrada',40,40,'Stock inicial','2026-11-15 22:33:16','2026-11-15 22:33:16');
INSERT INTO `movimientos_inventario` VALUES (2,2,NULL,NULL,'entrada',34,34,'Stock inicial','2026-11-15 22:33:16','2026-11-15 22:33:16');
INSERT INTO `movimientos_inventario` VALUES (3,3,NULL,NULL,'entrada',3,3,'Stock inicial','2026-11-15 22:33:16','2026-11-15 22:33:16');
INSERT INTO `movimientos_inventario` VALUES (4,4,NULL,NULL,'entrada',16,16,'Stock inicial','2026-11-15 22:33:16','2026-11-15 22:33:16');
INSERT INTO `movimientos_inventario` VALUES (5,5,NULL,NULL,'entrada',11,11,'Stock inicial','2026-11-15 22:33:16','2026-11-15 22:33:16');
INSERT INTO `movimientos_inventario` VALUES (6,6,NULL,NULL,'entrada',25,25,'Stock inicial','2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `movimientos_inventario` VALUES (7,7,NULL,NULL,'entrada',14,14,'Stock inicial','2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `movimientos_inventario` VALUES (8,8,NULL,NULL,'entrada',18,18,'Stock inicial','2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `movimientos_inventario` VALUES (9,9,NULL,NULL,'entrada',22,22,'Stock inicial','2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `movimientos_inventario` VALUES (10,10,NULL,NULL,'entrada',8,8,'Stock inicial','2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `movimientos_inventario` VALUES (11,11,NULL,NULL,'entrada',7,7,'Stock inicial','2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `movimientos_inventario` VALUES (12,12,NULL,NULL,'entrada',9,9,'Stock inicial','2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `movimientos_inventario` VALUES (13,14,NULL,NULL,'entrada',4,4,'Stock inicial','2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `movimientos_inventario` VALUES (14,15,NULL,NULL,'entrada',6,6,'Stock inicial','2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `movimientos_inventario` VALUES (15,16,NULL,NULL,'entrada',5,5,'Stock inicial','2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `movimientos_inventario` VALUES (16,17,NULL,NULL,'entrada',2,2,'Stock inicial','2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `movimientos_inventario` VALUES (17,18,NULL,NULL,'entrada',12,12,'Stock inicial','2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `movimientos_inventario` VALUES (18,19,NULL,NULL,'entrada',15,15,'Stock inicial','2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `movimientos_inventario` VALUES (19,20,NULL,NULL,'entrada',5,5,'Stock inicial','2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `movimientos_inventario` VALUES (20,21,NULL,NULL,'entrada',2,2,'Stock inicial','2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `movimientos_inventario` VALUES (21,22,NULL,NULL,'entrada',1,1,'Stock inicial','2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `movimientos_inventario` VALUES (22,23,NULL,NULL,'entrada',3,3,'Stock inicial','2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `movimientos_inventario` VALUES (23,24,NULL,NULL,'entrada',6,6,'Stock inicial','2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `movimientos_inventario` VALUES (24,25,NULL,NULL,'entrada',2,2,'Stock inicial','2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `movimientos_inventario` VALUES (25,7,NULL,1,'venta',-1,13,'Pedido JBL-000001','2026-11-02 23:38:00','2026-11-02 23:38:00');
INSERT INTO `movimientos_inventario` VALUES (26,4,NULL,1,'venta',-2,14,'Pedido JBL-000001','2026-11-02 23:38:00','2026-11-02 23:38:00');
INSERT INTO `movimientos_inventario` VALUES (27,10,NULL,1,'venta',-2,6,'Pedido JBL-000001','2026-11-02 23:38:00','2026-11-02 23:38:00');
INSERT INTO `movimientos_inventario` VALUES (28,4,NULL,2,'venta',-2,12,'Pedido JBL-000002','2026-11-02 18:40:00','2026-11-02 18:40:00');
INSERT INTO `movimientos_inventario` VALUES (29,9,NULL,2,'venta',-1,21,'Pedido JBL-000002','2026-11-02 18:40:00','2026-11-02 18:40:00');
INSERT INTO `movimientos_inventario` VALUES (30,19,NULL,3,'venta',-1,14,'Pedido JBL-000003','2026-11-03 18:38:00','2026-11-03 18:38:00');
INSERT INTO `movimientos_inventario` VALUES (31,4,NULL,4,'venta',-2,10,'Pedido JBL-000004','2026-11-03 14:55:00','2026-11-03 14:55:00');
INSERT INTO `movimientos_inventario` VALUES (32,1,NULL,5,'venta',-1,39,'Pedido JBL-000005','2026-11-04 14:39:00','2026-11-04 14:39:00');
INSERT INTO `movimientos_inventario` VALUES (33,1,2,5,'devolucion',1,40,'Cancelación del pedido JBL-000005','2026-11-05 14:39:00','2026-11-05 14:39:00');
INSERT INTO `movimientos_inventario` VALUES (34,2,NULL,6,'venta',-2,32,'Pedido JBL-000006','2026-11-05 19:23:00','2026-11-05 19:23:00');
INSERT INTO `movimientos_inventario` VALUES (35,7,NULL,6,'venta',-1,12,'Pedido JBL-000006','2026-11-05 19:23:00','2026-11-05 19:23:00');
INSERT INTO `movimientos_inventario` VALUES (36,4,NULL,6,'venta',-2,8,'Pedido JBL-000006','2026-11-05 19:23:00','2026-11-05 19:23:00');
INSERT INTO `movimientos_inventario` VALUES (37,4,NULL,7,'venta',-1,7,'Pedido JBL-000007','2026-11-06 00:55:00','2026-11-06 00:55:00');
INSERT INTO `movimientos_inventario` VALUES (38,11,NULL,7,'venta',-1,6,'Pedido JBL-000007','2026-11-06 00:55:00','2026-11-06 00:55:00');
INSERT INTO `movimientos_inventario` VALUES (39,12,NULL,7,'venta',-1,8,'Pedido JBL-000007','2026-11-06 00:55:00','2026-11-06 00:55:00');
INSERT INTO `movimientos_inventario` VALUES (40,1,NULL,8,'venta',-2,38,'Pedido JBL-000008','2026-11-06 16:24:00','2026-11-06 16:24:00');
INSERT INTO `movimientos_inventario` VALUES (41,18,NULL,8,'venta',-2,10,'Pedido JBL-000008','2026-11-06 16:24:00','2026-11-06 16:24:00');
INSERT INTO `movimientos_inventario` VALUES (42,5,NULL,9,'venta',-1,10,'Pedido JBL-000009','2026-11-07 17:21:00','2026-11-07 17:21:00');
INSERT INTO `movimientos_inventario` VALUES (43,12,NULL,9,'venta',-1,7,'Pedido JBL-000009','2026-11-07 17:21:00','2026-11-07 17:21:00');
INSERT INTO `movimientos_inventario` VALUES (44,7,NULL,9,'venta',-2,10,'Pedido JBL-000009','2026-11-07 17:21:00','2026-11-07 17:21:00');
INSERT INTO `movimientos_inventario` VALUES (45,7,NULL,10,'venta',-1,9,'Pedido JBL-000010','2026-11-08 02:10:00','2026-11-08 02:10:00');
INSERT INTO `movimientos_inventario` VALUES (46,4,NULL,11,'venta',-1,6,'Pedido JBL-000011','2026-11-08 14:21:00','2026-11-08 14:21:00');
INSERT INTO `movimientos_inventario` VALUES (47,12,NULL,11,'venta',-1,6,'Pedido JBL-000011','2026-11-08 14:21:00','2026-11-08 14:21:00');
INSERT INTO `movimientos_inventario` VALUES (48,7,NULL,12,'venta',-1,8,'Pedido JBL-000012','2026-11-09 18:18:00','2026-11-09 18:18:00');
INSERT INTO `movimientos_inventario` VALUES (49,5,NULL,12,'venta',-2,8,'Pedido JBL-000012','2026-11-09 18:18:00','2026-11-09 18:18:00');
INSERT INTO `movimientos_inventario` VALUES (50,1,NULL,12,'venta',-2,36,'Pedido JBL-000012','2026-11-09 18:18:00','2026-11-09 18:18:00');
INSERT INTO `movimientos_inventario` VALUES (51,9,NULL,13,'venta',-1,20,'Pedido JBL-000013','2026-11-09 19:35:00','2026-11-09 19:35:00');
INSERT INTO `movimientos_inventario` VALUES (52,19,NULL,14,'venta',-1,13,'Pedido JBL-000014','2026-11-10 18:35:00','2026-11-10 18:35:00');
INSERT INTO `movimientos_inventario` VALUES (53,19,2,14,'devolucion',1,14,'Cancelación del pedido JBL-000014','2026-11-11 18:35:00','2026-11-11 18:35:00');
INSERT INTO `movimientos_inventario` VALUES (54,7,NULL,14,'venta',-1,7,'Pedido JBL-000014','2026-11-10 18:35:00','2026-11-10 18:35:00');
INSERT INTO `movimientos_inventario` VALUES (55,7,2,14,'devolucion',1,8,'Cancelación del pedido JBL-000014','2026-11-11 18:35:00','2026-11-11 18:35:00');
INSERT INTO `movimientos_inventario` VALUES (56,19,NULL,15,'venta',-1,13,'Pedido JBL-000015','2026-11-10 17:13:00','2026-11-10 17:13:00');
INSERT INTO `movimientos_inventario` VALUES (57,5,NULL,16,'venta',-1,7,'Pedido JBL-000016','2026-11-11 19:51:00','2026-11-11 19:51:00');
INSERT INTO `movimientos_inventario` VALUES (58,18,NULL,16,'venta',-1,9,'Pedido JBL-000016','2026-11-11 19:51:00','2026-11-11 19:51:00');
INSERT INTO `movimientos_inventario` VALUES (59,8,NULL,17,'venta',-2,16,'Pedido JBL-000017','2026-11-12 20:36:00','2026-11-12 20:36:00');
INSERT INTO `movimientos_inventario` VALUES (60,2,NULL,17,'venta',-2,30,'Pedido JBL-000017','2026-11-12 20:36:00','2026-11-12 20:36:00');
INSERT INTO `movimientos_inventario` VALUES (61,5,NULL,17,'venta',-2,5,'Pedido JBL-000017','2026-11-12 20:36:00','2026-11-12 20:36:00');
INSERT INTO `movimientos_inventario` VALUES (62,18,NULL,18,'venta',-2,7,'Pedido JBL-000018','2026-11-12 19:38:00','2026-11-12 19:38:00');
INSERT INTO `movimientos_inventario` VALUES (63,7,NULL,18,'venta',-2,6,'Pedido JBL-000018','2026-11-12 19:38:00','2026-11-12 19:38:00');
INSERT INTO `movimientos_inventario` VALUES (64,18,NULL,19,'venta',-1,6,'Pedido JBL-000019','2026-11-13 20:50:00','2026-11-13 20:50:00');
INSERT INTO `movimientos_inventario` VALUES (65,8,NULL,19,'venta',-2,14,'Pedido JBL-000019','2026-11-13 20:50:00','2026-11-13 20:50:00');
INSERT INTO `movimientos_inventario` VALUES (66,19,NULL,20,'venta',-2,11,'Pedido JBL-000020','2026-11-14 22:21:00','2026-11-14 22:21:00');
INSERT INTO `movimientos_inventario` VALUES (67,2,NULL,20,'venta',-2,28,'Pedido JBL-000020','2026-11-14 22:21:00','2026-11-14 22:21:00');
INSERT INTO `movimientos_inventario` VALUES (68,9,NULL,20,'venta',-1,19,'Pedido JBL-000020','2026-11-14 22:21:00','2026-11-14 22:21:00');
INSERT INTO `movimientos_inventario` VALUES (69,19,NULL,21,'venta',-1,10,'Pedido JBL-000021','2026-11-14 20:36:00','2026-11-14 20:36:00');
INSERT INTO `movimientos_inventario` VALUES (70,9,NULL,22,'venta',-1,18,'Pedido JBL-000022','2026-11-15 21:04:00','2026-11-15 21:04:00');
INSERT INTO `movimientos_inventario` VALUES (71,8,NULL,22,'venta',-1,13,'Pedido JBL-000022','2026-11-15 21:04:00','2026-11-15 21:04:00');
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `pedido_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pedido_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pedido_id` bigint(20) unsigned NOT NULL,
  `producto_id` bigint(20) unsigned DEFAULT NULL,
  `nombre` varchar(80) NOT NULL,
  `precio_unitario` decimal(8,2) NOT NULL,
  `cantidad` smallint(5) unsigned NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pedido_items_pedido_id_foreign` (`pedido_id`),
  KEY `pedido_items_producto_id_foreign` (`producto_id`),
  CONSTRAINT `pedido_items_pedido_id_foreign` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pedido_items_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `pedido_items` VALUES (1,1,7,'JBL Flip 4',399.00,1,399.00);
INSERT INTO `pedido_items` VALUES (2,1,4,'JBL Clip 5',329.00,2,658.00);
INSERT INTO `pedido_items` VALUES (3,1,10,'JBL Charge 3',499.00,2,998.00);
INSERT INTO `pedido_items` VALUES (4,2,4,'JBL Clip 5',329.00,2,658.00);
INSERT INTO `pedido_items` VALUES (5,2,9,'JBL Charge 4',499.00,1,499.00);
INSERT INTO `pedido_items` VALUES (6,3,19,'JBL Endurance Peak 3',449.00,1,449.00);
INSERT INTO `pedido_items` VALUES (7,4,4,'JBL Clip 5',329.00,2,658.00);
INSERT INTO `pedido_items` VALUES (8,5,1,'JBL Go 4',199.00,1,199.00);
INSERT INTO `pedido_items` VALUES (9,6,2,'JBL Go 2',99.00,2,198.00);
INSERT INTO `pedido_items` VALUES (10,6,7,'JBL Flip 4',399.00,1,399.00);
INSERT INTO `pedido_items` VALUES (11,6,4,'JBL Clip 5',329.00,2,658.00);
INSERT INTO `pedido_items` VALUES (12,7,4,'JBL Clip 5',329.00,1,329.00);
INSERT INTO `pedido_items` VALUES (13,7,11,'JBL Pulse 5',1099.00,1,1099.00);
INSERT INTO `pedido_items` VALUES (14,7,12,'JBL Xtreme',899.00,1,899.00);
INSERT INTO `pedido_items` VALUES (15,8,1,'JBL Go 4',199.00,2,398.00);
INSERT INTO `pedido_items` VALUES (16,8,18,'JBL Live 650BTNC',649.00,2,1298.00);
INSERT INTO `pedido_items` VALUES (17,9,5,'JBL Clip 2',179.00,1,179.00);
INSERT INTO `pedido_items` VALUES (18,9,12,'JBL Xtreme',899.00,1,899.00);
INSERT INTO `pedido_items` VALUES (19,9,7,'JBL Flip 4',399.00,2,798.00);
INSERT INTO `pedido_items` VALUES (20,10,7,'JBL Flip 4',399.00,1,399.00);
INSERT INTO `pedido_items` VALUES (21,11,4,'JBL Clip 5',329.00,1,329.00);
INSERT INTO `pedido_items` VALUES (22,11,12,'JBL Xtreme',899.00,1,899.00);
INSERT INTO `pedido_items` VALUES (23,12,7,'JBL Flip 4',399.00,1,399.00);
INSERT INTO `pedido_items` VALUES (24,12,5,'JBL Clip 2',179.00,2,358.00);
INSERT INTO `pedido_items` VALUES (25,12,1,'JBL Go 4',199.00,2,398.00);
INSERT INTO `pedido_items` VALUES (26,13,9,'JBL Charge 4',499.00,1,499.00);
INSERT INTO `pedido_items` VALUES (27,14,19,'JBL Endurance Peak 3',449.00,1,449.00);
INSERT INTO `pedido_items` VALUES (28,14,7,'JBL Flip 4',399.00,1,399.00);
INSERT INTO `pedido_items` VALUES (29,15,19,'JBL Endurance Peak 3',449.00,1,449.00);
INSERT INTO `pedido_items` VALUES (30,16,5,'JBL Clip 2',179.00,1,179.00);
INSERT INTO `pedido_items` VALUES (31,16,18,'JBL Live 650BTNC',649.00,1,649.00);
INSERT INTO `pedido_items` VALUES (32,17,8,'JBL Flip 3',279.00,2,558.00);
INSERT INTO `pedido_items` VALUES (33,17,2,'JBL Go 2',99.00,2,198.00);
INSERT INTO `pedido_items` VALUES (34,17,5,'JBL Clip 2',179.00,2,358.00);
INSERT INTO `pedido_items` VALUES (35,18,18,'JBL Live 650BTNC',649.00,2,1298.00);
INSERT INTO `pedido_items` VALUES (36,18,7,'JBL Flip 4',399.00,2,798.00);
INSERT INTO `pedido_items` VALUES (37,19,18,'JBL Live 650BTNC',649.00,1,649.00);
INSERT INTO `pedido_items` VALUES (38,19,8,'JBL Flip 3',279.00,2,558.00);
INSERT INTO `pedido_items` VALUES (39,20,19,'JBL Endurance Peak 3',449.00,2,898.00);
INSERT INTO `pedido_items` VALUES (40,20,2,'JBL Go 2',99.00,2,198.00);
INSERT INTO `pedido_items` VALUES (41,20,9,'JBL Charge 4',499.00,1,499.00);
INSERT INTO `pedido_items` VALUES (42,21,19,'JBL Endurance Peak 3',449.00,1,449.00);
INSERT INTO `pedido_items` VALUES (43,22,9,'JBL Charge 4',499.00,1,499.00);
INSERT INTO `pedido_items` VALUES (44,22,8,'JBL Flip 3',279.00,1,279.00);
DROP TABLE IF EXISTS `pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pedidos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) NOT NULL,
  `cliente_nombre` varchar(80) NOT NULL,
  `cliente_correo` varchar(120) NOT NULL,
  `cliente_telefono` varchar(20) NOT NULL,
  `cliente_documento` varchar(15) DEFAULT NULL,
  `entrega` varchar(20) NOT NULL,
  `direccion` varchar(160) DEFAULT NULL,
  `distrito` varchar(60) DEFAULT NULL,
  `referencia` varchar(160) DEFAULT NULL,
  `pago` varchar(20) NOT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'pendiente',
  `subtotal` decimal(10,2) NOT NULL,
  `envio` decimal(8,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `notas` varchar(300) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pedidos_codigo_unique` (`codigo`),
  KEY `pedidos_cliente_correo_index` (`cliente_correo`),
  KEY `pedidos_estado_index` (`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `pedidos` VALUES (1,'JBL-000001','Luis Paredes Soto','luis.paredes@example.com','912345678',NULL,'delivery','Jr. Las Magnolias 118','San Borja',NULL,'transferencia','entregado',2055.00,0.00,2055.00,NULL,'2026-11-02 23:38:00','2026-11-15 22:33:17');
INSERT INTO `pedidos` VALUES (2,'JBL-000002','Pedro Castillo Núñez','pedro.castillo@example.com','967889900',NULL,'delivery','Av. Túpac Amaru 3290','Comas',NULL,'transferencia','entregado',1157.00,0.00,1157.00,NULL,'2026-11-02 18:40:00','2026-11-15 22:33:17');
INSERT INTO `pedidos` VALUES (3,'JBL-000003','Pedro Castillo Núñez','pedro.castillo@example.com','967889900',NULL,'delivery','Av. Túpac Amaru 3290','Comas',NULL,'transferencia','entregado',449.00,0.00,449.00,NULL,'2026-11-03 18:38:00','2026-11-15 22:33:17');
INSERT INTO `pedidos` VALUES (4,'JBL-000004','Ana Torres Vega','ana.torres@example.com','987654321',NULL,'recojo',NULL,NULL,NULL,'yape','entregado',658.00,0.00,658.00,NULL,'2026-11-03 14:55:00','2026-11-15 22:33:17');
INSERT INTO `pedidos` VALUES (5,'JBL-000005','Pedro Castillo Núñez','pedro.castillo@example.com','967889900',NULL,'delivery','Av. Túpac Amaru 3290','Comas',NULL,'yape','cancelado',199.00,12.00,211.00,NULL,'2026-11-04 14:39:00','2026-11-15 22:33:17');
INSERT INTO `pedidos` VALUES (6,'JBL-000006','Ana Torres Vega','ana.torres@example.com','987654321',NULL,'delivery','Av. Arequipa 2450, dpto. 302','Lince',NULL,'contraentrega','entregado',1255.00,0.00,1255.00,NULL,'2026-11-05 19:23:00','2026-11-15 22:33:17');
INSERT INTO `pedidos` VALUES (7,'JBL-000007','Luis Paredes Soto','luis.paredes@example.com','912345678',NULL,'delivery','Jr. Las Magnolias 118','San Borja',NULL,'contraentrega','entregado',2327.00,0.00,2327.00,NULL,'2026-11-06 00:55:00','2026-11-15 22:33:17');
INSERT INTO `pedidos` VALUES (8,'JBL-000008','Rosa Flores Chávez','rosa.flores@example.com','934556677',NULL,'delivery','Av. Universitaria 1801','San Miguel',NULL,'transferencia','entregado',1696.00,0.00,1696.00,NULL,'2026-11-06 16:24:00','2026-11-15 22:33:17');
INSERT INTO `pedidos` VALUES (9,'JBL-000009','Pedro Castillo Núñez','pedro.castillo@example.com','967889900',NULL,'recojo',NULL,NULL,NULL,'contraentrega','entregado',1876.00,0.00,1876.00,NULL,'2026-11-07 17:21:00','2026-11-15 22:33:17');
INSERT INTO `pedidos` VALUES (10,'JBL-000010','Ana Torres Vega','ana.torres@example.com','987654321',NULL,'delivery','Av. Arequipa 2450, dpto. 302','Lince',NULL,'yape','entregado',399.00,0.00,399.00,NULL,'2026-11-08 02:10:00','2026-11-15 22:33:17');
INSERT INTO `pedidos` VALUES (11,'JBL-000011','Lucía Ramírez Díaz','lucia.ramirez@example.com','923445566',NULL,'delivery','Jr. Huiracocha 2080','Jesús María',NULL,'yape','entregado',1228.00,0.00,1228.00,NULL,'2026-11-08 14:21:00','2026-11-15 22:33:17');
INSERT INTO `pedidos` VALUES (12,'JBL-000012','Luis Paredes Soto','luis.paredes@example.com','912345678',NULL,'delivery','Jr. Las Magnolias 118','San Borja',NULL,'contraentrega','entregado',1155.00,0.00,1155.00,NULL,'2026-11-09 18:18:00','2026-11-15 22:33:17');
INSERT INTO `pedidos` VALUES (13,'JBL-000013','Ana Torres Vega','ana.torres@example.com','987654321',NULL,'recojo',NULL,NULL,NULL,'transferencia','entregado',499.00,0.00,499.00,NULL,'2026-11-09 19:35:00','2026-11-15 22:33:17');
INSERT INTO `pedidos` VALUES (14,'JBL-000014','Rosa Flores Chávez','rosa.flores@example.com','934556677',NULL,'recojo',NULL,NULL,NULL,'contraentrega','cancelado',848.00,0.00,848.00,NULL,'2026-11-10 18:35:00','2026-11-15 22:33:17');
INSERT INTO `pedidos` VALUES (15,'JBL-000015','Jorge Salazar León','jorge.salazar@example.com','956778899',NULL,'delivery','Av. Larco 1150','Miraflores',NULL,'yape','enviado',449.00,0.00,449.00,NULL,'2026-11-10 17:13:00','2026-11-15 22:33:17');
INSERT INTO `pedidos` VALUES (16,'JBL-000016','Pedro Castillo Núñez','pedro.castillo@example.com','967889900',NULL,'delivery','Av. Túpac Amaru 3290','Comas',NULL,'transferencia','enviado',828.00,0.00,828.00,NULL,'2026-11-11 19:51:00','2026-11-15 22:33:17');
INSERT INTO `pedidos` VALUES (17,'JBL-000017','Luis Paredes Soto','luis.paredes@example.com','912345678',NULL,'delivery','Jr. Las Magnolias 118','San Borja',NULL,'yape','enviado',1114.00,0.00,1114.00,NULL,'2026-11-12 20:36:00','2026-11-15 22:33:17');
INSERT INTO `pedidos` VALUES (18,'JBL-000018','Lucía Ramírez Díaz','lucia.ramirez@example.com','923445566',NULL,'recojo',NULL,NULL,NULL,'transferencia','enviado',2096.00,0.00,2096.00,NULL,'2026-11-12 19:38:00','2026-11-15 22:33:17');
INSERT INTO `pedidos` VALUES (19,'JBL-000019','Lucía Ramírez Díaz','lucia.ramirez@example.com','923445566',NULL,'recojo',NULL,NULL,NULL,'contraentrega','confirmado',1207.00,0.00,1207.00,NULL,'2026-11-13 20:50:00','2026-11-15 22:33:17');
INSERT INTO `pedidos` VALUES (20,'JBL-000020','María Quispe Huamán','maria.quispe@example.com','945112233',NULL,'delivery','Av. Los Próceres 760','San Juan de Lurigancho',NULL,'contraentrega','confirmado',1595.00,0.00,1595.00,NULL,'2026-11-14 22:21:00','2026-11-15 22:33:17');
INSERT INTO `pedidos` VALUES (21,'JBL-000021','María Quispe Huamán','maria.quispe@example.com','945112233',NULL,'delivery','Av. Los Próceres 760','San Juan de Lurigancho',NULL,'yape','confirmado',449.00,0.00,449.00,NULL,'2026-11-14 20:36:00','2026-11-15 22:33:17');
INSERT INTO `pedidos` VALUES (22,'JBL-000022','María Quispe Huamán','maria.quispe@example.com','945112233',NULL,'delivery','Av. Los Próceres 760','San Juan de Lurigancho',NULL,'contraentrega','pendiente',778.00,0.00,778.00,NULL,'2026-11-15 21:04:00','2026-11-15 22:33:17');
DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `categoria_id` bigint(20) unsigned DEFAULT NULL,
  `nombre` varchar(80) NOT NULL,
  `slug` varchar(90) NOT NULL,
  `sku` varchar(30) DEFAULT NULL,
  `tipo` varchar(40) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `color` varchar(30) DEFAULT NULL,
  `precio` decimal(8,2) NOT NULL DEFAULT 0.00,
  `precio_oferta` decimal(8,2) DEFAULT NULL,
  `stock` int(10) unsigned NOT NULL DEFAULT 0,
  `stock_minimo` int(10) unsigned NOT NULL DEFAULT 5,
  `potencia_w` smallint(5) unsigned DEFAULT NULL,
  `bateria_h` smallint(5) unsigned DEFAULT NULL,
  `resistencia` varchar(10) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `foto_alt` varchar(160) DEFAULT NULL,
  `sello` varchar(30) DEFAULT NULL,
  `destacado` tinyint(1) NOT NULL DEFAULT 0,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `orden` smallint(5) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `parlantes_slug_unique` (`slug`),
  UNIQUE KEY `productos_sku_unique` (`sku`),
  KEY `productos_categoria_id_foreign` (`categoria_id`),
  CONSTRAINT `productos_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `productos` VALUES (1,1,'JBL Go 4','go-4','JBL-GO4-MO','Ultra compacto','La generación más reciente del parlante de bolsillo: a prueba de polvo y agua, con correa de tela para colgarlo de donde quieras.','Morado',199.00,NULL,36,5,4,7,'IP67','uploads/productos/go-4.jpg','Parlante compacto JBL Go 4 morado con correa de tela, visto de frente en un exhibidor','Nuevo',1,1,1,'2026-11-15 22:33:16','2026-11-15 22:33:17');
INSERT INTO `productos` VALUES (2,1,'JBL Go 2','go-2','JBL-GO2-AZ','Ultra compacto','Cabe en la palma de la mano y pesa menos de 200 gramos. Sumergible, con 5 horas de música y micrófono para atender llamadas.','Azul',129.00,99.00,28,5,3,5,'IPX7','uploads/productos/go-2.jpg','Parlante JBL Go 2 azul sostenido en la palma de una mano',NULL,0,1,2,'2026-11-15 22:33:16','2026-11-15 22:33:17');
INSERT INTO `productos` VALUES (3,1,'JBL Go','go','JBL-GO1-RJ','Ultra compacto','El primer Go: un cubo de 3 vatios con entrada auxiliar y manos libres. Para tener música en el escritorio o en la mochila.','Rojo',89.00,NULL,3,5,3,5,NULL,'uploads/productos/go.jpg','Parlante compacto JBL Go rojo de primera generación, de pie sobre una superficie de madera',NULL,0,1,3,'2026-11-15 22:33:16','2026-11-15 22:33:16');
INSERT INTO `productos` VALUES (4,1,'JBL Clip 5','clip-5','JBL-CLP5-AR','Con mosquetón','Se engancha a la mochila, al cinturón o a la bicicleta con su mosquetón integrado. A prueba de polvo y agua.','Arena',329.00,NULL,6,5,7,12,'IP67','uploads/productos/clip-5.jpg','Parlante JBL Clip 5 color arena con mosquetón integrado y logotipo grande, en un exhibidor',NULL,0,1,4,'2026-11-15 22:33:16','2026-11-15 22:33:17');
INSERT INTO `productos` VALUES (5,1,'JBL Clip 2','clip-2','JBL-CLP2-AZ','Con mosquetón','Redondo, sumergible y con mosquetón. Ocho horas de música para llevar colgadas a cualquier parte.','Azul',219.00,179.00,5,5,3,8,'IPX7','uploads/productos/clip-2.jpg','Parlante redondo JBL Clip 2 azul con su mosquetón naranja, sobre un fondo de colores',NULL,0,1,5,'2026-11-15 22:33:16','2026-11-15 22:33:17');
INSERT INTO `productos` VALUES (6,1,'JBL Flip 5','flip-5','JBL-FLIP5-GR','Portátil','El tamaño más pedido de la línea: 20 vatios, 12 horas de batería y carga por USB-C. Sumergible.','Gris',499.00,429.00,25,5,20,12,'IPX7','uploads/productos/flip-5.jpg','Parlante JBL Flip 5 gris sobre un escritorio de madera clara, conectado a su cable de carga',NULL,1,1,6,'2026-11-15 22:33:16','2026-11-15 22:33:16');
INSERT INTO `productos` VALUES (7,1,'JBL Flip 4','flip-4','JBL-FLIP4-BG','Portátil','Doce horas de música y cuerpo sumergible, con manos libres para atender llamadas sin sacar el celular.','Beige',399.00,NULL,6,5,16,12,'IPX7','uploads/productos/flip-4.jpg','Parlante cilíndrico JBL Flip 4 de tela beige, acostado sobre una mesa de madera, con el logotipo rojo al centro',NULL,0,1,7,'2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `productos` VALUES (8,1,'JBL Flip 3','flip-3','JBL-FLIP3-TQ','Portátil','El cilindro que hizo famosa a la línea: dos altavoces, radiadores pasivos a los lados y 10 horas de batería.','Turquesa',349.00,279.00,13,5,16,10,'IPX5','uploads/productos/flip-3.jpg','Parlante JBL Flip 3 turquesa apoyado sobre una mesa de madera',NULL,0,1,8,'2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `productos` VALUES (9,1,'JBL Charge 4','charge-4','JBL-CHG4-NG','Portátil · Powerbank','Veinte horas de música y un puerto USB que carga tu celular mientras suena. Sumergible hasta un metro.','Negro',599.00,499.00,18,5,30,20,'IPX7','uploads/productos/charge-4.jpg','Parlante JBL Charge 4 negro sobre una superficie naranja',NULL,1,1,9,'2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `productos` VALUES (10,1,'JBL Charge 3','charge-3','JBL-CHG3-NG','Portátil · Powerbank','Batería de 20 horas que también carga tu celular por USB. Cuerpo sumergible y graves con radiadores pasivos.','Negro',499.00,NULL,6,5,20,20,'IPX7','uploads/productos/charge-3.jpg','Parlante JBL Charge 3 negro visto de frente sobre un mantel estampado, con el logotipo naranja y los botones superiores',NULL,0,1,10,'2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `productos` VALUES (11,1,'JBL Pulse 5','pulse-5','JBL-PLS5-NG','Portátil con luces','Todo el cuerpo se ilumina al ritmo de la música. Sonido en 360 grados, a prueba de polvo y agua.','Negro',1099.00,NULL,6,5,40,12,'IP67','uploads/productos/pulse-5.jpg','Parlante JBL Pulse 5 de cuerpo translúcido y parte superior negra, de pie sobre una base iluminada',NULL,0,1,11,'2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `productos` VALUES (12,1,'JBL Xtreme','xtreme','JBL-XTR-NG','Portátil con correa','Cuatro altavoces y dos radiadores de graves en un cuerpo que se cuelga del hombro con su correa.','Negro',899.00,NULL,6,5,40,15,'IPX5','uploads/productos/xtreme.jpg','Detalle del radiador pasivo lateral de un parlante JBL Xtreme',NULL,0,1,12,'2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `productos` VALUES (13,1,'JBL Tuner FM','tuner-fm','JBL-TNR-NG','Portátil con radio','Parlante Bluetooth con radio FM, pantalla y botones para guardar tus emisoras favoritas.','Negro',349.00,NULL,0,5,NULL,8,NULL,'uploads/productos/tuner-fm.jpg','Radio parlante portátil JBL Tuner FM negro con pantalla y antena extendida, sobre un fondo rojo',NULL,0,1,13,'2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `productos` VALUES (14,2,'JBL Boombox 3','boombox-3','JBL-BB3-NG','Fiesta','El más potente de la línea portátil: 180 vatios con subwoofer dedicado, a prueba de polvo y agua.','Negro',2299.00,1999.00,4,3,180,24,'IP67','uploads/productos/boombox-3.jpg','Parlante JBL Boombox 3 negro sujeto por su asa','El más potente',1,1,14,'2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `productos` VALUES (15,2,'JBL Boombox 2','boombox-2','JBL-BB2-CM','Fiesta','Graves profundos, 24 horas de batería y un asa pensada para cargarlo con una mano. Sumergible.','Camuflaje',1899.00,NULL,6,3,80,24,'IPX7','uploads/productos/boombox-2.jpg','Parlante JBL Boombox 2 con acabado de camuflaje y asa de transporte',NULL,0,1,15,'2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `productos` VALUES (16,2,'JBL PartyBox On-The-Go','partybox-on-the-go','JBL-PBOTG-NG','Fiesta con micrófono','Cien vatios, aro de luces y micrófono inalámbrico incluido para el karaoke. Se lleva al hombro con su correa.','Negro',1599.00,1399.00,5,3,100,6,'IPX4','uploads/productos/partybox-on-the-go.jpg','Parlante de fiesta JBL PartyBox On-The-Go negro con aro de luz verde encendido y panel de controles superior',NULL,1,1,16,'2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `productos` VALUES (17,2,'JBL PartyBox 710','partybox-710','JBL-PB710-NG','Fiesta · Con ruedas','Ochocientos vatios, dos aros de luces y ruedas para moverlo. Funciona enchufado: no tiene batería.','Negro',3999.00,NULL,2,2,800,NULL,'IPX4','uploads/productos/partybox-710.jpg','Parlante de fiesta JBL PartyBox 710 negro, de pie, con dos aros de luz naranja encendidos',NULL,0,1,17,'2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `productos` VALUES (18,3,'JBL Live 650BTNC','live-650btnc','JBL-L650-NG','Diadema · Cancelación de ruido','Audífonos de diadema con cancelación activa de ruido y asistente de voz. Se pliegan para guardarlos.','Negro',749.00,649.00,6,5,NULL,20,NULL,'uploads/productos/live-650btnc.jpg','Audífonos de diadema JBL Live 650BTNC negros apoyados sobre una mesa de madera clara',NULL,1,1,18,'2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `productos` VALUES (19,3,'JBL Endurance Peak 3','endurance-peak-3','JBL-EP3-CR','Deportivos inalámbricos','Audífonos deportivos con gancho para la oreja: no se caen al correr. Resisten el sudor, el polvo y el agua.','Coral',449.00,NULL,10,5,NULL,50,'IP68','uploads/productos/endurance-peak-3.jpg','Audífono deportivo JBL Endurance Peak 3 color coral con gancho para la oreja, sostenido en una mano',NULL,0,1,19,'2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `productos` VALUES (20,3,'JBL Synchros E50BT','synchros-e50bt','JBL-E50-NG','Diadema Bluetooth','Diadema Bluetooth de almohadillas grandes, para escuchar durante horas. También funciona con cable.','Negro y gris',399.00,299.00,5,5,NULL,18,NULL,'uploads/productos/synchros-e50bt.jpg','Audífonos de diadema JBL Synchros E50BT negros con placa plateada, apoyados en un escritorio mientras cargan',NULL,0,1,20,'2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `productos` VALUES (21,4,'JBL L100 Classic','l100-classic','JBL-L100-MD','Parlante de sala','La reedición del parlante más vendido en la historia de JBL: caja de madera, woofer de 12 pulgadas y tres vías. Necesita amplificador.','Madera y negro',8999.00,NULL,2,1,NULL,NULL,NULL,'uploads/productos/l100-classic.jpg','Parlante de piso JBL L100 Classic con caja de madera, frente negro y woofer de cono blanco, en una exhibición','Clásico',0,1,21,'2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `productos` VALUES (22,4,'JBL TI 5000','ti-5000','JBL-TI5K-NG','Columna de alta fidelidad','Columna de alta fidelidad de colección, con cuatro altavoces. Pieza para quien busca el sonido JBL de otra época. Necesita amplificador.','Negro',6499.00,NULL,1,1,NULL,NULL,NULL,'uploads/productos/ti-5000.jpg','Columna de alta fidelidad JBL TI 5000 negra con cuatro altavoces y lateral inclinado, en la esquina de una sala',NULL,0,1,22,'2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `productos` VALUES (23,4,'JBL Radial','radial','JBL-RDL-NG','Base parlante','Base parlante circular con conector para iPod y entrada auxiliar. Una pieza de diseño para el velador o la sala.','Negro',599.00,449.00,3,2,NULL,NULL,NULL,'uploads/productos/radial.jpg','Base parlante circular JBL Radial negra con un iPod Classic acoplado al centro, sobre una mesa de madera',NULL,0,1,23,'2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `productos` VALUES (24,5,'JBL LSR305','lsr305','JBL-LSR305-PAR','Monitores de estudio · Par','Monitores activos de 5 pulgadas para producir y mezclar en casa. Se venden por par.','Negro',1499.00,NULL,6,2,82,NULL,NULL,'uploads/productos/lsr305.jpg','Par de monitores de estudio JBL LSR305 negros sobre una mesa blanca',NULL,0,1,24,'2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `productos` VALUES (25,5,'JBL VRX932LA','vrx932la','JBL-VRX932','Arreglo lineal','Módulo de arreglo lineal de 12 pulgadas para eventos y locales medianos. Se monta en soporte o se cuelga en columna.','Negro',7999.00,NULL,2,1,800,NULL,NULL,'uploads/productos/vrx932la.jpg','Parlante de arreglo lineal JBL VRX932LA de 12 pulgadas montado sobre un soporte',NULL,0,1,25,'2026-11-15 22:33:17','2026-11-15 22:33:17');
INSERT INTO `productos` VALUES (26,5,'JBL VT4886','vt4886','JBL-VT4886','Arreglo lineal de escenario','Módulo compacto de la serie VerTec para sistemas de escenario. Se vende por unidad, a pedido.','Negro',12999.00,NULL,0,1,NULL,NULL,NULL,'uploads/productos/vt4886.jpg','Módulo de arreglo lineal JBL VT4886 apoyado sobre un escenario al aire libre',NULL,0,1,26,'2026-11-15 22:33:17','2026-11-15 22:33:17');
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `rol` varchar(20) NOT NULL DEFAULT 'admin',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `ultimo_ingreso` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `users` VALUES (1,'Administrador JBL','admin@jbl.pe',NULL,'$2y$12$tMYZEHJYxd8WZ9kPrGZxxuYu1Mcbf9FjzZGLK7TRUjnEsILweUIca','admin',1,NULL,NULL,'2026-11-15 22:33:16','2026-11-15 22:33:16');
INSERT INTO `users` VALUES (2,'Editor de catálogo','editor@jbl.pe',NULL,'$2y$12$6DRYLmqFbOTK8h2vknR40eAGUk0Tlo7BtRctnTqB09WYFiLmO3iua','editor',1,NULL,NULL,'2026-11-15 22:33:16','2026-11-15 22:33:16');
INSERT INTO `users` VALUES (3,'Consulta','consulta@jbl.pe',NULL,'$2y$12$u3KfevrchoXY58SHpVhepOnETBGrAUuTQPo2jEgHCGSommIuoLic6','lector',1,NULL,NULL,'2026-11-15 22:33:16','2026-11-15 22:33:16');
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

