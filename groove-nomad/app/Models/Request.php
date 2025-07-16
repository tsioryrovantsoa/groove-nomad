<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $fillable = [
        'user_id',
        'genres',
        'budget',
        'date_start',
        'date_end',
        'region',
        'people_count',
        'cultural_tastes',
        'phobias',
        'allergies',
        'status'
    ];

    protected $casts = [
        'genres' => 'array',
        'cultural_tastes' => 'array',
        'phobias' => 'array',
        'allergies' => 'array',
        'date_start' => 'date',
        'date_end' => 'date',
    ];

    public function proposals()
    {
        return $this->hasMany(\App\Models\Proposal::class);
    }
}
