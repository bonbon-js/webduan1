-- Migration to add missing columns to orders table
-- This fixes the "Duplicate entry '0' for key 'PRIMARY'" error

ALTER TABLE `orders`
ADD COLUMN `user_id` INT(11) NULL AFTER `order_id`,
ADD COLUMN `fullname` VARCHAR(255) NOT NULL AFTER `user_id`,
ADD COLUMN `email` VARCHAR(255) NOT NULL AFTER `fullname`,
ADD COLUMN `phone` VARCHAR(20) NOT NULL AFTER `email`,
ADD COLUMN `address` TEXT NOT NULL AFTER `phone`,
ADD COLUMN `city` VARCHAR(100) NULL AFTER `address`,
ADD COLUMN `district` VARCHAR(100) NULL AFTER `city`,
ADD COLUMN `ward` VARCHAR(100) NULL AFTER `district`,
ADD COLUMN `note` TEXT NULL AFTER `ward`,
ADD COLUMN `order_code` VARCHAR(50) NULL AFTER `note`,
ADD COLUMN `discount_amount` DECIMAL(10,2) DEFAULT 0 AFTER `total_amount`,
ADD COLUMN `coupon_code` VARCHAR(50) NULL AFTER `coupon_id`,
ADD COLUMN `coupon_name` VARCHAR(255) NULL AFTER `coupon_code`,
ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `coupon_name`,
ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`,
ADD COLUMN `cancel_reason` TEXT NULL AFTER `updated_at`;

-- Add index for user_id
ALTER TABLE `orders`
ADD INDEX `idx_user_id` (`user_id`);

-- Add unique index for order_code
ALTER TABLE `orders`
ADD UNIQUE INDEX `idx_order_code` (`order_code`);
