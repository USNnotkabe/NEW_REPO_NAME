<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdoptionRequest;
use App\Models\Pet;
use Illuminate\Support\Facades\Storage;

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

                // ✅ Include valid ID URLs
                if ($req->valid_id_1) {
                    $req->valid_id_1_url = asset('storage/' . $req->valid_id_1);
                }
                if ($req->valid_id_2) {
                    $req->valid_id_2_url = asset('storage/' . $req->valid_id_2);
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
            'applicant_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'valid_id_1' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'valid_id_2' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
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

        // Upload valid IDs only if provided
        $id1Path = $request->hasFile('valid_id_1')
            ? $request->file('valid_id_1')->store('valid_ids', 'public')
            : null;

        $id2Path = $request->hasFile('valid_id_2')
            ? $request->file('valid_id_2')->store('valid_ids', 'public')
            : null;

        // Create adoption request
        $adoptionRequest = AdoptionRequest::create([
            'user_id' => $request->user()->id,
            'pet_id' => $validated['pet_id'],
            'message' => $validated['message'],
            'applicant_name' => $request->input('applicant_name'),
            'phone_number' => $request->input('phone_number'),
            'valid_id_1' => $id1Path,
            'valid_id_2' => $id2Path,
            'status' => 'pending',
        ]);

        $adoptionRequest->load('pet.user');

        // Add image URLs
        if ($adoptionRequest->pet && $adoptionRequest->pet->image) {
            $adoptionRequest->pet->image_url = asset('storage/' . $adoptionRequest->pet->image);
        }

        if ($adoptionRequest->valid_id_1) {
            $adoptionRequest->valid_id_1_url = asset('storage/' . $adoptionRequest->valid_id_1);
        }
        if ($adoptionRequest->valid_id_2) {
            $adoptionRequest->valid_id_2_url = asset('storage/' . $adoptionRequest->valid_id_2);
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

        // Delete uploaded IDs from storage
        if ($adoptionRequest->valid_id_1) {
            Storage::disk('public')->delete($adoptionRequest->valid_id_1);
        }
        if ($adoptionRequest->valid_id_2) {
            Storage::disk('public')->delete($adoptionRequest->valid_id_2);
        }

        $adoptionRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Request cancelled successfully'
        ]);
    }
    public function getMyPetRequests()
{
    $requests = AdoptionRequest::with(['user', 'pet'])
        ->whereHas('pet', function ($query) {
            $query->where('user_id', auth()->id());
        })
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($req) {
            // Add pet image URL
            if ($req->pet && $req->pet->image) {
                $req->pet->image_url = asset('storage/' . $req->pet->image);
            }

            // Add valid ID URLs
            if ($req->valid_id_1) {
                $req->valid_id_1_url = asset('storage/' . $req->valid_id_1);
            }
            if ($req->valid_id_2) {
                $req->valid_id_2_url = asset('storage/' . $req->valid_id_2);
            }

            return $req;
        });

    return response()->json(['data' => $requests]);
}
}
