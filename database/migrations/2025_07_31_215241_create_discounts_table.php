<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up(): void
        {
            Schema::create('discounts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id');
                $table->string('description')->nullable();
                $table->date('start_date');
                $table->date('end_date');
                $table->decimal('discount_percentage', 5, 2);
                $table->timestamps();

                $table->foreign('product_id')
                    ->references('id')
                    ->on('products')
                    ->onDelete('cascade');

                $table->softDeletes();
            });
        }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};