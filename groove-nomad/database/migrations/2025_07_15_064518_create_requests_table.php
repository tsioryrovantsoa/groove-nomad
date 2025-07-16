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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            // Relations
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Musical preferences (JSON for flexibility)
            $table->json('genres')->nullable();

            // Budget and Dates
            $table->unsignedInteger('budget')->nullable();
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();

            // Adventure details
            $table->string('region')->nullable();
            $table->unsignedTinyInteger('people_count')->nullable();

            // Cultural Tastes
            $table->json('cultural_tastes')->nullable();

            // Phobias and Allergies
            $table->json('phobias')->nullable();
            $table->json('allergies')->nullable();

            // Request status
            $table->string('status');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
