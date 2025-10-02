<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Coffee Shop</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>

<body>
    <div class="container" style="max-width: 400px; margin-top: 5rem;">
        <div class="card">
            <h1 class="card-header text-center">☕ Coffee Shop</h1>
            <h2 style="text-align: center; margin-bottom: 2rem; color: var(--text-light);">Login</h2>

            <form id="loginForm">
                <?php
                require_once './config/database.php';
                require_once './config/auth.php';
                $database = new Database();
                $db = $database->getConnection();
                $auth = new Auth($db);
                $csrf_token = $auth->generateCSRFToken();
                ?>
                <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $csrf_token; ?>">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-input" id="email" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-input" id="password" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
            </form>

            <p style="text-align: center; margin-top: 1.5rem; color: var(--text-light);">
                Don't have an account? <a href="/register.php"
                    style="color: var(--primary); font-weight: 600;">Register</a>
            </p>

            <div id="message" style="margin-top: 1rem; padding: 0.75rem; border-radius: 0.5rem; display: none;"></div>
        </div>
    </div>

    <script>
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
        e.preventDefault();

            const formData = {
                csrf_token: document.getElementById('csrf_token').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value
            };
            const messageDiv = document.getElementById('message');        try {
            const response = await fetch('/api/auth/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (data.success) {
                messageDiv.style.display = 'block';
                messageDiv.style.background = '#D1FAE5';
                messageDiv.style.color = '#065F46';
                messageDiv.textContent = 'Login successful! Redirecting...';

                setTimeout(() => {
                    if (data.user.role === 'admin') {
                        window.location.href = '/admin/dashboard.php';
                    } else {
                        window.location.href = '/user/menu.php';
                    }
                }, 1000);
            } else {
                messageDiv.style.display = 'block';
                messageDiv.style.background = '#FEE2E2';
                messageDiv.style.color = '#991B1B';
                messageDiv.textContent = data.message;
            }
        } catch (error) {
            messageDiv.style.display = 'block';
            messageDiv.style.background = '#FEE2E2';
            messageDiv.style.color = '#991B1B';
            messageDiv.textContent = 'An error occurred. Please try again.';
        }
    });
    </script>
</body>

</html>