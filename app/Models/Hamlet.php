<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hamlet extends Model
{
    use HasFactory;

    public function business_profiles(): HasMany
    {
        return $this->hasMany(BusinessProfile::class);
    }
}
