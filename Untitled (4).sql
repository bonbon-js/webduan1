CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `full_name` varchar(100),
  `email` varchar(100) UNIQUE,
  `password` varchar(255),
  `phone` varchar(15),
  `address` text,
  `role` varchar(20),
  `session_token` varchar(255),
  `session_token_expires` datetime,
  `created_at` datetime
);

CREATE TABLE `categories` (
  `category_id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `category_name` varchar(100),
  `description` text,
  `created_at` datetime
);

CREATE TABLE `products` (
  `product_id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `product_name` varchar(150),
  `description` text,
  `price` decimal(10,2),
  `stock` int,
  `category_id` int,
  `created_at` datetime
);

CREATE TABLE `product_variants` (
  `variant_id` int PRIMARY KEY,
  `product_id` int,
  `sku` varchar(50),
  `additional_price` decimal,
  `stock` int
);

CREATE TABLE `attributes` (
  `attribute_id` int PRIMARY KEY,
  `attribute_name` varchar(100)
);

CREATE TABLE `attribute_values` (
  `value_id` int PRIMARY KEY,
  `attribute_id` int,
  `value_name` varchar(100)
);

CREATE TABLE `product_attribute_values` (
  `id` int PRIMARY KEY,
  `product_id` int,
  `variant_id` int,
  `value_id` int
);

CREATE TABLE `product_images` (
  `image_id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `product_id` int,
  `image_url` varchar(255),
  `is_primary` boolean DEFAULT 0
);

CREATE TABLE `variant_images` (
  `variant_image_id` int PRIMARY KEY,
  `variant_id` int,
  `image_url` varchar(255),
  `is_primary` boolean
);

CREATE TABLE `carts` (
  `cart_id` int PRIMARY KEY,
  `user_id` int,
  `created_at` datetime
);

CREATE TABLE `cart_items` (
  `cart_item_id` int PRIMARY KEY,
  `cart_id` int,
  `product_id` int,
  `variant_id` int,
  `quantity` int
);

CREATE TABLE `coupons` (
  `coupon_id` int PRIMARY KEY,
  `code` varchar(50) UNIQUE,
  `discount_percent` int,
  `valid_from` date,
  `valid_to` date,
  `status` varchar(20)
);

CREATE TABLE `orders` (
  `order_id` int PRIMARY KEY,
  `user_id` int,
  `order_date` datetime,
  `total_amount` decimal,
  `status` varchar(50),
  `shipping_address` text,
  `payment_method` varchar(50),
  `coupon_id` int
);

CREATE TABLE `order_details` (
  `detail_id` int PRIMARY KEY,
  `order_id` int,
  `product_id` int,
  `variant_id` int,
  `quantity` int,
  `price` decimal
);

CREATE TABLE `reviews` (
  `review_id` int PRIMARY KEY,
  `user_id` int,
  `product_id` int,
  `rating` int,
  `comment` text,
  `is_hidden` boolean,
  `created_at` datetime
);

CREATE TABLE `posts` (
  `post_id` int PRIMARY KEY,
  `user_id` int,
  `title` varchar(200),
  `slug` varchar(255) UNIQUE,
  `content` text,
  `thumbnail` varchar(255),
  `created_at` datetime,
  `updated_at` datetime,
  `status` varchar(20)
);

CREATE TABLE `password_resets` (
  `reset_id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int,
  `token` varchar(255) UNIQUE,
  `otp_code` varchar(10),
  `expires_at` datetime,
  `is_used` boolean,
  `created_at` datetime
);

ALTER TABLE `products` ADD FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

ALTER TABLE `product_variants` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

ALTER TABLE `attribute_values` ADD FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`attribute_id`);

ALTER TABLE `product_attribute_values` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

ALTER TABLE `product_attribute_values` ADD FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`);

ALTER TABLE `product_attribute_values` ADD FOREIGN KEY (`value_id`) REFERENCES `attribute_values` (`value_id`);

ALTER TABLE `product_images` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

ALTER TABLE `variant_images` ADD FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`);

ALTER TABLE `carts` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

ALTER TABLE `cart_items` ADD FOREIGN KEY (`cart_id`) REFERENCES `carts` (`cart_id`);

ALTER TABLE `cart_items` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

ALTER TABLE `cart_items` ADD FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`);

ALTER TABLE `orders` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

ALTER TABLE `orders` ADD FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`coupon_id`);

ALTER TABLE `order_details` ADD FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

ALTER TABLE `order_details` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

ALTER TABLE `order_details` ADD FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`);

