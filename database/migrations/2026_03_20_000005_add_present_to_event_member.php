<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_member', function (Blueprint $table) {
            $table->boolean('present')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('event_member', function (Blueprint $table) {
            $table->dropColumn('present');
        });
    }
};
