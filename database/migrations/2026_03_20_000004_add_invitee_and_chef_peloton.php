<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->boolean('is_invitee')->default(false)->after('notes');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('chef_peloton_id')->nullable()->after('statuscode')->constrained('members');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('is_invitee');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['chef_peloton_id']);
            $table->dropColumn('chef_peloton_id');
        });
    }
};
