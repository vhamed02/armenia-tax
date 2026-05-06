<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scanning_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('triggered_at');
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending');
            $table->integer('transactions_scanned')->default(0);
            $table->integer('anomalies_found')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scanning_jobs');
    }
};
