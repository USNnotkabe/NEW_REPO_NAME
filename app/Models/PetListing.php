<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetListing extends Model
{
    use HasFactory;

    protected $table = 'petlisting';

    protected $fillable = [
        'user_id',
        'pet_name',
        'category',
        'age',
        'breed',
        'gender',
        'color',
        'description',
        'price',
        'listing_type',
        'status',
        'allergies',
        'medications',
        'food_preferences'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
