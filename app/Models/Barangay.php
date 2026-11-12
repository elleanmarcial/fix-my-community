<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barangay extends Model
{
    protected $fillable = [
        'psgc_code',
        'name',
        'city',
        'province',
        'region',
        'lead_user_id',
        'boundary',
    ];

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
