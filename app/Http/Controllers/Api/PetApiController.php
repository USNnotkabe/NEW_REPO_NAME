<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class PetApiController extends Controller
{
    /**
     * Add full image URL whenever loading pets
     */
    private function appendImageUrl($pet)
    {
        Log::info('🔍 appendImageUrl called for pet: ' . ($pet->pet_name ?? 'unknown'));
        Log::info('📷 Original image: ' . ($pet->image ?? 'null'));

        if ($pet && $pet->image) {
            // Always use the image field directly since it's already a full URL from Cloudinary
            $pet->image_url = $pet->image;
            Log::info('✅ Set image_url to: ' . $pet->image_url);
        } else {
            $pet->image_url = null;
            Log::info('❌ No image, set image_url to null');
        }
        return $pet;
    }

    private function appendImageUrlToCollection($pets)
    {
        return $pets->map(function ($pet) {
            return $this->appendImageUrl($pet);
        });
    }

    /**
     * Get all available pets (public)
     */
    public function index(Request $request)
    {
        $query = Pet::with('user')->where('status', 'available');

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('pet_name', 'like', "%{$search}%")
                  ->orWhere('breed', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $pets = $query->latest()->get();

        return response()->json(
            $this->appendImageUrlToCollection($pets)
        )->header('Access-Control-Allow-Origin', '*');
    }

    /**
     * Get single pet details (public)
     */
    public function show($id)
    {
        $pet = Pet::with('user')->findOrFail($id);

        return response()->json(
            $this->appendImageUrl($pet)
        )->header('Access-Control-Allow-Origin', '*');
    }

    /**
     * Get current user's pets
     */
    public function myPets()
    {
        $pets = Pet::where('user_id', Auth::id())
            ->latest()
            ->get();

        return response()->json(
            $this->appendImageUrlToCollection($pets)
        )->header('Access-Control-Allow-Origin', '*');
    }

    /**
     * Create new pet listing with Cloudinary upload
     */
    public function store(Request $request)
    {
        Log::info('🔥🔥🔥 STORE METHOD CALLED - NEW CODE RUNNING 🔥🔥🔥');

        // Handle OPTIONS preflight for CORS
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization');
        }

        $validatedData = $request->validate([
            'pet_name' => 'required|string|max:255',
            'category' => 'required|in:dog,cat',
            'age' => 'nullable|integer|min:0',
            'breed' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female',
            'color' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|numeric|min:0',
            'listing_type' => 'required|in:sell,adopt,foster',
            'status' => 'required|in:available,adopted,pending',
            'allergies' => 'nullable|string',
            'medications' => 'nullable|string',
            'food_preferences' => 'nullable|string',
        ]);

        // Upload to Cloudinary
        if ($request->hasFile('image')) {
            Log::info('=== IMAGE UPLOAD STARTED ===');

            try {
                $imageFile = $request->file('image');

                Log::info('File name: ' . $imageFile->getClientOriginalName());
                Log::info('File size: ' . $imageFile->getSize());

                // Read file as base64
                $fileContents = file_get_contents($imageFile->getRealPath());
                $base64 = base64_encode($fileContents);
                $dataUri = 'data:' . $imageFile->getMimeType() . ';base64,' . $base64;

                Log::info('Converted to base64');

                // Use Cloudinary SDK directly
                $cloudinary = new \Cloudinary\Cloudinary([
                    'cloud' => [
                        'cloud_name' => config('cloudinary.cloud_name'),
                        'api_key' => config('cloudinary.api_key'),
                        'api_secret' => config('cloudinary.api_secret'),
                    ]
                ]);

                Log::info('Cloudinary instance created, uploading...');

                // Upload using the SDK directly
                $result = $cloudinary->uploadApi()->upload($dataUri, [
                    'folder' => 'paws/pets',
                    'transformation' => [
                        'width' => 1024,
                        'height' => 1024,
                        'crop' => 'limit',
                        'quality' => 'auto'
                    ]
                ]);

                Log::info('Upload result: ' . json_encode($result));

                if (isset($result['secure_url'])) {
                    $validatedData['image'] = $result['secure_url'];
                    Log::info('✅ SUCCESS! Image URL: ' . $validatedData['image']);
                } else {
                    throw new \Exception('Upload failed - no secure_url in response');
                }

            } catch (\Exception $e) {
                Log::error('=== UPLOAD FAILED ===');
                Log::error('Error: ' . $e->getMessage());
                Log::error('Trace: ' . $e->getTraceAsString());

                return response()->json([
                    'message' => 'Image upload failed: ' . $e->getMessage()
                ], 500)->header('Access-Control-Allow-Origin', '*');
            }
        }

        $validatedData['user_id'] = Auth::id();
        $pet = Pet::create($validatedData);

        return response()->json([
            'message' => 'Pet listing created successfully',
            'pet' => $this->appendImageUrl($pet)
        ], 201)->header('Access-Control-Allow-Origin', '*');
    }

    /**
     * Update pet listing
     */
    public function update(Request $request, $id)
    {
        $pet = Pet::findOrFail($id);

        if ($pet->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403)
                ->header('Access-Control-Allow-Origin', '*');
        }

        $validatedData = $request->validate([
            'pet_name' => 'required|string|max:255',
            'category' => 'required|in:dog,cat',
            'age' => 'nullable|integer|min:0',
            'breed' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female',
            'color' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|numeric|min:0',
            'listing_type' => 'required|in:sell,adopt,foster',
            'status' => 'required|in:available,adopted,pending',
            'allergies' => 'nullable|string',
            'medications' => 'nullable|string',
            'food_preferences' => 'nullable|string',
        ]);

        // Upload new image to Cloudinary if provided
        if ($request->hasFile('image')) {
            try {
                // Delete old image if exists
                if ($pet->image && str_starts_with($pet->image, 'https://res.cloudinary.com/')) {
                    preg_match('/\/v\d+\/(.+)\.\w+$/', $pet->image, $matches);
                    if (isset($matches[1])) {
                        Cloudinary::destroy($matches[1]);
                    }
                } elseif ($pet->image) {
                    Storage::disk('public')->delete($pet->image);
                }

                // Upload new image
                $imageFile = $request->file('image');
                $uploadedFile = Cloudinary::upload(
                    $imageFile->getRealPath(),
                    [
                        'folder' => 'paws/pets',
                        'transformation' => [
                            'width' => 1024,
                            'height' => 1024,
                            'crop' => 'limit',
                            'quality' => 'auto'
                        ]
                    ]
                );

                $validatedData['image'] = $uploadedFile->getSecurePath();

            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Image upload failed: ' . $e->getMessage()
                ], 500)->header('Access-Control-Allow-Origin', '*');
            }
        }

        $pet->update($validatedData);

        return response()->json([
            'message' => 'Pet updated successfully',
            'pet' => $this->appendImageUrl($pet->fresh())
        ])->header('Access-Control-Allow-Origin', '*');
    }

    /**
     * Delete pet listing
     */
    public function destroy($id)
    {
        $pet = Pet::findOrFail($id);

        if ($pet->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403)
                ->header('Access-Control-Allow-Origin', '*');
        }

        // Delete image from Cloudinary or local storage
        if ($pet->image && str_starts_with($pet->image, 'https://res.cloudinary.com/')) {
            try {
                preg_match('/\/v\d+\/(.+)\.\w+$/', $pet->image, $matches);
                if (isset($matches[1])) {
                    Cloudinary::destroy($matches[1]);
                }
            } catch (\Exception $e) {
                // Log error but continue with deletion
            }
        } elseif ($pet->image) {
            Storage::disk('public')->delete($pet->image);
        }

        $pet->delete();

        return response()->json(['message' => 'Pet deleted successfully'])
            ->header('Access-Control-Allow-Origin', '*');
    }
}
