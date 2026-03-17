-- ============================================
-- StayManager HMS — Database Schema
-- ============================================

DROP DATABASE IF EXISTS hotel_reservation;
CREATE DATABASE hotel_reservation CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE hotel_reservation;

-- ============================================
-- Users (admins / staff)
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','staff','manager') DEFAULT 'admin',
    avatar VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- Rooms
-- ============================================
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(10) UNIQUE NOT NULL,
    type ENUM('Standard','Deluxe','Suite','Family') NOT NULL DEFAULT 'Standard',
    floor INT DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    max_guests INT DEFAULT 2,
    description TEXT,
    amenities VARCHAR(500) DEFAULT NULL,
    status ENUM('Available','Occupied','Maintenance','Reserved') DEFAULT 'Available',
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- Customers
-- ============================================
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) DEFAULT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT,
    id_proof ENUM('Passport','Driving License','National ID','Aadhar','Other') DEFAULT 'National ID',
    id_number VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- Bookings
-- ============================================
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    room_id INT NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    guests INT DEFAULT 1,
    total_amount DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('Confirmed','Checked-In','Checked-Out','Cancelled') DEFAULT 'Confirmed',
    notes TEXT,
    checked_in_at DATETIME DEFAULT NULL,
    checked_out_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- Payments
-- ============================================
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('Cash','Card','UPI','Bank Transfer','Other') DEFAULT 'Cash',
    transaction_id VARCHAR(100) DEFAULT NULL,
    payment_date DATE NOT NULL,
    status ENUM('Paid','Pending','Refunded') DEFAULT 'Paid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- Indexes
-- ============================================
ALTER TABLE bookings ADD INDEX idx_status (status);
ALTER TABLE bookings ADD INDEX idx_checkin (check_in);
ALTER TABLE rooms ADD INDEX idx_status (status);
ALTER TABLE customers ADD INDEX idx_phone (phone);

-- ============================================
-- Seed Data
-- ============================================

-- Default admin (password: admin123)
INSERT INTO users (name, email, password, role) VALUES
('Administrator', 'admin@nepstay.com.np', 'admin123', 'admin');

-- Sample rooms
INSERT INTO rooms (room_number, type, floor, price, max_guests, description, amenities, status) VALUES
('101', 'Standard', 1, 2500.00, 2, 'Comfortable standard room with city view', 'WiFi,TV,AC,Mini Bar', 'Available'),
('102', 'Standard', 1, 2500.00, 2, 'Standard room with garden view', 'WiFi,TV,AC', 'Available'),
('201', 'Deluxe', 2, 4500.00, 2, 'Spacious deluxe room with balcony', 'WiFi,TV,AC,Mini Bar,Balcony,Room Service', 'Available'),
('202', 'Deluxe', 2, 4500.00, 3, 'Deluxe room with king-size bed', 'WiFi,TV,AC,Mini Bar,Balcony', 'Available'),
('301', 'Suite', 3, 8000.00, 4, 'Luxury suite with living area and panoramic view', 'WiFi,TV,AC,Mini Bar,Balcony,Room Service,Jacuzzi,Living Area', 'Available'),
('302', 'Suite', 3, 8000.00, 4, 'Premium suite with separate dining', 'WiFi,TV,AC,Mini Bar,Balcony,Room Service,Jacuzzi', 'Available'),
('401', 'Family', 4, 6000.00, 5, 'Family room with extra beds and play area', 'WiFi,TV,AC,Mini Bar,Extra Beds,Kids Area', 'Available'),
('402', 'Family', 4, 6000.00, 5, 'Family room with connecting rooms option', 'WiFi,TV,AC,Extra Beds,Connecting Room', 'Available');

-- Sample customers
INSERT INTO customers (name, email, phone, address, id_proof, id_number) VALUES
('Rahul Sharma', 'rahul@email.com', '9876543210', '123 MG Road, Mumbai', 'Aadhar', '1234-5678-9012'),
('Priya Patel', 'priya@email.com', '9876543211', '456 Brigade Road, Bangalore', 'Passport', 'K1234567'),
('Amit Kumar', 'amit@email.com', '9876543212', '789 Park Street, Kolkata', 'Driving License', 'DL-0420110012345');

-- Sample bookings
INSERT INTO bookings (customer_id, room_id, check_in, check_out, guests, total_amount, status) VALUES
(1, 3, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 3 DAY), 2, 13500.00, 'Checked-In'),
(2, 5, DATE_ADD(CURDATE(), INTERVAL 1 DAY), DATE_ADD(CURDATE(), INTERVAL 4 DAY), 3, 24000.00, 'Confirmed'),
(3, 7, DATE_SUB(CURDATE(), INTERVAL 2 DAY), CURDATE(), 4, 12000.00, 'Checked-Out');

-- Update room status based on bookings
UPDATE rooms SET status = 'Occupied' WHERE id = 3;
UPDATE rooms SET status = 'Reserved' WHERE id = 5;

-- Sample payment
INSERT INTO payments (booking_id, amount, payment_method, payment_date, status) VALUES
(1, 13500.00, 'Card', CURDATE(), 'Paid'),
(3, 12000.00, 'Cash', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'Paid');
