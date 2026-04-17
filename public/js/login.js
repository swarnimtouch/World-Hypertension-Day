(function () {
  const toggleBtn = document.getElementById("togglePassword");
  const pwd = document.getElementById("password");

  if (!toggleBtn || !pwd) return;

  pwd.type = pwd.type || "password";
  const icon = toggleBtn.querySelector("i");
  if (icon) {
    icon.classList.remove("fa-eye");
    icon.classList.add("fa-eye-slash");
  }
  toggleBtn.setAttribute("aria-label", "Show password");

  toggleBtn.addEventListener("click", function () {
    const isHidden = pwd.type === "password";

    if (isHidden) {
      pwd.type = "text";
      if (icon) {
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
      }
      toggleBtn.setAttribute("aria-label", "Hide password");
    } else {
      pwd.type = "password";
      if (icon) {
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
      }
      toggleBtn.setAttribute("aria-label", "Show password");
    }
  });
})();
