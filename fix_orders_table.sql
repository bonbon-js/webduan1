-- Script để sửa lỗi AUTO_INCREMENT và xóa bản ghi id = 0
-- Dựa trên schema từ bonbon_shop.sql

-- Bước 1: Xác định bảng đang dùng
-- Kiểm tra xem bảng nào có cột fullname
-- Nếu có fullname -> dùng orders_new (PRIMARY KEY là 'id')
-- Nếu không có fullname -> dùng orders (PRIMARY KEY là 'order_id')

-- ============================================
-- SỬA BẢNG orders_new (nếu đang dùng bảng này)
-- ============================================

-- Xóa bản ghi có id = 0
DELETE FROM orders_new WHERE id = 0;

-- Reset AUTO_INCREMENT
SET @max_id = (SELECT COALESCE(MAX(id), 0) + 1 FROM orders_new);
SET @sql = CONCAT('ALTER TABLE orders_new AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Đảm bảo cột id có AUTO_INCREMENT
ALTER TABLE orders_new MODIFY COLUMN id INT(11) NOT NULL AUTO_INCREMENT;

-- ============================================
-- SỬA BẢNG orders (nếu đang dùng bảng này)
-- ============================================

-- Xóa bản ghi có order_id = 0
DELETE FROM orders WHERE order_id = 0;

-- Reset AUTO_INCREMENT
SET @max_id = (SELECT COALESCE(MAX(order_id), 0) + 1 FROM orders);
SET @sql = CONCAT('ALTER TABLE orders AUTO_INCREMENT = ', @max_id);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Đảm bảo cột order_id có AUTO_INCREMENT
ALTER TABLE orders MODIFY COLUMN order_id INT(11) NOT NULL AUTO_INCREMENT;

-- ============================================
-- TẮT SQL_MODE NO_AUTO_VALUE_ON_ZERO (nếu có)
-- ============================================
-- Lưu ý: Chỉ chạy nếu bạn muốn tắt mode này
-- SET SESSION sql_mode = '';

-- Kiểm tra kết quả
SELECT 'Fixed! Current AUTO_INCREMENT values:' as message;
SHOW TABLE STATUS LIKE 'orders%';

