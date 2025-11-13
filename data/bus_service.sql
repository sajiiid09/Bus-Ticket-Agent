-- Bus Service Database Schema

CREATE TABLE IF NOT EXISTS bus_info (
  id INT PRIMARY KEY AUTO_INCREMENT,
  bus_name VARCHAR(100) NOT NULL,
  company VARCHAR(100) NOT NULL,
  no_bus INT,
  routes VARCHAR(255),
  image VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS bus_lists (
  id INT PRIMARY KEY AUTO_INCREMENT,
  bus_id INT NOT NULL,
  route_from VARCHAR(100) NOT NULL,
  route_to VARCHAR(100) NOT NULL,
  departure_time TIME NOT NULL,
  arrival_time TIME NOT NULL,
  available_seats INT DEFAULT 40,
  total_seats INT DEFAULT 40,
  fare DECIMAL(10,2) NOT NULL,
  boarding_points VARCHAR(255),
  trip_no INT UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (bus_id) REFERENCES bus_info(id)
);

CREATE TABLE IF NOT EXISTS seat_info (
  id INT PRIMARY KEY AUTO_INCREMENT,
  trip_no INT NOT NULL,
  seat_number VARCHAR(10) NOT NULL,
  status ENUM('available', 'reserved', 'booked') DEFAULT 'available',
  FOREIGN KEY (trip_no) REFERENCES bus_lists(trip_no),
  UNIQUE KEY unique_seat (trip_no, seat_number)
);

CREATE TABLE IF NOT EXISTS bookings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  trip_no INT NOT NULL,
  passenger_name VARCHAR(100) NOT NULL,
  mobile VARCHAR(15) NOT NULL,
  gender VARCHAR(10),
  selected_seats VARCHAR(255) NOT NULL,
  boarding_point VARCHAR(100),
  payment_method VARCHAR(20) NOT NULL,
  transaction_id VARCHAR(100),
  total_fare DECIMAL(10,2) NOT NULL,
  status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (trip_no) REFERENCES bus_lists(trip_no)
);

-- Sample Data
INSERT INTO bus_info (bus_name, company, no_bus, routes, image) VALUES
('Green Line Super', 'Green Line Paribahan', 15, 'Dhaka-Sylhet-Moulvi Bazar', 'greenline.jpg bus1.jpg bus2.jpg'),
('Hanif Enterprise', 'Hanif Enterprise', 20, 'Dhaka-Chattogram', 'hanif1.jpg hanif2.jpg hanif3.jpg'),
('Shohag Paribahan', 'Shohag Paribahan', 12, 'Dhaka-Barishal', 'shohag1.jpg shohag2.jpg shohag3.jpg');

INSERT INTO bus_lists (bus_id, route_from, route_to, departure_time, arrival_time, available_seats, total_seats, fare, boarding_points, trip_no) VALUES
(1, 'Dhaka', 'Sylhet', '22:00', '06:00', 40, 40, 450.00, 'Motijheel, Farmgate, Mirpur', 10001),
(1, 'Dhaka', 'Sylhet', '18:00', '02:00', 35, 40, 450.00, 'Motijheel, Farmgate, Mirpur', 10002),
(2, 'Dhaka', 'Chattogram', '08:00', '14:00', 38, 40, 350.00, 'Sadarghat, Motijheel', 10003),
(2, 'Dhaka', 'Chattogram', '20:00', '02:00', 32, 40, 350.00, 'Sadarghat, Motijheel', 10004),
(3, 'Dhaka', 'Barishal', '16:00', '21:00', 40, 40, 280.00, 'Farmgate, Gulshan', 10005);

-- Initialize seat_info for each trip
INSERT INTO seat_info (trip_no, seat_number, status) VALUES
(10001, 'A1', 'available'), (10001, 'A2', 'available'), (10001, 'A3', 'available'), (10001, 'A4', 'available'),
(10001, 'B1', 'available'), (10001, 'B2', 'available'), (10001, 'B3', 'available'), (10001, 'B4', 'available'),
(10001, 'C1', 'available'), (10001, 'C2', 'available'), (10001, 'C3', 'available'), (10001, 'C4', 'available'),
(10001, 'D1', 'available'), (10001, 'D2', 'available'), (10001, 'D3', 'available'), (10001, 'D4', 'available'),
(10001, 'E1', 'available'), (10001, 'E2', 'available'), (10001, 'E3', 'available'), (10001, 'E4', 'available'),
(10001, 'F1', 'available'), (10001, 'F2', 'available'), (10001, 'F3', 'available'), (10001, 'F4', 'available'),
(10001, 'G1', 'available'), (10001, 'G2', 'available'), (10001, 'G3', 'available'), (10001, 'G4', 'available'),
(10001, 'H1', 'available'), (10001, 'H2', 'available'), (10001, 'H3', 'available'), (10001, 'H4', 'available'),
(10001, 'I1', 'available'), (10001, 'I2', 'available'), (10001, 'I3', 'available'), (10001, 'I4', 'available'),
(10001, 'J1', 'available'), (10001, 'J2', 'available'), (10001, 'J3', 'available'), (10001, 'J4', 'available');
