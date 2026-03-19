<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['members', 'events', 'event_member', 'member_phones'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->foreignId('modified_by_id')->nullable()->constrained('users');
            });
        }
    }

    public function down(): void
    {
        foreach (['member_phones', 'event_member', 'events', 'members'] as $table) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                $t->dropForeign([$table . '_modified_by_id_foreign']);
                $t->dropColumn('modified_by_id');
            });
        }
    }
};
