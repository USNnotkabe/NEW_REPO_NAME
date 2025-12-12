<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdoptionHistory;
use Illuminate\Support\Facades\DB;

class AdoptionHistoryController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // Get ALL adoption history where user is involved (as adopter OR original owner)
        $history = DB::table('adoption_history')
            ->join('pets', 'adoption_history.pet_id', '=', 'pets.id')
            ->join('users as original_owner', 'pets.user_id', '=', 'original_owner.id')
            ->join('users as new_owner', 'adoption_history.user_id', '=', 'new_owner.id')
            ->where(function($query) use ($userId) {
                $query->where('adoption_history.user_id', $userId)  // I adopted
                      ->orWhere('pets.user_id', $userId);           // My pet was adopted
            })
            ->select(
                'adoption_history.id',
                'adoption_history.user_id',
                'adoption_history.pet_id',
                'adoption_history.adoption_date',
                'adoption_history.created_at',
                'pets.pet_name',
                'pets.breed',
                'pets.category',
                'pets.age',
                'pets.gender',
                'pets.listing_type',
                'pets.price',
                'pets.image',
                'pets.user_id as original_owner_id',
                'original_owner.name as original_owner_name',
                'original_owner.email as original_owner_email',
                'new_owner.name as new_owner_name',
                'new_owner.email as new_owner_email'
            )
            ->orderBy('adoption_history.adoption_date', 'desc')
            ->get()
            ->map(function ($item) use ($userId) {
                // Add image URL
                if ($item->image) {
                    $item->image_url = asset('storage/' . $item->image);
                } else {
                    $item->image_url = null;
                }

                // Determine role
                if ($item->user_id == $userId) {
                    $item->role = 'adopter'; // I adopted this pet
                } else {
                    $item->role = 'owner'; // My pet was adopted
                }

                return $item;
            });

        return response()->json(['data' => $history]);
    }
}
