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

    // Append image_url to JSON responses automatically
    protected $appends = ['image_url'];

    /**
     * Get the image URL for API responses
     * Handles both Cloudinary URLs and local storage
     */
    public function getImageUrlAttribute()
    {
        // If image is a full Cloudinary URL, use it directly
        if ($this->image && (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://'))) {
            return $this->image;
        }
        
        // If it's a local path, create the API route
        if ($this->image) {
            $filename = basename($this->image);
            return url('/api/pet-image/' . $filename);
        }
        
        return null;
    }

    /**
     * Relationship: Pet belongs to a User (owner)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Pet has many adoption requests
     */
    public function adoptionRequests()
    {
        return $this->hasMany(AdoptionRequest::class);
    }

    /**
     * Get formatted status with first letter capitalized
     */
    public function getFormattedStatusAttribute()
    {
        return ucfirst($this->status);
    }

    /**
     * Scope: Get only available pets
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope: Get only adopted pets
     */
    public function scopeAdopted($query)
    {
        return $query->where('status', 'adopted');
    }
}