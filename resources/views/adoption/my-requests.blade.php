<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Adoption Requests</title>
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
        max-width: 1000px;
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
    }

    .back-btn {
        background: #667eea;
        color: white;
        padding: 12px 24px;
        text-decoration: none;
        border-radius: 8px;
        font-weight: bold;
        transition: background 0.3s;
    }

    .back-btn:hover {
        background: #5568d3;
    }

    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #c3e6cb;
    }

    .request-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .request-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 20px;
        gap: 20px;
    }

    .pet-info {
        display: flex;
        gap: 20px;
        flex: 1;
    }

    .pet-info img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 10px;
    }

    .pet-details h2 {
        color: #333;
        margin-bottom: 10px;
    }

    .pet-details p {
        color: #666;
        margin: 5px 0;
    }

    .badge {
        padding: 10px 20px;
        border-radius: 20px;
        font-size: 0.9em;
        font-weight: bold;
        white-space: nowrap;
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

    .message-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin: 15px 0;
    }

    .message-section h4 {
        color: #555;
        margin-bottom: 10px;
    }

    .owner-response {
        background: #fff3cd;
        padding: 15px;
        border-radius: 8px;
        margin-top: 15px;
        border-left: 4px solid #ffc107;
    }

    .owner-response.approved {
        background: #d4edda;
        border-left-color: #28a745;
    }

    .owner-response.rejected {
        background: #f8d7da;
        border-left-color: #dc3545;
    }

    .contact-box {
        background: #e7f3ff;
        padding: 20px;
        border-radius: 8px;
        margin-top: 15px;
        border-left: 4px solid #2196F3;
    }

    .contact-box h4 {
        color: #0c5460;
        margin-bottom: 15px;
    }

    .contact-box p {
        color: #0c5460;
        margin: 8px 0;
    }

    .contact-box strong {
        color: #004085;
    }

    .actions {
        margin-top: 15px;
    }

    .btn-cancel {
        background: #dc3545;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }

    .btn-cancel:hover {
        background: #c82333;
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

    .timeline {
        color: #999;
        font-size: 0.9em;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #dee2e6;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>📋 My Requests</h1>
            <a href="{{ route('petlisting.index') }}" class="back-btn">← Back to Pet Listings</a>
        </div>

        @if(session('success'))
        <div class="success-message">
            ✓ {{ session('success') }}
        </div>
        @endif

        @if($requests->count() > 0)
        @foreach($requests as $request)
        <div class="request-card">
            <div class="request-header">
                <div class="pet-info">
                    @if($request->pet->image)
                    <img src="{{ asset('storage/' . $request->pet->image) }}" alt="{{ $request->pet->pet_name }}">
                    @else
                    <img src="https://via.placeholder.com/120/667eea/ffffff?text=No+Image" alt="No image">
                    @endif

                    <div class="pet-details">
                        <h2>{{ $request->pet->pet_name }}</h2>
                        <p><strong>Category:</strong> {{ ucfirst($request->pet->category) }}</p>
                        <p><strong>Breed:</strong> {{ $request->pet->breed ?? 'Mixed' }}</p>
                        <p><strong>Type:</strong> {{ ucfirst($request->pet->listing_type) }}</p>
                        @if($request->pet->listing_type === 'sell')
                        <p><strong>Price:</strong> ${{ number_format($request->pet->price, 2) }}</p>
                        @endif
                        <p><strong>Owner:</strong> {{ $request->pet->user->name }}</p>
                    </div>
                </div>

                <span class="badge badge-{{ $request->status }}">
                    @if($request->status === 'pending')
                    ⏳ Awaiting Owner Review
                    @elseif($request->status === 'approved')
                    ✓ Approved by Owner
                    @else
                    ✗ Not Approved
                    @endif
                </span>
            </div>

            <div class="message-section">
                <h4>Your Message to Owner:</h4>
                <p>{{ $request->message }}</p>
            </div>

            @if($request->status === 'approved')
            <div class="owner-response approved">
                <h4>🎉 Great News! Your request has been approved!</h4>
                <p>The owner reviewed your request and approved it. You can now contact them to complete the
                    transaction.</p>
                @if($request->reviewed_at)
                <p style="margin-top: 10px;"><small>Approved on {{ $request->reviewed_at->format('F d, Y') }}</small>
                </p>
                @endif
            </div>

            <div class="contact-box">
                <h4>📞 Owner Contact Information</h4>
                <p><strong>Owner Name:</strong> {{ $request->pet->user->name }}</p>
                <p><strong>Email:</strong> {{ $request->pet->user->email }}</p>
                <p style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #bee5eb;">
                    <strong>Next Steps:</strong><br>
                    • Contact the owner via email to arrange meeting<br>
                    • Discuss payment and pickup/delivery details<br>
                    • Complete the transaction directly with the owner
                </p>
            </div>
            @elseif($request->status === 'rejected')
            <div class="owner-response rejected">
                <h4>❌ Request Not Approved</h4>
                <p>Unfortunately, the pet owner did not approve your request.</p>
                @if($request->owner_notes)
                <p style="margin-top: 10px;"><strong>Owner's reason:</strong> {{ $request->owner_notes }}</p>
                @endif
                @if($request->reviewed_at)
                <p style="margin-top: 10px;"><small>Reviewed on {{ $request->reviewed_at->format('F d, Y') }}</small>
                </p>
                @endif
            </div>
            @else
            <div class="owner-response">
                <p>⏳ Your request is being reviewed by the pet owner. You'll be notified once they make a decision.</p>
                <p style="margin-top: 10px;"><small>The owner will receive your contact information if they approve your
                        request.</small></p>
            </div>
            @endif

            @if($request->status === 'pending')
            <div class="actions">
                <form action="{{ route('adoption.cancel', $request->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-cancel"
                        onclick="return confirm('Are you sure you want to cancel this request?')">
                        Cancel Request
                    </button>
                </form>
            </div>
            @endif

            <div class="timeline">
                Requested on {{ $request->created_at->format('F d, Y \a\t h:i A') }}
            </div>
        </div>
        @endforeach
        @else
        <div class="empty-state">
            <h2>No Requests Yet</h2>
            <p>You haven't submitted any requests to adopt or buy pets. Browse available pets and submit a request!</p>
            <a href="{{ route('petlisting.index') }}" class="back-btn">Browse Pets</a>
        </div>
        @endif
    </div>
</body>

</html>