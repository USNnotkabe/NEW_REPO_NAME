<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Adoption History</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 40px 20px;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .header {
        background: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header h1 {
        color: #333;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .nav-links {
        display: flex;
        gap: 15px;
    }

    .btn {
        padding: 12px 24px;
        text-decoration: none;
        border-radius: 8px;
        font-weight: bold;
        transition: all 0.3s;
    }

    .btn-primary {
        background: #667eea;
        color: white;
    }

    .btn-primary:hover {
        background: #5568d3;
    }

    .stats-card {
        background: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .stats-card h2 {
        color: #667eea;
        font-size: 3em;
        margin-bottom: 10px;
    }

    .stats-card p {
        color: #666;
        font-size: 1.2em;
    }

    .timeline {
        position: relative;
        padding-left: 50px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #667eea;
    }

    .history-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .history-card::before {
        content: '🎉';
        position: absolute;
        left: -35px;
        top: 25px;
        width: 30px;
        height: 30px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2em;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        z-index: 1;
    }

    .history-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 20px;
        gap: 20px;
    }

    .pet-info-history {
        display: flex;
        gap: 20px;
        flex: 1;
    }

    .pet-info-history img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 12px;
    }

    .pet-details h2 {
        color: #333;
        margin-bottom: 10px;
        font-size: 1.8em;
    }

    .pet-details p {
        color: #666;
        margin: 5px 0;
    }

    .adoption-date {
        background: #667eea;
        color: white;
        padding: 15px 25px;
        border-radius: 10px;
        text-align: center;
    }

    .adoption-date .day {
        font-size: 2em;
        font-weight: bold;
        display: block;
    }

    .adoption-date .month {
        font-size: 1em;
        display: block;
    }

    .info-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-top: 15px;
    }

    .info-section h4 {
        color: #555;
        margin-bottom: 10px;
    }

    .info-section p {
        color: #666;
        line-height: 1.6;
    }

    .empty-state {
        background: white;
        padding: 80px 40px;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .empty-state h2 {
        color: #666;
        margin-bottom: 20px;
    }

    .empty-state p {
        color: #999;
        margin-bottom: 30px;
    }

    .badge-success {
        background: #28a745;
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.9em;
        font-weight: bold;
        display: inline-block;
        margin-top: 10px;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>
                <span>📚</span>
                <span>My Adoption History</span>
            </h1>
            <div class="nav-links">
                <a href="{{ route('adoption.my-requests') }}" class="btn btn-primary">My Requests</a>
                <a href="{{ route('petlisting.index') }}" class="btn btn-primary">Browse Pets</a>
            </div>
        </div>

        @if($history->count() > 0)
        <div class="stats-card">
            <h2>{{ $history->count() }}</h2>
            <p>Total Successful Adoptions</p>
        </div>

        <div class="timeline">
            @foreach($history as $record)
            <div class="history-card">
                <div class="history-header">
                    <div class="pet-info-history">
                        @if($record->pet->image)
                        <img src="{{ asset('storage/' . $record->pet->image) }}" alt="{{ $record->pet->pet_name }}">
                        @else
                        <img src="https://via.placeholder.com/150/667eea/ffffff?text=No+Image" alt="No image">
                        @endif

                        <div class="pet-details">
                            <h2>{{ $record->pet->pet_name }}</h2>
                            <p><strong>Category:</strong> {{ ucfirst($record->pet->category) }}</p>
                            <p><strong>Breed:</strong> {{ $record->pet->breed ?? 'Mixed' }}</p>
                            <p><strong>Age at Adoption:</strong> {{ $record->pet->age ?? 'Unknown' }} years</p>
                            <p><strong>Gender:</strong>
                                {{ $record->pet->gender ? ucfirst($record->pet->gender) : 'N/A' }}</p>
                            <span class="badge-success">✓ Successfully Adopted</span>
                        </div>
                    </div>

                    <div class="adoption-date">
                        <span class="day">{{ $record->adoption_date->format('d') }}</span>
                        <span class="month">{{ $record->adoption_date->format('M Y') }}</span>
                    </div>
                </div>

                @if($record->adoptionRequest)
                <div class="info-section">
                    <h4>Your Original Request:</h4>
                    <p>{{ $record->adoptionRequest->message }}</p>
                </div>
                @endif

                @if($record->notes)
                <div class="info-section" style="margin-top: 10px;">
                    <h4>Notes:</h4>
                    <p>{{ $record->notes }}</p>
                </div>
                @endif

                <p style="color: #999; margin-top: 15px; font-size: 0.9em;">
                    <strong>Adoption completed:</strong> {{ $record->adoption_date->diffForHumans() }}
                </p>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <h2>No Adoption History Yet</h2>
            <p>You haven't successfully adopted any pets yet. Once your adoption requests are approved, they'll appear
                here!</p>
            <a href="{{ route('petlisting.index') }}" class="btn btn-primary">Start Browsing Pets</a>
        </div>
        @endif
    </div>
</body>

</html>
