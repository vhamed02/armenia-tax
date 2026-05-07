<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['bank_account_id']);
            $table->foreignId('bank_account_id')->nullable()->change();
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['bank_account_id']);
            $table->foreignId('bank_account_id')->nullable(false)->change();
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->cascadeOnDelete();
        });
    }
};
