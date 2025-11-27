-- Fix AUTO_INCREMENT cho bảng users
-- Chạy file này trong phpMyAdmin hoặc MySQL để sửa lỗi đăng ký

ALTER TABLE `users` 
MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

-- Nếu đã có dữ liệu, set AUTO_INCREMENT tiếp theo
-- Thay đổi số 2 thành số lớn hơn user_id lớn nhất hiện tại nếu cần
ALTER TABLE `users` AUTO_INCREMENT = 2;

