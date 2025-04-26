<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Create a new migration with:
    // php artisan make:migration add_seller_request_fields_to_users_table

    // Then in the migration file:
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('seller_request_status')->nullable();
            $table->timestamp('seller_request_at')->nullable();
            $table->json('seller_details')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['seller_request_status', 'seller_request_at', 'seller_details']);
        });
    }
};
