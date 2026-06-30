<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Login</title>
</head>
<body>
    <div style="max-width: 400px; margin: 50px auto;">
        <h2>Student Login</h2>

        @if (session('success'))
            <div style="color: green;">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div style="color: red;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('student.login.submit') }}" method="POST">
            @csrf

            <div>
                <label>Email Address:</label><br>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </div><br>

            <div>
                <label>Password:</label><br>
                <input type="password" name="password" required>
            </div><br>

            <div>
                <label>
                    <input type="checkbox" name="remember"> Remember Me
                </label>
            </div><br>

            <button type="submit">Login</button>
        </form>
        <p>New student? <a href="{{ route('student.register') }}">Register here</a></p>
    </div>
</body>
</html>