<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProposalDetail extends Model
{
    protected $fillable = [
        'proposal_id',
        'name',
        'description',
        'price',
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }
}
