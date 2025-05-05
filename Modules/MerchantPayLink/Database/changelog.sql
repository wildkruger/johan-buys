ALTER TABLE `users` ADD `profile_paylink_code` VARCHAR(65) NULL DEFAULT NULL AFTER `email`;
INSERT INTO `transaction_types` (`name`) VALUES ('Profile_payment');
