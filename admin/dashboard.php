<?php
require_once '../config/auth.php';
requireAdmin();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Coffee Shop</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>

<body>
    <header class="header">
        <div class="container header-content">
            <a href="/admin/dashboard.php" class="logo">☕ Coffee Shop Admin</a>
            <nav class="nav">
                <a href="/admin/dashboard.php">Dashboard</a>
                <a href="/admin/coffee-items.php">Coffee Items</a>
                <a href="/admin/orders.php">Orders</a>
                <a href="/admin/users.php">Users</a>
                <a href="/api/auth/logout.php" class="btn btn-danger btn-sm">Logout</a>
            </nav>
        </div>
    </header>

    <main class="container" style="padding: 2rem 1.5rem;">
        <h1 style="margin-bottom: 2rem; color: var(--primary);">Dashboard</h1>

        <div class="stats-grid" id="statsGrid"></div>

        <div class="grid grid-2" style="margin-top: 2rem;">
            <div class="card">
                <h2 class="card-header">Top Selling Items</h2>
                <div id="topItems"></div>
            </div>

            <div class="card">
                <h2 class="card-header">Orders by Status</h2>
                <div id="ordersByStatus"></div>
            </div>
        </div>
    </main>

    <script>
    async function loadStats() {
        try {
            const response = await fetch('/api/analytics/get-stats.php');
            const data = await response.json();

            if (data.success) {
                renderStats(data.stats);
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    function renderStats(stats) {
        // Stats cards
        document.getElementById('statsGrid').innerHTML = `
                <div class="stat-card">
                    <div class="stat-value">$${parseFloat(stats.total_sales || 0).toFixed(2)}</div>
                    <div class="stat-label">Total Sales</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">${stats.total_orders}</div>
                    <div class="stat-label">Total Orders</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">${stats.total_users}</div>
                    <div class="stat-label">Total Users</div>
                </div>
            `;

        // Top items
        document.getElementById('topItems').innerHTML = stats.top_items.map(item => `
                <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid var(--border);">
                    <div>
                        <strong>${item.name}</strong><br>
                        <span style="color: var(--text-light); font-size: 0.875rem;">${item.total_sold} sold</span>
                    </div>
                    <span style="color: var(--primary); font-weight: 700;">$${parseFloat(item.revenue).toFixed(2)}</span>
                </div>
            `).join('');

        // Orders by status
        document.getElementById('ordersByStatus').innerHTML = stats.orders_by_status.map(status => `
                <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid var(--border);">
                    <span class="badge badge-${status.status.toLowerCase()}">${status.status}</span>
                    <strong>${status.count}</strong>
                </div>
            `).join('');
    }

    loadStats();
    </script>
</body>

</html>