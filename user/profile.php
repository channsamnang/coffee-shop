<?php
require_once '../config/auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Coffee Shop</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>

<body>
    <header class="header">
        <div class="container header-content">
            <a href="/user/menu.php" class="logo">☕ Coffee Shop</a>
            <nav class="nav">
                <a href="/user/menu.php">Menu</a>
                <a href="/user/orders.php">My Orders</a>
                <a href="/user/profile.php">Profile</a>
                <a href="/api/auth/logout.php" class="btn btn-danger btn-sm">Logout</a>
            </nav>
        </div>
    </header>

    <main class="container" style="padding: 2rem 1.5rem; max-width: 600px;">
        <h1 style="margin-bottom: 2rem; color: var(--primary);">My Profile</h1>

        <div class="card">
            <h2 class="card-header">Account Information</h2>
            <div style="padding: 1rem 0;">
                <p><strong>Username:</strong> <?php echo htmlspecialchars(getUsername()); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                <p><strong>Full Name:</strong> <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                <p><strong>Role:</strong> <?php echo htmlspecialchars(getUserRole()); ?></p>
            </div>
        </div>
    </main>
</body>

</html>