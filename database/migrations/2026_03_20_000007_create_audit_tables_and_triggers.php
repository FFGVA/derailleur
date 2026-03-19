<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── members_audit ──
        DB::unprepared("
            CREATE TABLE members_audit (
                audit_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                audit_action CHAR(1) NOT NULL,
                audit_user_id BIGINT UNSIGNED NULL,
                audit_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                id BIGINT UNSIGNED NOT NULL,
                first_name VARCHAR(40) NOT NULL,
                last_name VARCHAR(60) NOT NULL,
                email VARCHAR(255) NOT NULL,
                date_of_birth DATE NULL,
                address TEXT NULL,
                postal_code VARCHAR(10) NULL,
                city VARCHAR(255) NULL,
                country VARCHAR(2) DEFAULT 'CH',
                statuscode CHAR(1) DEFAULT 'D',
                membership_start DATE NULL,
                membership_end DATE NULL,
                notes TEXT NULL,
                is_invitee TINYINT(1) DEFAULT 0,
                updated_at TIMESTAMP NULL,
                deleted_at TIMESTAMP NULL
            )
        ");

        DB::unprepared("
            CREATE TRIGGER members_before_update
            BEFORE UPDATE ON members
            FOR EACH ROW
            BEGIN
                INSERT INTO members_audit (audit_action, audit_user_id, id, first_name, last_name, email, date_of_birth, address, postal_code, city, country, statuscode, membership_start, membership_end, notes, is_invitee, updated_at, deleted_at)
                VALUES ('U', @current_user_id, OLD.id, OLD.first_name, OLD.last_name, OLD.email, OLD.date_of_birth, OLD.address, OLD.postal_code, OLD.city, OLD.country, OLD.statuscode, OLD.membership_start, OLD.membership_end, OLD.notes, OLD.is_invitee, OLD.updated_at, OLD.deleted_at);
            END
        ");

        DB::unprepared("
            CREATE TRIGGER members_before_delete
            BEFORE DELETE ON members
            FOR EACH ROW
            BEGIN
                INSERT INTO members_audit (audit_action, audit_user_id, id, first_name, last_name, email, date_of_birth, address, postal_code, city, country, statuscode, membership_start, membership_end, notes, is_invitee, updated_at, deleted_at)
                VALUES ('D', @current_user_id, OLD.id, OLD.first_name, OLD.last_name, OLD.email, OLD.date_of_birth, OLD.address, OLD.postal_code, OLD.city, OLD.country, OLD.statuscode, OLD.membership_start, OLD.membership_end, OLD.notes, OLD.is_invitee, OLD.updated_at, OLD.deleted_at);
            END
        ");

        // ── events_audit ──
        DB::unprepared("
            CREATE TABLE events_audit (
                audit_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                audit_action CHAR(1) NOT NULL,
                audit_user_id BIGINT UNSIGNED NULL,
                audit_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                id BIGINT UNSIGNED NOT NULL,
                title VARCHAR(200) NOT NULL,
                description TEXT NULL,
                location VARCHAR(255) NULL,
                starts_at DATETIME NOT NULL,
                ends_at DATETIME NULL,
                max_participants INT UNSIGNED NULL,
                price DECIMAL(8,2) DEFAULT 0,
                statuscode CHAR(1) DEFAULT 'N',
                chef_peloton_id BIGINT UNSIGNED NULL,
                updated_at TIMESTAMP NULL,
                deleted_at TIMESTAMP NULL
            )
        ");

        DB::unprepared("
            CREATE TRIGGER events_before_update
            BEFORE UPDATE ON events
            FOR EACH ROW
            BEGIN
                INSERT INTO events_audit (audit_action, audit_user_id, id, title, description, location, starts_at, ends_at, max_participants, price, statuscode, chef_peloton_id, updated_at, deleted_at)
                VALUES ('U', @current_user_id, OLD.id, OLD.title, OLD.description, OLD.location, OLD.starts_at, OLD.ends_at, OLD.max_participants, OLD.price, OLD.statuscode, OLD.chef_peloton_id, OLD.updated_at, OLD.deleted_at);
            END
        ");

        DB::unprepared("
            CREATE TRIGGER events_before_delete
            BEFORE DELETE ON events
            FOR EACH ROW
            BEGIN
                INSERT INTO events_audit (audit_action, audit_user_id, id, title, description, location, starts_at, ends_at, max_participants, price, statuscode, chef_peloton_id, updated_at, deleted_at)
                VALUES ('D', @current_user_id, OLD.id, OLD.title, OLD.description, OLD.location, OLD.starts_at, OLD.ends_at, OLD.max_participants, OLD.price, OLD.statuscode, OLD.chef_peloton_id, OLD.updated_at, OLD.deleted_at);
            END
        ");

        // ── event_member_audit ──
        DB::unprepared("
            CREATE TABLE event_member_audit (
                audit_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                audit_action CHAR(1) NOT NULL,
                audit_user_id BIGINT UNSIGNED NULL,
                audit_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                id BIGINT UNSIGNED NOT NULL,
                event_id BIGINT UNSIGNED NOT NULL,
                member_id BIGINT UNSIGNED NOT NULL,
                status CHAR(1) DEFAULT 'N',
                present TINYINT(1) NULL,
                updated_at TIMESTAMP NULL,
                deleted_at TIMESTAMP NULL
            )
        ");

        DB::unprepared("
            CREATE TRIGGER event_member_before_update
            BEFORE UPDATE ON event_member
            FOR EACH ROW
            BEGIN
                INSERT INTO event_member_audit (audit_action, audit_user_id, id, event_id, member_id, status, present, updated_at, deleted_at)
                VALUES ('U', @current_user_id, OLD.id, OLD.event_id, OLD.member_id, OLD.status, OLD.present, OLD.updated_at, OLD.deleted_at);
            END
        ");

        DB::unprepared("
            CREATE TRIGGER event_member_before_delete
            BEFORE DELETE ON event_member
            FOR EACH ROW
            BEGIN
                INSERT INTO event_member_audit (audit_action, audit_user_id, id, event_id, member_id, status, present, updated_at, deleted_at)
                VALUES ('D', @current_user_id, OLD.id, OLD.event_id, OLD.member_id, OLD.status, OLD.present, OLD.updated_at, OLD.deleted_at);
            END
        ");

        // ── member_phones_audit ──
        DB::unprepared("
            CREATE TABLE member_phones_audit (
                audit_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                audit_action CHAR(1) NOT NULL,
                audit_user_id BIGINT UNSIGNED NULL,
                audit_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                id BIGINT UNSIGNED NOT NULL,
                member_id BIGINT UNSIGNED NOT NULL,
                phone_number VARCHAR(20) NOT NULL,
                label VARCHAR(40) NULL,
                is_whatsapp TINYINT(1) DEFAULT 0,
                sort_order TINYINT UNSIGNED DEFAULT 0,
                updated_at TIMESTAMP NULL,
                deleted_at TIMESTAMP NULL
            )
        ");

        DB::unprepared("
            CREATE TRIGGER member_phones_before_update
            BEFORE UPDATE ON member_phones
            FOR EACH ROW
            BEGIN
                INSERT INTO member_phones_audit (audit_action, audit_user_id, id, member_id, phone_number, label, is_whatsapp, sort_order, updated_at, deleted_at)
                VALUES ('U', @current_user_id, OLD.id, OLD.member_id, OLD.phone_number, OLD.label, OLD.is_whatsapp, OLD.sort_order, OLD.updated_at, OLD.deleted_at);
            END
        ");

        DB::unprepared("
            CREATE TRIGGER member_phones_before_delete
            BEFORE DELETE ON member_phones
            FOR EACH ROW
            BEGIN
                INSERT INTO member_phones_audit (audit_action, audit_user_id, id, member_id, phone_number, label, is_whatsapp, sort_order, updated_at, deleted_at)
                VALUES ('D', @current_user_id, OLD.id, OLD.member_id, OLD.phone_number, OLD.label, OLD.is_whatsapp, OLD.sort_order, OLD.updated_at, OLD.deleted_at);
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS members_before_update');
        DB::unprepared('DROP TRIGGER IF EXISTS members_before_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS events_before_update');
        DB::unprepared('DROP TRIGGER IF EXISTS events_before_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS event_member_before_update');
        DB::unprepared('DROP TRIGGER IF EXISTS event_member_before_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS member_phones_before_update');
        DB::unprepared('DROP TRIGGER IF EXISTS member_phones_before_delete');

        DB::unprepared('DROP TABLE IF EXISTS members_audit');
        DB::unprepared('DROP TABLE IF EXISTS events_audit');
        DB::unprepared('DROP TABLE IF EXISTS event_member_audit');
        DB::unprepared('DROP TABLE IF EXISTS member_phones_audit');
    }
};
