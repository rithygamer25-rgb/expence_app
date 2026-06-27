<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            
            // Nullable: Null means global default option, otherwise bound to a specific user account.
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            $table->string('name'); // e.g., "Cash", "Credit Card", "Bank Transfer"
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
