<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PAWS</title>
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

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-card h3 {
        color: #666;
        font-size: 0.9em;
        margin-bottom: 10px;
        text-transform: uppercase;
    }

    .stat-card .number {
        font-size: 3em;
        font-weight: bold;
        color: #667eea;
    }

    .stat-card.users .number {
        color: #4CAF50;
    }

    .stat-card.pets .number {
        color: #FF9800;
    }

    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #c3e6cb;
    }

    .info-box {
        background: white;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        text-align: center;
        margin-top: 40px;
    }

    .info-box h3 {
        color: #667eea;
        margin-bottom: 15px;
        font-size: 1.5em;
    }

    .info-box p {
        color: #666;
        line-height: 1.6;
        margin-bottom: 10px;
    }

    .quick-links {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }

    .quick-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 12px;
        text-decoration: none;
        text-align: center;
        transition: transform 0.3s;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .quick-link:hover {
        transform: translateY(-5px);
    }

    .quick-link h4 {
        margin-bottom: 10px;
        font-size: 1.2em;
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

        <h2 style="color: #333; margin-bottom: 30px; font-size: 2em;">Dashboard Overview</h2>

        <div class="stats-grid">
            <div class="stat-card users">
                <h3>Total Users</h3>
                <div class="number">{{ $stats['total_users'] }}</div>
            </div>

            <div class="stat-card pets">
                <h3>Total Pets</h3>
                <div class="number">{{ $stats['total_pets'] }}</div>
            </div>
        </div>

        <div class="info-box">
            <h3>👨‍💼 Admin Role Update</h3>
            <p><strong>New System:</strong> Pet owners now manage adoption/purchase requests directly!</p>
            <p>As an admin, you can monitor users and pets, but owners approve their own requests.</p>
            <p>This creates a more efficient peer-to-peer marketplace.</p>
        </div>

        <h2 style="color: #333; margin: 40px 0 20px 0; font-size: 1.5em;">Quick Actions</h2>

        <div class="quick-links">
            <a href="{{ route('admin.users') }}" class="quick-link">
                <h4>👥 Manage Users</h4>
                <p>View and manage all users</p>
            </a>

            <a href="{{ route('admin.pets') }}" class="quick-link">
                <h4>🐾 Monitor Pets</h4>
                <p>View and manage all pet listings</p>
            </a>

            <a href="{{ route('petlisting.index') }}" class="quick-link">
                <h4>🏠 View Public Listings</h4>
                <p>See what users see</p>
            </a>
        </div>
    </div>
</body>

</html>
