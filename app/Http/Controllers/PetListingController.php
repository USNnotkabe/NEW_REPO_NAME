<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pet;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class PetListingController extends Controller
{
    // GET /api/petlisting - List all pets (API)
    public function index(Request $request)
    {
        $query = Pet::query();

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->search) {
            $query->where('pet_name', 'like', '%' . $request->search . '%');
        }

        // Check if it's an API request or web request
        if ($request->wantsJson() || $request->is('api/*')) {
            $pets = $query->get();
            return response()->json($pets);
        }

        // For web request, return view
        $pets = $query->where('status', 'available')->get();
        return view('petlisting.index', compact('pets'));
    }

    // GET /api/petlisting/{id} - Get single pet
    public function show($id)
    {
        $pet = Pet::with('user')->find($id);

        if (!$pet) {
            // Check if it's API request
            if (request()->wantsJson() || request()->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pet not found'
                ], 404);
            }

            abort(404, 'Pet not found');
        }

        // Check if it's API request
        if (request()->wantsJson() || request()->is('api/*')) {
            return response()->json($pet);
        }

      $hasPendingRequest = false;
if (Auth::check()) {
    $hasPendingRequest = \App\Models\AdoptionRequest::where('user_id', Auth::id())
        ->where('pet_id', $id)
        ->where('status', 'pending')
        ->exists();
}

        return view('petlisting.show', compact('pet', 'hasPendingRequest'));
    }

    // POST /api/petlisting - Create new pet
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_name' => 'required',
            'category' => 'required|in:dog,cat',
            'age' => 'nullable|integer|min:0',
            'breed' => 'nullable',
            'gender' => 'nullable|in:male,female',
            'color' => 'nullable',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|numeric|min:0',
            'listing_type' => 'required|in:sell,adopt',
            'status' => 'required|in:available,adopted',
            'allergies' => 'nullable',
            'medications' => 'nullable',
            'food_preferences' => 'nullable',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('pets', 'public');
            $validated['image'] = $imagePath;
        }

        // Automatically assign the authenticated user
        $validated['user_id'] = $request->user()->id;

        $pet = Pet::create($validated);

        // Return full image URL in response
        if ($pet->image) {
            $pet->image_url = asset('storage/' . $pet->image);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pet created successfully',
            'data' => $pet
        ], 201);
    }

    // PUT /api/petlisting/{id} - Update pet
    public function update(Request $request, $id)
    {
        $pet = Pet::find($id);

        if (!$pet) {
            return response()->json([
                'success' => false,
                'message' => 'Pet not found'
            ], 404);
        }

        // Check if user owns this pet
        if ($pet->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - You can only edit your own pets'
            ], 403);
        }

        $validated = $request->validate([
            'pet_name' => 'required',
            'category' => 'required|in:dog,cat',
            'age' => 'nullable|integer|min:0',
            'breed' => 'nullable',
            'gender' => 'nullable|in:male,female',
            'color' => 'nullable',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|numeric|min:0',
            'listing_type' => 'required|in:sell,adopt',
            'status' => 'required|in:available,adopted',
            'allergies' => 'nullable',
            'medications' => 'nullable',
            'food_preferences' => 'nullable',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($pet->image) {
                Storage::disk('public')->delete($pet->image);
            }

            $imagePath = $request->file('image')->store('pets', 'public');
            $validated['image'] = $imagePath;
        }

        $pet->update($validated);

        // Return full image URL in response
        if ($pet->image) {
            $pet->image_url = asset('storage/' . $pet->image);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pet updated successfully',
            'data' => $pet
        ]);
    }

    // DELETE /api/petlisting/{id} - Delete pet
    public function destroy(Request $request, $id)
    {
        $pet = Pet::find($id);

        if (!$pet) {
            return response()->json([
                'success' => false,
                'message' => 'Pet not found'
            ], 404);
        }

        // Check if user owns this pet
        if ($pet->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - You can only delete your own pets'
            ], 403);
        }

        // Delete image if exists
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
