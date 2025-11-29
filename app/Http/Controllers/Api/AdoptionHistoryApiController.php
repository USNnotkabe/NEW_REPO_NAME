<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdoptionHistory;

class AdoptionHistoryApiController extends Controller
{
    // Get user's adoption history
    public function index(Request $request)
    {
        $history = AdoptionHistory::with(['pet', 'adoptionRequest'])
            ->where('user_id', $request->user()->id)
            ->orderBy('adoption_date', 'desc')
            ->get()
            ->map(function ($record) {
                if ($record->pet && $record->pet->image) {
                    $record->pet->image_url = asset('storage/' . $record->pet->image);
                }
                return $record;
            });

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }
}
