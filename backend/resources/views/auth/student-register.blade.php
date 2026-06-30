<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Registration</title>
    </head>
<body>
    <div style="max-width: 400px; margin: 50px auto;">
        <h2>Student Registration</h2>

        @if ($errors->any())
            <div style="color: red;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('student.register.submit') }}" method="POST">
            @csrf 

            <div>
                <label>Full Name:</label><br>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div><br>

            <div>
                <label>Email Address:</label><br>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </div><br>

            <div>
                <label>Password:</label><br>
                <input type="password" name="password" required>
            </div><br>

            <div>
                <label>Confirm Password:</label><br>
                <input type="password" name="password_confirmation" required>
            </div><br>

            <button type="submit">Register as Student</button>
        </form>
        <p>Already have an account? <a href="{{ route('student.login') }}">Login here</a></p>
    </div>
</body>
</html>