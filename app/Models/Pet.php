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
    ];


    protected $appends = ['image_url'];

 public function getImageUrlAttribute()
{
    // If it's already a full URL (Cloudinary), return it as-is
    if ($this->image && (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://'))) {
        return $this->image;
    }

    // For local storage images
    if ($this->image) {
        $filename = basename($this->image);
        return url('/api/pet-image/' . $filename);
    }

    return null;
}

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function adoptionRequests()
    {
        return $this->hasMany(AdoptionRequest::class);
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