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
            $table->integer('stock')->default(0);
            $table->integer('sold')->default(0);
            $table->string('status');
            $table->text('description');
            $table->date('expDate');
            $table->string('image')->nullable();
            $table->timestamps();
        });


        Schema::create('product_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('stock_expDate')->unique(); // Ensure 'category' is unique
            $table->integer('count')->unique(); // Ensure 'category' is unique
            $table->timestamps();
        });

        DB::table('product_notification_settings')->insert([
            ['stock_expDate' => 'stock', 'count' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['stock_expDate' => 'expDate', 'count' => 7, 'created_at' => now(), 'updated_at' => now()],
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_notification_settings');
    }
};
