-- Script SQL để tạo bảng product_images
-- Chạy script này trong phpMyAdmin hoặc MySQL để tạo bảng product_images

-- Tạo bảng product_images
CREATE TABLE IF NOT EXISTS `product_images` (
  `image_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`image_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Nếu MySQL/MariaDB không hỗ trợ IF NOT EXISTS, chạy lệnh này thay thế:
-- CREATE TABLE `product_images` (
--   `image_id` int(11) NOT NULL AUTO_INCREMENT,
--   `product_id` int(11) DEFAULT NULL,
--   `image_url` varchar(255) DEFAULT NULL,
--   `is_primary` tinyint(1) DEFAULT NULL,
--   PRIMARY KEY (`image_id`),
--   KEY `product_id` (`product_id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

