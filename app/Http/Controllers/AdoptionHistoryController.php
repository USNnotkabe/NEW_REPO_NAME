<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdoptionHistory;

class AdoptionHistoryController extends Controller
{
    public function index()
    {
        $history = AdoptionHistory::with(['pet', 'adoptionRequest'])
            ->where('user_id', auth()->id())
            ->orderBy('adoption_date', 'desc')
            ->get();

        return view('adoption.history', compact('history'));
    }
}
