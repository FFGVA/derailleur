<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_phones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members');
            $table->string('phone_number', 20);
            $table->string('label', 40)->nullable();
            $table->tinyInteger('is_whatsapp')->default(0);
            $table->tinyInteger('sort_order')->unsigned()->default(0);
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_phones');
    }
};
