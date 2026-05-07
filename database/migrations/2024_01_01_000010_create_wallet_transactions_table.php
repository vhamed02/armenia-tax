<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('casino_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_provider_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['deposit', 'withdrawal']);
            $table->bigInteger('amount');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->foreignId('bank_account_id')->nullable()->constrained()->nullOnDelete();
            $table->bigInteger('balance_before');
            $table->bigInteger('balance_after');
            $table->string('external_reference')->nullable();
            $table->string('failure_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