ALTER TABLE `reviews` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

ALTER TABLE `reviews` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

ALTER TABLE `posts` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

ALTER TABLE `password_resets` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

-- Insert dữ liệu mẫu cho categories
INSERT INTO `categories` (`category_name`, `description`, `created_at`) VALUES
('Áo Nam', 'Áo thun, áo sơ mi, áo khoác dành cho nam giới', NOW()),
('Áo Nữ', 'Áo thun, áo sơ mi, áo khoác dành cho nữ giới', NOW()),
('Quần Nam', 'Quần jean, quần tây, quần short dành cho nam giới', NOW()),
('Quần Nữ', 'Quần jean, quần tây, quần legging dành cho nữ giới', NOW()),
('Váy & Đầm', 'Váy, đầm thời trang cho nữ giới', NOW()),
('Phụ Kiện', 'Mũ, túi xách, thắt lưng và các phụ kiện thời trang', NOW());

-- Insert dữ liệu mẫu cho products
INSERT INTO `products` (`product_name`, `description`, `price`, `stock`, `category_id`, `created_at`) VALUES
-- Quần áo Nam
('Áo Thun Nam Basic', 'Áo thun nam form chuẩn, chất liệu cotton mềm mại, thoáng mát', 150000.00, 50, 1, NOW()),
('Áo Sơ Mi Nam Dài Tay', 'Áo sơ mi nam công sở, form slim fit, lịch sự và sang trọng', 350000.00, 40, 1, NOW()),
('Áo Khoác Nam Kaki', 'Áo khoác nam màu kaki, chống nắng, phong cách trẻ trung', 450000.00, 35, 1, NOW()),
('Quần Jean Nam Ống Đứng', 'Quần jean nam ống đứng, chất liệu denim cao cấp, bền đẹp', 550000.00, 45, 3, NOW()),
('Quần Tây Nam Âu', 'Quần tây nam công sở, form ôm, chất liệu vải cao cấp', 400000.00, 40, 3, NOW()),
('Quần Short Nam Thể Thao', 'Quần short nam thể thao, co giãn tốt, thoáng mát', 200000.00, 60, 3, NOW()),
-- Quần áo Nữ
('Áo Thun Nữ Form Rộng', 'Áo thun nữ form rộng, nhiều màu sắc, dễ phối đồ', 120000.00, 70, 2, NOW()),
('Áo Sơ Mi Nữ Cổ Bẻ', 'Áo sơ mi nữ cổ bẻ, form vừa vặn, thanh lịch', 280000.00, 55, 2, NOW()),
('Áo Khoác Nữ Bomber', 'Áo khoác nữ phong cách bomber, trẻ trung và năng động', 380000.00, 42, 2, NOW()),
('Quần Jean Nữ Ống Loe', 'Quần jean nữ ống loe, tôn dáng, chất liệu co giãn', 420000.00, 58, 4, NOW()),
('Quần Tây Nữ Cạp Cao', 'Quần tây nữ cạp cao, form ôm, tôn dáng chân dài', 320000.00, 50, 4, NOW()),
('Quần Legging Nữ', 'Quần legging nữ thể thao, co giãn tốt, thoải mái khi vận động', 180000.00, 65, 4, NOW()),
-- Váy & Đầm
('Đầm Xòe Nữ Hoa', 'Đầm xòe nữ họa tiết hoa, nữ tính và thanh lịch', 450000.00, 35, 5, NOW()),
('Váy Ngắn Nữ Phối Màu', 'Váy ngắn nữ phối màu, trẻ trung và cá tính', 320000.00, 48, 5, NOW()),
('Đầm Dài Nữ Cổ Yếm', 'Đầm dài nữ cổ yếm, vintage và sang trọng', 520000.00, 30, 5, NOW()),
('Váy Body Nữ', 'Váy body nữ ôm dáng, tôn đường cong cơ thể', 380000.00, 40, 5, NOW()),
-- Phụ Kiện
('Mũ Lưỡi Trai', 'Mũ lưỡi trai unisex, nhiều màu, bảo vệ khỏi nắng', 150000.00, 80, 6, NOW()),
('Túi Xách Tote', 'Túi xách tote nữ, da giả cao cấp, tiện lợi', 250000.00, 45, 6, NOW()),
('Thắt Lưng Da', 'Thắt lưng da nam/nữ, bền đẹp, nhiều size', 180000.00, 60, 6, NOW()),
('Túi Đeo Chéo', 'Túi đeo chéo unisex, phong cách streetwear', 200000.00, 55, 6, NOW());

