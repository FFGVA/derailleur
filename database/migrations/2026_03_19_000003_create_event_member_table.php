<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_member', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained();
            $table->foreignId('member_id')->constrained();
            $table->char('status', 1)->default('N');
            $table->timestamp('updated_at')->nullable();

            $table->unique(['event_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_member');
    }
};
