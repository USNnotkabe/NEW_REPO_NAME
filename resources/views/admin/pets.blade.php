<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Pets - PAWS Admin</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f5f7fa;
    }

    .navbar {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .navbar h1 {
        font-size: 1.8em;
    }

    .nav-links {
        display: flex;
        gap: 20px;
        align-items: center;
    }

    .nav-links a {
        color: white;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 5px;
        transition: background 0.3s;
    }

    .nav-links a:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .logout-btn {
        background: #dc3545;
        border: none;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }

    .container {
        max-width: 1400px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .section {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .section h2 {
        color: #333;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 3px solid #667eea;
    }

    .info-banner {
        background: #e7f3ff;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 30px;
        border-left: 4px solid #2196F3;
    }

    .info-banner p {
        color: #0c5460;
        margin: 5px 0;
    }

    .pet-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 25px;
        margin-top: 20px;
    }

    .pet-card {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .pet-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .pet-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .pet-info {
        padding: 20px;
    }

    .pet-info h3 {
        margin-bottom: 10px;
        color: #333;
    }

    .pet-info p {
        color: #666;
        margin: 5px 0;
        font-size: 0.9em;
    }

    .badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85em;
        font-weight: bold;
        margin-top: 10px;
    }

    .badge-available {
        background: #d4edda;
        color: #155724;
    }

    .badge-adopted {
        background: #f8d7da;
        color: #721c24;
    }

    .owner-info {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        margin-top: 10px;
        font-size: 0.85em;
    }

    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #c3e6cb;
    }
    </style>
</head>

<body>
    <nav class="navbar">
        <h1>🐾 PAWS Admin Panel</h1>
        <div class="nav-links">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.users') }}">Users</a>
            <a href="{{ route('admin.pets') }}">Monitor Pets</a>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container">
        @if(session('success'))
        <div class="success-message">
            ✓ {{ session('success') }}
        </div>
        @endif

        <div class="section">
            <h2>Monitor All Pet Listings ({{ $pets->count() }} Total)</h2>

            <div class="info-banner">
                <p><strong>ℹ️ Admin Note:</strong> You are viewing all pet listings for monitoring purposes.</p>
                <p>Pet owners manage their own listings. Admins can only observe to ensure platform safety.</p>
            </div>

            <div class="pet-grid">
                @foreach($pets as $pet)
                <div class="pet-card">
                    @if($pet->image)
                    <img src="{{ asset('storage/' . $pet->image) }}" alt="{{ $pet->pet_name }}">
                    @else
                    <img src="https://via.placeholder.com/280x200/667eea/ffffff?text=No+Image" alt="No image">
                    @endif

                    <div class="pet-info">
                        <h3>{{ $pet->pet_name }}</h3>
                        <p><strong>Category:</strong> {{ ucfirst($pet->category) }}</p>
                        <p><strong>Breed:</strong> {{ $pet->breed ?? 'N/A' }}</p>
                        <p><strong>Age:</strong> {{ $pet->age ?? 'N/A' }} years</p>
                        <p><strong>Listing Type:</strong> {{ ucfirst($pet->listing_type) }}</p>
                        @if($pet->listing_type === 'sell')
                        <p><strong>Price:</strong> ${{ number_format($pet->price, 2) }}</p>
                        @endif

                        <span class="badge badge-{{ $pet->status }}">
                            {{ ucfirst($pet->status) }}
                        </span>

                        <div class="owner-info">
                            <strong>Owner:</strong> {{ $pet->user->name }}<br>
                            <strong>Email:</strong> {{ $pet->user->email }}<br>
                            <strong>Posted:</strong> {{ $pet->created_at->format('M d, Y') }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</body>

</html>