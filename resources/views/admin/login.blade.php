<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        World Heart Day |
        {{
            collect(explode('.', request()->route()->getName()))
                ->reject(fn($part) => $part === 'admin')
                ->map(fn($part) => ucfirst($part))
                ->implode(' ')
        }}
    </title>

    <!-- Bootstrap 5, FontAwesome & Google Fonts -->
    <!-- Bootstrap 5, FontAwesome & Custom CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Admin CSS link -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 m-0 px-3">

    <div class="card login-card">
        <div class="card-header bg-transparent px-4 pt-4 pb-0 text-center">
            <!-- Logo Section added as per requirement -->
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo mb-2" onerror="this.style.display='none'">
            <h2 class="login-title">ADMIN LOGIN</h2>
        </div>

        <div class="card-body p-4">

            <!-- Dynamic Error Handling from original code -->
            @if ($errors->any())
                <div class="alert alert-danger py-2 px-3 text-center" style="font-size: 14px; border-radius: 10px; font-weight: 600;" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Dynamic Success Handling from original code -->
            @if (session('success'))
                <div class="alert alert-success py-2 px-3 text-center" style="font-size: 14px; border-radius: 10px; font-weight: 600;" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <form id="adminLoginForm" method="POST" action="{{ route('admin.login.submit') }}" novalidate>
                @csrf

                <!-- Username Field -->
                <div class="mb-4">
                    <label class="form-label" for="username">Username</label>
                    <div class="icon-input-wrapper">
                        <!-- Changed icon to user instead of envelope -->
                        <i class="fa-solid fa-user left-icon"></i>
                        <input type="text" name="username" id="username" class="form-control" placeholder="Enter Username" value="{{ old('username') }}" autocomplete="username">
                    </div>
                </div>

                <!-- Password Field -->
                <div class="mb-4">
                    <label class="form-label" for="password">Password</label>
                    <div class="icon-input-wrapper">
                        <i class="fa-solid fa-lock left-icon"></i>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Enter Password" autocomplete="current-password">
                        <i class="fa-solid fa-eye-slash toggle-password" title="Show/Hide Password"></i>
                    </div>
                </div>

                <button type="submit" class="btn btn-submit w-100 mt-2">Login</button>
            </form>
        </div>
    </div>

    <!-- Scripts for jQuery, Bootstrap, and Validation -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function () {

            // Password Show/Hide Toggle
            $(".toggle-password").click(function() {
                var input = $("#password");
                
                // Agar password hidden hai, toh usko show karna hai aur eye open karna hai
                if (input.attr("type") === "password") {
                    input.attr("type", "text"); // Password show ho jayega
                    $(this).removeClass("fa-eye-slash").addClass("fa-eye"); // Eye icon open ho jayega
                } 
                // Agar password dikh raha hai, toh usko hide karna hai aur eye close karna hai
                else {
                    input.attr("type", "password"); // Password hide ho jayega (dots me)
                    $(this).removeClass("fa-eye").addClass("fa-eye-slash"); // Eye icon par slash aa jayega
                }
            });

            // jQuery Form Validation
            $("#adminLoginForm").validate({
                errorElement: 'label',
                errorClass: 'error',
                highlight: function(element) {
                    $(element).addClass('error');
                },
                unhighlight: function(element) {
                    $(element).removeClass('error');
                },
                rules: {
                    username: {
                        required: true
                    },
                    password: {
                        required: true,
                    }
                },
                messages: {
                    username: {
                        required: "Please enter your username."
                    },
                    password: {
                        required: "Please enter your password."
                    }
                },
                errorPlacement: function(error, element) {
                    // Place error message below the input wrapper beautifully
                    error.insertAfter(element.parent(".icon-input-wrapper"));
                },
                submitHandler: function(form) {
                    $('.btn-submit').prop('disabled', true).text('Logging in...');
                    form.submit();
                }
            });
        });
    </script>

</body>
</html>