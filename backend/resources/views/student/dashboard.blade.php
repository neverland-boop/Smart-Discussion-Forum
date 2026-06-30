<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
</head>
<body>
    <div style="max-width: 800px; margin: 50px auto;">
        <h1>Welcome to the Smart Discussion Forum, {{ Auth::user()->name }}!</h1>
        <p>You are successfully logged in as a <strong>{{ Auth::user()->role }}</strong>.</p>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" style="background-color: #f44336; color: white; padding: 10px; border: none; cursor: pointer;">
                Log Out
            </button>
        </form>
    </div>
</body>
</html>