<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE TABLE `portal_audit_log` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `member_id` BIGINT UNSIGNED NOT NULL,
                `member_number` VARCHAR(4) NULL,
                `action` VARCHAR(50) NOT NULL,
                `detail` TEXT NULL,
                `ip_address` VARCHAR(45) NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                KEY `idx_portal_audit_member` (`member_id`),
                KEY `idx_portal_audit_created` (`created_at`),
                CONSTRAINT `fk_portal_audit_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS `portal_audit_log`");
    }
};
