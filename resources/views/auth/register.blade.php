<x-layout>
    <style>
    /* Body styling */
    body,
    html {
        margin: 0;
        padding: 0;
        height: 100%;
        font-family: Arial, sans-serif;
        background-color: #f3f4f6;
    }

    /* Center the register card */
    .register-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        padding: 20px;
        /* for mobile */
    }

    /* Card styling */
    .register-card {
        background-color: #fff;
        padding: 40px 30px;
        border-radius: 20px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
        box-sizing: border-box;
    }

    .register-card h2 {
        text-align: center;
        margin-bottom: 24px;
        color: #1f2937;
    }

    /* Form styling */
    .register-card form {
        display: flex;
        flex-direction: column;
    }

    .register-card .form-group {
        margin-bottom: 16px;
    }

    .register-card label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }

    .register-card input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        font-size: 14px;
        outline: none;
        transition: 0.3s;
        box-sizing: border-box;
    }

    .register-card input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
    }

    /* Button styling */
    .register-card button {
        width: 100%;
        background-color: #3b82f6;
        color: white;
        font-weight: 600;
        padding: 12px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s;
        margin-top: 8px;
    }

    .register-card button:hover {
        background-color: #2563eb;
    }

    /* Error messages */
    .register-card ul {
        margin-top: 12px;
        color: #dc2626;
        /* red */
        padding-left: 20px;
    }

    .register-card ul li {
        margin-bottom: 4px;
    }
    </style>

    <div class="register-container">
        <div class="register-card">
            <h2>Register Your Account</h2>

            <form action="{{ route('register') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required value="{{ old('email') }}">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required>
                </div>

                <button type="submit">Register</button>

                @if ($errors->any())
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                @endif
            </form>
        </div>
    </div>
</x-layout>