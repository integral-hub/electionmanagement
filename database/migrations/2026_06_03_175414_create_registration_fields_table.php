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
        Schema::create('registration_fields', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('election_id')->constrained()->cascadeOnDelete();
        /* JSON array of field definitions. Each item shape:
         {
            "label": "Display label for the field (string)",
            "field_name": "Database/key name for the field (string)",
            "field_type": "type: text|email|select|checkbox|date (string)",
            "required": "whether the field is required (boolean)",
            "unique_field": "whether values must be unique across registrations (boolean)",
            "is_hash": "whether to store/hash the value (boolean)",
            "options": "array|null - for select/checkbox options (array or null)",
            "sort_order": "ordering integer (optional)",
            optional: "description": "human-readable note about the field"
         } */
            $table->json('fields');
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_fields');
    }
};
