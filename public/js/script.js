/* ============================================
   Tugon - Login & Register page scripts
   ============================================ */

// ---------- 1. Show / Hide password (login page) ----------
// We check "if it exists" first, because this button only exists
// on the login page, not the register page. Without this check,
// the script would throw an error on register.html.
const togglePasswordBtn = document.getElementById("togglePassword");

if (togglePasswordBtn) {
  togglePasswordBtn.addEventListener("click", function () {
    const passwordInput = document.getElementById("password");

    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      togglePasswordBtn.textContent = "Hide";
    } else {
      passwordInput.type = "password";
      togglePasswordBtn.textContent = "Show";
    }
  });
}


// ---------- 2. Password match check (register page) ----------
const registerForm = document.getElementById("registerForm");

if (registerForm) {
  registerForm.addEventListener("submit", function (event) {
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;
    const mismatchMsg = document.getElementById("passwordMismatchMsg");

    if (password !== confirmPassword) {
      // Stop the form from submitting to the server
      event.preventDefault();
      mismatchMsg.style.display = "block";
    } else {
      mismatchMsg.style.display = "none";
    }
  });
}


// ---------- 3. ID photo preview (register page) ----------
const idUploadInput = document.getElementById("idUpload");

if (idUploadInput) {
  idUploadInput.addEventListener("change", function () {
    const file = idUploadInput.files[0]; // the photo the user picked
    const previewImg = document.getElementById("idPreviewImg");
    const previewText = document.getElementById("idPreviewText");

    if (file) {
      // FileReader lets us show the image before it's even uploaded anywhere
      const reader = new FileReader();

      reader.onload = function (e) {
        previewImg.src = e.target.result;
        previewImg.style.display = "block";
        previewText.textContent = file.name; // show the file name instead
      };

      reader.readAsDataURL(file);
    }
  });
}
