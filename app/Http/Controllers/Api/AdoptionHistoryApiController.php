<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdoptionHistoryApiController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // Get ALL adoption history where user is involved (as adopter OR original owner)
        $history = DB::table('adoption_history')
            ->join('pets', 'adoption_history.pet_id', '=', 'pets.id')
            ->join('users as original_owner', 'pets.user_id', '=', 'original_owner.id')
            ->join('users as new_owner', 'adoption_history.user_id', '=', 'new_owner.id')
            ->where(function ($query) use ($userId) {
                $query->where('adoption_history.user_id', $userId)  // I adopted
                    ->orWhere('pets.user_id', $userId);           // My pet was adopted
            })
            ->select(
                'adoption_history.*',
                'pets.pet_name',
                'pets.breed',
                'pets.category',
                'pets.image',
                'original_owner.name as original_owner_name',
                'new_owner.name as new_owner_name'
            )
            ->orderBy('adoption_history.adoption_date', 'desc')
            ->get()
            ->map(function ($item) {
                if ($item->image) {
                    // Use dynamic URL for CORS support
                    $baseUrl = request()->getSchemeAndHttpHost();
                    $filename = basename($item->image);
                    $item->image_url = $baseUrl . '/api/pet-image/' . $filename;
                }
                return $item;
            });

        return response()->json($history);
    }
}
