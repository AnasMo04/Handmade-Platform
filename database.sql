-- Database Schema for Productive Families & Handmade Crafts Platform

CREATE DATABASE IF NOT EXISTS craft_platform;
USE craft_platform;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'craftsman', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crafts Table
CREATE TABLE IF NOT EXISTS crafts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    tags VARCHAR(255),
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Ratings Table
CREATE TABLE IF NOT EXISTS ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    craft_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (craft_id) REFERENCES crafts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- --- DUMMY DATA GENERATION ---

-- 1. Admin User (Password: Admin123!)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@craft.com', 'Admin123!', 'admin');

-- 2. Craftsmen Profiles (Password: craft123)
INSERT INTO users (username, email, password, role) VALUES
('artisan_jane', 'jane@example.com', 'craft123', 'craftsman'),
('wood_works', 'wood@example.com', 'craft123', 'craftsman'),
('pottery_pro', 'pottery@example.com', 'craft123', 'craftsman'),
('weaver_will', 'weaver@example.com', 'craft123', 'craftsman'),
('glass_gal', 'glass@example.com', 'craft123', 'craftsman');

-- 3. Regular Users (Password: user123)
INSERT INTO users (username, email, password, role) VALUES
('buyer_mark', 'mark@example.com', 'user123', 'user'),
('collector_lily', 'lily@example.com', 'user123', 'user'),
('gift_hunter', 'gift@example.com', 'user123', 'user');

-- 4. Craft Items
INSERT INTO crafts (user_id, title, description, price, tags, image_url) VALUES
(2, 'Hand-Painted Silk Scarf', 'A luxurious silk scarf featuring unique floral patterns painted by hand using traditional techniques.', 45.00, 'Silk, Handmade, Fashion', 'https://images.unsplash.com/photo-1584030373081-f37b7bb4fa8e?auto=format&fit=crop&q=80&w=600'),
(3, 'Hand-Carved Walnut Bowl', 'Solid walnut bowl, meticulously carved and finished with food-safe natural oils. Perfect for fruit or decor.', 85.00, 'Wood, Kitchen, Decor', 'https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&q=80&w=600'),
(4, 'Turquoise Ceramic Vase', 'Elegant wheel-thrown pottery with a stunning turquoise crackle glaze. A centerpiece for any room.', 65.00, 'Pottery, Ceramic, Home', 'https://images.unsplash.com/photo-1578749553842-84bc79d20241?auto=format&fit=crop&q=80&w=600'),
(5, 'Hand-Woven Macrame Wall Hanging', 'Intricate boho-style wall art crafted from high-quality 100% natural cotton cord.', 55.00, 'Macrame, Wall Art, Boho', 'https://images.unsplash.com/photo-1528698851261-ce00f12338b9?auto=format&fit=crop&q=80&w=600'),
(6, 'Stained Glass Sun Catcher', 'Handcrafted geometric sun catcher that casts beautiful colorful shadows when hit by sunlight.', 38.00, 'Glass, Sun Catcher, Gift', 'https://images.unsplash.com/photo-1520408222757-6f9f95d87d5d?auto=format&fit=crop&q=80&w=600'),
(2, 'Embroidered Linen Cushion', 'Soft linen cushion cover with delicate hand-embroidered botanical designs.', 42.00, 'Linen, Embroidery, Home', 'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?auto=format&fit=crop&q=80&w=600'),
(3, 'Reclaimed Wood Cutting Board', 'Sustainably sourced reclaimed wood assembled into a durable and beautiful end-grain cutting board.', 75.00, 'Wood, Kitchen, Eco-friendly', 'https://images.unsplash.com/photo-1594385208974-2e75f9d8bb28?auto=format&fit=crop&q=80&w=600'),
(4, 'Rustic Terra Cotta Planter', 'Hand-molded terra cotta planter with a weathered finish, ideal for indoor or outdoor succulents.', 25.00, 'Pottery, Garden, Terra Cotta', 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?auto=format&fit=crop&q=80&w=600'),
(5, 'Hand-Knitted Woolen Throw', 'Chunky, warm throw blanket made from ethically sourced merino wool. Perfect for cozy evenings.', 120.00, 'Wool, Knitting, Comfort', 'https://images.unsplash.com/photo-1520699049698-acd2fccb8cc8?auto=format&fit=crop&q=80&w=600'),
(6, 'Recycled Glass Jar Set', 'Set of three decorative jars made from recycled glass with cork lids.', 30.00, 'Glass, Eco-friendly, Storage', 'https://images.unsplash.com/photo-1544473244-f6895a69ad41?auto=format&fit=crop&q=80&w=600');

-- 5. Ratings and Feedback
INSERT INTO ratings (craft_id, user_id, rating, comment) VALUES
(1, 7, 5, 'Absolutely beautiful scarf! The colors are even better in person.'),
(1, 8, 4, 'Very high quality silk. Shipping was a bit slow though.'),
(2, 9, 5, 'The craftsmanship on this bowl is incredible. It feels so solid and smooth.'),
(3, 7, 5, 'I love the turquoise color! It looks perfect in my living room.'),
(4, 8, 4, 'Really nice wall hanging, exactly as described.'),
(5, 9, 5, 'Beautiful piece of art. It lights up the whole room!'),
(6, 7, 3, 'Nice embroidery, but the cushion is a bit smaller than expected.'),
(7, 8, 5, 'Best cutting board I have ever owned. Truly a work of art.'),
(8, 9, 4, 'Lovely planter, simple and elegant.'),
(9, 7, 5, 'So warm and cozy! Definitely worth the price.'),
(10, 8, 4, 'Great for organizing my spices. Very eco-friendly.'),
(2, 7, 5, 'Bought this as a gift and they loved it. Excellent quality wood.'),
(3, 9, 4, 'Very pretty vase, arrived well packaged.'),
(4, 7, 5, 'Perfect addition to my nursery. So well made.'),
(1, 9, 5, 'A stunning piece of wearable art. Highly recommend!');
