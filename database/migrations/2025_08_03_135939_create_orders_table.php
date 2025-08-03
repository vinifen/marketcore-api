<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('address_id')->nullable();
            $table->dateTime('order_date');
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->decimal('total_amount', 10, 2);

            $table->enum('status', [
                'PENDING',
                'PROCESSING',
                'SHIPPED',
                'COMPLETED',
                'CANCELED'
            ])->default('PENDING');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('address_id')
                ->references('id')
                ->on('addresses')
                ->onDelete('set null');

            $table->foreign('coupon_id')
                ->references('id')
                ->on('coupons')
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};