-- Add membership_requested_at to members and audit tables
-- Production: run on Hostpoint MariaDB

ALTER TABLE `members` ADD COLUMN `membership_requested_at` DATETIME NULL DEFAULT NULL AFTER `membership_end`;
ALTER TABLE `members_audit` ADD COLUMN `membership_requested_at` DATETIME NULL DEFAULT NULL AFTER `membership_end`;

-- Update triggers to include the new column
DROP TRIGGER IF EXISTS `members_before_update`;
DROP TRIGGER IF EXISTS `members_before_delete`;

DELIMITER $$

CREATE TRIGGER `members_before_update` BEFORE UPDATE ON `members`
FOR EACH ROW
BEGIN
    INSERT INTO `members_audit` (`audit_action`, `audit_user_id`, `id`, `member_number`, `first_name`, `last_name`, `email`, `date_of_birth`, `address`, `postal_code`, `city`, `country`, `statuscode`, `membership_start`, `membership_end`, `membership_requested_at`, `notes`, `is_invitee`, `photo_ok`, `metadata`, `activation_token`, `activation_sent_at`, `email_verified_at`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('U', @current_user_id, OLD.`id`, OLD.`member_number`, OLD.`first_name`, OLD.`last_name`, OLD.`email`, OLD.`date_of_birth`, OLD.`address`, OLD.`postal_code`, OLD.`city`, OLD.`country`, OLD.`statuscode`, OLD.`membership_start`, OLD.`membership_end`, OLD.`membership_requested_at`, OLD.`notes`, OLD.`is_invitee`, OLD.`photo_ok`, OLD.`metadata`, OLD.`activation_token`, OLD.`activation_sent_at`, OLD.`email_verified_at`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END$$

CREATE TRIGGER `members_before_delete` BEFORE DELETE ON `members`
FOR EACH ROW
BEGIN
    INSERT INTO `members_audit` (`audit_action`, `audit_user_id`, `id`, `member_number`, `first_name`, `last_name`, `email`, `date_of_birth`, `address`, `postal_code`, `city`, `country`, `statuscode`, `membership_start`, `membership_end`, `membership_requested_at`, `notes`, `is_invitee`, `photo_ok`, `metadata`, `activation_token`, `activation_sent_at`, `email_verified_at`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('D', @current_user_id, OLD.`id`, OLD.`member_number`, OLD.`first_name`, OLD.`last_name`, OLD.`email`, OLD.`date_of_birth`, OLD.`address`, OLD.`postal_code`, OLD.`city`, OLD.`country`, OLD.`statuscode`, OLD.`membership_start`, OLD.`membership_end`, OLD.`membership_requested_at`, OLD.`notes`, OLD.`is_invitee`, OLD.`photo_ok`, OLD.`metadata`, OLD.`activation_token`, OLD.`activation_sent_at`, OLD.`email_verified_at`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END$$

DELIMITER ;

-- Migrate existing P members who already confirmed their email (token cleared)
-- These become N with membership_requested_at set to their last update time
UPDATE `members` SET `statuscode` = 'N', `membership_requested_at` = `updated_at`
WHERE `statuscode` = 'P' AND `activation_token` IS NULL AND `deleted_at` IS NULL;
