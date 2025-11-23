<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Pets</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        padding: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .header {
        background: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    h1 {
        color: #333;
        font-size: 2em;
    }

    .add-btn {
        background-color: #4CAF50;
        color: white;
        padding: 12px 24px;
        text-decoration: none;
        border-radius: 5px;
        display: inline-block;
        font-weight: bold;
        transition: background-color 0.3s;
    }

    .add-btn:hover {
        background-color: #45a049;
    }

    .success-message {
        background-color: #d4edda;
        color: #155724;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        border: 1px solid #c3e6cb;
    }

    .pet-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 25px;
    }

    .pet-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .pet-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .pet-card img {
        width: 100%;
        height: 220px;
        object-fit: cover;
    }

    .pet-info {
        padding: 20px;
    }

    .pet-card h3 {
        margin: 0 0 15px 0;
        color: #333;
        font-size: 1.4em;
    }

    .pet-card p {
        color: #666;
        margin: 8px 0;
        font-size: 0.95em;
    }

    .pet-card p strong {
        color: #333;
    }

    .status-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85em;
        font-weight: bold;
        margin-top: 10px;
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
        margin-top: 15px;
        display: flex;
        gap: 10px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }

    .actions a,
    .actions button {
        flex: 1;
        padding: 8px 0;
        text-decoration: none;
        border-radius: 5px;
        font-size: 14px;
        text-align: center;
        border: none;
        cursor: pointer;
        font-weight: bold;
        transition: all 0.3s;
    }

    .view-btn {
        background-color: #17a2b8;
        color: white;
    }

    .view-btn:hover {
        background-color: #138496;
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
    }

    .delete-btn:hover {
        background-color: #da190b;
    }

    .empty-state {
        background: white;
        padding: 60px;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .empty-state h2 {
        color: #666;
        margin-bottom: 20px;
    }

    .logout-btn {
        background-color: #dc3545;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        margin-left: 15px;
    }

    .logout-btn:hover {
        background-color: #c82333;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🐾 My Pets</h1>
            <div>
                <a href="{{ route('pets.create') }}" class="add-btn">+ Add New Pet</a>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>
        </div>

        @if(session('success'))
        <div class="success-message">
            ✓ {{ session('success') }}
        </div>
        @endif

        @if($pets->count() > 0)
        <div class="pet-grid">
            @foreach($pets as $pet)
            <div class="pet-card">
                @if($pet->image)
                <img src="{{ asset('storage/' . $pet->image) }}" alt="{{ $pet->pet_name }}">
                @else
                <img src="https://via.placeholder.com/280x220/667eea/ffffff?text=No+Image" alt="No image">
                @endif

                <div class="pet-info">
                    <h3>{{ $pet->pet_name }}</h3>
                    <p><strong>Category:</strong> {{ ucfirst($pet->category) }}</p>
                    <p><strong>Breed:</strong> {{ $pet->breed ?? 'N/A' }}</p>
                    <p><strong>Age:</strong> {{ $pet->age ?? 'N/A' }} years</p>
                    <p><strong>Gender:</strong> {{ $pet->gender ? ucfirst($pet->gender) : 'N/A' }}</p>
                    <p><strong>Price:</strong> ${{ number_format($pet->price, 2) }}</p>
                    <p><strong>Type:</strong> {{ ucfirst($pet->listing_type) }}</p>

                    <span class="status-badge status-{{ $pet->status }}">
                        {{ ucfirst($pet->status) }}
                    </span>

                    <div class="actions">
                        <a href="{{ route('pets.show', $pet->id) }}" class="view-btn">View</a>
                        <a href="{{ route('pets.edit', $pet->id) }}" class="edit-btn">Edit</a>
                        <form action="{{ route('pets.destroy', $pet->id) }}" method="POST" style="flex: 1;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-btn"
                                onclick="return confirm('Are you sure you want to delete {{ $pet->pet_name }}?')">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <h2>No pets found</h2>
            <p style="color: #888; margin-bottom: 20px;">Start by adding your first pet!</p>
            <a href="{{ route('pets.create') }}" class="add-btn">+ Add Your First Pet</a>
        </div>
        @endif
    </div>
</body>

</html>