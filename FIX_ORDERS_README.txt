========================================
HƯỚNG DẪN SỬA LỖI: Duplicate entry '0' for key 'PRIMARY'
========================================

Lỗi này xảy ra khi:
- Có dữ liệu với order_id = 0 trong database
- AUTO_INCREMENT chưa được bật cho cột order_id
- Code đang cố insert giá trị vào PRIMARY KEY

CÁCH SỬA (Chọn 1 trong 2 cách):

CÁCH 1: Chạy SQL trong phpMyAdmin (KHUYẾN NGHỊ)
-----------------------------------------------
1. Mở phpMyAdmin
2. Chọn database: bonbon_shop
3. Vào tab SQL
4. Copy và chạy các câu lệnh sau (từng câu một):

DELETE FROM order_items WHERE order_id = 0 OR order_id IS NULL;
DELETE FROM orders WHERE order_id = 0 OR order_id IS NULL;
ALTER TABLE orders MODIFY order_id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE orders AUTO_INCREMENT = 1;

5. Kiểm tra lại:
SHOW TABLE STATUS LIKE 'orders';
SHOW COLUMNS FROM orders WHERE `Key` = 'PRI';

CÁCH 2: Chạy file SQL
---------------------
1. Mở file: fix_orders_duplicate_key.sql
2. Copy toàn bộ nội dung
3. Paste vào phpMyAdmin (tab SQL)
4. Chạy

SAU KHI SỬA:
- Thử đặt hàng lại
- Nếu vẫn lỗi, kiểm tra error log để xem chi tiết

LƯU Ý:
- Code đã được cập nhật để KHÔNG insert PRIMARY KEY
- Nếu vẫn lỗi, có thể do dữ liệu cũ trong database
- Hãy chạy script SQL để xóa dữ liệu có order_id = 0

