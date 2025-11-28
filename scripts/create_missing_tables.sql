-- Script SQL để tạo các bảng còn thiếu trong database
-- Chạy script này trong phpMyAdmin hoặc MySQL

-- Tạo bảng product_images nếu chưa tồn tại
CREATE TABLE IF NOT EXISTS `product_images` (
  `image_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`image_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tạo bảng product_variants nếu chưa tồn tại
CREATE TABLE IF NOT EXISTS `product_variants` (
  `variant_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `additional_price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  PRIMARY KEY (`variant_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tạo bảng product_attribute_values nếu chưa tồn tại
CREATE TABLE IF NOT EXISTS `product_attribute_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `value_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `variant_id` (`variant_id`),
  KEY `value_id` (`value_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Thêm cột deleted_at vào bảng products nếu chưa có
ALTER TABLE `products` 
ADD COLUMN IF NOT EXISTS `deleted_at` DATETIME NULL DEFAULT NULL;

-- Thêm cột updated_at vào bảng products nếu chưa có
ALTER TABLE `products` 
ADD COLUMN IF NOT EXISTS `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP;

