<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 40);
            $table->string('last_name', 60);
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('city')->nullable();
            $table->string('country', 2)->default('CH');
            $table->char('statuscode', 1)->default('D');
            $table->date('membership_start')->nullable();
            $table->date('membership_end')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