-- Insert dữ liệu mẫu cho product_images
INSERT INTO `product_images` (`product_id`, `image_url`, `is_primary`) VALUES
-- Hình ảnh cho Áo Thun Nam Basic (product_id = 1)
(1, 'assets/uploads/products/ao-thun-nam-1.jpg', 1),
(1, 'assets/uploads/products/ao-thun-nam-2.jpg', 0),
-- Hình ảnh cho Áo Sơ Mi Nam Dài Tay (product_id = 2)
(2, 'assets/uploads/products/ao-so-mi-nam-1.jpg', 1),
(2, 'assets/uploads/products/ao-so-mi-nam-2.jpg', 0),
-- Hình ảnh cho Áo Khoác Nam Kaki (product_id = 3)
(3, 'assets/uploads/products/ao-khoac-nam-1.jpg', 1),
-- Hình ảnh cho Quần Jean Nam Ống Đứng (product_id = 4)
(4, 'assets/uploads/products/quan-jean-nam-1.jpg', 1),
(4, 'assets/uploads/products/quan-jean-nam-2.jpg', 0),
-- Hình ảnh cho Quần Tây Nam Âu (product_id = 5)
(5, 'assets/uploads/products/quan-tay-nam-1.jpg', 1),
-- Hình ảnh cho Quần Short Nam Thể Thao (product_id = 6)
(6, 'assets/uploads/products/quan-short-nam-1.jpg', 1),
-- Hình ảnh cho Áo Thun Nữ Form Rộng (product_id = 7)
(7, 'assets/uploads/products/ao-thun-nu-1.jpg', 1),
(7, 'assets/uploads/products/ao-thun-nu-2.jpg', 0),
-- Hình ảnh cho Áo Sơ Mi Nữ Cổ Bẻ (product_id = 8)
(8, 'assets/uploads/products/ao-so-mi-nu-1.jpg', 1),
-- Hình ảnh cho Áo Khoác Nữ Bomber (product_id = 9)
(9, 'assets/uploads/products/ao-khoac-nu-1.jpg', 1),
(9, 'assets/uploads/products/ao-khoac-nu-2.jpg', 0),
-- Hình ảnh cho Quần Jean Nữ Ống Loe (product_id = 10)
(10, 'assets/uploads/products/quan-jean-nu-1.jpg', 1),
-- Hình ảnh cho Quần Tây Nữ Cạp Cao (product_id = 11)
(11, 'assets/uploads/products/quan-tay-nu-1.jpg', 1),
(11, 'assets/uploads/products/quan-tay-nu-2.jpg', 0),
-- Hình ảnh cho Quần Legging Nữ (product_id = 12)
(12, 'assets/uploads/products/quan-legging-nu-1.jpg', 1),
-- Hình ảnh cho Đầm Xòe Nữ Hoa (product_id = 13)
(13, 'assets/uploads/products/dam-xoe-nu-1.jpg', 1),
(13, 'assets/uploads/products/dam-xoe-nu-2.jpg', 0),
-- Hình ảnh cho Váy Ngắn Nữ Phối Màu (product_id = 14)
(14, 'assets/uploads/products/vay-ngan-nu-1.jpg', 1),
-- Hình ảnh cho Đầm Dài Nữ Cổ Yếm (product_id = 15)
(15, 'assets/uploads/products/dam-dai-nu-1.jpg', 1),
(15, 'assets/uploads/products/dam-dai-nu-2.jpg', 0),
-- Hình ảnh cho Váy Body Nữ (product_id = 16)
(16, 'assets/uploads/products/vay-body-nu-1.jpg', 1),
-- Hình ảnh cho Mũ Lưỡi Trai (product_id = 17)
(17, 'assets/uploads/products/mu-luoi-trai-1.jpg', 1),
-- Hình ảnh cho Túi Xách Tote (product_id = 18)
(18, 'assets/uploads/products/tui-xach-tote-1.jpg', 1),
(18, 'assets/uploads/products/tui-xach-tote-2.jpg', 0),
-- Hình ảnh cho Thắt Lưng Da (product_id = 19)
(19, 'assets/uploads/products/that-lung-da-1.jpg', 1),
-- Hình ảnh cho Túi Đeo Chéo (product_id = 20)
(20, 'assets/uploads/products/tui-deo-cheo-1.jpg', 1);
