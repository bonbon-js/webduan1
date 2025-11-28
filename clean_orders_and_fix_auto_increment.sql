-- ============================================
-- Script SQL để SỬA HOÀN TOÀN lỗi Duplicate entry '0' for key 'PRIMARY'
-- Chạy TẤT CẢ các câu lệnh này trong phpMyAdmin (tab SQL)
-- ============================================

-- Bước 1: Xóa dữ liệu trong order_items có order_id = 0 hoặc NULL
DELETE FROM order_items WHERE order_id = 0 OR order_id IS NULL;

-- Bước 2: Xóa dữ liệu trong orders có order_id = 0 hoặc NULL
DELETE FROM orders WHERE order_id = 0 OR order_id IS NULL;

-- Bước 3: Đảm bảo AUTO_INCREMENT được bật (chạy ngay cả khi đã có)
ALTER TABLE orders MODIFY order_id INT(11) NOT NULL AUTO_INCREMENT;

-- Bước 4: Lấy MAX ID và set AUTO_INCREMENT
-- (Chạy câu này để xem MAX ID trước)
SELECT MAX(order_id) as max_id, MAX(order_id) + 1 as next_id FROM orders;

-- Bước 5: Set AUTO_INCREMENT = MAX(order_id) + 1
-- (Thay số 1 bằng giá trị từ bước 4, ví dụ: nếu MAX = 5 thì dùng 6)
ALTER TABLE orders AUTO_INCREMENT = 1;

-- Bước 6: Kiểm tra lại (chạy để xác nhận)
SHOW TABLE STATUS LIKE 'orders';
SHOW COLUMNS FROM orders WHERE `Key` = 'PRI';

