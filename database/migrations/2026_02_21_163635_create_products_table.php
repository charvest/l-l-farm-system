<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
   public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();

        $table->foreignId('category_id')->constrained()->onDelete('cascade');

        $table->string('name');
        $table->string('type')->nullable(); // Live pig, piglet, rooster etc
        $table->decimal('price', 10, 2);
        $table->integer('stock')->default(0);

        $table->string('health')->nullable();
        $table->string('size')->nullable();
        $table->string('gender')->nullable();
        $table->string('status')->default('Available'); // Born, Ready, Sold

        $table->date('availability_date')->nullable();
        $table->text('description')->nullable();

        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
