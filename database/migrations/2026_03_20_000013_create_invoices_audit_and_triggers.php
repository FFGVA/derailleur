<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("
            CREATE TABLE IF NOT EXISTS `invoices_audit` (
                `audit_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `audit_action` CHAR(1) NOT NULL,
                `audit_user_id` BIGINT UNSIGNED NULL,
                `audit_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `id` BIGINT UNSIGNED NOT NULL,
                `member_id` BIGINT UNSIGNED NOT NULL,
                `invoice_number` VARCHAR(20) NOT NULL,
                `amount` DECIMAL(8,2) NOT NULL,
                `statuscode` CHAR(1) DEFAULT 'N',
                `payment_date` DATE NULL,
                `notes` TEXT NULL,
                `modified_by_id` BIGINT UNSIGNED NULL,
                `updated_at` TIMESTAMP NULL,
                `deleted_at` TIMESTAMP NULL,
                CONSTRAINT `invoices_audit_user_id_foreign` FOREIGN KEY (`audit_user_id`) REFERENCES `users` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        DB::unprepared("
            CREATE TRIGGER `invoices_before_update`
            BEFORE UPDATE ON `invoices`
            FOR EACH ROW
            BEGIN
                INSERT INTO `invoices_audit` (`audit_action`, `audit_user_id`, `id`, `member_id`, `invoice_number`, `amount`, `statuscode`, `payment_date`, `notes`, `modified_by_id`, `updated_at`, `deleted_at`)
                VALUES ('U', @current_user_id, OLD.`id`, OLD.`member_id`, OLD.`invoice_number`, OLD.`amount`, OLD.`statuscode`, OLD.`payment_date`, OLD.`notes`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
            END
        ");

        DB::unprepared("
            CREATE TRIGGER `invoices_before_delete`
            BEFORE DELETE ON `invoices`
            FOR EACH ROW
            BEGIN
                INSERT INTO `invoices_audit` (`audit_action`, `audit_user_id`, `id`, `member_id`, `invoice_number`, `amount`, `statuscode`, `payment_date`, `notes`, `modified_by_id`, `updated_at`, `deleted_at`)
                VALUES ('D', @current_user_id, OLD.`id`, OLD.`member_id`, OLD.`invoice_number`, OLD.`amount`, OLD.`statuscode`, OLD.`payment_date`, OLD.`notes`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `invoices_before_update`');
        DB::unprepared('DROP TRIGGER IF EXISTS `invoices_before_delete`');
        DB::unprepared('DROP TABLE IF EXISTS `invoices_audit`');
    }
};
