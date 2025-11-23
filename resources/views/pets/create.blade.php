<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Pet</title>
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
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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

    input,
    select,
    textarea {
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
        background-color: #4CAF50;
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        margin-top: 10px;
    }

    button:hover {
        background-color: #45a049;
    }

    .back-link {
        display: inline-block;
        margin-bottom: 20px;
        color: #4CAF50;
        text-decoration: none;
    }

    .image-preview {
        margin-top: 10px;
        max-width: 200px;
        display: none;
    }

    .image-preview img {
        width: 100%;
        border-radius: 5px;
        border: 2px solid #ddd;
    }

    .error-messages {
        background-color: #ffebee;
        color: #c62828;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    </style>
</head>

<body>
    <div class="form-container">
        <a href="{{ route('pets.index') }}" class="back-link">← Back to Pets List</a>

        <h1>Add New Pet</h1>

        @if ($errors->any())
        <div class="error-messages">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('pets.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="pet_name">Pet Name *</label>
                <input type="text" name="pet_name" id="pet_name" required value="{{ old('pet_name') }}"
                    placeholder="Enter pet name">
            </div>

            <div class="form-group">
                <label for="category">Category *</label>
                <select name="category" id="category" required>
                    <option value="">Select Category</option>
                    <option value="dog" {{ old('category') == 'dog' ? 'selected' : '' }}>Dog</option>
                    <option value="cat" {{ old('category') == 'cat' ? 'selected' : '' }}>Cat</option>
                </select>
            </div>

            <div class="form-group">
                <label for="image">Pet Image</label>
                <input type="file" name="image" id="image" accept="image/*" onchange="previewImage(event)">
                <div class="image-preview" id="imagePreview">
                    <img src="" alt="Preview">
                </div>
            </div>

            <div class="form-group">
                <label for="age">Age (years)</label>
                <input type="number" name="age" id="age" min="0" value="{{ old('age') }}" placeholder="Enter age">
            </div>

            <div class="form-group">
                <label for="breed">Breed</label>
                <input type="text" name="breed" id="breed" value="{{ old('breed') }}" placeholder="Enter breed">
            </div>

            <div class="form-group">
                <label for="gender">Gender</label>
                <select name="gender" id="gender">
                    <option value="">Select Gender</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>

            <div class="form-group">
                <label for="color">Color</label>
                <input type="text" name="color" id="color" value="{{ old('color') }}" placeholder="Enter color">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description"
                    placeholder="Describe the pet...">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label for="price">Price *</label>
                <input type="number" name="price" id="price" min="0" step="0.01" required value="{{ old('price', 0) }}"
                    placeholder="0.00">
            </div>

            <div class="form-group">
                <label for="listing_type">Listing Type *</label>
                <select name="listing_type" id="listing_type" required>
                    <option value="adopt" {{ old('listing_type') == 'adopt' ? 'selected' : '' }}>Adopt</option>
                    <option value="sell" {{ old('listing_type') == 'sell' ? 'selected' : '' }}>Sell</option>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status *</label>
                <select name="status" id="status" required>
                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="adopted" {{ old('status') == 'adopted' ? 'selected' : '' }}>Adopted</option>
                </select>
            </div>

            <div class="form-group">
                <label for="allergies">Allergies</label>
                <textarea name="allergies" id="allergies"
                    placeholder="List any allergies...">{{ old('allergies') }}</textarea>
            </div>

            <div class="form-group">
                <label for="medications">Medications</label>
                <textarea name="medications" id="medications"
                    placeholder="List any medications...">{{ old('medications') }}</textarea>
            </div>

            <div class="form-group">
                <label for="food_preferences">Food Preferences</label>
                <textarea name="food_preferences" id="food_preferences"
                    placeholder="List food preferences...">{{ old('food_preferences') }}</textarea>
            </div>

            <button type="submit">Add Pet</button>
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