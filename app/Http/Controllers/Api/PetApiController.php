<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pet;
use Illuminate\Support\Facades\Storage;

class PetApiController extends Controller
{
    // Get all available pets (public)
    public function index()
    {
        $pets = Pet::with('user:id,name,email')
            ->where('status', 'available')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($pet) {
                if ($pet->image) {
                    $pet->image_url = asset('storage/' . $pet->image);
                }
                return $pet;
            });

        return response()->json([
            'success' => true,
            'data' => $pets
        ]);
    }

    // Get single pet (public)
    public function show($id)
    {
        $pet = Pet::with('user:id,name,email')->findOrFail($id);

        if ($pet->image) {
            $pet->image_url = asset('storage/' . $pet->image);
        }

        return response()->json([
            'success' => true,
            'data' => $pet
        ]);
    }

    // Get authenticated user's pets
    public function myPets(Request $request)
    {
        $pets = Pet::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($pet) {
                if ($pet->image) {
                    $pet->image_url = asset('storage/' . $pet->image);
                }
                return $pet;
            });

        return response()->json([
            'success' => true,
            'data' => $pets
        ]);
    }

    // Create pet
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_name' => 'required|string|max:255',
            'category' => 'required|in:dog,cat',
            'breed' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:0',
            'gender' => 'nullable|in:male,female',
            'description' => 'nullable|string',
            'listing_type' => 'required|in:adopt,sell',
            'price' => 'required_if:listing_type,sell|nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 'available';

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('pets', 'public');
            $validated['image'] = $imagePath;
        }

        $pet = Pet::create($validated);

        if ($pet->image) {
            $pet->image_url = asset('storage/' . $pet->image);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pet posted successfully',
            'data' => $pet
        ], 201);
    }

    // Update pet
    public function update(Request $request, $id)
    {
        $pet = Pet::findOrFail($id);

        // Check ownership
        if ($pet->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validated = $request->validate([
            'pet_name' => 'required|string|max:255',
            'category' => 'required|in:dog,cat',
            'breed' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:0',
            'gender' => 'nullable|in:male,female',
            'description' => 'nullable|string',
            'listing_type' => 'required|in:adopt,sell',
            'price' => 'required_if:listing_type,sell|nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($pet->image) {
                Storage::disk('public')->delete($pet->image);
            }
            $imagePath = $request->file('image')->store('pets', 'public');
            $validated['image'] = $imagePath;
        }

        $pet->update($validated);

        if ($pet->image) {
            $pet->image_url = asset('storage/' . $pet->image);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pet updated successfully',
            'data' => $pet
        ]);
    }

    // Delete pet
    public function destroy(Request $request, $id)
    {
        $pet = Pet::findOrFail($id);

        // Check ownership
        if ($pet->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Delete image
        if ($pet->image) {
            Storage::disk('public')->delete($pet->image);
        }

        $pet->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pet deleted successfully'
        ]);
    }
}
