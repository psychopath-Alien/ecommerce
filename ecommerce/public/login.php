<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow rounded" style="width: 100%; max-width: 400px;">
      <h4 class="mb-3 text-center">Login</h4>
      <form id="loginForm">
        <div class="mb-3">
          <label>Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
        <div class="text-center mt-3">
          <a href="register.php">Don't have an account?</a>
        </div>
        <div id="loginError" class="text-danger mt-2" style="display:none;"></div>
      </form>
    </div>
  </div>

  <script>
    document.getElementById('loginForm').onsubmit = async function(e) {
      e.preventDefault();
      const form = new FormData(this);
      const data = Object.fromEntries(form.entries());

      const response = await fetch('../api/auth.php?action=login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });

      const result = await response.json();

      if (response.ok) {
        // Store token in both localStorage and cookie
        localStorage.setItem('token', result.token);
        document.cookie = `token=${result.token}; path=/;`; // Set cookie readable by PHP
        window.location.href = '../user/dashboard.php';
      } else {
        document.getElementById('loginError').style.display = 'block';
        document.getElementById('loginError').textContent = result.message || 'Login failed';
      }
    };
  </script>
</body>
</html>
