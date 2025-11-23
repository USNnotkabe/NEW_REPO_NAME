<x-layout>
    <style>
    /* Body and page background */
    body,
    html {
        margin: 0;
        padding: 0;
        height: 100%;
        font-family: Arial, sans-serif;
        background-color: #f3f4f6;
    }

    /* Center the login card */
    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        padding: 20px;
        /* for mobile */
    }

    /* Card styling */
    .login-card {
        background-color: #fff;
        padding: 40px 30px;
        border-radius: 20px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
        box-sizing: border-box;
        /* ensure padding included in width */
    }

    .login-card h2 {
        text-align: center;
        margin-bottom: 24px;
        color: #1f2937;
    }

    /* Form styling */
    .login-card form {
        display: flex;
        flex-direction: column;
    }

    .login-card .form-group {
        margin-bottom: 16px;
    }

    .login-card label {
        display: block;
        /* ensures label takes full width */
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }

    .login-card input[type="email"],
    .login-card input[type="password"] {
        width: 100%;
        /* make inputs full width */
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        font-size: 14px;
        outline: none;
        transition: 0.3s;
        box-sizing: border-box;
        /* ensure padding included in width */
    }

    .login-card input[type="email"]:focus,
    .login-card input[type="password"]:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
    }

    /* Button styling */
    .login-card button {
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
    }

    .login-card button:hover {
        background-color: #2563eb;
    }

    /* Footer text */
    .login-card p {
        text-align: center;
        color: #6b7280;
        margin-top: 16px;
        font-size: 14px;
    }

    .login-card p a {
        color: #3b82f6;
        text-decoration: none;
    }

    .login-card p a:hover {
        text-decoration: underline;
    }
    </style>

    <div class="login-container">
        <div class="login-card">
            <h2>Login to Your Account</h2>

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <button type="submit">Login</button>
            </form>

            <p>
                Don't have an account?
                <a href="{{ route('register') }}">Register</a>
            </p>
        </div>
    </div>
</x-layout>