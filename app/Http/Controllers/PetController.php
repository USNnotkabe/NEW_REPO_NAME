<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pet;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PetController extends Controller
{
    public function index()
    {
        // Only show pets belonging to the logged-in user
        $pets = Pet::where('user_id', Auth::id())->get();
        return view('pets.index', ['pets' => $pets]);
    }

    public function create()
    {
        return view('pets.create');
    }

public function store(Request $request)
{
    $data = $request->validate([
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

    // Handle image upload to Cloudinary
    if ($request->hasFile('image')) {
        try {
            // Use Laravel's Cloudinary facade with custom HTTP client
            $uploadResult = $request->file('image')->storeOnCloudinary('pets');
            $data['image'] = $uploadResult->getSecurePath();
            
        } catch (\Exception $e) {
            \Log::error('Cloudinary upload failed: ' . $e->getMessage());
            return back()->withErrors(['image' => 'Failed to upload image: ' . $e->getMessage()]);
        }
    }

    // Automatically assign the authenticated user
    $data['user_id'] = Auth::id();

    $newPet = Pet::create($data);
    return redirect(route('pets.index'))->with('success', 'Pet added successfully!');
}

public function update(Request $request, Pet $pet)
{
    // Check if user owns this pet
    if ($pet->user_id !== Auth::id()) {
        abort(403, 'Unauthorized - You can only edit your own pets');
    }

    $data = $request->validate([
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
        'allergies' => 'nullable|string',
        'medications' => 'nullable|string',
        'food_preferences' => 'nullable|string',
    ]);

    // Handle image upload to Cloudinary
    if ($request->hasFile('image')) {
        try {
            // Use Laravel's Cloudinary facade
            $uploadResult = $request->file('image')->storeOnCloudinary('pets');
            $data['image'] = $uploadResult->getSecurePath();
            
        } catch (\Exception $e) {
            \Log::error('Cloudinary upload failed: ' . $e->getMessage());
            return back()->withErrors(['image' => 'Failed to upload image: ' . $e->getMessage()]);
        }
    }

    $pet->update($data);

    return redirect()->route('pets.index')->with('success', 'Pet updated successfully!');
}

    public function show(Pet $pet)
    {
        // Check if user owns this pet
        if ($pet->user_id !== Auth::id()) {
            abort(403, 'Unauthorized - You can only view your own pets');
        }

        return view('pets.show', ['pet' => $pet]);
    }

    public function destroy(Pet $pet)
    {
        // Check if user owns this pet
        if ($pet->user_id !== Auth::id()) {
            abort(403, 'Unauthorized - You can only delete your own pets');
        }

        $pet->delete();
        return redirect()->route('pets.index')->with('success', 'Pet deleted successfully!');
    }
}