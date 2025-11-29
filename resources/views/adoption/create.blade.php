<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request to {{ $pet->listing_type === 'adopt' ? 'Adopt' : 'Buy' }} {{ $pet->pet_name }}</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        max-width: 700px;
        margin: 50px auto;
        padding: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }

    .container {
        background: white;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    h1 {
        color: #333;
        margin-bottom: 10px;
    }

    .pet-info {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 30px;
        display: flex;
        gap: 20px;
        align-items: center;
    }

    .pet-info img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 10px;
    }

    .pet-details h2 {
        color: #667eea;
        margin-bottom: 10px;
    }

    .pet-details p {
        color: #666;
        margin: 5px 0;
    }

    .form-group {
        margin-bottom: 25px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: #555;
    }

    textarea {
        width: 100%;
        padding: 12px;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        box-sizing: border-box;
        resize: vertical;
        min-height: 150px;
        font-family: Arial, sans-serif;
    }

    textarea:focus {
        border-color: #667eea;
        outline: none;
    }

    .char-count {
        text-align: right;
        color: #999;
        font-size: 0.9em;
        margin-top: 5px;
    }

    .buttons {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }

    .btn {
        flex: 1;
        padding: 15px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-submit {
        background: #28a745;
        color: white;
    }

    .btn-submit:hover {
        background: #218838;
    }

    .btn-cancel {
        background: #6c757d;
        color: white;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-cancel:hover {
        background: #5a6268;
    }

    .info-box {
        background: #e7f3ff;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 25px;
        border-left: 4px solid #2196F3;
    }

    .info-box p {
        margin: 5px 0;
        color: #0c5460;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>🐾 Request to {{ $pet->listing_type === 'adopt' ? 'Adopt' : 'Buy' }}</h1>

        <div class="pet-info">
            @if($pet->image)
            <img src="{{ asset('storage/' . $pet->image) }}" alt="{{ $pet->pet_name }}">
            @else
            <img src="https://via.placeholder.com/150/667eea/ffffff?text=No+Image" alt="No image">
            @endif

            <div class="pet-details">
                <h2>{{ $pet->pet_name }}</h2>
                <p><strong>Category:</strong> {{ ucfirst($pet->category) }}</p>
                <p><strong>Breed:</strong> {{ $pet->breed ?? 'Mixed' }}</p>
                <p><strong>Age:</strong> {{ $pet->age ?? 'Unknown' }} years old</p>
                <p><strong>Type:</strong> {{ ucfirst($pet->listing_type) }}</p>
                @if($pet->listing_type === 'sell')
                <p><strong>Price:</strong> ${{ number_format($pet->price, 2) }}</p>
                @endif
                <p><strong>Owner:</strong> {{ $pet->user->name }}</p>
            </div>
        </div>

        <div class="info-box">
            <p><strong>ℹ️ Important Information:</strong></p>
            <p>• Your request will be reviewed by the pet owner</p>
            <p>• Please provide detailed information about why you want to
                {{ $pet->listing_type === 'adopt' ? 'adopt' : 'buy' }} this pet</p>
            <p>• You'll be notified once the owner reviews your request</p>
            <p>• If approved, you'll receive the owner's contact information</p>
        </div>

        <form action="{{ route('adoption.store') }}" method="POST">
            @csrf
            <input type="hidden" name="pet_id" value="{{ $pet->id }}">

            <div class="form-group">
                <label for="message">Why do you want to {{ $pet->listing_type === 'adopt' ? 'adopt' : 'buy' }}
                    {{ $pet->pet_name }}? *</label>
                <textarea name="message" id="message" required
                    placeholder="Tell us about yourself, your living situation, experience with pets, and why you'd be a great owner for {{ $pet->pet_name }}..."
                    oninput="updateCharCount()">{{ old('message') }}</textarea>
                <div class="char-count">
                    <span id="charCount">0</span> characters (minimum 20)
                </div>
                @error('message')
                <p style="color: red; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div class="buttons">
                <a href="{{ route('petlisting.index') }}" class="btn btn-cancel">Cancel</a>
                <button type="submit" class="btn btn-submit">📨 Submit
                    {{ $pet->listing_type === 'adopt' ? 'Adoption' : 'Purchase' }} Request</button>
            </div>
        </form>
    </div>

    <script>
    function updateCharCount() {
        const textarea = document.getElementById('message');
        const charCount = document.getElementById('charCount');
        charCount.textContent = textarea.value.length;
    }

    // Initialize count on page load
    updateCharCount();
    </script>
</body>

</html>