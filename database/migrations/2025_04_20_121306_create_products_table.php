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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // ผู้ขาย
            $table->foreignId('category_id')->constrained();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->string('type')->default('steam_key'); // steam_key, account, item
            $table->string('status')->default('available'); // available, sold, pending
            $table->json('attributes')->nullable(); // เช่น แพลตฟอร์ม, อายุบัญชี, ฯลฯ
            $table->integer('views')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
