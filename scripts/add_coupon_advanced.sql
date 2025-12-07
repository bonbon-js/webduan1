-- Advanced coupon fields and usage log

-- Add new columns to coupons table (ignore errors if columns already exist)
ALTER TABLE `coupons`
    ADD COLUMN `per_user_limit` INT NULL AFTER `usage_limit`,
    ADD COLUMN `apply_scope` VARCHAR(20) NOT NULL DEFAULT 'all' AFTER `per_user_limit`,
    ADD COLUMN `apply_product_ids` TEXT NULL AFTER `apply_scope`,
    ADD COLUMN `apply_category_ids` TEXT NULL AFTER `apply_product_ids`,
    ADD COLUMN `require_login` TINYINT(1) NOT NULL DEFAULT 0 AFTER `apply_category_ids`,
    ADD COLUMN `new_customer_only` TINYINT(1) NOT NULL DEFAULT 0 AFTER `require_login`,
    ADD COLUMN `exclude_sale_items` TINYINT(1) NOT NULL DEFAULT 0 AFTER `new_customer_only`,
    ADD COLUMN `exclude_other_coupons` TINYINT(1) NOT NULL DEFAULT 0 AFTER `exclude_sale_items`,
    ADD COLUMN `customer_group` VARCHAR(50) NULL AFTER `exclude_other_coupons`,
    ADD COLUMN `return_on_refund` TINYINT(1) NOT NULL DEFAULT 0 AFTER `customer_group`;

-- Coupon usage log
CREATE TABLE IF NOT EXISTS `coupon_usage` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `coupon_id` INT NOT NULL,
    `user_id` INT NULL,
    `order_id` INT NULL,
    `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `used_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_coupon_usage_coupon` (`coupon_id`),
    INDEX `idx_coupon_usage_user` (`user_id`),
    INDEX `idx_coupon_usage_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

