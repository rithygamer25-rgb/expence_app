<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to build the structure.
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('location');
        $table->date('date');
        $table->decimal('amount', 10, 2);
        
        // FIXED HERE: Changed 'restrict' to 'no action' for SQL Server compatibility
        $table->foreignId('category_id')->constrained('categories')->onDelete('no action');
        $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('no action');
        
        $table->timestamps();
    });
    
    }

    /**
     * Reverse the migrations by dropping the tracking tables.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
