@extends('layouts.app')
@section('content')
<div class="auth-wrapper">
  <div class="auth-card">
    <h2>Welcome Back!</h2>
    <p class="sub">Login to continue to your account</p>

    @if (session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
      <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="/login">
      @csrf
      <label for="email">Email</label><br>
      <input type="email" id="email" name="email" placeholder="Email" required><br>

      <label for="password">Password</label><br>
      <input type="password" id="password" name="password" placeholder="Password" required><br>

      
      <input type="checkbox" id="forgot" name="forgot" required>
      <label for="forgot">Remember me</label><br>
      

      <button type="submit" class="btn-primary">Login →</button>
    </form>
    <p class="switch-link">Don't have an account? <a href="/register">Register here</a></p>
  </div>
</div>
@endsection
