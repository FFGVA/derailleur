<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained();
            $table->string('invoice_number', 20)->unique();
            $table->decimal('amount', 8, 2);
            $table->char('statuscode', 1)->default('N');
            $table->date('payment_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('modified_by_id')->nullable()->constrained('users');
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
