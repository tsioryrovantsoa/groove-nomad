<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Festival extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'url',
        'image',
        'start_date',
        'end_date',
        'description',
        'location',
        'city',
        'region',
        'page',
        'region_abbr',
    ];

    public $translatable = ['description'];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }
}
