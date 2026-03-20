<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('activation_token', 64)->nullable();
            $table->timestamp('activation_sent_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
        });

        Schema::table('members_audit', function (Blueprint $table) {
            $table->string('activation_token', 64)->nullable();
            $table->timestamp('activation_sent_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
        });

        // Recreate triggers to include new columns
        DB::unprepared('DROP TRIGGER IF EXISTS `members_before_update`');
        DB::unprepared("
            CREATE TRIGGER `members_before_update`
            BEFORE UPDATE ON `members`
            FOR EACH ROW
            BEGIN
                INSERT INTO `members_audit` (`audit_action`, `audit_user_id`, `id`, `first_name`, `last_name`, `email`, `date_of_birth`, `address`, `postal_code`, `city`, `country`, `statuscode`, `membership_start`, `membership_end`, `notes`, `is_invitee`, `activation_token`, `activation_sent_at`, `email_verified_at`, `updated_at`, `deleted_at`)
                VALUES ('U', @current_user_id, OLD.`id`, OLD.`first_name`, OLD.`last_name`, OLD.`email`, OLD.`date_of_birth`, OLD.`address`, OLD.`postal_code`, OLD.`city`, OLD.`country`, OLD.`statuscode`, OLD.`membership_start`, OLD.`membership_end`, OLD.`notes`, OLD.`is_invitee`, OLD.`activation_token`, OLD.`activation_sent_at`, OLD.`email_verified_at`, OLD.`updated_at`, OLD.`deleted_at`);
            END
        ");

        DB::unprepared('DROP TRIGGER IF EXISTS `members_before_delete`');
        DB::unprepared("
            CREATE TRIGGER `members_before_delete`
            BEFORE DELETE ON `members`
            FOR EACH ROW
            BEGIN
                INSERT INTO `members_audit` (`audit_action`, `audit_user_id`, `id`, `first_name`, `last_name`, `email`, `date_of_birth`, `address`, `postal_code`, `city`, `country`, `statuscode`, `membership_start`, `membership_end`, `notes`, `is_invitee`, `activation_token`, `activation_sent_at`, `email_verified_at`, `updated_at`, `deleted_at`)
                VALUES ('D', @current_user_id, OLD.`id`, OLD.`first_name`, OLD.`last_name`, OLD.`email`, OLD.`date_of_birth`, OLD.`address`, OLD.`postal_code`, OLD.`city`, OLD.`country`, OLD.`statuscode`, OLD.`membership_start`, OLD.`membership_end`, OLD.`notes`, OLD.`is_invitee`, OLD.`activation_token`, OLD.`activation_sent_at`, OLD.`email_verified_at`, OLD.`updated_at`, OLD.`deleted_at`);
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `members_before_update`');
        DB::unprepared("
            CREATE TRIGGER `members_before_update`
            BEFORE UPDATE ON `members`
            FOR EACH ROW
            BEGIN
                INSERT INTO `members_audit` (`audit_action`, `audit_user_id`, `id`, `first_name`, `last_name`, `email`, `date_of_birth`, `address`, `postal_code`, `city`, `country`, `statuscode`, `membership_start`, `membership_end`, `notes`, `is_invitee`, `updated_at`, `deleted_at`)
                VALUES ('U', @current_user_id, OLD.`id`, OLD.`first_name`, OLD.`last_name`, OLD.`email`, OLD.`date_of_birth`, OLD.`address`, OLD.`postal_code`, OLD.`city`, OLD.`country`, OLD.`statuscode`, OLD.`membership_start`, OLD.`membership_end`, OLD.`notes`, OLD.`is_invitee`, OLD.`updated_at`, OLD.`deleted_at`);
            END
        ");

        DB::unprepared('DROP TRIGGER IF EXISTS `members_before_delete`');
        DB::unprepared("
            CREATE TRIGGER `members_before_delete`
            BEFORE DELETE ON `members`
            FOR EACH ROW
            BEGIN
                INSERT INTO `members_audit` (`audit_action`, `audit_user_id`, `id`, `first_name`, `last_name`, `email`, `date_of_birth`, `address`, `postal_code`, `city`, `country`, `statuscode`, `membership_start`, `membership_end`, `notes`, `is_invitee`, `updated_at`, `deleted_at`)
                VALUES ('D', @current_user_id, OLD.`id`, OLD.`first_name`, OLD.`last_name`, OLD.`email`, OLD.`date_of_birth`, OLD.`address`, OLD.`postal_code`, OLD.`city`, OLD.`country`, OLD.`statuscode`, OLD.`membership_start`, OLD.`membership_end`, OLD.`notes`, OLD.`is_invitee`, OLD.`updated_at`, OLD.`deleted_at`);
            END
        ");

        Schema::table('members_audit', function (Blueprint $table) {
            $table->dropColumn(['activation_token', 'activation_sent_at', 'email_verified_at']);
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['activation_token', 'activation_sent_at', 'email_verified_at']);
        });
    }
};
