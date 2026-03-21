<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `members` ADD COLUMN `photo_ok` TINYINT(1) NOT NULL DEFAULT 1 AFTER `is_invitee`");
        DB::statement("ALTER TABLE `members_audit` ADD COLUMN `photo_ok` TINYINT(1) DEFAULT 1 AFTER `is_invitee`");

        // Rebuild triggers to include photo_ok
        DB::unprepared("DROP TRIGGER IF EXISTS `members_before_update`");
        DB::unprepared("DROP TRIGGER IF EXISTS `members_before_delete`");

        DB::unprepared("
            CREATE TRIGGER `members_before_update`
            BEFORE UPDATE ON `members`
            FOR EACH ROW
            BEGIN
                INSERT INTO `members_audit` (`audit_action`, `audit_user_id`, `id`, `member_number`, `first_name`, `last_name`, `email`, `date_of_birth`, `address`, `postal_code`, `city`, `country`, `statuscode`, `membership_start`, `membership_end`, `notes`, `is_invitee`, `photo_ok`, `metadata`, `activation_token`, `activation_sent_at`, `email_verified_at`, `modified_by_id`, `updated_at`, `deleted_at`)
                VALUES ('U', @current_user_id, OLD.`id`, OLD.`member_number`, OLD.`first_name`, OLD.`last_name`, OLD.`email`, OLD.`date_of_birth`, OLD.`address`, OLD.`postal_code`, OLD.`city`, OLD.`country`, OLD.`statuscode`, OLD.`membership_start`, OLD.`membership_end`, OLD.`notes`, OLD.`is_invitee`, OLD.`photo_ok`, OLD.`metadata`, OLD.`activation_token`, OLD.`activation_sent_at`, OLD.`email_verified_at`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
            END
        ");

        DB::unprepared("
            CREATE TRIGGER `members_before_delete`
            BEFORE DELETE ON `members`
            FOR EACH ROW
            BEGIN
                INSERT INTO `members_audit` (`audit_action`, `audit_user_id`, `id`, `member_number`, `first_name`, `last_name`, `email`, `date_of_birth`, `address`, `postal_code`, `city`, `country`, `statuscode`, `membership_start`, `membership_end`, `notes`, `is_invitee`, `photo_ok`, `metadata`, `activation_token`, `activation_sent_at`, `email_verified_at`, `modified_by_id`, `updated_at`, `deleted_at`)
                VALUES ('D', @current_user_id, OLD.`id`, OLD.`member_number`, OLD.`first_name`, OLD.`last_name`, OLD.`email`, OLD.`date_of_birth`, OLD.`address`, OLD.`postal_code`, OLD.`city`, OLD.`country`, OLD.`statuscode`, OLD.`membership_start`, OLD.`membership_end`, OLD.`notes`, OLD.`is_invitee`, OLD.`photo_ok`, OLD.`metadata`, OLD.`activation_token`, OLD.`activation_sent_at`, OLD.`email_verified_at`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
            END
        ");

        // Migrate existing photo_ok from metadata JSON to the new column
        DB::statement("UPDATE `members` SET `photo_ok` = 0 WHERE JSON_UNQUOTE(JSON_EXTRACT(`metadata`, '\$.photo_ok')) = 'non'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `members` DROP COLUMN `photo_ok`");
        DB::statement("ALTER TABLE `members_audit` DROP COLUMN `photo_ok`");
    }
};
