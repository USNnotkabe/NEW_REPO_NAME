<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Pet Requests - PAWS</title>
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

    .info-banner {
        background: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-left: 4px solid #667eea;
    }

    .info-banner h3 {
        color: #667eea;
        margin-bottom: 10px;
    }

    .info-banner p {
        color: #666;
        line-height: 1.6;
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

    .requester-info {
        display: flex;
        gap: 20px;
        flex: 1;
    }

    .requester-avatar {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2em;
        color: white;
        font-weight: bold;
    }

    .requester-details h2 {
        color: #333;
        margin-bottom: 10px;
    }

    .requester-details p {
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

    .pet-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin: 15px 0;
    }

    .pet-section h4 {
        color: #555;
        margin-bottom: 10px;
    }

    .pet-details {
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .pet-details img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
    }

    .message-section {
        background: #fff3cd;
        padding: 15px;
        border-radius: 8px;
        margin: 15px 0;
        border-left: 4px solid #ffc107;
    }

    .message-section h4 {
        color: #856404;
        margin-bottom: 10px;
    }

    .message-section p {
        color: #856404;
        line-height: 1.6;
    }

    .actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
        transition: all 0.3s;
        font-size: 1em;
    }

    .btn-approve {
        background: #28a745;
        color: white;
        flex: 1;
    }

    .btn-approve:hover {
        background: #218838;
    }

    .btn-reject {
        background: #dc3545;
        color: white;
        flex: 1;
    }

    .btn-reject:hover {
        background: #c82333;
    }

    .btn-cancel {
        background: #6c757d;
        color: white;
    }

    .btn-cancel:hover {
        background: #5a6268;
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
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 10px;
        resize: vertical;
        min-height: 100px;
        font-family: Arial, sans-serif;
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

    .reviewed-info {
        background: #e7f3ff;
        padding: 15px;
        border-radius: 8px;
        margin-top: 15px;
    }

    .reviewed-info.approved {
        background: #d4edda;
    }

    .reviewed-info.rejected {
        background: #f8d7da;
    }

    .contact-box {
        background: #d4edda;
        padding: 15px;
        border-radius: 8px;
        margin-top: 15px;
        border-left: 4px solid #28a745;
    }

    .contact-box h4 {
        color: #155724;
        margin-bottom: 10px;
    }

    .contact-box p {
        color: #155724;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>
                <span>📬</span>
                <span>Requests for My Pets</span>
            </h1>
            <a href="{{ route('petlisting.index') }}" class="back-btn">← Back to Listings</a>
        </div>

        @if(session('success'))
        <div class="success-message">
            ✓ {{ session('success') }}
        </div>
        @endif

        <div class="info-banner">
            <h3>💡 How This Works</h3>
            <p><strong>You're in control!</strong> These are requests from users who want to adopt or buy your pets.</p>
            <p>• Review each request carefully</p>
            <p>• Approve to share contact information with the buyer/adopter</p>
            <p>• Reject if you don't think it's a good match</p>
            <p>• Once approved, both parties can contact each other to complete the transaction</p>
        </div>

        @if($requests->count() > 0)
        @foreach($requests as $request)
        <div class="request-card">
            <div class="request-header">
                <div class="requester-info">
                    <div class="requester-avatar">
                        {{ strtoupper(substr($request->user->name, 0, 1)) }}
                    </div>

                    <div class="requester-details">
                        <h2>{{ $request->user->name }}</h2>
                        <p><strong>Email:</strong> {{ $request->user->email }}</p>
                        <p><strong>Requested:</strong> {{ $request->created_at->format('F d, Y \a\t h:i A') }}</p>
                    </div>
                </div>

                <span class="badge badge-{{ $request->status }}">
                    @if($request->status === 'pending')
                    ⏳ Pending Your Review
                    @elseif($request->status === 'approved')
                    ✓ Approved
                    @else
                    ✗ Rejected
                    @endif
                </span>
            </div>

            <div class="pet-section">
                <h4>Pet Requested:</h4>
                <div class="pet-details">
                    @if($request->pet->image)
                    <img src="{{ asset('storage/' . $request->pet->image) }}" alt="{{ $request->pet->pet_name }}">
                    @else
                    <img src="https://via.placeholder.com/100/667eea/ffffff?text=No+Image" alt="No image">
                    @endif

                    <div>
                        <h3 style="color: #333; margin-bottom: 5px;">{{ $request->pet->pet_name }}</h3>
                        <p style="color: #666;"><strong>Type:</strong> {{ ucfirst($request->pet->listing_type) }}</p>
                        @if($request->pet->listing_type === 'sell')
                        <p style="color: #666;"><strong>Price:</strong> ${{ number_format($request->pet->price, 2) }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="message-section">
                <h4>Their Message:</h4>
                <p>{{ $request->message }}</p>
            </div>

            @if($request->status === 'pending')
            <div class="actions">
                <form action="{{ route('owner.approve-request', $request->id) }}" method="POST" style="flex: 1;">
                    @csrf
                    <button type="submit" class="btn btn-approve"
                        onclick="return confirm('Approve this request? Your contact info will be shared with {{ $request->user->name }}.')">
                        ✓ Approve & Share Contact
                    </button>
                </form>

                <button type="button" class="btn btn-reject" onclick="toggleRejectForm({{ $request->id }})"
                    style="flex: 1;">
                    ✗ Reject Request
                </button>
            </div>

            <div id="reject-form-{{ $request->id }}" class="reject-form">
                <form action="{{ route('owner.reject-request', $request->id) }}" method="POST">
                    @csrf
                    <label><strong>Reason for rejection (will be sent to requester):</strong></label>
                    <textarea name="owner_notes" required
                        placeholder="Please explain why you're rejecting this request..."></textarea>
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-reject">Submit Rejection</button>
                        <button type="button" class="btn btn-cancel"
                            onclick="toggleRejectForm({{ $request->id }})">Cancel</button>
                    </div>
                </form>
            </div>
            @else
            <div class="reviewed-info {{ $request->status }}">
                @if($request->status === 'approved')
                <h4 style="color: #155724;">✓ You approved this request</h4>
                <p style="color: #155724;"><strong>Approved on:</strong> {{ $request->reviewed_at->format('F d, Y') }}
                </p>

                <div class="contact-box">
                    <h4>📞 Contact Information Shared</h4>
                    <p><strong>Adopter/Buyer:</strong> {{ $request->user->name }}</p>
                    <p><strong>Email:</strong> {{ $request->user->email }}</p>
                    <p style="margin-top: 10px;"><em>Please contact them to arrange the transaction!</em></p>
                </div>
                @else
                <h4 style="color: #721c24;">✗ You rejected this request</h4>
                <p style="color: #721c24;"><strong>Rejected on:</strong> {{ $request->reviewed_at->format('F d, Y') }}
                </p>
                @if($request->owner_notes)
                <p style="color: #721c24; margin-top: 10px;"><strong>Your reason:</strong> {{ $request->owner_notes }}
                </p>
                @endif
                @endif
            </div>
            @endif
        </div>
        @endforeach
        @else
        <div class="empty-state">
            <h2>No Requests Yet</h2>
            <p>You haven't received any requests for your pets yet. When users request to adopt or buy your pets,
                they'll appear here!</p>
            <a href="{{ route('pets.index') }}" class="back-btn">View My Pets</a>
        </div>
        @endif
    </div>

    <script>
    function toggleRejectForm(requestId) {
        const form = document.getElementById('reject-form-' + requestId);
        form.classList.toggle('show');
    }
    </script>
</body>

</html>
