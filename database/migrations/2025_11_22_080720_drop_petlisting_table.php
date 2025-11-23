<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('petlisting');
    }

    public function down(): void
    {
        // Recreate table if rollback is needed
        Schema::create('petlisting', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained('pets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('listing_type', ['sell', 'adopt'])->default('sell');
            $table->enum('status', ['available', 'adopted'])->default('available');
            $table->timestamps();
        });
    }
};