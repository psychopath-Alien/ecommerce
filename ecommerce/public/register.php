<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow rounded" style="width: 100%; max-width: 400px;">
      <h4 class="mb-3 text-center">Register</h4>
      <form id="registerForm">
        <div class="mb-3">
          <label>Name</label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Register</button>
        <div class="text-center mt-3">
          <a href="login.php">Already have an account?</a>
        </div>
        <div id="registerMessage" class="mt-2" style="display:none;"></div>
      </form>
    </div>
  </div>

  <script>
    document.getElementById('registerForm').onsubmit = async function(e) {
      e.preventDefault();
      const form = new FormData(this);
      const data = Object.fromEntries(form.entries());

      const response = await fetch('../api/auth.php?action=register', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });

      const result = await response.json();

      const message = document.getElementById('registerMessage');
      message.style.display = 'block';

      if (response.ok && result.success) {
        message.className = 'text-success mt-2';
        message.textContent = 'Registered successfully! Redirecting to login...';
        setTimeout(() => window.location.href = 'login.php', 2000);
      } else {
        message.className = 'text-danger mt-2';
        message.textContent = result.message || 'Registration failed';
      }
    };
  </script>
</body>
</html>
