<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Pets for Adoption</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
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
        font-size: 2em;
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
        font-weight: bold;
    }

    .nav-links a:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .container {
        max-width: 1400px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .header-section {
        text-align: center;
        margin-bottom: 40px;
    }

    .header-section h2 {
        color: #333;
        font-size: 2.5em;
        margin-bottom: 10px;
    }

    .header-section p {
        color: #666;
        font-size: 1.2em;
    }

    .cta-box {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 40px;
        text-align: center;
        color: white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .cta-box h3 {
        margin-bottom: 15px;
        font-size: 1.8em;
    }

    .cta-box p {
        margin-bottom: 20px;
        font-size: 1.1em;
    }

    .cta-box a {
        background: white;
        color: #28a745;
        padding: 15px 40px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: bold;
        display: inline-block;
        transition: transform 0.3s;
    }

    .cta-box a:hover {
        transform: scale(1.05);
    }

    .pet-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
    }

    .pet-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .pet-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .pet-card img {
        width: 100%;
        height: 250px;
        object-fit: cover;
    }

    .pet-info {
        padding: 20px;
    }

    .pet-info h3 {
        color: #333;
        margin-bottom: 15px;
        font-size: 1.5em;
    }

    .pet-info p {
        color: #666;
        margin: 8px 0;
    }

    .badge {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.85em;
        font-weight: bold;
        margin-top: 10px;
    }

    .badge-available {
        background: #d4edda;
        color: #155724;
    }

    .btn-adopt {
        background: #28a745;
        color: white;
        padding: 12px 0;
        text-align: center;
        text-decoration: none;
        display: block;
        border-radius: 8px;
        font-weight: bold;
        margin-top: 15px;
        transition: background 0.3s;
    }

    .btn-adopt:hover {
        background: #218838;
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .empty-state h3 {
        color: #666;
        margin-bottom: 20px;
    }
    </style>
</head>

<body>
    <nav class="navbar">
        <h1>🐾 PAWS Pet Adoption</h1>
        <div class="nav-links">
            @auth
            <a href="{{ route('adoption.history') }}">📚 My History</a>
            <a href="{{ route('adoption.my-requests') }}">📋 My Requests</a>
            <a href="{{ route('owner.requests') }}" style="background: #FF9800; border-radius: 8px;">📬 Pet Requests</a>
            <a href="{{ route('pets.index') }}">🏠 My Pets</a>
            <a href="{{ route('pets.create') }}" style="background: #28a745; border-radius: 8px;">➕ Post a Pet</a>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit"
                    style="background: #dc3545; border: none; color: white; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold;">
                    Logout
                </button>
            </form>
            @else
            <a href="{{ route('show.login') }}">Login</a>
            <a href="{{ route('show.register') }}">Register</a>
            @endauth
        </div>
    </nav>

    <div class="container">
        <div class="header-section">
            <h2>Find Your Perfect Companion</h2>
            <p>Browse available pets and give them a loving home</p>
        </div>

        @auth
        <div class="cta-box">
            <h3>💡 Have a pet to rehome?</h3>
            <p>Post your pet for adoption and help them find a loving home!</p>
            <a href="{{ route('pets.create') }}">
                ➕ Post a Pet for Adoption
            </a>
        </div>
        @endauth

        @if($pets->count() > 0)
        <div class="pet-grid">
            @foreach($pets as $pet)
            <div class="pet-card">
                @if($pet->image)
             <img src="{{ $pet->image }}" alt="{{ $pet->pet_name }}">
                @else
                <img src="https://via.placeholder.com/300x250/667eea/ffffff?text={{ $pet->pet_name }}" alt="No image">
                @endif

                <div class="pet-info">
                    <h3>{{ $pet->pet_name }}</h3>
                    <p><strong>Category:</strong> {{ ucfirst($pet->category) }}</p>
                    <p><strong>Breed:</strong> {{ $pet->breed ?? 'Mixed' }}</p>
                    <p><strong>Age:</strong> {{ $pet->age ?? 'Unknown' }} years</p>
                    <p><strong>Gender:</strong> {{ $pet->gender ? ucfirst($pet->gender) : 'N/A' }}</p>

                    <span class="badge badge-available">Available for Adoption</span>

                    @auth
                    <a href="{{ route('adoption.create', $pet->id) }}" class="btn-adopt">
                        🏠 Request to Adopt
                    </a>
                    @else
                    <a href="{{ route('show.login') }}" class="btn-adopt">
                        Login to Adopt
                    </a>
                    @endauth
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <h3>No pets available for adoption at the moment</h3>
            <p>Check back soon for new pets!</p>
        </div>
        @endif
    </div>
</body>

</html>