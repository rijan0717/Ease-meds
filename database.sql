-- Database: ease_meds
CREATE DATABASE IF NOT EXISTS ease_meds;
USE ease_meds;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Medicines Table
CREATE TABLE IF NOT EXISTS medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(20) DEFAULT 'cod',
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    transaction_id VARCHAR(100),
    status ENUM('pending', 'confirmed', 'packaging', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    prescription_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order Tracking Table
CREATE TABLE IF NOT EXISTS order_tracking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    status_description VARCHAR(255) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id)
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    medicine_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (medicine_id) REFERENCES medicines(id)
);

-- Doctors Table (NEW)
CREATE TABLE IF NOT EXISTS doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    specialty VARCHAR(100) NOT NULL,
    experience VARCHAR(50),
    image VARCHAR(255),
    bio_link VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Appointments Table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    doctor_name VARCHAR(100) NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    problem TEXT,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Prescriptions Table
CREATE TABLE IF NOT EXISTS prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('pending', 'reviewed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Coupons Table
CREATE TABLE IF NOT EXISTS coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    discount_type ENUM('percentage', 'fixed') DEFAULT 'percentage',
    discount_value DECIMAL(10, 2) NOT NULL,
    max_discount DECIMAL(10, 2),
    min_order_amount DECIMAL(10, 2) DEFAULT 0,
    max_uses INT,
    times_used INT DEFAULT 0,
    valid_from DATETIME,
    valid_to DATETIME,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Order Coupons Table (to track which coupon was used on which order)
CREATE TABLE IF NOT EXISTS order_coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    coupon_id INT NOT NULL,
    discount_amount DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (coupon_id) REFERENCES coupons(id)
);

-- Add coupon_id to orders table
ALTER TABLE orders ADD COLUMN coupon_id INT DEFAULT NULL AFTER transaction_id;
ALTER TABLE orders ADD COLUMN discount_amount DECIMAL(10, 2) DEFAULT 0 AFTER coupon_id;

-- SAMPLE DATA OF MEDICINE 

INSERT INTO medicines (name, category, description, price, quantity, image) VALUES 
('Napa (Paracetamol)', 'Pain Relief', 'Effective for pain relief and fever.', 50.00, 100, 'https://via.placeholder.com/300x200?text=Paracetamol'),
('Flexon (Ibuprofen)', 'Pain Relief', 'Anti-inflammatory drug for pain.', 80.00, 100, 'https://via.placeholder.com/300x200?text=Ibuprofen'),
('Cetrizine 10mg', 'Allergy', 'Relief from allergy symptoms.', 30.00, 200, 'https://via.placeholder.com/300x200?text=Cetrizine'),
('Pantop 40mg', 'Acidity', 'For acid reflux and heartburn.', 120.00, 150, 'https://via.placeholder.com/300x200?text=Pantop'),
('Azithromycin 500mg', 'Antibiotic', 'Antibiotic for bacterial infections.', 300.00, 50, 'https://via.placeholder.com/300x200?text=Azithromycin'),
('Vitamin C Chewable', 'Supplement', 'Immune system booster.', 150.00, 300, 'https://via.placeholder.com/300x200?text=Vitamin+C'),
('Sinex Nasal Drops', 'Cold & Flu', 'Relief for blocked nose.', 90.00, 100, 'https://via.placeholder.com/300x200?text=Sinex'),
('Benadryl Cough Syrup', 'Cough', 'Relief for dry or chesty coughs.', 180.00, 80, 'https://via.placeholder.com/300x200?text=Benadryl'),
('Dettol Antiseptic', 'First Aid', 'Antiseptic liquid for cuts.', 110.00, 120, 'https://via.placeholder.com/300x200?text=Dettol'),
('Savlon Cream', 'First Aid', 'Antiseptic cream for healing.', 60.00, 100, 'https://via.placeholder.com/300x200?text=Savlon'),
('Hand Sanitizer 100ml', 'Hygiene', 'Kill 99.9% germs.', 95.00, 500, 'https://via.placeholder.com/300x200?text=Sanitizer'),
('Face Mask (N95)', 'Protection', 'Protects against dust and virus.', 50.00, 1000, 'https://via.placeholder.com/300x200?text=Mask'),
('Thermometer Digital', 'Device', 'Accurate body temperature reading.', 450.00, 50, 'https://via.placeholder.com/300x200?text=Thermometer'),
('Oximeter', 'Device', 'Measure oxygen saturation.', 1200.00, 30, 'https://via.placeholder.com/300x200?text=Oximeter'),
('Volini Spray', 'Pain Relief', 'Instant relief from muscle pain.', 220.00, 75, 'https://via.placeholder.com/300x200?text=Volini'),
('Move Ointment', 'Pain Relief', 'Relief from back pain.', 130.00, 100, 'https://via.placeholder.com/300x200?text=Move');

-- Sample Doctors
INSERT INTO doctors (name, specialty, experience, image, bio_link) VALUES 
('Dr. Ram Sharma', 'General Physician', '10 Years', 'https://via.placeholder.com/150?text=Dr+Ram', '#'),
('Dr. Sita Karki', 'Pediatrician', '8 Years', 'https://via.placeholder.com/150?text=Dr+Sita', '#'),
('Dr. Aayush Malik', 'Cardiologist', '15 Years', 'https://via.placeholder.com/150?text=Dr+Aayush', '#'),
('Dr. Nita Ray', 'Dermatologist', '5 Years', 'https://via.placeholder.com/150?text=Dr+Nita', '#');


