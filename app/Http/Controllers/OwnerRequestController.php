<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdoptionRequest;
use App\Models\Pet;
use Illuminate\Support\Facades\DB;

class OwnerRequestController extends Controller  // ✅ FIXED
{
    // Get all requests for pets owned by authenticated user
    public function index()
    {
        $requests = AdoptionRequest::with(['pet', 'user'])
            ->whereHas('pet', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($requests, 200);
    }

    // Approve adoption request
    public function approve($id)
    {
        DB::beginTransaction();

        try {
            $adoptionRequest = AdoptionRequest::findOrFail($id);

            if ($adoptionRequest->status !== 'pending') {
                DB::rollBack();
                return response()->json([
                    'message' => 'This request has already been processed'
                ], 400);
            }

            $pet = Pet::findOrFail($adoptionRequest->pet_id);

            if ($pet->user_id !== auth()->id()) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Unauthorized - You are not the pet owner'
                ], 403);
            }

            // Approve request
            AdoptionRequest::where('id', $id)->update(['status' => 'approved']);

            // Mark pet as adopted
            Pet::where('id', $adoptionRequest->pet_id)->update(['status' => 'adopted']);

            // Reject all other pending requests for this pet
            AdoptionRequest::where('pet_id', $adoptionRequest->pet_id)
                ->where('id', '!=', $id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected']);

            DB::commit();

            return response()->json([
                'message' => 'Request approved successfully. Pet marked as adopted and other requests rejected.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error approving request: ' . $e->getMessage()
            ], 500);
        }
    }

    // Reject adoption request
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
