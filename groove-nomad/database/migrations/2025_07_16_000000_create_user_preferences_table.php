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
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            
            // Relation avec l'utilisateur (une seule préférence par utilisateur)
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            
            $table->timestamps();
        });
        
        // Table de liaison pour les genres musicaux
        Schema::create('user_preference_music_genres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_preference_id')->constrained()->onDelete('cascade');
            $table->foreignId('music_genre_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Éviter les doublons
            $table->unique(['user_preference_id', 'music_genre_id'], 'upmg_unique');
        });
        
        // Table de liaison pour les goûts culturels
        Schema::create('user_preference_cultural_tastes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_preference_id')->constrained()->onDelete('cascade');
            $table->foreignId('cultural_taste_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Éviter les doublons
            $table->unique(['user_preference_id', 'cultural_taste_id'], 'upct_unique');
        });
        
        // Table de liaison pour les phobies
        Schema::create('user_preference_phobias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_preference_id')->constrained()->onDelete('cascade');
            $table->foreignId('phobia_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Éviter les doublons
            $table->unique(['user_preference_id', 'phobia_id'], 'upp_unique');
        });
        
        // Table de liaison pour les allergies
        Schema::create('user_preference_allergies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_preference_id')->constrained()->onDelete('cascade');
            $table->foreignId('allergy_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Éviter les doublons
            $table->unique(['user_preference_id', 'allergy_id'], 'upa_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preference_allergies');
        Schema::dropIfExists('user_preference_phobias');
        Schema::dropIfExists('user_preference_cultural_tastes');
        Schema::dropIfExists('user_preference_music_genres');
        Schema::dropIfExists('user_preferences');
    }
}; 