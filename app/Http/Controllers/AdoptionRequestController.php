<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdoptionRequest;
use App\Models\Pet;
use Illuminate\Support\Facades\DB; // ✅ ADD THIS

class AdoptionRequestController extends Controller
{
    // User views their adoption requests
    public function myRequests()
    {
        $requests = AdoptionRequest::with('pet')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('adoption.my-requests', compact('requests'));
    }

    // Show adoption request form
    public function create($petId)
    {
        $pet = Pet::findOrFail($petId);

        // Check if pet is available
        if ($pet->status !== 'available') {
            return redirect()->back()->with('error', 'This pet is no longer available for adoption');
        }

        // Check if user already has a pending request for this pet
        $existingRequest = AdoptionRequest::where('user_id', auth()->id())
            ->where('pet_id', $petId)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'You already have a pending request for this pet');
        }

        return view('adoption.create', compact('pet'));
    }

    // Submit adoption request
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'message' => 'required|string|min:20',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        AdoptionRequest::create($validated);

        return redirect()->route('adoption.my-requests')
            ->with('success', 'Adoption request submitted! Please wait for admin approval.');
    }

    // Cancel adoption request (user)
    public function cancel($id)
    {
        $request = AdoptionRequest::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();

        $request->delete();

        return redirect()->back()->with('success', 'Adoption request cancelled');
    }

    // ✅ ADD THIS METHOD - Approve adoption request
    public function approve($id)
    {
        DB::beginTransaction();

        try {
            // Find the request
            $adoptionRequest = AdoptionRequest::findOrFail($id);

            // Check if request is still pending
            if ($adoptionRequest->status !== 'pending') {
                return response()->json([
                    'message' => 'This request has already been processed'
                ], 400);
            }

            // Check if the pet owner is the authenticated user
            $pet = Pet::findOrFail($adoptionRequest->pet_id);

            if ($pet->user_id !== auth()->id()) {
                return response()->json([
                    'message' => 'Unauthorized - You are not the pet owner'
                ], 403);
            }

            // Approve this request
            $adoptionRequest->status = 'approved';
            $adoptionRequest->save();

            // Mark the pet as adopted
            $pet->status = 'adopted';
            $pet->save();

            // ✅ REJECT ALL OTHER PENDING REQUESTS FOR THIS PET
            AdoptionRequest::where('pet_id', $adoptionRequest->pet_id)
                ->where('id', '!=', $id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);

            DB::commit();

            return response()->json([
                'message' => 'Request approved successfully. Pet marked as adopted and other requests rejected.',
                'data' => $adoptionRequest->load(['pet', 'user'])
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error approving request: ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ ADD THIS METHOD - Reject adoption request
    // In AdoptionRequestController.php
    public function reject(Request $request, $id)
    {
        // Make owner_notes optional
        $validated = $request->validate([
            'owner_notes' => 'nullable|string'
        ]);

        try {
            $adoptionRequest = AdoptionRequest::findOrFail($id);

            if ($adoptionRequest->status !== 'pending') {
                return response()->json([
                    'message' => 'This request has already been processed'
                ], 400);
            }

            $pet = Pet::findOrFail($adoptionRequest->pet_id);

            if ($pet->user_id !== auth()->id()) {
                return response()->json([
                    'message' => 'Unauthorized - You are not the pet owner'
                ], 403);
            }

            AdoptionRequest::where('id', $id)->update([
                'status' => 'rejected',
                'owner_notes' => $validated['owner_notes'] ?? null
            ]);

            return response()->json([
                'message' => 'Request rejected successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error rejecting request: ' . $e->getMessage()
            ], 500);
        }
    }
}