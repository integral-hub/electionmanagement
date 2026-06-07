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
        Schema::create('voter_unique_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voter_id')->constrained('voters')->cascadeOnDelete();
            $table->string('field_name', 191);
            $table->string('value')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voter_unique_data');
    }
};
