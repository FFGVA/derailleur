<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `invoices` ADD COLUMN `pdf_filename` VARCHAR(255) NULL AFTER `payment_date`");
        DB::statement("ALTER TABLE `invoices_audit` ADD COLUMN `pdf_filename` VARCHAR(255) NULL AFTER `payment_date`");

        // Rebuild triggers to include pdf_filename
        DB::unprepared("DROP TRIGGER IF EXISTS `invoices_before_update`");
        DB::unprepared("DROP TRIGGER IF EXISTS `invoices_before_delete`");

        DB::unprepared("
            CREATE TRIGGER `invoices_before_update`
            BEFORE UPDATE ON `invoices`
            FOR EACH ROW
            BEGIN
                INSERT INTO `invoices_audit` (`audit_action`, `audit_user_id`, `id`, `member_id`, `type`, `cotisation_year`, `invoice_number`, `amount`, `statuscode`, `payment_date`, `pdf_filename`, `notes`, `modified_by_id`, `updated_at`, `deleted_at`)
                VALUES ('U', @current_user_id, OLD.`id`, OLD.`member_id`, OLD.`type`, OLD.`cotisation_year`, OLD.`invoice_number`, OLD.`amount`, OLD.`statuscode`, OLD.`payment_date`, OLD.`pdf_filename`, OLD.`notes`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
            END
        ");

        DB::unprepared("
            CREATE TRIGGER `invoices_before_delete`
            BEFORE DELETE ON `invoices`
            FOR EACH ROW
            BEGIN
                INSERT INTO `invoices_audit` (`audit_action`, `audit_user_id`, `id`, `member_id`, `type`, `cotisation_year`, `invoice_number`, `amount`, `statuscode`, `payment_date`, `pdf_filename`, `notes`, `modified_by_id`, `updated_at`, `deleted_at`)
                VALUES ('D', @current_user_id, OLD.`id`, OLD.`member_id`, OLD.`type`, OLD.`cotisation_year`, OLD.`invoice_number`, OLD.`amount`, OLD.`statuscode`, OLD.`payment_date`, OLD.`pdf_filename`, OLD.`notes`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
            END
        ");

        // Backfill existing invoices with their pdf_filename
        DB::statement("
            UPDATE `invoices` i
            JOIN `members` m ON m.id = i.member_id
            SET i.pdf_filename = CONCAT('ffgva_',
                REPLACE(REPLACE(m.last_name, ' ', '_'), '''', ''), '_',
                REPLACE(REPLACE(m.first_name, ' ', '_'), '''', ''),
                '-facture-', i.invoice_number, '.pdf')
            WHERE i.pdf_filename IS NULL
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `invoices` DROP COLUMN `pdf_filename`");
        DB::statement("ALTER TABLE `invoices_audit` DROP COLUMN `pdf_filename`");
    }
};
