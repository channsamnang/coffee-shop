<?php
require_once '../config/auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Coffee Shop</title>
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

    <main class="container" style="padding: 2rem 1.5rem;">
        <h1 style="margin-bottom: 2rem; color: var(--primary);">My Orders</h1>

        <div id="ordersContainer"></div>
    </main>

    <script>
    async function loadOrders() {
        try {
            const response = await fetch('/api/orders/get-orders.php');
            const data = await response.json();

            if (data.success) {
                renderOrders(data.orders);
            }
        } catch (error) {
            console.error('Error loading orders:', error);
        }
    }

    function renderOrders(orders) {
        const container = document.getElementById('ordersContainer');

        if (orders.length === 0) {
            container.innerHTML = '<p style="text-align: center; color: var(--text-light);">No orders yet</p>';
            return;
        }

        container.innerHTML = orders.map(order => `
                <div class="card mb-3">
                    <div class="flex flex-between mb-2">
                        <div>
                            <strong>Order #${order.id}</strong><br>
                            <span style="color: var(--text-light); font-size: 0.875rem;">${new Date(order.created_at).toLocaleString()}</span>
                        </div>
                        <span class="badge badge-${order.status.toLowerCase()}">${order.status}</span>
                    </div>
                    
                    <div style="margin: 1rem 0;">
                        ${order.items.map(item => `
                            <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                                <span>${item.name} × ${item.quantity}</span>
                                <span>$${(parseFloat(item.price) * item.quantity).toFixed(2)}</span>
                            </div>
                        `).join('')}
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; padding-top: 1rem; border-top: 2px solid var(--border);">
                        <div>
                            <strong>Total:</strong> <span style="color: var(--primary); font-size: 1.25rem; font-weight: 700;">$${parseFloat(order.total_amount).toFixed(2)}</span>
                        </div>
                    </div>
                    
                    ${order.delivery_address ? `
                        <div style="margin-top: 1rem; padding: 0.75rem; background: var(--background); border-radius: 0.5rem;">
                            <strong>Delivery Address:</strong><br>
                            ${order.delivery_address}
                        </div>
                    ` : ''}
                    
                    ${order.notes ? `
                        <div style="margin-top: 0.5rem; padding: 0.75rem; background: var(--background); border-radius: 0.5rem;">
                            <strong>Notes:</strong> ${order.notes}
                        </div>
                    ` : ''}
                </div>
            `).join('');
    }

    loadOrders();
    </script>
</body>

</html>