-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 28, 2025 lúc 05:26 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `bonbon_shop`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `attributes`
--

CREATE TABLE `attributes` (
  `attribute_id` int(11) NOT NULL,
  `attribute_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `attributes`
--

INSERT INTO `attributes` (`attribute_id`, `attribute_name`) VALUES
(1, 'Size'),
(2, 'Color');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `attribute_values`
--

CREATE TABLE `attribute_values` (
  `value_id` int(11) NOT NULL,
  `attribute_id` int(11) DEFAULT NULL,
  `value_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `attribute_values`
--

INSERT INTO `attribute_values` (`value_id`, `attribute_id`, `value_name`) VALUES
(1, 1, 'S'),
(2, 1, 'M'),
(3, 1, 'L'),
(4, 1, 'XL'),
(5, 1, 'XXL'),
(6, 2, 'Black'),
(7, 2, 'White'),
(8, 2, 'Navy'),
(9, 2, 'Gray'),
(10, 2, 'Red'),
(11, 2, 'Beige'),
(12, 2, 'Blue'),
(13, 1, '29'),
(14, 1, '30'),
(15, 1, '31'),
(16, 1, '32'),
(17, 1, '33');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `carts`
--

CREATE TABLE `carts` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `carts`
--

INSERT INTO `carts` (`cart_id`, `user_id`, `created_at`) VALUES
(2, 1, '2025-11-27 03:46:22'),
(3, 3, '2025-11-27 21:03:46'),
(4, 4, '2025-11-28 11:02:59');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` int(11) NOT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cart_items`
--

INSERT INTO `cart_items` (`cart_item_id`, `cart_id`, `product_id`, `variant_id`, `quantity`) VALUES
(8, 2, 1, 2, 2),
(11, 2, 1, NULL, 1),
(12, 2, 3, 10, 1),
(13, 3, 2, NULL, 1),
(14, 2, 9, NULL, 1),
(15, 4, 13, NULL, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`, `created_at`) VALUES
(0, 'link', 'slall', NULL),
(1, 'Áo Polo', 'Áo polo nam cao cấp, phong cách lịch lãm', '2025-11-26 18:07:33'),
(2, 'Áo Khoác', 'Áo khoác nam thời trang, giữ ấm tốt', '2025-11-26 18:07:33'),
(3, 'Hoodie', 'Áo hoodie nam trẻ trung, năng động', '2025-11-26 18:07:33'),
(4, 'Quần', 'Quần nam đa dạng kiểu dáng', '2025-11-26 18:07:33'),
(5, 'Sơ mi', 'Áo sơ mi nam công sở, lịch sự', '2025-11-26 18:07:33');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `coupons`
--

CREATE TABLE `coupons` (
  `coupon_id` int(11) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL DEFAULT 'percentage',
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_discount_amount` decimal(10,2) DEFAULT NULL,
  `start_date` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `end_date` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `discount_percent` int(11) DEFAULT NULL,
  `valid_from` date DEFAULT NULL,
  `valid_to` date DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `coupons`
--

INSERT INTO `coupons` (`coupon_id`, `code`, `name`, `description`, `discount_type`, `discount_value`, `min_order_amount`, `max_discount_amount`, `start_date`, `end_date`, `usage_limit`, `used_count`, `discount_percent`, `valid_from`, `valid_to`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'WELCOME10', '', NULL, 'percentage', 0.00, 0.00, NULL, '2025-11-27 21:26:29', '2025-12-27 21:26:29', NULL, 0, 10, '2025-01-01', '2025-12-31', 'active', '2025-11-27 21:24:27', '2025-11-27 21:30:19', '2025-11-27 21:30:19'),
(2, 'SUMMER20', 'mã chào hè', '', 'percentage', 10.00, 500000.00, 100000.00, '2025-11-27 21:26:00', '2025-12-27 21:26:00', 2, 0, 20, '2025-06-01', '2025-08-31', 'active', '2025-11-27 21:24:27', '2025-11-27 22:09:35', NULL),
(3, 'VIP30', 'mã khách vip', '', 'percentage', 20.00, 700000.00, 200000.00, '2025-11-27 21:26:00', '2025-12-27 21:26:00', 5, 0, 30, '2025-01-01', '2025-12-31', 'active', '2025-11-27 21:24:27', '2025-11-27 22:10:34', NULL),
(4, 'FLASH15', 'mã giảm nhanh', '', 'fixed', 15000.00, 500000.00, NULL, '2025-11-27 21:26:00', '2025-12-27 21:26:00', 5, 0, 15, '2025-11-01', '2025-11-30', 'active', '2025-11-27 21:24:27', '2025-11-27 22:11:36', NULL),
(5, 'NEWUSER5', 'mã người mới', '', 'fixed', 50000.00, 500000.00, NULL, '2025-11-27 21:26:00', '2025-12-27 21:26:00', 6, 0, 5, '2025-01-01', '2025-12-31', 'active', '2025-11-27 21:24:27', '2025-11-27 22:12:19', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `order_date` datetime DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `coupon_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders_new`
--

CREATE TABLE `orders_new` (
  `id` int(11) NOT NULL,
  `order_code` varchar(50) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `ward` varchar(100) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `cancel_reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `detail_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `variant_size` varchar(50) DEFAULT NULL,
  `variant_color` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_resets`
--

CREATE TABLE `password_resets` (
  `reset_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `otp_code` varchar(10) DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `is_used` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `password_resets`
--

INSERT INTO `password_resets` (`reset_id`, `user_id`, `token`, `otp_code`, `expires_at`, `is_used`, `created_at`) VALUES
(1, 5, 'e43ed36d4002eb26ae9bdeee9970fa636769b88d959f47a035b66af7cb0a8295', '664262', '2025-11-25 20:34:33', 1, '2025-11-26 02:04:33');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `posts`
--

CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `excerpt` text DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `posts`
--

INSERT INTO `posts` (`post_id`, `user_id`, `title`, `excerpt`, `slug`, `content`, `thumbnail`, `created_at`, `updated_at`, `status`, `is_featured`) VALUES
(1, NULL, 'BST Polo Thu Đông 2025', NULL, 'bst-polo-thu-dong-2025', 'Bộ sưu tập Polo Thu Đông 2025 của BonBonwear mang đến phong cách tối giản, chất liệu mềm mại cùng gam màu trung tính. Được thiết kế dành cho những người đàn ông hiện đại, yêu thích sự lịch lãm nhưng vẫn thoải mái trong mọi hoàn cảnh.\n\nCác sản phẩm trong BST được làm từ cotton cao cấp, thấm hút mồ hôi tốt, phù hợp cho cả thời tiết se lạnh của mùa thu và những ngày đông ấm áp. Gam màu chủ đạo là đen, trắng, navy và xám - những tông màu dễ phối đồ và luôn hợp thời trang.', 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=600&q=80', '2025-11-26 18:07:33', '2025-11-26 18:07:33', 'published', 0),
(2, NULL, 'Cách phối áo Polo đa dạng', NULL, 'cach-phoi-ao-polo-da-dang', 'Áo Polo là món đồ vô cùng linh hoạt trong tủ đồ của mọi quý ông. Từ công sở đến dạo phố, Polo luôn phù hợp mọi hoàn cảnh.\n\n**Phối đồ công sở:** Kết hợp áo Polo với quần kaki hoặc quần tây, giày da lịch sự. Chọn màu trung tính như navy, đen hoặc trắng để tạo vẻ chuyên nghiệp.\n\n**Phối đồ dạo phố:** Mix cùng quần jean, sneaker trắng và áo khoác bomber để có set đồ năng động, trẻ trung.\n\n**Phối đồ cuối tuần:** Áo Polo + quần short + sandal = outfit hoàn hảo cho những buổi gặp gỡ bạn bè.', 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?auto=format&fit=crop&w=600&q=80', '2025-11-26 18:07:33', '2025-11-26 18:07:33', 'published', 1),
(3, NULL, 'Chất liệu tái chế bền vững', NULL, 'chat-lieu-tai-che-ben-vung', 'BonBonwear tiên phong đưa cotton tái chế vào dòng sản phẩm chủ lực, góp phần bảo vệ môi trường và phát triển bền vững.\n\nCotton tái chế được sản xuất từ vải cotton cũ, giảm thiểu lượng nước và năng lượng tiêu thụ so với cotton truyền thống. Chất liệu này vẫn đảm bảo độ mềm mại, thoáng mát và bền bỉ như cotton thông thường.\n\nChúng tôi cam kết 50% sản phẩm năm 2025 sẽ sử dụng chất liệu tái chế hoặc thân thiện với môi trường.', 'https://images.unsplash.com/photo-1445205170230-053b83016050?auto=format&fit=crop&w=600&q=80', '2025-11-26 18:07:33', '2025-11-26 18:07:33', 'published', 1),
(4, NULL, 'Xu hướng thời trang nam 2025', NULL, 'xu-huong-thoi-trang-nam-2025', 'Năm 2025 đánh dấu sự trở lại của phong cách minimalism - tối giản nhưng tinh tế. Các gam màu trung tính như be, xám, navy tiếp tục thống trị.\n\nOversized vẫn là xu hướng được yêu thích, đặc biệt là áo khoác và hoodie. Tuy nhiên, sự kết hợp giữa oversized và tailored (may đo) tạo nên sự cân bằng hoàn hảo.\n\nChất liệu tự nhiên, thân thiện môi trường ngày càng được ưa chuộng. Người tiêu dùng không chỉ quan tâm đến thiết kế mà còn về nguồn gốc và quy trình sản xuất.', 'https://images.unsplash.com/photo-1490578474895-699cd4e2cf59?auto=format&fit=crop&w=600&q=80', '2025-11-26 18:07:33', '2025-11-26 18:07:33', 'published', 0),
(5, NULL, 'Bí quyết chọn size áo phù hợp', NULL, 'bi-quyet-chon-size-ao-phu-hop', 'Chọn đúng size áo là yếu tố quan trọng để bạn trông thật phong cách và tự tin.\n\n**Đo chính xác:** Sử dụng thước dây để đo vòng ngực, vai, và chiều dài áo. So sánh với bảng size của từng thương hiệu.\n\n**Thử trước khi mua:** Nếu có thể, hãy thử áo trực tiếp. Kiểm tra độ rộng vai, chiều dài tay, và độ ôm ở thân áo.\n\n**Lưu ý chất liệu:** Một số chất liệu như cotton có thể co lại sau khi giặt, nên chọn size lớn hơn một chút.\n\nBonBonwear cung cấp bảng size chi tiết và dịch vụ tư vấn miễn phí để bạn chọn được size hoàn hảo.', 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?auto=format&fit=crop&w=600&q=80', '2025-11-26 18:07:33', '2025-11-26 18:07:33', 'published', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `description`, `price`, `stock`, `category_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Áo Polo Essential', 'Áo polo nam thiết kế tối giản, chất liệu cotton cao cấp, thoáng mát', 399000.00, 100, 1, '2025-11-26 18:07:33', NULL, NULL),
(2, 'Áo Khoác Dệt Kim', 'Áo khoác dệt kim ấm áp, phù hợp mùa thu đông', 659000.00, 50, 2, '2025-11-26 18:07:33', NULL, NULL),
(3, 'Áo Hoodie Urban', 'Hoodie phong cách đường phố, chất liệu nỉ mềm mại', 549000.00, 80, 3, '2025-11-26 18:07:33', NULL, NULL),
(4, 'Quần Kaki Slimfit', 'Quần kaki ôm vừa phải, form dáng hiện đại', 489000.00, 120, 4, '2025-11-26 18:07:33', NULL, NULL),
(5, 'Áo Sơ Mi Cotton', 'Áo sơ mi cotton 100%, phù hợp công sở', 429000.00, 90, 5, '2025-11-26 18:07:33', NULL, NULL),
(6, 'Áo Polo Stripe', 'Áo polo họa tiết sọc ngang, trẻ trung năng động', 419000.00, 70, 1, '2025-11-26 18:07:33', NULL, NULL),
(7, 'Áo Khoác Utility', 'Áo khoác nhiều túi tiện dụng, phong cách quân đội', 699000.00, 40, 2, '2025-11-26 18:07:33', NULL, NULL),
(8, 'Quần Jean Darkwash', 'Quần jean màu tối, bền đẹp theo thời gian', 559000.00, 60, 4, '2025-11-26 18:07:33', NULL, NULL),
(9, 'Áo Polo Premium', 'Áo polo cao cấp, chất liệu pique cotton, logo thêu tinh tế', 499000.00, 55, 1, '2025-11-26 18:07:33', NULL, NULL),
(10, 'Áo Khoác Bomber', 'Áo khoác bomber phong cách pilot, chống gió tốt', 799000.00, 35, 2, '2025-11-26 18:07:33', NULL, NULL),
(11, 'Áo Hoodie Zip', 'Hoodie có khóa kéo, tiện lợi và năng động', 589000.00, 65, 3, '2025-11-26 18:07:33', NULL, NULL),
(12, 'Quần Jogger', 'Quần jogger thể thao, co giãn thoải mái', 449000.00, 85, 4, '2025-11-26 18:07:33', NULL, NULL),
(13, 'Áo Sơ Mi Oxford', 'Áo sơ mi vải oxford cao cấp, form regular fit', 479000.00, 70, 5, '2025-11-26 18:07:33', NULL, NULL),
(14, 'Áo Khoác Denim', 'Áo khoác jean classic, phong cách bất hủ', 729000.00, 45, 2, '2025-11-26 18:07:33', NULL, NULL),
(15, 'Quần Short Kakoo', 'Quần short kaki mùa hè, thoáng mát', 35900000.00, 95, 4, '2025-11-26 18:07:33', '2025-11-28 10:52:19', NULL),
(0, 'Bố Hiệp vĩ đại', 'dg', 10000.00, 11, 2, NULL, NULL, NULL),
(0, 'h', 'lll', 200000.00, 19, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_attribute_values`
--

CREATE TABLE `product_attribute_values` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `value_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_attribute_values`
--

INSERT INTO `product_attribute_values` (`id`, `product_id`, `variant_id`, `value_id`) VALUES
(1, 1, 1, 2),
(2, 1, 1, 6),
(3, 1, 2, 3),
(4, 1, 2, 6),
(5, 1, 3, 4),
(6, 1, 3, 6),
(7, 1, 4, 2),
(8, 1, 4, 8),
(9, 1, 5, 3),
(10, 1, 5, 7),
(11, 2, 6, 2),
(12, 2, 6, 9),
(13, 2, 7, 3),
(14, 2, 7, 9),
(15, 2, 8, 4),
(16, 2, 8, 11),
(17, 2, 9, 2),
(18, 2, 9, 11),
(19, 3, 10, 1),
(20, 3, 10, 6),
(21, 3, 11, 2),
(22, 3, 11, 6),
(23, 3, 12, 3),
(24, 3, 12, 9),
(25, 3, 13, 4),
(26, 3, 13, 8),
(27, 4, 14, 14),
(28, 4, 14, 11),
(29, 4, 15, 15),
(30, 4, 15, 11),
(31, 4, 16, 16),
(32, 4, 16, 8),
(33, 4, 17, 14),
(34, 4, 17, 6),
(35, 5, 18, 2),
(36, 5, 18, 7),
(37, 5, 19, 3),
(38, 5, 19, 7),
(39, 5, 20, 4),
(40, 5, 20, 12),
(41, 5, 21, 3),
(42, 5, 21, 8),
(43, 6, 22, 2),
(44, 6, 22, 8),
(45, 6, 23, 3),
(46, 6, 23, 8),
(47, 6, 24, 4),
(48, 6, 24, 10),
(49, 6, 25, 2),
(50, 6, 25, 7),
(51, 7, 26, 2),
(52, 7, 26, 6),
(53, 7, 27, 3),
(54, 7, 27, 6),
(55, 7, 28, 4),
(56, 7, 28, 8),
(57, 7, 29, 5),
(58, 7, 29, 9),
(59, 8, 30, 13),
(60, 8, 30, 12),
(61, 8, 31, 14),
(62, 8, 31, 12),
(63, 8, 32, 15),
(64, 8, 32, 12),
(65, 8, 33, 16),
(66, 8, 33, 12),
(67, 8, 34, 17),
(68, 8, 34, 12),
(69, 9, 35, 2),
(70, 9, 35, 6),
(71, 9, 36, 3),
(72, 9, 36, 8),
(73, 9, 37, 4),
(74, 9, 37, 7),
(75, 9, 38, 2),
(76, 9, 38, 8),
(77, 10, 39, 2),
(78, 10, 39, 6),
(79, 10, 40, 3),
(80, 10, 40, 6),
(81, 10, 41, 3),
(82, 10, 41, 8),
(83, 10, 42, 4),
(84, 10, 42, 8),
(85, 11, 43, 2),
(86, 11, 43, 6),
(87, 11, 44, 3),
(88, 11, 44, 9),
(89, 11, 45, 4),
(90, 11, 45, 6),
(91, 11, 46, 1),
(92, 11, 46, 9),
(93, 12, 47, 13),
(94, 12, 47, 9),
(95, 12, 48, 14),
(96, 12, 48, 6),
(97, 12, 49, 15),
(98, 12, 49, 9),
(99, 12, 50, 16),
(100, 12, 50, 6),
(101, 13, 51, 2),
(102, 13, 51, 7),
(103, 13, 52, 3),
(104, 13, 52, 8),
(105, 13, 53, 4),
(106, 13, 53, 12),
(107, 13, 54, 3),
(108, 13, 54, 7),
(109, 14, 55, 2),
(110, 14, 55, 12),
(111, 14, 56, 3),
(112, 14, 56, 12),
(113, 14, 57, 4),
(114, 14, 57, 6),
(115, 14, 58, 3),
(116, 14, 58, 6),
(117, 15, 59, 13),
(118, 15, 59, 11),
(119, 15, 60, 14),
(120, 15, 60, 8),
(121, 15, 61, 15),
(122, 15, 61, 11),
(123, 15, 62, 16),
(124, 15, 62, 8);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_images`
--

CREATE TABLE `product_images` (
  `image_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_images`
--

INSERT INTO `product_images` (`image_id`, `product_id`, `image_url`, `is_primary`) VALUES
(1, 1, 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?auto=format&fit=crop&w=600&q=80', 1),
(2, 2, 'https://images.unsplash.com/photo-1503341455253-b2e723bb3dbb?auto=format&fit=crop&w=600&q=80', 1),
(3, 3, 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?auto=format&fit=crop&w=600&q=80', 1),
(4, 4, 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&w=600&q=80', 1),
(5, 5, 'https://images.unsplash.com/photo-1475180098004-ca77a66827be?auto=format&fit=crop&w=600&q=80', 1),
(6, 6, 'https://images.unsplash.com/photo-1514996937319-344454492b37?auto=format&fit=crop&w=600&q=80', 1),
(7, 7, 'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?auto=format&fit=crop&w=600&q=80', 1),
(8, 8, 'https://images.unsplash.com/photo-1475180098004-ca77a66827be?auto=format&fit=crop&w=600&q=80', 1),
(9, 9, 'https://images.unsplash.com/photo-1586790170083-2f9ceadc732d?auto=format&fit=crop&w=600&q=80', 1),
(10, 10, 'https://images.unsplash.com/photo-1551028719-00167b16eac5?auto=format&fit=crop&w=600&q=80', 1),
(11, 11, 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?auto=format&fit=crop&w=600&q=80', 1),
(12, 12, 'https://images.unsplash.com/photo-1555689502-c4b22d76c56f?auto=format&fit=crop&w=600&q=80', 1),
(13, 13, 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&w=600&q=80', 1),
(14, 14, 'https://images.unsplash.com/photo-1576995853123-5a10305d93c0?auto=format&fit=crop&w=600&q=80', 1),
(15, 15, 'https://images.unsplash.com/photo-1591195853828-11db59a44f6b?auto=format&fit=crop&w=600&q=80', 1),
(16, 1, 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?auto=format&fit=crop&w=600&q=80', 0),
(17, 1, 'https://images.unsplash.com/photo-1537832816519-689ad163238b?auto=format&fit=crop&w=600&q=80', 0),
(18, 2, 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?auto=format&fit=crop&w=600&q=80', 0),
(19, 2, 'https://images.unsplash.com/photo-1537832816519-689ad163238b?auto=format&fit=crop&w=600&q=80', 0),
(20, 3, 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?auto=format&fit=crop&w=600&q=80', 0),
(21, 3, 'https://images.unsplash.com/photo-1537832816519-689ad163238b?auto=format&fit=crop&w=600&q=80', 0),
(22, 4, 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?auto=format&fit=crop&w=600&q=80', 0),
(23, 4, 'https://images.unsplash.com/photo-1537832816519-689ad163238b?auto=format&fit=crop&w=600&q=80', 0),
(24, 5, 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?auto=format&fit=crop&w=600&q=80', 0),
(25, 5, 'https://images.unsplash.com/photo-1537832816519-689ad163238b?auto=format&fit=crop&w=600&q=80', 0),
(26, 6, 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?auto=format&fit=crop&w=600&q=80', 0),
(27, 6, 'https://images.unsplash.com/photo-1537832816519-689ad163238b?auto=format&fit=crop&w=600&q=80', 0),
(28, 7, 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?auto=format&fit=crop&w=600&q=80', 0),
(29, 7, 'https://images.unsplash.com/photo-1537832816519-689ad163238b?auto=format&fit=crop&w=600&q=80', 0),
(30, 8, 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?auto=format&fit=crop&w=600&q=80', 0),
(31, 8, 'https://images.unsplash.com/photo-1537832816519-689ad163238b?auto=format&fit=crop&w=600&q=80', 0),
(32, 9, 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?auto=format&fit=crop&w=600&q=80', 0),
(33, 9, 'https://images.unsplash.com/photo-1537832816519-689ad163238b?auto=format&fit=crop&w=600&q=80', 0),
(34, 10, 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?auto=format&fit=crop&w=600&q=80', 0),
(35, 10, 'https://images.unsplash.com/photo-1537832816519-689ad163238b?auto=format&fit=crop&w=600&q=80', 0),
(36, 11, 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?auto=format&fit=crop&w=600&q=80', 0),
(37, 11, 'https://images.unsplash.com/photo-1537832816519-689ad163238b?auto=format&fit=crop&w=600&q=80', 0),
(38, 12, 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?auto=format&fit=crop&w=600&q=80', 0),
(39, 12, 'https://images.unsplash.com/photo-1537832816519-689ad163238b?auto=format&fit=crop&w=600&q=80', 0),
(40, 13, 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?auto=format&fit=crop&w=600&q=80', 0),
(41, 13, 'https://images.unsplash.com/photo-1537832816519-689ad163238b?auto=format&fit=crop&w=600&q=80', 0),
(42, 14, 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?auto=format&fit=crop&w=600&q=80', 0),
(43, 14, 'https://images.unsplash.com/photo-1537832816519-689ad163238b?auto=format&fit=crop&w=600&q=80', 0),
(44, 15, 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?auto=format&fit=crop&w=600&q=80', 0),
(45, 15, 'https://images.unsplash.com/photo-1537832816519-689ad163238b?auto=format&fit=crop&w=600&q=80', 0),
(0, 0, 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBxITEhUTExIVFhUXFRUVFRcVGBUXFRUXFRUXFxcYFRcYHSggGBolHRUVITEhJSkrLi4uFx8zODMtNygtLisBCgoKDg0OGhAQGy0fHx0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLf/AABEIAKgBLAMBIgACEQEDEQH/', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_variants`
--

CREATE TABLE `product_variants` (
  `variant_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `additional_price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_variants`
--

INSERT INTO `product_variants` (`variant_id`, `product_id`, `sku`, `additional_price`, `stock`) VALUES
(1, 1, 'POLO-ESS-M-BLK', 0.00, 20),
(2, 1, 'POLO-ESS-L-BLK', 0.00, 15),
(3, 1, 'POLO-ESS-XL-BLK', 0.00, 10),
(4, 1, 'POLO-ESS-M-NAV', 0.00, 18),
(5, 1, 'POLO-ESS-L-WHT', 0.00, 12),
(6, 2, 'JACKET-KNT-M-GRY', 0.00, 8),
(7, 2, 'JACKET-KNT-L-GRY', 0.00, 10),
(8, 2, 'JACKET-KNT-XL-BEI', 0.00, 5),
(9, 2, 'JACKET-KNT-M-BEI', 0.00, 7),
(10, 3, 'HOODIE-URB-S-BLK', 0.00, 15),
(11, 3, 'HOODIE-URB-M-BLK', 0.00, 20),
(12, 3, 'HOODIE-URB-L-GRY', 0.00, 18),
(13, 3, 'HOODIE-URB-XL-NAV', 0.00, 10),
(14, 4, 'PANT-KAKI-30-BEI', 0.00, 25),
(15, 4, 'PANT-KAKI-31-BEI', 0.00, 20),
(16, 4, 'PANT-KAKI-32-NAV', 0.00, 15),
(17, 4, 'PANT-KAKI-30-BLK', 0.00, 18),
(18, 5, 'SHIRT-COT-M-WHT', 0.00, 22),
(19, 5, 'SHIRT-COT-L-WHT', 0.00, 20),
(20, 5, 'SHIRT-COT-XL-BLU', 0.00, 12),
(21, 5, 'SHIRT-COT-L-NAV', 0.00, 15),
(22, 6, 'POLO-STR-M-NAV', 0.00, 16),
(23, 6, 'POLO-STR-L-NAV', 0.00, 14),
(24, 6, 'POLO-STR-XL-RED', 0.00, 8),
(25, 6, 'POLO-STR-M-WHT', 0.00, 12),
(26, 7, 'JACKET-UTL-M-BLK', 0.00, 10),
(27, 7, 'JACKET-UTL-L-BLK', 0.00, 8),
(28, 7, 'JACKET-UTL-XL-NAV', 0.00, 6),
(29, 7, 'JACKET-UTL-XXL-GRY', 0.00, 4),
(30, 8, 'JEAN-DRK-29-BLU', 0.00, 12),
(31, 8, 'JEAN-DRK-30-BLU', 0.00, 18),
(32, 8, 'JEAN-DRK-31-BLU', 0.00, 15),
(33, 8, 'JEAN-DRK-32-BLU', 0.00, 10),
(34, 8, 'JEAN-DRK-33-BLU', 0.00, 8),
(35, 9, 'POLO-PRM-M-BLK', 0.00, 18),
(36, 9, 'POLO-PRM-L-NAV', 0.00, 15),
(37, 9, 'POLO-PRM-XL-WHT', 0.00, 12),
(38, 9, 'POLO-PRM-M-NAV', 0.00, 16),
(39, 10, 'BOMBER-M-BLK', 0.00, 10),
(40, 10, 'BOMBER-L-BLK', 0.00, 9),
(41, 10, 'BOMBER-L-NAV', 0.00, 8),
(42, 10, 'BOMBER-XL-NAV', 0.00, 7),
(43, 11, 'HOODIE-ZIP-M-BLK', 0.00, 14),
(44, 11, 'HOODIE-ZIP-L-GRY', 0.00, 12),
(45, 11, 'HOODIE-ZIP-XL-BLK', 0.00, 10),
(46, 11, 'HOODIE-ZIP-S-GRY', 0.00, 8),
(47, 12, 'JOGGER-29-GRY', 0.00, 20),
(48, 12, 'JOGGER-30-BLK', 0.00, 18),
(49, 12, 'JOGGER-31-GRY', 0.00, 16),
(50, 12, 'JOGGER-32-BLK', 0.00, 14),
(51, 13, 'SHIRT-OXF-M-WHT', 0.00, 20),
(52, 13, 'SHIRT-OXF-L-NAV', 0.00, 16),
(53, 13, 'SHIRT-OXF-XL-BLU', 0.00, 12),
(54, 13, 'SHIRT-OXF-L-WHT', 0.00, 14),
(55, 14, 'DENIM-M-BLU', 0.00, 12),
(56, 14, 'DENIM-L-BLU', 0.00, 10),
(57, 14, 'DENIM-XL-BLK', 0.00, 9),
(58, 14, 'DENIM-L-BLK', 0.00, 8),
(59, 15, 'SHORT-KAKI-29-BEI', 0.00, 22),
(60, 15, 'SHORT-KAKI-30-NAV', 0.00, 20),
(61, 15, 'SHORT-KAKI-31-BEI', 0.00, 18),
(62, 15, 'SHORT-KAKI-32-NAV', 0.00, 16);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `is_hidden` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` varchar(20) DEFAULT NULL,
  `session_token` varchar(255) DEFAULT NULL,
  `session_expires` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `first_name` varchar(80) DEFAULT NULL,
  `last_name` varchar(80) DEFAULT NULL,
  `gender` enum('female','male') DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `is_locked` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password`, `phone`, `address`, `role`, `session_token`, `session_expires`, `created_at`, `first_name`, `last_name`, `gender`, `birthday`, `is_locked`) VALUES
(1, 'fff rể', 'le3221981@gmail.com', '$2y$10$pfMflz91BmTYXGRLRd6rP.jE97atHKYzaq/AFn.Rb87JvWNp0EOEi', '0393561314', 'Hà Nội', 'admin', NULL, NULL, '2025-11-27 10:34:49', 'fff', 'rể', 'male', '2007-11-01', 0),
(2, 'ee re', 'nguyenvanlinh25062006@gmail.com', '$2y$10$7d.4VUybvBcNL6thEsZb0.v9u554mW2Z377NpwHtaohJ67e2DW7Ay', '0333044840', 'Hà Nội', 'customer', '6ee393e0b6c046db44e7ecb65de566bfad15df7492cff3707877d1295675fabb', '2025-11-28 05:29:48', '2025-11-27 11:29:48', 'ee', 're', 'female', '2000-02-23', 0),
(3, 'Lê Phương Hà', 'phuongha9112006@gmail.com', '$2y$10$uBEzaOf/IQF3O53pHU4NqeSfdH.uiDcnjvtxKNtP10QEKfNc0vHCq', '0343748764', 'Thanh Hóa', 'customer', NULL, NULL, '2025-11-27 21:01:53', 'Lê', 'Phương Hà', 'female', '2006-11-09', 0),
(4, 'Phạm Văn Hiệp', 'phamvanhiep210306@gmail.com', '$2y$10$RwV7yFPY7.AKBdiEjR9MROkPj.k9sZu.Nb57XK97Aj.745qjaRLSe', '9749264441', 'sn:245 đường thanh chương phố tân trọng phường quảng phú', 'customer', NULL, NULL, '2025-11-28 11:02:33', 'Phạm', 'Văn Hiệp', 'male', '2006-03-21', 0);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `attributes`
--
ALTER TABLE `attributes`
  ADD PRIMARY KEY (`attribute_id`);

--
-- Chỉ mục cho bảng `attribute_values`
--
ALTER TABLE `attribute_values`
  ADD PRIMARY KEY (`value_id`),
  ADD KEY `attribute_id` (`attribute_id`);

--
-- Chỉ mục cho bảng `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Chỉ mục cho bảng `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`coupon_id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `coupon_id` (`coupon_id`);

--
-- Chỉ mục cho bảng `orders_new`
--
ALTER TABLE `orders_new`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Chỉ mục cho bảng `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`reset_id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
