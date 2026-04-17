$(document).ready(function () {
  
  // 1. Password Hide & Show Functionality
  const toggleBtn = document.getElementById("togglePassword");
  const pwd = document.getElementById("password");

  if (toggleBtn && pwd) {
    // Default setup: Password should be hidden, eye slash icon
    pwd.type = "password";
    const icon = toggleBtn.querySelector("i");
    if (icon) {
      icon.classList.remove("fa-eye");
      icon.classList.add("fa-eye-slash");
    }
    toggleBtn.setAttribute("aria-label", "Show password");

    toggleBtn.addEventListener("click", function () {
      const isHidden = pwd.type === "password";

      if (isHidden) {
        pwd.type = "text"; // Show password
        if (icon) {
          icon.classList.remove("fa-eye-slash");
          icon.classList.add("fa-eye");
        }
        toggleBtn.setAttribute("aria-label", "Hide password");
      } else {
        pwd.type = "password"; // Hide password
        if (icon) {
          icon.classList.remove("fa-eye");
          icon.classList.add("fa-eye-slash");
        }
        toggleBtn.setAttribute("aria-label", "Show password");
      }
    });
  }

  // 2. jQuery Form Validation Plugin Setup
  if ($('#loginForm').length) {
    $('#loginForm').validate({
      rules: {
        emp_code: {
          required: true,
        },
        password: {
          required: true,
        }
      },
      messages: {
        emp_code: {
          required: "Please enter your Employee Code.",
        },
        password: {
          required: "Please enter your Password.",
        }
      },
      errorElement: "label",
      errorPlacement: function (error, element) {
        // Place the error label cleanly right after the .input-group wrapper
        error.insertAfter(element.closest('.input-group'));
      },
      highlight: function (element, errorClass, validClass) {
        $(element).addClass('error').removeClass('valid');
      },
      unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('error').addClass('valid');
      }
    });
  }

});