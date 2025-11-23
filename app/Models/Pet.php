<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pet_name',
        'category',
        'age',
        'breed',
        'gender',
        'color',
        'description',
         'image',
        'price',
        'listing_type',
        'status',
        'allergies',
        'medications',
        'food_preferences',
        'image_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedStatusAttribute()
    {
        return ucfirst($this->status);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeAdopted($query)
    {
        return $query->where('status', 'adopted');
    }
}
