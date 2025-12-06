-- Script sửa giá sản phẩm (chia cho 100)
-- Chạy script này trong phpMyAdmin hoặc MySQL client

UPDATE products 
SET price = price / 100 
WHERE price > 10000;

-- Kiểm tra kết quả
SELECT product_id, product_name, price 
FROM products 
ORDER BY product_id;
