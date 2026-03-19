<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("INSERT INTO member_phones (member_id, phone_number, label) SELECT id, phone, 'Mobile' FROM members WHERE phone IS NOT NULL");

        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
        });
    }
};
