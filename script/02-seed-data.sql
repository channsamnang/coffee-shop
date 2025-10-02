-- Seed initial data
USE coffee_shop;

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password, full_name, role) VALUES
('admin', 'admin@coffeeshop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'admin');

-- Insert sample coffee items
INSERT INTO coffee_items (name, description, price, category, availability) VALUES
('Espresso', 'Rich and bold single shot of espresso', 3.50, 'Hot', TRUE),
('Cappuccino', 'Espresso with steamed milk and foam', 4.50, 'Hot', TRUE),
('Latte', 'Smooth espresso with steamed milk', 4.75, 'Hot', TRUE),
('Americano', 'Espresso with hot water', 3.75, 'Hot', TRUE),
('Mocha', 'Espresso with chocolate and steamed milk', 5.25, 'Hot', TRUE),
('Iced Coffee', 'Cold brewed coffee over ice', 4.00, 'Cold', TRUE),
('Iced Latte', 'Espresso with cold milk over ice', 5.00, 'Cold', TRUE),
('Frappuccino', 'Blended ice coffee drink', 5.50, 'Cold', TRUE),
('Cold Brew', 'Smooth cold-steeped coffee', 4.50, 'Cold', TRUE),
('Caramel Macchiato', 'Vanilla and caramel layered espresso drink', 5.75, 'Specialty', TRUE),
('Flat White', 'Espresso with microfoam milk', 4.95, 'Specialty', TRUE),
('Affogato', 'Espresso poured over vanilla ice cream', 6.00, 'Dessert', TRUE),
('Tiramisu', 'Classic Italian coffee-flavored dessert', 6.50, 'Dessert', TRUE);
