<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_magic_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->restrictOnDelete();
            $table->binary('token_hash', 32);
            $table->dateTime('expires_at');
            $table->dateTime('used_at')->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->unique('token_hash', 'uk_member_magic_token_hash');
            $table->index('member_id', 'idx_member_magic_token_member');
            $table->index('expires_at', 'idx_member_magic_token_expires');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_magic_tokens');
    }
};
