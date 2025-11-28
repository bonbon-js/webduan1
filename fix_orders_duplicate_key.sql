-- ============================================
-- Script SQL để sửa lỗi Duplicate entry '0' for key 'PRIMARY'
-- Chạy từng câu lệnh này trong phpMyAdmin (tab SQL)
-- ============================================

-- Bước 1: Xóa các bản ghi trong order_items có order_id = 0 hoặc NULL
DELETE FROM order_items WHERE order_id = 0 OR order_id IS NULL;

-- Bước 2: Xóa các bản ghi trong orders có order_id = 0 hoặc NULL
DELETE FROM orders WHERE order_id = 0 OR order_id IS NULL;

-- Bước 3: Lấy ID lớn nhất hiện tại (chạy để xem)
SELECT MAX(order_id) as max_id FROM orders;

-- Bước 4: Đảm bảo AUTO_INCREMENT được bật cho order_id
ALTER TABLE orders MODIFY order_id INT(11) NOT NULL AUTO_INCREMENT;

-- Bước 5: Set AUTO_INCREMENT bắt đầu từ giá trị lớn hơn MAX ID
-- (Thay số 1 bằng MAX(order_id) + 1 từ bước 3, ví dụ: nếu MAX = 5 thì dùng 6)
ALTER TABLE orders AUTO_INCREMENT = 1;

-- Bước 6: Kiểm tra lại (chạy để xác nhận)
SHOW TABLE STATUS LIKE 'orders';

-- Bước 7: Kiểm tra cấu trúc cột order_id (chạy để xác nhận có AUTO_INCREMENT)
SHOW COLUMNS FROM orders WHERE `Key` = 'PRI';

