<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title> World Hypertension Day</title>

    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <style>
      .left-side-line {
  position: fixed;
  top: 0;
  left: 0;
  height: 100vh;       /* full screen height */
  width: 80px;         /* adjust to your line’s thickness */
  z-index: 1;          /* stay behind topbar and content */
}

.left-side-line img {
  height: 100%;
  width: 100%;
  object-fit: cover;   /* stretch nicely */
}
.right-side-line {
  position: fixed;
  top: 0;
  right: 0;
  height: 100vh;
  width: 80px;
  z-index: 1;
}

.right-side-line img {
  height: 100%;
  width: 100%;
  object-fit: cover;
}
      </style>

    <link rel="stylesheet" href="{{asset('css/login.css')}}" />
  </head>
  <body>
      <div class="left-side-line">
  <img src="{{ asset('images/Left-Side.png') }}" alt="Left Line" />
</div>
<div class="right-side-line">
  <img src="{{ asset('images/Right-Side.png') }}" alt="Right Line">
</div>
    <div class="login-container">
      <div class="logo">
        <img src="{{asset('images/logo.png')}}" alt="Logo (place logo.png in same folder)" />
      </div>
@if ($errors->any())
    <div class="alert alert-danger mb-3">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

      <h2>Login</h2>
      <form action="{{ route('login.submit') }}" method="POST">
        @csrf
        <div class="input-group">
          <i class="fa-solid fa-envelope left-icon" aria-hidden="true"></i>
         <input type="text" name="emp_code" placeholder="Enter Employee Code" autocomplete="username" required>
        </div>

        <div class="input-group">
          <i class="fa-solid fa-lock left-icon" aria-hidden="true"></i>

          <input
            type="password"
            name="password"
            placeholder="Password"
            autocomplete="current-password"
          />

          <button
            type="button"
            class="toggle-password"
            id="togglePassword"
            aria-label="Show password"
          >
            <i class="fa-solid fa-eye-slash" aria-hidden="true"></i>
          </button>
        </div>
        <button class="login-btn" type="submit">Login</button>
      </form>
      <div class="logo">
        <img src="{{asset('images/LIPITAS-LOGO.png')}}" alt="Logo (place logo.png in same folder)" style="margin-top:7px;"/>
      </div>
    </div>

    {{-- <script src="{{asset('js/login.js')}}"></script> --}}
  </body>
</html>
