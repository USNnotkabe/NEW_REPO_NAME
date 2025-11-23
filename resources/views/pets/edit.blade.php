<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pet - {{ $pet->pet_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        button {
            background-color: #2196F3;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            width: 100%;
        }
        button:hover {
            background-color: #0b7dda;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #2196F3;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .current-image {
            margin-top: 10px;
            margin-bottom: 15px;
        }
        .current-image p {
            color: #666;
            font-size: 14px;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .current-image img {
            width: 100%;
            max-width: 300px;
            border-radius: 8px;
            border: 3px solid #ddd;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .image-preview {
            margin-top: 15px;
            display: none;
        }
        .image-preview p {
            color: #2196F3;
            font-size: 14px;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .image-preview img {
            width: 100%;
            max-width: 300px;
            border-radius: 8px;
            border: 3px solid #2196F3;
            box-shadow: 0 2px 8px rgba(33, 150, 243, 0.3);
        }
        .image-note {
            color: #888;
            font-size: 12px;
            margin-top: 5px;
            font-style: italic;
        }
        .error-messages {
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .error-messages ul {
            margin: 0;
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <a href="{{ route('pets.index') }}" class="back-link">← Back to Pets List</a>

        <h1>Edit Pet: {{ $pet->pet_name }}</h1>

        @if ($errors->any())
            <div class="error-messages">
                <strong>Please fix the following errors:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pets.update', $pet->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="pet_name">Pet Name *</label>
                <input type="text" name="pet_name" id="pet_name" required value="{{ old('pet_name', $pet->pet_name) }}" placeholder="Enter pet name">
            </div>

            <div class="form-group">
                <label for="category">Category *</label>
                <select name="category" id="category" required>
                    <option value="">Select Category</option>
                    <option value="dog" {{ old('category', $pet->category) == 'dog' ? 'selected' : '' }}>Dog</option>
                    <option value="cat" {{ old('category', $pet->category) == 'cat' ? 'selected' : '' }}>Cat</option>
                </select>
            </div>

            <div class="form-group">
                <label for="image">Pet Image</label>

                @if($pet->image)
                    <div class="current-image">
                        <p>📷 Current Image:</p>
                        <img src="{{ asset('storage/' . $pet->image) }}" alt="{{ $pet->pet_name }}">
                    </div>
                @endif

                <input type="file" name="image" id="image" accept="image/*" onchange="previewImage(event)">
                <p class="image-note">💡 Upload a new image to replace the current one (leave empty to keep current image)</p>

                <div class="image-preview" id="imagePreview">
                    <p>🎨 New Image Preview:</p>
                    <img src="" alt="Preview">
                </div>
            </div>

            <div class="form-group">
                <label for="age">Age (years)</label>
                <input type="number" name="age" id="age" min="0" value="{{ old('age', $pet->age) }}" placeholder="Enter age in years">
            </div>

            <div class="form-group">
                <label for="breed">Breed</label>
                <input type="text" name="breed" id="breed" value="{{ old('breed', $pet->breed) }}" placeholder="e.g., Golden Retriever, Persian">
            </div>

            <div class="form-group">
                <label for="gender">Gender</label>
                <select name="gender" id="gender">
                    <option value="">Select Gender</option>
                    <option value="male" {{ old('gender', $pet->gender) == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender', $pet->gender) == 'female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>

            <div class="form-group">
                <label for="color">Color</label>
                <input type="text" name="color" id="color" value="{{ old('color', $pet->color) }}" placeholder="e.g., Brown, White, Black">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" placeholder="Tell us about this pet's personality and behavior...">{{ old('description', $pet->description) }}</textarea>
            </div>

            <div class="form-group">
                <label for="price">Price *</label>
                <input type="number" name="price" id="price" min="0" step="0.01" required value="{{ old('price', $pet->price) }}" placeholder="0.00">
            </div>

            <div class="form-group">
                <label for="listing_type">Listing Type *</label>
                <select name="listing_type" id="listing_type" required>
                    <option value="adopt" {{ old('listing_type', $pet->listing_type) == 'adopt' ? 'selected' : '' }}>Adopt</option>
                    <option value="sell" {{ old('listing_type', $pet->listing_type) == 'sell' ? 'selected' : '' }}>Sell</option>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status *</label>
                <select name="status" id="status" required>
                    <option value="available" {{ old('status', $pet->status) == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="adopted" {{ old('status', $pet->status) == 'adopted' ? 'selected' : '' }}>Adopted</option>
                </select>
            </div>

            <div class="form-group">
                <label for="allergies">Allergies</label>
                <textarea name="allergies" id="allergies" placeholder="List any known allergies...">{{ old('allergies', $pet->allergies) }}</textarea>
            </div>

            <div class="form-group">
                <label for="medications">Medications</label>
                <textarea name="medications" id="medications" placeholder="List current medications and dosages...">{{ old('medications', $pet->medications) }}</textarea>
            </div>

            <div class="form-group">
                <label for="food_preferences">Food Preferences</label>
                <textarea name="food_preferences" id="food_preferences" placeholder="List favorite foods, dietary restrictions, etc...">{{ old('food_preferences', $pet->food_preferences) }}</textarea>
            </div>

            <button type="submit">💾 Update Pet</button>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const preview = document.getElementById('imagePreview');
            const img = preview.querySelector('img');

            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    img.src = e.target.result;
                    preview.style.display = 'block';
                }

                reader.readAsDataURL(event.target.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }
    </script>
</body>
</html>
