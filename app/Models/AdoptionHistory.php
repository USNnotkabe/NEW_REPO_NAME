<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdoptionHistory extends Model
{
    use HasFactory;

    protected $table = 'adoption_history';

    protected $fillable = [
        'user_id',
        'pet_id',
        'adoption_request_id',
        'adoption_date',
        'notes',
    ];

    protected $casts = [
        'adoption_date' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function adoptionRequest()
    {
        return $this->belongsTo(AdoptionRequest::class);
    }
}
