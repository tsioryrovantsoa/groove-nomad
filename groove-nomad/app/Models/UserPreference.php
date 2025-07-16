<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les genres musicaux
     */
    public function musicGenres()
    {
        return $this->belongsToMany(MusicGenre::class, 'user_preference_music_genres');
    }

    /**
     * Relation avec les goûts culturels
     */
    public function culturalTastes()
    {
        return $this->belongsToMany(CulturalTaste::class, 'user_preference_cultural_tastes');
    }

    /**
     * Relation avec les phobies
     */
    public function phobias()
    {
        return $this->belongsToMany(Phobia::class, 'user_preference_phobias');
    }

    /**
     * Relation avec les allergies
     */
    public function allergies()
    {
        return $this->belongsToMany(Allergy::class, 'user_preference_allergies');
    }

    /**
     * Méthode pour ajouter des genres musicaux
     */
    public function addMusicGenres($genreIds)
    {
        $this->musicGenres()->sync($genreIds);
    }

    /**
     * Méthode pour ajouter des goûts culturels
     */
    public function addCulturalTastes($tasteIds)
    {
        $this->culturalTastes()->sync($tasteIds);
    }

    /**
     * Méthode pour ajouter des phobies
     */
    public function addPhobias($phobiaIds)
    {
        $this->phobias()->sync($phobiaIds);
    }

    /**
     * Méthode pour ajouter des allergies
     */
    public function addAllergies($allergyIds)
    {
        $this->allergies()->sync($allergyIds);
    }
} 