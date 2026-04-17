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
    
    <link rel="stylesheet" href="{{asset('css/login.css')}}" />
  </head>
  <body>
      
    <div class="login-container">
      <div class="top-logo">
        <img src="{{asset('images/hypertension day logo.jpg')}}" alt="Hypertension Day Logo" />
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
      <form id="loginForm" action="{{ route('login.submit') }}" method="POST">
        @csrf
        <div class="input-group">
          <i class="fa-solid fa-envelope left-icon" aria-hidden="true"></i>
          <input type="text" name="emp_code" placeholder="Enter Employee Code" autocomplete="username">
        </div>

        <div class="input-group">
          <i class="fa-solid fa-lock left-icon" aria-hidden="true"></i>

          <input
            type="password"
            name="password"
            id="password"
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

      <div class="bottom-logo">
        <img src="{{asset('images/sartel.jpg')}}" alt="Sartel Logo" />
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="{{asset('js/login.js')}}"></script>
  </body>
</html>