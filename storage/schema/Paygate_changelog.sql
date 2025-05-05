INSERT INTO `payment_methods` (`name`, `status`) VALUES ('Paygate', 'Active');

ALTER TABLE `transactions` CHANGE `uuid` `uuid` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Unique ID';

ALTER TABLE `deposits` CHANGE `uuid` `uuid` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Unique ID (For Each Deposit)';