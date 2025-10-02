<?php
require_once '../config/auth.php';
requireAdmin();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Coffee Shop</title>
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
        <h1 style="margin-bottom: 2rem; color: var(--primary);">Manage Users</h1>

        <div class="card">
            <table class="table" id="usersTable">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Full Name</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </main>

    <script>
    let users = [];

    async function loadUsers() {
        try {
            const response = await fetch('/api/users/manage-user.php');
            const data = await response.json();
            if (data.success) {
                users = data.users;
                renderUsers();
            }
        } catch (error) {
            console.error('Error loading users:', error);
        }
    }

    function renderUsers() {
        const tbody = document.querySelector('#usersTable tbody');
        tbody.innerHTML = users.map(user => `
                <tr>
                    <td>${user.username}</td>
                    <td>${user.email}</td>
                    <td>${user.full_name}</td>
                    <td><span class="badge">${user.role}</span></td>
                    <td><span class="badge badge-${user.status === 'active' ? 'ready' : 'cancelled'}">${user.status}</span></td>
                    <td>
                        <button class="btn btn-sm btn-secondary" onclick="toggleUserStatus(${user.id}, '${user.status}')">
                            ${user.status === 'active' ? 'Block' : 'Unblock'}
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})">Delete</button>
                    </td>
                </tr>
            `).join('');
    }

    async function toggleUserStatus(id, currentStatus) {
        const newStatus = currentStatus === 'active' ? 'blocked' : 'active';
        const user = users.find(u => u.id === id);

        try {
            const response = await fetch('/api/users/manage-user.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id,
                    status: newStatus,
                    role: user.role
                })
            });

            const data = await response.json();
            if (data.success) {
                alert('User status updated');
                loadUsers();
            } else {
                alert('Failed to update user');
            }
        } catch (error) {
            alert('An error occurred');
        }
    }

    async function deleteUser(id) {
        if (!confirm('Are you sure you want to delete this user?')) return;

        try {
            const response = await fetch('/api/users/manage-user.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id
                })
            });

            const data = await response.json();
            if (data.success) {
                alert('User deleted successfully');
                loadUsers();
            } else {
                alert('Failed to delete user');
            }
        } catch (error) {
            alert('An error occurred');
        }
    }

    loadUsers();
    </script>
</body>

</html>