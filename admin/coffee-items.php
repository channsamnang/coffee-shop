<?php
require_once '../config/auth.php';
requireAdmin();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Coffee Items - Coffee Shop</title>
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
        <div class="flex flex-between mb-3">
            <h1 style="color: var(--primary);">Coffee Items</h1>
            <button class="btn btn-primary" onclick="openItemModal()">Add New Item</button>
        </div>

        <div class="card">
            <table class="table" id="itemsTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Availability</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </main>

    <!-- Item Modal -->
    <div class="modal" id="itemModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Add Coffee Item</h2>
                <button class="modal-close" onclick="closeItemModal()">&times;</button>
            </div>
            <form id="itemForm">
                <input type="hidden" id="itemId">
                <div class="form-group">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-input" id="itemName" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-textarea" id="itemDescription" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" class="form-input" id="itemPrice" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select class="form-select" id="itemCategory" required>
                        <option value="Hot">Hot</option>
                        <option value="Cold">Cold</option>
                        <option value="Specialty">Specialty</option>
                        <option value="Dessert">Dessert</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <input type="checkbox" id="itemAvailability" checked> Available
                    </label>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Save Item</button>
            </form>
        </div>
    </div>

    <script>
    let items = [];
    let editingId = null;

    async function loadItems() {
        try {
            const response = await fetch('/api/coffee/get-items.php');
            const data = await response.json();
            if (data.success) {
                items = data.items;
                renderItems();
            }
        } catch (error) {
            console.error('Error loading items:', error);
        }
    }

    function renderItems() {
        const tbody = document.querySelector('#itemsTable tbody');
        tbody.innerHTML = items.map(item => `
                <tr>
                    <td>${item.name}</td>
                    <td><span class="badge">${item.category}</span></td>
                    <td>$${parseFloat(item.price).toFixed(2)}</td>
                    <td>${item.availability ? '✓ Available' : '✗ Unavailable'}</td>
                    <td>
                        <button class="btn btn-sm btn-secondary" onclick="editItem(${item.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteItem(${item.id})">Delete</button>
                    </td>
                </tr>
            `).join('');
    }

    function openItemModal(item = null) {
        editingId = item ? item.id : null;
        document.getElementById('modalTitle').textContent = item ? 'Edit Coffee Item' : 'Add Coffee Item';

        if (item) {
            document.getElementById('itemId').value = item.id;
            document.getElementById('itemName').value = item.name;
            document.getElementById('itemDescription').value = item.description;
            document.getElementById('itemPrice').value = item.price;
            document.getElementById('itemCategory').value = item.category;
            document.getElementById('itemAvailability').checked = item.availability == 1;
        } else {
            document.getElementById('itemForm').reset();
            document.getElementById('itemId').value = '';
        }

        document.getElementById('itemModal').classList.add('active');
    }

    function closeItemModal() {
        document.getElementById('itemModal').classList.remove('active');
    }

    function editItem(id) {
        const item = items.find(i => i.id === id);
        if (item) openItemModal(item);
    }

    async function deleteItem(id) {
        if (!confirm('Are you sure you want to delete this item?')) return;

        try {
            const response = await fetch('/api/coffee/manage-item.php', {
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
                alert('Item deleted successfully');
                loadItems();
            } else {
                alert('Failed to delete item');
            }
        } catch (error) {
            alert('An error occurred');
        }
    }

    document.getElementById('itemForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const itemData = {
            name: document.getElementById('itemName').value,
            description: document.getElementById('itemDescription').value,
            price: document.getElementById('itemPrice').value,
            category: document.getElementById('itemCategory').value,
            image_url: '',
            availability: document.getElementById('itemAvailability').checked ? 1 : 0
        };

        if (editingId) {
            itemData.id = editingId;
        }

        try {
            const response = await fetch('/api/coffee/manage-item.php', {
                method: editingId ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(itemData)
            });

            const data = await response.json();
            if (data.success) {
                alert(editingId ? 'Item updated successfully' : 'Item created successfully');
                closeItemModal();
                loadItems();
            } else {
                alert('Failed to save item');
            }
        } catch (error) {
            alert('An error occurred');
        }
    });

    loadItems();
    </script>
</body>

</html>