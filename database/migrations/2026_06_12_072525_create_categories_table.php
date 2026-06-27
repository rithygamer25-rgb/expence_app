<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            
            // Nullable: Null means it's a default global choice for everyone. 
            // If it has an id, it belongs exclusively to that specific user.
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            $table->string('name'); // e.g., "Food & Dining", "Shopping"
            $table->string('icon')->nullable(); // e.g., "bi-shop", "bi-cart" (for Bootstrap Icons)
            $table->string('color_theme')->nullable(); // e.g., "primary", "success" (for dynamic styling)
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
