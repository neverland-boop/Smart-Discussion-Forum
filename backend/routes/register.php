
@extends('layouts.app')
@section('content')
<div class="auth-wrapper">
    <div class="brand-banner">
        <h2>Smart Discussion Forum</h2><p>Share Learn Grow Together</p>
    </div>
  <div class="auth-card">

  <div class="form-container">
    <h2>Create an Account</h2>
    <p class="sub">Join the Smart Discussion Forum</p>

    @if ($errors->any())
      <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="/register">
      @csrf

      <div class="input-group">
      <label for="name">Full Name</label><br>
      <input type="text" id="name" name="name" id="name" placeholder="Full Name" required><br>
      </div>

      <div class="input-group">
      <label for="email">Email Address</label><br>
      <input type="email" id="email" name="email" placeholder="Email Address" required><br>
      </div>

      <div class="input-group">
      <label for="username">Username</label><br>
      <input type="text" id="username" name="username" placeholder="Username" required><br>
      </div>

      <div class="input-group">
      <label for="password">Enter Password</label><br>
      <input type="password" id="password" name="password" placeholder="Password" required><br>
      </div>

      <div>
      <label for="comfirm">Comfirm Password</label><br>
      <input type="password" id="comfirm" name="password_confirmation" placeholder="Confirm Password" required>
      </div>

      <div class="right-actions-wrapper">
      <div class="terms-checkbox">
        <input type="checkbox" id="terms" name="terms" required>
        <label for="terms">I agree to the platform rules and terms</label>
        </div>

      <button type="submit" class="btn-register">Register</button>
      </div>

    </form>
    <p class="switch-link">Already have an account? <a href="/login">Login here</a></p>
  </div>

  
  <div class="rules-panel">
    <h3>Platform Rules</h3>
    <ul>
      <li>Be respectful to all members.</li>
      <li>No spam or irrelevant content.</li>
      <li>Use the platform for academic discussions.</li>
      <li>Violations may lead to warnings or bans.</li>
    </ul>
  </div>
</div>
</div>
@endsection
