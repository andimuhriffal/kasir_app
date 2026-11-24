/*
SQLyog Ultimate v13.1.1 (64 bit)
MySQL - 8.0.30 : Database - db_kasir_app
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`db_kasir_app` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `db_kasir_app`;

/*Table structure for table `cache` */

DROP TABLE IF EXISTS `cache`;

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cache` */

/*Table structure for table `cache_locks` */

DROP TABLE IF EXISTS `cache_locks`;

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cache_locks` */

/*Table structure for table `categories` */

DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('aktif','nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `categories` */

insert  into `categories`(`id`,`nama_kategori`,`slug`,`status`,`created_at`,`updated_at`) values 
(1,'Coffee','coffee','aktif','2026-11-24 07:17:46','2026-11-24 07:17:46'),
(2,'Non-Coffee','non-coffee','aktif','2026-11-24 07:17:46','2026-11-24 07:17:46'),
(3,'Food','food','aktif','2026-11-24 07:17:46','2026-11-24 07:17:46'),
(4,'Pastry','pastry','aktif','2026-11-24 07:17:46','2026-11-24 07:17:46');

/*Table structure for table `failed_jobs` */

DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `failed_jobs` */

/*Table structure for table `job_batches` */

DROP TABLE IF EXISTS `job_batches`;

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `job_batches` */

/*Table structure for table `jobs` */

DROP TABLE IF EXISTS `jobs`;

CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `jobs` */

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values 
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2026_11_24_000001_create_categories_table',1),
(5,'2026_11_24_000002_create_products_table',1),
(6,'2026_11_24_000003_create_orders_table',1),
(7,'2026_11_24_000004_create_order_details_table',1),
(8,'2026_11_24_000005_add_nama_pemesan_to_orders_table',2);

/*Table structure for table `order_details` */

DROP TABLE IF EXISTS `order_details`;

CREATE TABLE `order_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `harga_satuan` decimal(12,2) NOT NULL,
  `kuantiti` int NOT NULL,
  `subtotal_baris` decimal(12,2) NOT NULL,
  `catatan_khusus` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_details_order_id_foreign` (`order_id`),
  KEY `order_details_product_id_foreign` (`product_id`),
  CONSTRAINT `order_details_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `order_details` */

insert  into `order_details`(`id`,`order_id`,`product_id`,`harga_satuan`,`kuantiti`,`subtotal_baris`,`catatan_khusus`,`created_at`,`updated_at`) values 
(1,1,1,18000.00,1,18000.00,NULL,'2026-11-18 09:15:00','2026-11-18 09:15:00'),
(2,1,2,20000.00,2,40000.00,NULL,'2026-11-18 09:15:00','2026-11-18 09:15:00'),
(3,2,3,25000.00,2,50000.00,NULL,'2026-11-18 13:25:00','2026-11-18 13:25:00'),
(4,2,4,25000.00,1,25000.00,NULL,'2026-11-18 13:25:00','2026-11-18 13:25:00'),
(5,3,5,15000.00,1,15000.00,NULL,'2026-11-19 09:15:00','2026-11-19 09:15:00'),
(6,3,6,28000.00,2,56000.00,NULL,'2026-11-19 09:15:00','2026-11-19 09:15:00'),
(7,4,7,26000.00,2,52000.00,NULL,'2026-11-19 13:25:00','2026-11-19 13:25:00'),
(8,4,8,24000.00,1,24000.00,NULL,'2026-11-19 13:25:00','2026-11-19 13:25:00'),
(9,5,9,15000.00,1,15000.00,NULL,'2026-11-20 09:15:00','2026-11-20 09:15:00'),
(10,5,10,18000.00,2,36000.00,NULL,'2026-11-20 09:15:00','2026-11-20 09:15:00'),
(11,6,11,28000.00,2,56000.00,NULL,'2026-11-20 13:25:00','2026-11-20 13:25:00'),
(12,6,12,25000.00,1,25000.00,NULL,'2026-11-20 13:25:00','2026-11-20 13:25:00'),
(13,7,13,20000.00,1,20000.00,NULL,'2026-11-21 09:15:00','2026-11-21 09:15:00'),
(14,7,14,18000.00,2,36000.00,NULL,'2026-11-21 09:15:00','2026-11-21 09:15:00'),
(15,8,15,22000.00,2,44000.00,NULL,'2026-11-21 13:25:00','2026-11-21 13:25:00'),
(16,8,16,12000.00,1,12000.00,NULL,'2026-11-21 13:25:00','2026-11-21 13:25:00'),
(17,9,17,18000.00,1,18000.00,NULL,'2026-11-22 09:15:00','2026-11-22 09:15:00'),
(18,9,18,25000.00,2,50000.00,NULL,'2026-11-22 09:15:00','2026-11-22 09:15:00'),
(19,10,1,18000.00,2,36000.00,NULL,'2026-11-22 13:25:00','2026-11-22 13:25:00'),
(20,10,2,20000.00,1,20000.00,NULL,'2026-11-22 13:25:00','2026-11-22 13:25:00'),
(21,11,3,25000.00,1,25000.00,NULL,'2026-11-23 09:15:00','2026-11-23 09:15:00'),
(22,11,4,25000.00,2,50000.00,NULL,'2026-11-23 09:15:00','2026-11-23 09:15:00'),
(23,12,5,15000.00,2,30000.00,NULL,'2026-11-23 13:25:00','2026-11-23 13:25:00'),
(24,12,6,28000.00,1,28000.00,NULL,'2026-11-23 13:25:00','2026-11-23 13:25:00'),
(25,13,7,26000.00,1,26000.00,NULL,'2026-11-24 09:15:00','2026-11-24 09:15:00'),
(26,13,8,24000.00,2,48000.00,NULL,'2026-11-24 09:15:00','2026-11-24 09:15:00'),
(27,14,9,15000.00,2,30000.00,NULL,'2026-11-24 13:25:00','2026-11-24 13:25:00'),
(28,14,10,18000.00,1,18000.00,NULL,'2026-11-24 13:25:00','2026-11-24 13:25:00');

/*Table structure for table `orders` */

DROP TABLE IF EXISTS `orders`;

CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `tipe_pesanan` enum('dine_in','takeaway') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_meja` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_pemesan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_subtotal` decimal(12,2) NOT NULL,
  `pajak_nominal` decimal(12,2) NOT NULL,
  `diskon_nominal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `grand_total` decimal(12,2) NOT NULL,
  `metode_bayar` enum('tunai','qris','transfer','debit') COLLATE utf8mb4_unicode_ci NOT NULL,
  `uang_dibayar` decimal(12,2) NOT NULL,
  `uang_kembali` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` enum('diproses','selesai','dibatalkan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'diproses',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_invoice_number_unique` (`invoice_number`),
  KEY `orders_user_id_foreign` (`user_id`),
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `orders` */

insert  into `orders`(`id`,`invoice_number`,`user_id`,`tipe_pesanan`,`nomor_meja`,`nama_pemesan`,`total_subtotal`,`pajak_nominal`,`diskon_nominal`,`grand_total`,`metode_bayar`,`uang_dibayar`,`uang_kembali`,`status`,`created_at`,`updated_at`) values 
(1,'INV-POS-20261118-0001',2,'dine_in','Meja 5','Budi Santoso',58000.00,5800.00,0.00,63800.00,'transfer',63800.00,0.00,'selesai','2026-11-18 09:15:00','2026-11-18 09:15:00'),
(2,'INV-POS-20261118-0002',2,'takeaway',NULL,'Siti Rahma',75000.00,7000.00,5000.00,77000.00,'debit',77000.00,0.00,'selesai','2026-11-18 13:25:00','2026-11-18 13:25:00'),
(3,'INV-POS-20261119-0001',2,'takeaway',NULL,'Andi Wijaya',71000.00,7100.00,0.00,78100.00,'qris',78100.00,0.00,'selesai','2026-11-19 09:15:00','2026-11-19 09:15:00'),
(4,'INV-POS-20261119-0002',2,'dine_in','Meja 4','Dewi Lestari',76000.00,7600.00,0.00,83600.00,'transfer',83600.00,0.00,'selesai','2026-11-19 13:25:00','2026-11-19 13:25:00'),
(5,'INV-POS-20261120-0001',2,'dine_in','Meja 1','Rina Marlina',51000.00,5100.00,0.00,56100.00,'tunai',66100.00,10000.00,'selesai','2026-11-20 09:15:00','2026-11-20 09:15:00'),
(6,'INV-POS-20261120-0002',2,'takeaway',NULL,'Agus Prasetyo',81000.00,7600.00,5000.00,83600.00,'qris',83600.00,0.00,'selesai','2026-11-20 13:25:00','2026-11-20 13:25:00'),
(7,'INV-POS-20261121-0001',2,'takeaway',NULL,'Maya Putri',56000.00,5600.00,0.00,61600.00,'debit',61600.00,0.00,'selesai','2026-11-21 09:15:00','2026-11-21 09:15:00'),
(8,'INV-POS-20261121-0002',2,'dine_in','Meja 8','Joko Susilo',56000.00,5600.00,0.00,61600.00,'tunai',71600.00,10000.00,'selesai','2026-11-21 13:25:00','2026-11-21 13:25:00'),
(9,'INV-POS-20261122-0001',2,'dine_in','Meja 5','Budi Santoso',68000.00,6800.00,0.00,74800.00,'transfer',74800.00,0.00,'selesai','2026-11-22 09:15:00','2026-11-22 09:15:00'),
(10,'INV-POS-20261122-0002',2,'takeaway',NULL,'Siti Rahma',56000.00,5100.00,5000.00,56100.00,'debit',56100.00,0.00,'selesai','2026-11-22 13:25:00','2026-11-22 13:25:00'),
(11,'INV-POS-20261123-0001',2,'takeaway',NULL,'Andi Wijaya',75000.00,7500.00,0.00,82500.00,'qris',82500.00,0.00,'selesai','2026-11-23 09:15:00','2026-11-23 09:15:00'),
(12,'INV-POS-20261123-0002',2,'dine_in','Meja 4','Dewi Lestari',58000.00,5800.00,0.00,63800.00,'transfer',63800.00,0.00,'selesai','2026-11-23 13:25:00','2026-11-23 13:25:00'),
(13,'INV-POS-20261124-0001',2,'dine_in','Meja 1','Rina Marlina',74000.00,7400.00,0.00,81400.00,'tunai',91400.00,10000.00,'selesai','2026-11-24 09:15:00','2026-11-24 09:15:00'),
(14,'INV-POS-20261124-0002',2,'takeaway',NULL,'Agus Prasetyo',48000.00,4300.00,5000.00,47300.00,'qris',47300.00,0.00,'selesai','2026-11-24 13:25:00','2026-11-24 07:24:07');

/*Table structure for table `products` */

DROP TABLE IF EXISTS `products`;

CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `kode_produk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_produk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga_jual` decimal(12,2) NOT NULL,
  `harga_pokok` decimal(12,2) NOT NULL,
  `stok` int NOT NULL DEFAULT '0',
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pcs',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_kode_produk_unique` (`kode_produk`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `products` */

insert  into `products`(`id`,`category_id`,`kode_produk`,`nama_produk`,`harga_jual`,`harga_pokok`,`stok`,`unit`,`created_at`,`updated_at`) values 
(1,1,'K001','Espresso',18000.00,8000.00,47,'cup','2026-11-24 07:17:46','2026-11-24 09:07:22'),
(2,1,'K002','Americano',20000.00,9000.00,47,'cup','2026-11-24 07:17:46','2026-11-24 08:17:42'),
(3,1,'K003','Cafe Latte',25000.00,11000.00,37,'cup','2026-11-24 07:17:46','2026-11-24 08:08:19'),
(4,1,'K004','Cappuccino',25000.00,11000.00,37,'cup','2026-11-24 07:17:46','2026-11-24 08:18:26'),
(5,1,'K005','Kopi Tubruk',15000.00,6000.00,57,'cup','2026-11-24 07:17:46','2026-11-24 07:17:46'),
(6,1,'K006','V60 Manual Brew',28000.00,12000.00,22,'cup','2026-11-24 07:17:46','2026-11-24 07:17:46'),
(7,2,'N001','Matcha Latte',26000.00,12000.00,27,'cup','2026-11-24 07:17:46','2026-11-24 07:17:46'),
(8,2,'N002','Chocolate',24000.00,10000.00,32,'cup','2026-11-24 07:17:46','2026-11-24 07:17:46'),
(9,2,'N003','Teh Melati',15000.00,5000.00,47,'cup','2026-11-24 07:17:46','2026-11-24 07:17:46'),
(10,2,'N004','Lemon Tea',18000.00,6000.00,37,'cup','2026-11-24 07:17:46','2026-11-24 07:17:46'),
(11,3,'F001','Nasi Goreng Titik Temu',28000.00,12000.00,28,'porsi','2026-11-24 07:17:46','2026-11-24 07:17:46'),
(12,3,'F002','Mie Goreng',25000.00,11000.00,29,'porsi','2026-11-24 07:17:46','2026-11-24 07:17:46'),
(13,3,'F003','Kentang Goreng',20000.00,8000.00,39,'porsi','2026-11-24 07:17:46','2026-11-24 08:17:42'),
(14,3,'F004','Roti Bakar Coklat Keju',18000.00,7000.00,33,'porsi','2026-11-24 07:17:46','2026-11-24 07:17:46'),
(15,4,'P001','Croissant',22000.00,10000.00,18,'pcs','2026-11-24 07:17:46','2026-11-24 08:08:19'),
(16,4,'P002','Donat Gula',12000.00,5000.00,29,'pcs','2026-11-24 07:17:46','2026-11-24 08:18:26'),
(17,4,'P003','Brownies',18000.00,8000.00,24,'pcs','2026-11-24 07:17:46','2026-11-24 07:17:46'),
(18,4,'P004','Cake Slice',25000.00,11000.00,13,'pcs','2026-11-24 07:17:46','2026-11-24 09:06:22');

/*Table structure for table `sessions` */

DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sessions` */

insert  into `sessions`(`id`,`user_id`,`ip_address`,`user_agent`,`payload`,`last_activity`) values 
('2pf4PsqidHq0EADXsTYIT3IagjASEhmUY0OQgSg2',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJpNXJ0MTVhVzJxa3QxYkVsSXR3eEZCS3VtbXhyb0NCdU82bHlOZkZWIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1795511791),
('4IFyLzjkBW5GbIgxEteKebqzyEzNxuRXlI7CVmq7',1,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJMUXBWOUdyaXJ3cXZmSExUSnFQYjgzOWFkZmI3SGQ3bzJxbDZuVjhTIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9kYXNoYm9hcmRcL2twaXMiLCJyb3V0ZSI6ImRhc2hib2FyZC5rcGlzIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsInBhc3N3b3JkX2hhc2hfd2ViIjoiYjk3M2U5ZjcxOWYwMzA2ZjdlOWE1YmFkZGRlZTU0YWQzYjBlZjczOThmM2IyMGFjNzIwMGQ3NmFmMWZiMTFmMCJ9',1795507341),
('4Z9NbrsT0lAYljkv6NDOmnc4clVCrB50a4hRBA7c',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiIzQ0dmaHJJNnJhTnkxeE1kNFlEYzlOdHYxV0RTcm1YTERyeUN6RGZrIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1795512433),
('5MGuCEYXwwKb0pxNgBYlFCnFf3YcZftgrVslfYV8',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJIYXkyZjQ3S1hSYjhlbVZYWHhZdXJYRGwyVkRLdzlndnd2cGFsbGdJIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1795512258),
('7OLITV8JzKo0Tls6iRhXmugrMpQwlODzjPKZkaVt',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJJc05ReEhTalpEWEVzOVVtZXl2RjMzT3YxSEQxR2JFeENOWldXVWZaIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1795512433),
('AIBxQppyssiTcjPdVzCJAKxF6I16gZ9OfMRdoGdw',1,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJNUkFlWEhXNnFKZFhQbU11OGN6MWM4YjFWRlpKcENiODJoNjhJQ0dTIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9kYXNoYm9hcmQiLCJyb3V0ZSI6ImRhc2hib2FyZCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxLCJwYXNzd29yZF9oYXNoX3dlYiI6ImI5NzNlOWY3MTlmMDMwNmY3ZTlhNWJhZGRkZWU1NGFkM2IwZWY3Mzk4ZjNiMjBhYzcyMDBkNzZhZjFmYjExZjAifQ==',1795511706),
('aU7vfywdsv1kXRqRFwOE0X17IcXs1YorZDMNUq8V',1,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJnVEt4UFRrOHk2QmZDbkZmdFA5QklBRnVhdkFxRGROTkd4VzN2VERSIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9kYXNoYm9hcmRcL2twaXMiLCJyb3V0ZSI6ImRhc2hib2FyZC5rcGlzIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsInBhc3N3b3JkX2hhc2hfd2ViIjoiYjk3M2U5ZjcxOWYwMzA2ZjdlOWE1YmFkZGRlZTU0YWQzYjBlZjczOThmM2IyMGFjNzIwMGQ3NmFmMWZiMTFmMCJ9',1795511182),
('b9Y4uCuIyf3US3KOYzQXhZcbQbdBXnjAOMpo0cFj',1,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiIzTk1nTHdUaTBqbk91amFzZUU2emhEa2gyNFQ3OXhXaUpHS0tWeWxvIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9kYXNoYm9hcmRcL2twaXMiLCJyb3V0ZSI6ImRhc2hib2FyZC5rcGlzIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsInBhc3N3b3JkX2hhc2hfd2ViIjoiYjk3M2U5ZjcxOWYwMzA2ZjdlOWE1YmFkZGRlZTU0YWQzYjBlZjczOThmM2IyMGFjNzIwMGQ3NmFmMWZiMTFmMCJ9',1795511242),
('bnf1tgp1XlOkoM4i75QWGeB22d0QS634U0cE6wUB',1,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiIwVjh5bjVVUTl4ajBVQ1pHVEVJUXR5eFFneWhoSzZQcE9veFFHR2FWIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9kYXNoYm9hcmRcL2twaXMiLCJyb3V0ZSI6ImRhc2hib2FyZC5rcGlzIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsInBhc3N3b3JkX2hhc2hfd2ViIjoiYjk3M2U5ZjcxOWYwMzA2ZjdlOWE1YmFkZGRlZTU0YWQzYjBlZjczOThmM2IyMGFjNzIwMGQ3NmFmMWZiMTFmMCJ9',1795511066),
('cbX9ynqiW7XMEvZFudMuA4Oq9YxxPMmJv4E8dmZz',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJHZ1dhNGxYbFNieDVMR3RKVEQ5SHJXdVVYYW1SS1g5VjhGbkZCdGM5IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1795511791),
('cj7gpwcNwhCseUP48w1vT86vrYzy8n3kNZyrDQmy',1,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiI1Z0tQTmsxUExuRE1rWjBzTXk0OEEydWVDYnQxbjJOVWxhVFdYZFlXIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9kYXNoYm9hcmQiLCJyb3V0ZSI6ImRhc2hib2FyZCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxLCJwYXNzd29yZF9oYXNoX3dlYiI6ImI5NzNlOWY3MTlmMDMwNmY3ZTlhNWJhZGRkZWU1NGFkM2IwZWY3Mzk4ZjNiMjBhYzcyMDBkNzZhZjFmYjExZjAifQ==',1795513398),
('d3KcuM8kc5jEcBtiCOKLe8xG0gpH0rRSTLv2G65A',1,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJobnJIc3JHTTEzTjBkRDRTekxtWjRSd2lGekx3WDhWV3ZISVdMb01wIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9kYXNoYm9hcmQiLCJyb3V0ZSI6ImRhc2hib2FyZCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxLCJwYXNzd29yZF9oYXNoX3dlYiI6ImI5NzNlOWY3MTlmMDMwNmY3ZTlhNWJhZGRkZWU1NGFkM2IwZWY3Mzk4ZjNiMjBhYzcyMDBkNzZhZjFmYjExZjAifQ==',1795507985),
('dBqLRJwiCxnvwNw9KOi7GxavSoag5FYexmYZ5K5x',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJqeUJTNDg3TFdJVlFrQ2l6VWxrNFB6OExycXhmeUdjcU12TER1cEwyIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1795507662),
('DfYEkc2aAKuwA9eOUYO5l1PIHCV9RXSVDlORb07E',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJzOWRBOTU5WXdKRHZ0MFNhMEZ5SEptbXNRY1IwbDhEbVVnd29PRUx1IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1795513837),
('DNk769H9ceGwB15KISjxzTWkso36qFU36RZwKDd0',2,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJYeTRDVGtpZm1hdEhNNjVRTWVIclBleXNWMkdvVHJ3d0Z0MWtqSmlNIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9vcmRlcnNcLzI4Iiwicm91dGUiOiJvcmRlcnMuc2hvdyJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoyLCJwYXNzd29yZF9oYXNoX3dlYiI6IjZkNDM1NzBhZjk3NmZlOTNmOWI1NTdiMjAzYTU0Mzk5ZGJhNzg3YzAyNTQyNGU3MjI2ZDJiYjI4ODQxZGY1MjEifQ==',1795511242),
('EAVQ1BLDrlY2PmP8aR2Lm8EYQxuQd0Z5aVdZ4M1U',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJIdUlYN1RQRTZTZ0J1eWFhc3ZEMVRJZHBVbUVzdm9EaHg0NXE5NGZhIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1795511554),
('eK4J046QDBxRvkGS4a4z4jFfqRYpIHuml9XHyJno',2,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJtRmFUY0Voa3h0WnJGNHB4WE1VVnk4c3BHc056dHRpZDY5aFFoRFFKIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9kYXNoYm9hcmQiLCJyb3V0ZSI6ImRhc2hib2FyZCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoyLCJwYXNzd29yZF9oYXNoX3dlYiI6IjZkNDM1NzBhZjk3NmZlOTNmOWI1NTdiMjAzYTU0Mzk5ZGJhNzg3YzAyNTQyNGU3MjI2ZDJiYjI4ODQxZGY1MjEifQ==',1795507985),
('fokJlwpDomeAEEJEqpxgCxXLSs44cKqBHeDhOxiR',2,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJ0WjlINU5teklTVmZiRElZaTc1RGZDRUlhYTc5ZU5nNlJmcE90VE1kIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9yZXBvcnRzXC9sYWJhLXJ1Z2kiLCJyb3V0ZSI6InJlcG9ydHMubGFiYS1ydWdpIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjIsInBhc3N3b3JkX2hhc2hfd2ViIjoiNmQ0MzU3MGFmOTc2ZmU5M2Y5YjU1N2IyMDNhNTQzOTlkYmE3ODdjMDI1NDI0ZTcyMjZkMmJiMjg4NDFkZjUyMSJ9',1795513840),
('fR9X4dKYTyh0P64W3vzoiU5vdstBSgE9SJIgHmp4',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJ6eXBJRmJvU1hzYUMzVGphYUl0MjNnWWlxTjFrbFJNTlVtelo1WHBMIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1795507316),
('hFHubpYzBaOWkHrwE7T0GJm8nJgVf428KzaelRBO',1,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJSektsWHRkd0NTOWhnSFVFa0k0RzBCM0NWTWhYOEZyY2JjNk9FQzVxIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9yZXBvcnRzIiwicm91dGUiOiJyZXBvcnRzLmluZGV4In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsInBhc3N3b3JkX2hhc2hfd2ViIjoiYjk3M2U5ZjcxOWYwMzA2ZjdlOWE1YmFkZGRlZTU0YWQzYjBlZjczOThmM2IyMGFjNzIwMGQ3NmFmMWZiMTFmMCJ9',1795513840),
('IFos5Iaw7tyXXwNIDkcbmQaLu7pop08VfArAQpkr',1,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJpYXBQeDFpcGl0dGZqMkZRRlNlcU55RVMyQndxMG4xMzNmcjZxb1NlIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9kYXNoYm9hcmRcL2twaXMiLCJyb3V0ZSI6ImRhc2hib2FyZC5rcGlzIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsInBhc3N3b3JkX2hhc2hfd2ViIjoiYjk3M2U5ZjcxOWYwMzA2ZjdlOWE1YmFkZGRlZTU0YWQzYjBlZjczOThmM2IyMGFjNzIwMGQ3NmFmMWZiMTFmMCJ9',1795513113),
('isCngxcDeP9L1IWYyPchjteuSrvPHtIqVQfP5ogj',2,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJ0Nk1LWHZYNUg4YXVjdEhwNVdXTTkzWTBYZERWb094WWY4RUtZbjBGIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9vcmRlcnNcLzIzIiwicm91dGUiOiJvcmRlcnMuc2hvdyJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoyLCJwYXNzd29yZF9oYXNoX3dlYiI6IjZkNDM1NzBhZjk3NmZlOTNmOWI1NTdiMjAzYTU0Mzk5ZGJhNzg3YzAyNTQyNGU3MjI2ZDJiYjI4ODQxZGY1MjEifQ==',1795507699),
('jxmnxeJmo2t5apsKxB7UpMmwnwLVS3hFLStLarep',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJ3dElva25XYm5xellmdXVnODd2Q21TbTR1MVQyT2VLaExtZEpKaWpwIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1795511666),
('jyziaAwr8R4WZFa4nvrmzadqctpsTKCrPn2Mzsxe',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/158.0.0.0 Safari/537.36 Edg/158.0.0.0','eyJfdG9rZW4iOiJyNjM2aTcxRmc3VnJwNVcyMmxENndqbWhQcjJNSlFoYmFhaEVMODg3IiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9fQ==',1795511855),
('mINlkJS9D63p5W816NUiyPyrdSgjxK3PdwNcZ6fA',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiI5UHY1a0FGc294a0ZxODRYMTFNdDVYd1l6UDI2VHZ3M21vUUhmcWdYIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1795512732),
('Ms0VpXSwmKzWxDDJvNRL14dbs3Mk69cLh3UH7jiu',2,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJvelJlTkxBaTMzUjlNT0pkakFYYVFVUUlIaFJ2MXF4anFhRUgybzl0IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9vcmRlcnNcLzIxIiwicm91dGUiOiJvcmRlcnMuc2hvdyJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoyLCJwYXNzd29yZF9oYXNoX3dlYiI6IjZkNDM1NzBhZjk3NmZlOTNmOWI1NTdiMjAzYTU0Mzk5ZGJhNzg3YzAyNTQyNGU3MjI2ZDJiYjI4ODQxZGY1MjEifQ==',1795507011),
('mSwXfuPJn3YYb0qRpwpCU3a4cRnlSKEI3enHY0yP',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJXQ0wyU3lNUlQwTEpHTEVMRUdvNHdxV3lSV0F4cHBuRVJxSTR6RkRCIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1795511666),
('mu7ElneyCtRzjZwBVfkZZebTgFh66sbY21KDxBk9',1,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJ4ZkVsRGZUc1E3NFdJUDRpb2pIYVRHVWEwd2U5cDhWQUQ2dXZoc0twIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9kYXNoYm9hcmRcL2twaXMiLCJyb3V0ZSI6ImRhc2hib2FyZC5rcGlzIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsInBhc3N3b3JkX2hhc2hfd2ViIjoiYjk3M2U5ZjcxOWYwMzA2ZjdlOWE1YmFkZGRlZTU0YWQzYjBlZjczOThmM2IyMGFjNzIwMGQ3NmFmMWZiMTFmMCJ9',1795512259),
('O5s8FPjUP89jOfInueHt7DS2dPmindYjsJ0TBG0I',2,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJPNFZBVXBoalJiVVRjVlo1RW5CVnZkUlBaZkRvNTlwZkxXOWlvTXRUIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9vcmRlcnNcLzI3Iiwicm91dGUiOiJvcmRlcnMuc2hvdyJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoyLCJwYXNzd29yZF9oYXNoX3dlYiI6IjZkNDM1NzBhZjk3NmZlOTNmOWI1NTdiMjAzYTU0Mzk5ZGJhNzg3YzAyNTQyNGU3MjI2ZDJiYjI4ODQxZGY1MjEifQ==',1795511182),
('oyzNvlTumXb4F9lELZ1HGF6RvpovPTlSmBQCHiTP',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJwYnFhYW1oWldzZXNrcU5Sa2VOM1FuWXBHdUEySE5PaW82ZDhVc2xYIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1795513028),
('PmABhMx802YqLKMMdeFaMZ343obeQW9kfjYi36M5',2,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJMWEhxblcwbGJBdHFUMk1FVHk1cmYwV0pqWlh3NDRTWk5LeTh3SEFNIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9vcmRlcnNcLzIyIiwicm91dGUiOiJvcmRlcnMuc2hvdyJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoyLCJwYXNzd29yZF9oYXNoX3dlYiI6IjZkNDM1NzBhZjk3NmZlOTNmOWI1NTdiMjAzYTU0Mzk5ZGJhNzg3YzAyNTQyNGU3MjI2ZDJiYjI4ODQxZGY1MjEifQ==',1795507340),
('PvkwN4zC33RDtCE2QvIKcmpMMGmsfdNyaZozl2is',1,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJVWE85MjNjTFJnV2ZNQjM4aUFwV2x6QldkNWdxQ3ZqZ1Q2YVZmWVFKIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9vcmRlcnNcL2NyZWF0ZSIsInJvdXRlIjoib3JkZXJzLmNyZWF0ZSJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxLCJwYXNzd29yZF9oYXNoX3dlYiI6ImI5NzNlOWY3MTlmMDMwNmY3ZTlhNWJhZGRkZWU1NGFkM2IwZWY3Mzk4ZjNiMjBhYzcyMDBkNzZhZjFmYjExZjAifQ==',1795513375),
('q1seUEX5NrC5Lz1yvdKKR2NIt1BkaDlCQE1omo0Z',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJFOHAyaHpOQ3VET0owbDJ0SDY1SVdFQ1hnazRtVVZ2UGJ6UTF2WklpIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1795508590),
('rsB6Q7xhNzs7zFL1LyYP6X0Obbkt5MHxLPlFEDoi',1,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJ4OEtScVZBU2dhcW8zaXBuUjNPWmlDNHM0WVY0TVduMkJXREtQUG1PIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9kYXNoYm9hcmRcL2twaXMiLCJyb3V0ZSI6ImRhc2hib2FyZC5rcGlzIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsInBhc3N3b3JkX2hhc2hfd2ViIjoiYjk3M2U5ZjcxOWYwMzA2ZjdlOWE1YmFkZGRlZTU0YWQzYjBlZjczOThmM2IyMGFjNzIwMGQ3NmFmMWZiMTFmMCJ9',1795506733),
('RYhVoBvKCGYwTKRM7HDT3JvWoTAjz8scXQHBKZym',1,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJjWm5ZRWs0WXFFMFVSaGp6QzhPTjRNc3FBenQ3OTNvbk9uRndSbmt0IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9kYXNoYm9hcmRcL2twaXMiLCJyb3V0ZSI6ImRhc2hib2FyZC5rcGlzIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsInBhc3N3b3JkX2hhc2hfd2ViIjoiYjk3M2U5ZjcxOWYwMzA2ZjdlOWE1YmFkZGRlZTU0YWQzYjBlZjczOThmM2IyMGFjNzIwMGQ3NmFmMWZiMTFmMCJ9',1795507011),
('rz2PjzlrizOkA6Ql5zSEZ0wtLrf1DR150WtbvFX6',1,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJJMXEyUE5WOXFIQWJQVDZyb09kM09RNlJHZHN2Ymk3NDNxQ2xCeTgyIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9kYXNoYm9hcmQiLCJyb3V0ZSI6ImRhc2hib2FyZCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxLCJwYXNzd29yZF9oYXNoX3dlYiI6ImI5NzNlOWY3MTlmMDMwNmY3ZTlhNWJhZGRkZWU1NGFkM2IwZWY3Mzk4ZjNiMjBhYzcyMDBkNzZhZjFmYjExZjAifQ==',1795511667),
('TUVyTGWgKlSOG7hOkp57O3jLXe5ijBW5LBPt1bDU',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiI0dExKWERCd3U3azJ0bGJCaEJqZ2Z1aXJVZVY2Uk1NQzhEc1pzRTFvIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1795511553),
('U8FZ3cXLYBsiieKfn8FalzUf7lMitHq4jnZCyIr9',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiIwQ082Z25FTFhLOEZ5OFZ6SVZRUTFma0REWnZOV2xEVURWT2hWZnV6IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1795507984),
('UEljjBq2NcIwRh8CsVhPeCS19kBxxrCO4VdYEHl3',1,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJpczhEY3FoMk5PeDBXWmlYRkZGZGFsc3RwT3hNakRybFRoUlJQSWNsIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9kYXNoYm9hcmRcL2twaXMiLCJyb3V0ZSI6ImRhc2hib2FyZC5rcGlzIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsInBhc3N3b3JkX2hhc2hfd2ViIjoiYjk3M2U5ZjcxOWYwMzA2ZjdlOWE1YmFkZGRlZTU0YWQzYjBlZjczOThmM2IyMGFjNzIwMGQ3NmFmMWZiMTFmMCJ9',1795513035),
('VBYmdgNrr7iBikbKHSwhlCshbetwJhsVZ906BqQZ',2,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/158.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJmZjNWMlF2WjdsSzFIU2hzdTY3NUFhSUZFRGc1eDNBUElXYjVFSVltIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2Rhc2hib2FyZCIsInJvdXRlIjoiZGFzaGJvYXJkIn0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoyLCJwYXNzd29yZF9oYXNoX3dlYiI6IjZkNDM1NzBhZjk3NmZlOTNmOWI1NTdiMjAzYTU0Mzk5ZGJhNzg3YzAyNTQyNGU3MjI2ZDJiYjI4ODQxZGY1MjEifQ==',1795513919),
('Vl1r12YSEgSaZXFoY91MjQ9nwZTTU48sKCJkZQUG',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJFQVVhaFFKaWNBb3JvMkhCUTlDcEZzTE9PWDB0b2JDbnlTUUZrWUFPIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1795506711),
('W2SqdyCj91Gt7lM23MH0h38CIUI1YBTltnnok1kd',1,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJmRzEzajlBSloyVDZ0RmVNWjFYdWNzbTdERlJLODV4NU1TbVNNVzdHIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9kYXNoYm9hcmQiLCJyb3V0ZSI6ImRhc2hib2FyZCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxLCJwYXNzd29yZF9oYXNoX3dlYiI6ImI5NzNlOWY3MTlmMDMwNmY3ZTlhNWJhZGRkZWU1NGFkM2IwZWY3Mzk4ZjNiMjBhYzcyMDBkNzZhZjFmYjExZjAifQ==',1795508590),
('XDQv33N9ZRYYAZRXUd05ciVsSnBj29zf8kvSS1X0',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJrUG43TU5OVWU4V0hlaTg1OVA3UlBUTzhmVVVTejFvWjM4T3VtZFpXIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1795510994),
('xoj9ObcJyTauatVmn6H3nHCGiuyv9kXGrvv2cQee',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJEVU1YbG1FMVhLckRZY05aWGVYdjBncDNTbXc0dlg2SjF3TnY5Qkt0IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1795513374),
('xVc3thKyLsrOFkF9dAyCXugxoNpuV5EfGzzRluBJ',1,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJGYUlVY3pocW4zT21BMEhlVGlHbFRsTjFRbGpkSHRGOEtUU2IyRUNHIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9kYXNoYm9hcmQiLCJyb3V0ZSI6ImRhc2hib2FyZCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxLCJwYXNzd29yZF9oYXNoX3dlYiI6ImI5NzNlOWY3MTlmMDMwNmY3ZTlhNWJhZGRkZWU1NGFkM2IwZWY3Mzk4ZjNiMjBhYzcyMDBkNzZhZjFmYjExZjAifQ==',1795511555),
('Ykuo8KFiu6KFC76uXKvoDe5kFTUsojlQN4nxoILA',2,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJia0xpUHRIUjNwOVNzUjZQVUdKVndWNVlTRE0xcHBPbWlVbmlUOWVNIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9vcmRlcnNcLzIwIiwicm91dGUiOiJvcmRlcnMuc2hvdyJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoyLCJwYXNzd29yZF9oYXNoX3dlYiI6IjZkNDM1NzBhZjk3NmZlOTNmOWI1NTdiMjAzYTU0Mzk5ZGJhNzg3YzAyNTQyNGU3MjI2ZDJiYjI4ODQxZGY1MjEifQ==',1795506733),
('ysfQLFWjA8cyK5viGTkvITv4FdCsaQghzdkMHcn0',2,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiI2V1R3WXRoelNRRkl4VktLM1gxc0ZBWENDMWFzSGE1MDh6ckFQTzVvIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9vcmRlcnMiLCJyb3V0ZSI6Im9yZGVycy5pbmRleCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoyLCJwYXNzd29yZF9oYXNoX3dlYiI6IjZkNDM1NzBhZjk3NmZlOTNmOWI1NTdiMjAzYTU0Mzk5ZGJhNzg3YzAyNTQyNGU3MjI2ZDJiYjI4ODQxZGY1MjEifQ==',1795511066),
('YtZIpXacoeq3KC617QSiYGqC3jDWrgFFvdtzo6rA',1,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJzeEpoUWxuUXR2SWxlaEJINUlQZkxrQjRYYU9iREN5SnRRS2hzZndHIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9vcmRlcnNcL3N0YXR1c2VzP2lkcz0xJTJDMiIsInJvdXRlIjoib3JkZXJzLnN0YXR1c2VzIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsInBhc3N3b3JkX2hhc2hfd2ViIjoiYjk3M2U5ZjcxOWYwMzA2ZjdlOWE1YmFkZGRlZTU0YWQzYjBlZjczOThmM2IyMGFjNzIwMGQ3NmFmMWZiMTFmMCJ9',1795512734),
('zDm8d1QDOKaiFS0yVkk78VjpByFvQGAXYtF4NQw0',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJFZUVQcXh2ZXM0WWRJalpEaHNTS0dvckw2WHJkUDRJV1dmWTNpcVN6IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1795513112);

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_lengkap` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','kasir') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kasir',
  `status` enum('aktif','nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`username`,`nama_lengkap`,`password`,`role`,`status`,`remember_token`,`created_at`,`updated_at`) values 
(1,'admin','Administrator','$2y$12$PuxiwjQp97XfGN/yg0u2cucGuHrUxWYmBQp3W/YDp3m1ZW0RftQsy','admin','aktif',NULL,'2026-11-24 07:17:46','2026-11-24 07:17:46'),
(2,'kasir','Kasir Utama','$2y$12$AVOpY9B2rYKt642w2QehquyeprEVWhrneQi.lYynPqwW7zNCQu7du','kasir','aktif',NULL,'2026-11-24 07:17:46','2026-11-24 07:17:46');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
