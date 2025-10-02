<?php
require_once '../config/auth.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Coffee Shop</title>
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
                <button class="btn btn-primary btn-sm" onclick="openCart()">Cart (<span
                        id="cartCount">0</span>)</button>
                <a href="/api/auth/logout.php" class="btn btn-danger btn-sm">Logout</a>
            </nav>
        </div>
    </header>

    <main class="container" style="padding: 2rem 1.5rem;">
        <h1 style="margin-bottom: 2rem; color: var(--primary);">Our Menu</h1>

        <div class="search-bar">
            <input type="text" class="search-input" id="searchInput" placeholder="Search coffee...">
        </div>

        <div class="filter-buttons">
            <button class="filter-btn active" data-category="">All</button>
            <button class="filter-btn" data-category="Hot">Hot</button>
            <button class="filter-btn" data-category="Cold">Cold</button>
            <button class="filter-btn" data-category="Specialty">Specialty</button>
            <button class="filter-btn" data-category="Dessert">Dessert</button>
        </div>

        <div class="grid grid-3" id="menuGrid"></div>
    </main>

    <!-- Cart Modal -->
    <div class="modal" id="cartModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Shopping Cart</h2>
                <button class="modal-close" onclick="closeCart()">&times;</button>
            </div>
            <div id="cartItems"></div>
            <div class="cart-total">Total: $<span id="cartTotal">0.00</span></div>
            <div style="margin-top: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Delivery Address</label>
                    <textarea class="form-textarea" id="deliveryAddress"
                        placeholder="Enter your delivery address"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea class="form-textarea" id="orderNotes" placeholder="Any special requests?"></textarea>
                </div>
                <button class="btn btn-primary" style="width: 100%;" onclick="placeOrder()">Place Order</button>
            </div>
        </div>
    </div>

    <script>
    let menuItems = [];
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    let currentCategory = '';
    let searchQuery = '';

    // Load menu items
    async function loadMenu() {
        try {
            const response = await fetch('/api/coffee/get-items.php?available=true');
            const data = await response.json();
            if (data.success) {
                menuItems = data.items;
                renderMenu();
            }
        } catch (error) {
            console.error('Error loading menu:', error);
        }
    }

    // Render menu
    function renderMenu() {
        const grid = document.getElementById('menuGrid');
        const filtered = menuItems.filter(item => {
            const matchesCategory = !currentCategory || item.category === currentCategory;
            const matchesSearch = !searchQuery ||
                item.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
                item.description.toLowerCase().includes(searchQuery.toLowerCase());
            return matchesCategory && matchesSearch;
        });

        grid.innerHTML = filtered.map(item => `
                <div class="coffee-card">
                    <div class="coffee-image"></div>
                    <div class="coffee-content">
                        <h3 class="coffee-name">${item.name}</h3>
                        <p class="coffee-description">${item.description}</p>
                        <div class="coffee-footer">
                            <span class="coffee-price">$${parseFloat(item.price).toFixed(2)}</span>
                            <span class="coffee-category">${item.category}</span>
                        </div>
                        <button class="btn btn-primary" style="width: 100%; margin-top: 1rem;" onclick="addToCart(${item.id})">
                            Add to Cart
                        </button>
                    </div>
                </div>
            `).join('');
    }

    // Add to cart
    function addToCart(itemId) {
        const item = menuItems.find(i => i.id === itemId);
        const existingItem = cart.find(i => i.id === itemId);

        if (existingItem) {
            existingItem.quantity++;
        } else {
            cart.push({
                ...item,
                quantity: 1
            });
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        alert('Added to cart!');
    }

    // Update cart count
    function updateCartCount() {
        const count = cart.reduce((sum, item) => sum + item.quantity, 0);
        document.getElementById('cartCount').textContent = count;
    }

    // Open cart
    function openCart() {
        const modal = document.getElementById('cartModal');
        const cartItems = document.getElementById('cartItems');
        const cartTotal = document.getElementById('cartTotal');

        if (cart.length === 0) {
            cartItems.innerHTML = '<p style="text-align: center; color: var(--text-light);">Your cart is empty</p>';
            cartTotal.textContent = '0.00';
        } else {
            cartItems.innerHTML = cart.map(item => `
                    <div class="cart-item">
                        <div>
                            <strong>${item.name}</strong><br>
                            <span style="color: var(--text-light);">$${parseFloat(item.price).toFixed(2)} × ${item.quantity}</span>
                        </div>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <button class="btn btn-sm btn-secondary" onclick="updateQuantity(${item.id}, -1)">-</button>
                            <span>${item.quantity}</span>
                            <button class="btn btn-sm btn-secondary" onclick="updateQuantity(${item.id}, 1)">+</button>
                            <button class="btn btn-sm btn-danger" onclick="removeFromCart(${item.id})">Remove</button>
                        </div>
                    </div>
                `).join('');

            const total = cart.reduce((sum, item) => sum + (parseFloat(item.price) * item.quantity), 0);
            cartTotal.textContent = total.toFixed(2);
        }

        modal.classList.add('active');
    }

    // Close cart
    function closeCart() {
        document.getElementById('cartModal').classList.remove('active');
    }

    // Update quantity
    function updateQuantity(itemId, change) {
        const item = cart.find(i => i.id === itemId);
        if (item) {
            item.quantity += change;
            if (item.quantity <= 0) {
                removeFromCart(itemId);
            } else {
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCartCount();
                openCart();
            }
        }
    }

    // Remove from cart
    function removeFromCart(itemId) {
        cart = cart.filter(i => i.id !== itemId);
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        openCart();
    }

    // Place order
    async function placeOrder() {
        if (cart.length === 0) {
            alert('Your cart is empty');
            return;
        }

        const deliveryAddress = document.getElementById('deliveryAddress').value;
        const notes = document.getElementById('orderNotes').value;

        if (!deliveryAddress) {
            alert('Please enter a delivery address');
            return;
        }

        try {
            const response = await fetch('/api/orders/create-order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    items: cart,
                    delivery_address: deliveryAddress,
                    notes: notes
                })
            });

            const data = await response.json();

            if (data.success) {
                alert('Order placed successfully!');
                cart = [];
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCartCount();
                closeCart();
                window.location.href = '/user/orders.php';
            } else {
                alert('Failed to place order: ' + data.message);
            }
        } catch (error) {
            alert('An error occurred. Please try again.');
        }
    }

    // Filter buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentCategory = btn.dataset.category;
            renderMenu();
        });
    });

    // Search
    document.getElementById('searchInput').addEventListener('input', (e) => {
        searchQuery = e.target.value;
        renderMenu();
    });

    // Initialize
    loadMenu();
    updateCartCount();
    </script>
</body>

</html>