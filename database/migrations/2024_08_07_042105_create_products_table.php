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
            $table->foreign('business_id')->references('business_id')->on('businesses')->onDelete('cascade');
            $table->unsignedBigInteger('business_id')->default(1);
            $table->enum('on_sale', ['yes','no'])->default('no');
            $table->decimal('on_sale_price', 8, 2)->default(0);
            $table->enum('featured', ['true','false'])->default('false');
            $table->id();
            $table->string('name');
            $table->string('brand');
            $table->decimal('price', 8, 2);
            $table->string('category');
            $table->integer('stock');
            $table->integer('sold')->default(0);
            $table->string('status');
            $table->text('description');
            $table->date('expDate');
            $table->string('image')->nullable();
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
