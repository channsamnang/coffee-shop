<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="w            const formData = {
                csrf_token: document.getElementById('csrf_token').value,
                full_name: document.getElementById('full_name').value.trim(),
                username: document.getElementById('username').value.trim(),
                email: document.getElementById('email').value.trim(),
                phone: document.getElementById('phone').value.trim(),
                password: document.getElementById('password').valuevice-width, initial-scale=1.0">
    <title>Register - Coffee Shop</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>

<body>
    <div class="container" style="max-width: 500px; margin-top: 3rem;">
        <div class="card">
            <h1 class="card-header text-center">☕ Coffee Shop</h1>
            <h2 style="text-align: center; margin-bottom: 2rem; color: var(--text-light);">Create Account</h2>

            <form id="registerForm">
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
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-input" id="full_name" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-input" id="username" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-input" id="email" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="tel" class="form-input" id="phone">
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-input" id="password" required minlength="6">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>
            </form>

            <p style="text-align: center; margin-top: 1.5rem; color: var(--text-light);">
                Already have an account? <a href="/login.php" style="color: var(--primary); font-weight: 600;">Login</a>
            </p>

            <div id="message" style="margin-top: 1rem; padding: 0.75rem; border-radius: 0.5rem; display: none;"></div>
        </div>
    </div>

    <script>
    // Helper function to show messages
    function showMessage(message, type = 'success') {
        const messageDiv = document.getElementById('message');
        messageDiv.style.display = 'block';
        messageDiv.style.background = type === 'success' ? '#D1FAE5' : '#FEE2E2';
        messageDiv.style.color = type === 'success' ? '#065F46' : '#991B1B';
        messageDiv.textContent = message;
    }

    // Input validation functions
    const validators = {
        full_name: (value) => {
            if (value.length < 2) return "Full name must be at least 2 characters long";
            if (!/^[a-zA-Z\s]+$/.test(value)) return "Full name can only contain letters and spaces";
            return null;
        },
        username: (value) => {
            if (value.length < 3) return "Username must be at least 3 characters long";
            if (!/^[a-zA-Z0-9_]+$/.test(value)) return "Username can only contain letters, numbers, and underscores";
            return null;
        },
        email: (value) => {
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) return "Please enter a valid email address";
            return null;
        },
        phone: (value) => {
            if (value && !/^\+?[\d\s-]{10,}$/.test(value)) return "Please enter a valid phone number";
            return null;
        },
        password: (value) => {
            if (value.length < 8) return "Password must be at least 8 characters long";
            if (!/[A-Z]/.test(value)) return "Password must contain at least one uppercase letter";
            if (!/[a-z]/.test(value)) return "Password must contain at least one lowercase letter";
            if (!/[0-9]/.test(value)) return "Password must contain at least one number";
            return null;
        }
    };

    document.getElementById('registerForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = {
                csrf_token: document.getElementById('csrf_token').value,
                full_name: document.getElementById('full_name').value.trim(),
                username: document.getElementById('username').value.trim(),
                email: document.getElementById('email').value.trim(),
                phone: document.getElementById('phone').value.trim(),
                password: document.getElementById('password').value

        // Validate all fields
        for (const [field, value] of Object.entries(formData)) {
            const error = validators[field]?.(value);
            if (error) {
                showMessage(error, 'error');
                return;
            }
        }

        const messageDiv = document.getElementById('message');

        try {
            const response = await fetch('/api/auth/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (data.success) {
                showMessage('Registration successful! Redirecting to login...');
                setTimeout(() => {
                    window.location.href = '/login.php';
                }, 1500);
            } else {
                showMessage(data.message, 'error');
            }
        } catch (error) {
            showMessage('An error occurred. Please try again.', 'error');
        }
    });
    </script>
</body>

</html>