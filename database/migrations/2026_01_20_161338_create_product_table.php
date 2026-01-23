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
            $table->id();//BIGINT UNSIGNED AUTO INCREMENT PRIMARY KEY
            $table->string('name', 64);//VARCHAR(64)
            $table->integer('price');//INT
            $table->integer('quantity')->default(100);//INT DEFAULT 100
            $table->boolean('on_sale')->default(false);//TINYINT(1) -> 0,1 -> DEFAULT: false
            $table->text('description');//TEXT -> NULLABLE
            $table->timestamps();//CREATED_AT, UPDATED_AT -> DATETIME ->DEFAULT: NOW()
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
