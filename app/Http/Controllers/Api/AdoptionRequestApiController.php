<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdoptionRequest;
use App\Models\Pet;

class AdoptionRequestApiController extends Controller
{
    // Get user's adoption requests
    public function myRequests(Request $request)
    {
        $requests = AdoptionRequest::with(['pet.user'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($req) {
                if ($req->pet && $req->pet->image) {
                    $req->pet->image_url = asset('storage/' . $req->pet->image);
                }
                return $req;
            });

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    // Create adoption request
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'message' => 'required|string|min:20',
        ]);

        $pet = Pet::findOrFail($validated['pet_id']);

        // Check if pet is available
        if ($pet->status !== 'available') {
            return response()->json([
                'success' => false,
                'message' => 'This pet is no longer available'
            ], 400);
        }

        // Check if user already has pending request for this pet
        $existingRequest = AdoptionRequest::where('user_id', $request->user()->id)
            ->where('pet_id', $validated['pet_id'])
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a pending request for this pet'
            ], 400);
        }

        // Check if user is trying to adopt their own pet
        if ($pet->user_id === $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot request your own pet'
            ], 400);
        }

        $adoptionRequest = AdoptionRequest::create([
            'user_id' => $request->user()->id,
            'pet_id' => $validated['pet_id'],
            'message' => $validated['message'],
            'status' => 'pending',
        ]);

        $adoptionRequest->load('pet.user');

        if ($adoptionRequest->pet && $adoptionRequest->pet->image) {
            $adoptionRequest->pet->image_url = asset('storage/' . $adoptionRequest->pet->image);
        }

        return response()->json([
            'success' => true,
            'message' => 'Request submitted successfully',
            'data' => $adoptionRequest
        ], 201);
    }

    // Cancel adoption request
    public function cancel(Request $request, $id)
    {
        $adoptionRequest = AdoptionRequest::findOrFail($id);

        // Check ownership
        if ($adoptionRequest->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Can only cancel pending requests
        if ($adoptionRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Can only cancel pending requests'
            ], 400);
        }

        $adoptionRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Request cancelled successfully'
        ]);
    }
}
