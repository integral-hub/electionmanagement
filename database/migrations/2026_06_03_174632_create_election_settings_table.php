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
        Schema::create('election_settings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('election_id')->constrained()->cascadeOnDelete();
            $table->enum('registration_mode', ['open','closed'])->default('open');
            $table->json('voters_verification_requirement')->nullable(); //{"email": true, "phone": false,"image_compare": false}
            $table->boolean('vote_before_validation')->default(false);
            $table->json('login_fields')->nullable();
            $table->boolean('voters_require_2fa')->default(false);
            $table->enum('voters_2fa_type', ['sms', 'email', 'authenticator', 'none'])->default('none');
            $table->boolean('is_started')->default(false);
            $table->timestamp('voting_start')->nullable();
            $table->timestamp('voting_end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('election_settings');
    }
};
