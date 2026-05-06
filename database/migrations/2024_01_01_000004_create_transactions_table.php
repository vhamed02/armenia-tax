<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('transaction_type', ['credit', 'debit']);
            $table->bigInteger('amount');
            $table->string('description')->nullable();
            $table->date('transaction_date');
            $table->string('external_reference')->nullable();
            $table->enum('source_type', ['salary', 'freelance', 'transfer', 'unknown'])->default('unknown');
            $table->boolean('is_flagged')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
