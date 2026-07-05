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
        Schema::create('voters', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->json('voter_data')->nullable(); //JSON payload for registration fields / metadata;
            $table->string('batch_code')->nullable();
            $table->string('phone')->nullable()->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->boolean('is_verified_email')->default(false);
            $table->boolean('is_verified_phone')->default(false);
            $table->timestamp('last_login_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voters');
    }
};
