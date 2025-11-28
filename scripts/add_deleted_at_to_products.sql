-- Script SQL để thêm cột deleted_at vào bảng products
-- Chạy script này trong phpMyAdmin hoặc MySQL để thêm cột deleted_at

-- Kiểm tra và thêm cột deleted_at nếu chưa tồn tại
ALTER TABLE `products` 
ADD COLUMN IF NOT EXISTS `deleted_at` DATETIME NULL DEFAULT NULL AFTER `created_at`;

-- Nếu MySQL/MariaDB không hỗ trợ IF NOT EXISTS, chạy lệnh này thay thế:
-- ALTER TABLE `products` ADD COLUMN `deleted_at` DATETIME NULL DEFAULT NULL;

-- Thêm cột updated_at nếu chưa có (tùy chọn, để theo dõi thời gian cập nhật)
ALTER TABLE `products` 
ADD COLUMN IF NOT EXISTS `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

