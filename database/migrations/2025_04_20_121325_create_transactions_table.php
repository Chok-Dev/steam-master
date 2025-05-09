<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained();
            $table->foreignId('user_id')->constrained(); // ผู้ซื้อหรือผู้ขาย
            $table->string('transaction_id')->unique(); // รหัสอ้างอิงจากระบบชำระเงิน
            $table->decimal('amount', 10, 2);
            $table->string('type'); // payment, payout, refund
            $table->string('status'); // pending, successful, failed
            $table->text('notes')->nullable();
            $table->json('payment_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
