<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pet->pet_name }} - Details</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 40px 20px;
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    .header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 30px;
        color: white;
    }

    .back-link {
        color: white;
        text-decoration: none;
        display: inline-block;
        margin-bottom: 15px;
        opacity: 0.9;
    }

    .back-link:hover {
        opacity: 1;
    }

    h1 {
        margin: 0;
        font-size: 2.5em;
    }

    .pet-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
    }

    .pet-details {
        padding: 40px;
    }

    .detail-row {
        display: flex;
        padding: 15px 0;
        border-bottom: 1px solid #eee;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: bold;
        color: #555;
        width: 200px;
        flex-shrink: 0;
    }

    .detail-value {
        color: #333;
        flex-grow: 1;
    }

    .status-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.9em;
        font-weight: bold;
    }

    .status-available {
        background-color: #d4edda;
        color: #155724;
    }

    .status-adopted {
        background-color: #f8d7da;
        color: #721c24;
    }

    .actions {
        padding: 30px 40px;
        background-color: #f8f9fa;
        display: flex;
        gap: 15px;
    }

    .btn {
        padding: 12px 30px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
        text-align: center;
        flex: 1;
        transition: all 0.3s;
    }

    .edit-btn {
        background-color: #2196F3;
        color: white;
    }

    .edit-btn:hover {
        background-color: #0b7dda;
    }

    .delete-btn {
        background-color: #f44336;
        color: white;
        border: none;
        cursor: pointer;
        font-size: 16px;
    }

    .delete-btn:hover {
        background-color: #da190b;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <a href="{{ route('pets.index') }}" class="back-link">← Back to Pets List</a>
            <h1>{{ $pet->pet_name }}</h1>
        </div>

        @if($pet->image)
        <img src="{{ asset('storage/' . $pet->image) }}" alt="{{ $pet->pet_name }}" class="pet-image">
        @else
        <img src="https://via.placeholder.com/800x400/667eea/ffffff?text=No+Image+Available" alt="No image"
            class="pet-image">
        @endif

        <div class="pet-details">
            <div class="detail-row">
                <div class="detail-label">Category:</div>
                <div class="detail-value">{{ ucfirst($pet->category) }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Breed:</div>
                <div class="detail-value">{{ $pet->breed ?? 'Not specified' }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Age:</div>
                <div class="detail-value">{{ $pet->age ?? 'Unknown' }} years old</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Gender:</div>
                <div class="detail-value">{{ $pet->gender ? ucfirst($pet->gender) : 'Not specified' }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Color:</div>
                <div class="detail-value">{{ $pet->color ?? 'Not specified' }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Description:</div>
                <div class="detail-value">{{ $pet->description ?? 'No description available' }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Price:</div>
                <div class="detail-value">${{ number_format($pet->price, 2) }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Listing Type:</div>
                <div class="detail-value">{{ ucfirst($pet->listing_type) }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Status:</div>
                <div class="detail-value">
                    <span class="status-badge status-{{ $pet->status }}">
                        {{ ucfirst($pet->status) }}
                    </span>
                </div>
            </div>

            @if($pet->allergies)
            <div class="detail-row">
                <div class="detail-label">Allergies:</div>
                <div class="detail-value">{{ $pet->allergies }}</div>
            </div>
            @endif

            @if($pet->medications)
            <div class="detail-row">
                <div class="detail-label">Medications:</div>
                <div class="detail-value">{{ $pet->medications }}</div>
            </div>
            @endif

            @if($pet->food_preferences)
            <div class="detail-row">
                <div class="detail-label">Food Preferences:</div>
                <div class="detail-value">{{ $pet->food_preferences }}</div>
            </div>
            @endif
        </div>

        <div class="actions">
            <a href="{{ route('pets.edit', $pet->id) }}" class="btn edit-btn">Edit Pet</a>
            <form action="{{ route('pets.destroy', $pet->id) }}" method="POST" style="flex: 1;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn delete-btn"
                    onclick="return confirm('Are you sure you want to delete {{ $pet->pet_name }}? This action cannot be undone.')">Delete
                    Pet</button>
            </form>
        </div>
    </div>
</body>

</html>