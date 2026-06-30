
@extends('layouts.app')
@section('content')
<div class="onboard-wrapper">
  <h2>Community Guidelines</h2>
  <p>Before accessing the Smart Discussion Forum, you must read and accept the platform rules.</p>

  <div class="rules-scroll-box">
    <h4>1. Participation Policy</h4>
    <p>Members must post at least once per configured inactivity period. Two missed periods
       result in a temporary blacklist.</p>
    <h4>2. Content Standards</h4>
    <p>Posts must be relevant to the academic topic. Flooding, spam, and off-topic content
       are subject to automatic flagging by the platform blacklist engine.</p>
    <h4>3. Quiz Integrity</h4>
    <p>During a scheduled quiz the application enters kiosk mode. Attempting to Alt+Tab or
       open other windows is a violation and may trigger automatic submission.</p>
    <h4>4. Respectful Communication</h4>
    <p>Harassment, discrimination, and offensive language are prohibited and will result in
       immediate administrator review.</p>
  </div>

  <div class="onboard-actions">
    <form method="POST" action="/onboard">
      @csrf
      <input type="hidden" name="decision" value="AGREE">
      <button type="submit" class="btn-primary">✔ I Agree – Activate My Account</button>
    </form>
    <form method="POST" action="/onboard">
      @csrf
      <input type="hidden" name="decision" value="DECLINE">
      <button type="submit" class="btn-secondary">Decline</button>
    </form>
  </div>
</div>
@endsection
