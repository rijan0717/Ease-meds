-- ============================================================
--  Ease Meds – Complete Database Schema
--  Import this file fresh: drop the old ease_meds DB first.
--  After import, visit http://localhost/ease-meds/setup_admin.php
--  to create the admin account (email: rijan@gmail.com / Password@123).
-- ============================================================

CREATE DATABASE IF NOT EXISTS ease_meds CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ease_meds;

-- ------------------------------------------------------------
-- Users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id           INT          AUTO_INCREMENT PRIMARY KEY,
    username     VARCHAR(100) NOT NULL,
    email        VARCHAR(100) NOT NULL UNIQUE,
    phone        VARCHAR(25),
    password     VARCHAR(255) NOT NULL,
    role         ENUM('user','admin') DEFAULT 'user',
    created_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- Medicines
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS medicines (
    id          INT            AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(150)   NOT NULL,
    category    VARCHAR(100),
    description TEXT,
    price       DECIMAL(10,2)  NOT NULL,
    quantity    INT            NOT NULL DEFAULT 0,
    image       VARCHAR(500),
    created_at  TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- Orders
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
    id                INT           AUTO_INCREMENT PRIMARY KEY,
    user_id           INT           NOT NULL,
    total_amount      DECIMAL(10,2) NOT NULL,
    payment_method    VARCHAR(20)   DEFAULT 'cod',
    payment_status    ENUM('pending','paid','failed') DEFAULT 'pending',
    transaction_id    VARCHAR(150),
    status            ENUM('pending','confirmed','packaging','shipped','delivered','cancelled') DEFAULT 'pending',
    prescription_image VARCHAR(500),
    created_at        TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- Order Items
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS order_items (
    id          INT           AUTO_INCREMENT PRIMARY KEY,
    order_id    INT           NOT NULL,
    medicine_id INT           NOT NULL,
    quantity    INT           NOT NULL,
    price       DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id)    REFERENCES orders(id)   ON DELETE CASCADE,
    FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- Order Tracking Log
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS order_tracking (
    id                 INT          AUTO_INCREMENT PRIMARY KEY,
    order_id           INT          NOT NULL,
    status_description VARCHAR(255) NOT NULL,
    updated_at         TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- Doctors
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS doctors (
    id          INT          AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(150) NOT NULL,
    specialty   VARCHAR(150) NOT NULL,
    experience  VARCHAR(100),
    image       VARCHAR(500),
    bio_link    VARCHAR(500),
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- Appointments
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS appointments (
    id               INT     AUTO_INCREMENT PRIMARY KEY,
    user_id          INT     NOT NULL,
    doctor_name      VARCHAR(150) NOT NULL,
    appointment_date DATE    NOT NULL,
    appointment_time TIME    NOT NULL,
    problem          TEXT,
    status           ENUM('pending','confirmed','completed','cancelled') DEFAULT 'pending',
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- Prescriptions
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS prescriptions (
    id          INT           AUTO_INCREMENT PRIMARY KEY,
    user_id     INT           NOT NULL,
    image_path  VARCHAR(500)  NOT NULL,
    description TEXT,
    status      ENUM('pending','reviewed') DEFAULT 'pending',
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- Coupons
-- Column names match coupons.php exactly.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS coupons (
    id               INT            AUTO_INCREMENT PRIMARY KEY,
    coupon_code      VARCHAR(100)   NOT NULL UNIQUE,
    coupon_type      ENUM('fixed','percentage','full','medicines') NOT NULL DEFAULT 'fixed',
    discount_value   DECIMAL(10,2)  DEFAULT 0,
    min_order_amount DECIMAL(10,2)  DEFAULT 0,
    usage_limit      INT            DEFAULT NULL,
    expiry_date      DATE           DEFAULT NULL,
    status           ENUM('active','inactive') DEFAULT 'active',
    medicine_ids     TEXT           DEFAULT NULL,
    created_at       TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
--  SAMPLE DATA
-- ============================================================

-- Medicines ------------------------------------------------
INSERT INTO medicines (name, category, description, price, quantity, image) VALUES
('Napa (Paracetamol)',   'Pain Relief',  'Effective for pain relief and fever.',             50.00,  100, 'https://via.placeholder.com/300x200?text=Paracetamol'),
('Flexon (Ibuprofen)',   'Pain Relief',  'Anti-inflammatory drug for pain.',                 80.00,  100, 'https://via.placeholder.com/300x200?text=Ibuprofen'),
('Cetrizine 10mg',       'Allergy',      'Relief from allergy symptoms.',                    30.00,  200, 'https://via.placeholder.com/300x200?text=Cetrizine'),
('Pantop 40mg',          'Acidity',      'For acid reflux and heartburn.',                  120.00,  150, 'https://via.placeholder.com/300x200?text=Pantop'),
('Azithromycin 500mg',   'Antibiotic',   'Antibiotic for bacterial infections.',            300.00,   50, 'https://via.placeholder.com/300x200?text=Azithromycin'),
('Vitamin C Chewable',   'Supplement',   'Immune system booster.',                          150.00,  300, 'https://via.placeholder.com/300x200?text=Vitamin+C'),
('Sinex Nasal Drops',    'Cold & Flu',   'Relief for blocked nose.',                         90.00,  100, 'https://via.placeholder.com/300x200?text=Sinex'),
('Benadryl Cough Syrup', 'Cough',        'Relief for dry or chesty coughs.',                180.00,   80, 'https://via.placeholder.com/300x200?text=Benadryl'),
('Dettol Antiseptic',    'First Aid',    'Antiseptic liquid for cuts.',                     110.00,  120, 'https://via.placeholder.com/300x200?text=Dettol'),
('Savlon Cream',         'First Aid',    'Antiseptic cream for healing.',                    60.00,  100, 'https://via.placeholder.com/300x200?text=Savlon'),
('Hand Sanitizer 100ml', 'Hygiene',      'Kill 99.9% germs.',                                95.00,  500, 'https://via.placeholder.com/300x200?text=Sanitizer'),
('Face Mask (N95)',       'Protection',   'Protects against dust and virus.',                 50.00, 1000, 'https://via.placeholder.com/300x200?text=Mask'),
('Thermometer Digital',  'Device',       'Accurate body temperature reading.',              450.00,   50, 'https://via.placeholder.com/300x200?text=Thermometer'),
('Oximeter',             'Device',       'Measure oxygen saturation.',                     1200.00,   30, 'https://via.placeholder.com/300x200?text=Oximeter'),
('Volini Spray',         'Pain Relief',  'Instant relief from muscle pain.',                220.00,   75, 'https://via.placeholder.com/300x200?text=Volini'),
('Move Ointment',        'Pain Relief',  'Relief from back pain.',                          130.00,  100, 'https://via.placeholder.com/300x200?text=Move');

-- Doctors --------------------------------------------------
INSERT INTO doctors (name, specialty, experience, image, bio_link) VALUES
('Dr. Ram Sharma',   'General Physician', '10 Years', 'https://via.placeholder.com/150?text=Dr+Ram',    '#'),
('Dr. Sita Karki',   'Pediatrician',      '8 Years',  'https://via.placeholder.com/150?text=Dr+Sita',   '#'),
('Dr. Aayush Malik', 'Cardiologist',      '15 Years', 'https://via.placeholder.com/150?text=Dr+Aayush', '#'),
('Dr. Nita Ray',     'Dermatologist',     '5 Years',  'https://via.placeholder.com/150?text=Dr+Nita',   '#');
