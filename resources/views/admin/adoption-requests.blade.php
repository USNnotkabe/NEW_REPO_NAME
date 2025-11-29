<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adoption Requests - PAWS Admin</title>
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

    .request-card {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
    }

    .request-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 20px;
    }

    .request-info h3 {
        color: #333;
        margin-bottom: 10px;
    }

    .request-info p {
        color: #666;
        margin: 5px 0;
    }

    .badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.9em;
        font-weight: bold;
    }

    .badge-pending {
        background: #fff3cd;
        color: #856404;
    }

    .badge-approved {
        background: #d4edda;
        color: #155724;
    }

    .badge-rejected {
        background: #f8d7da;
        color: #721c24;
    }

    .message-box {
        background: white;
        padding: 15px;
        border-radius: 8px;
        margin: 15px 0;
        border-left: 4px solid #667eea;
    }

    .actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        transition: all 0.3s;
    }

    .btn-approve {
        background: #28a745;
        color: white;
    }

    .btn-approve:hover {
        background: #218838;
    }

    .btn-reject {
        background: #dc3545;
        color: white;
    }

    .btn-reject:hover {
        background: #c82333;
    }

    .reject-form {
        margin-top: 15px;
        padding: 15px;
        background: #fff3cd;
        border-radius: 8px;
        display: none;
    }

    .reject-form.show {
        display: block;
    }

    .reject-form textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 10px;
        resize: vertical;
        min-height: 80px;
    }

    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #c3e6cb;
    }

    .reviewed-info {
        background: #e7f3ff;
        padding: 15px;
        border-radius: 8px;
        margin-top: 15px;
    }
    </style>
</head>

<body>
    <nav class="navbar">
        <h1>🐾 PAWS Admin Panel</h1>
        <div class="nav-links">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.users') }}">Users</a>
            <a href="{{ route('admin.pets') }}">Pets</a>
            <a href="{{ route('admin.adoption-requests') }}">Adoption Requests</a>
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
            <h2>Adoption Requests ({{ $requests->count() }} Total)</h2>

            @foreach($requests as $request)
            <div class="request-card">
                <div class="request-header">
                    <div class="request-info">
                        <h3>{{ $request->pet->pet_name }} - {{ ucfirst($request->pet->category) }}</h3>
                        <p><strong>Requested by:</strong> {{ $request->user->name }} ({{ $request->user->email }})</p>
                        <p><strong>Request Date:</strong> {{ $request->created_at->format('F d, Y \a\t h:i A') }}</p>
                        <p><strong>Pet Owner:</strong> {{ $request->pet->user->name }}</p>
                    </div>
                    <span class="badge badge-{{ $request->status }}">
                        {{ ucfirst($request->status) }}
                    </span>
                </div>

                <div class="message-box">
                    <strong>User Message:</strong>
                    <p style="margin-top: 10px;">{{ $request->message }}</p>
                </div>


                <div class="reviewed-info">
                    <p><strong>Reviewed by:</strong> {{ $request->reviewer ? $request->reviewer->name : 'N/A' }}</p>
                    <p><strong>Review Date:</strong>
                        {{ $request->reviewed_at ? $request->reviewed_at->format('F d, Y \a\t h:i A') : 'N/A' }}</p>
                    @if($request->admin_notes)
                    <p><strong>Admin Notes:</strong> {{ $request->admin_notes }}</p>
                    @endif
                </div>
                @endif
            </div>
            @endforeach

            @if($requests->count() === 0)
            <div style="text-align: center; padding: 60px; color: #999;">
                <h3>No adoption requests yet</h3>
                <p>Adoption requests will appear here when users request to adopt pets.</p>
            </div>
            @endif
        </div>
    </div>


</body>

</html>
