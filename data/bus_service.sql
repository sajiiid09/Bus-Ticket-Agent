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
('Shohag Paribahan', 'Shohag Paribahan', 12, 'Dhaka-Barishal', 'shohag1.jpg shohag2.jpg shohag3.jpg'),
('Ena Transport', 'Ena Transport', 18, 'Dhaka-Cox\'s Bazar-Rangamati', 'ena1.jpg ena2.jpg ena3.jpg');

-- Update Shohag Paribahan routes to include Jessore
UPDATE bus_info SET routes = 'Dhaka-Barishal-Jessore' WHERE id = 3;

-- Extended bus_lists with round trips for all buses
INSERT INTO bus_lists (bus_id, route_from, route_to, departure_time, arrival_time, available_seats, total_seats, fare, boarding_points, trip_no) VALUES
-- Green Line (id=1) - Additional Routes
(1, 'Sylhet', 'Dhaka', '07:00', '15:00', 40, 40, 450.00, 'Sylhet Station, Kumarpara', 10006),
(1, 'Sylhet', 'Dhaka', '23:00', '07:00', 37, 40, 450.00, 'Sylhet Station, Kumarpara', 10007),
(1, 'Dhaka', 'Moulvi Bazar', '21:00', '06:00', 40, 40, 500.00, 'Motijheel, Farmgate, Mirpur', 10008),
(1, 'Moulvi Bazar', 'Dhaka', '22:00', '07:00', 40, 40, 500.00, 'Moulvi Bazar Bus Stand', 10009),

-- Hanif Enterprise (id=2) - Additional Routes
(2, 'Chattogram', 'Dhaka', '09:00', '15:00', 40, 40, 350.00, 'GEC Circle, Bahaddarhat', 10010),
(2, 'Chattogram', 'Dhaka', '21:00', '03:00', 36, 40, 350.00, 'GEC Circle, Bahaddarhat', 10011),
(2, 'Dhaka', 'Chattogram', '14:00', '20:00', 40, 40, 350.00, 'Sadarghat, Motijheel, Sayedabad', 10012),
(2, 'Chattogram', 'Dhaka', '15:00', '21:00', 34, 40, 350.00, 'GEC Circle, Bahaddarhat, Oxygen', 10013),

-- Shohag Paribahan (id=3) - Additional Routes
(3, 'Barishal', 'Dhaka', '08:00', '13:00', 40, 40, 280.00, 'Barishal Bus Terminal, Nathullabad', 10014),
(3, 'Barishal', 'Dhaka', '17:00', '22:00', 38, 40, 280.00, 'Barishal Bus Terminal, Nathullabad', 10015),
(3, 'Dhaka', 'Barishal', '07:00', '12:00', 40, 40, 280.00, 'Farmgate, Gulshan, Sayedabad', 10016),
(3, 'Dhaka', 'Barishal', '22:00', '03:00', 35, 40, 280.00, 'Farmgate, Gulshan', 10017),
(3, 'Dhaka', 'Jessore', '06:00', '11:00', 40, 40, 320.00, 'Gabtoli, Farmgate, Kalyanpur', 10026),
(3, 'Dhaka', 'Jessore', '15:00', '20:00', 38, 40, 320.00, 'Gabtoli, Farmgate, Kalyanpur', 10027),
(3, 'Jessore', 'Dhaka', '07:00', '12:00', 40, 40, 320.00, 'Jessore Bus Terminal, Bypass', 10028),
(3, 'Jessore', 'Dhaka', '16:00', '21:00', 36, 40, 320.00, 'Jessore Bus Terminal, Bypass', 10029),

-- Ena Transport (id=4) - Routes to Cox's Bazar and Rangamati
(4, 'Dhaka', 'Cox\'s Bazar', '19:00', '07:00', 40, 40, 850.00, 'Motijheel, Sayedabad, Jatrabari', 10018),
(4, 'Dhaka', 'Cox\'s Bazar', '21:00', '09:00', 38, 40, 850.00, 'Motijheel, Sayedabad, Jatrabari', 10019),
(4, 'Cox\'s Bazar', 'Dhaka', '20:00', '08:00', 40, 40, 850.00, 'Kolatoli, Hotel Motel Zone', 10020),
(4, 'Cox\'s Bazar', 'Dhaka', '22:00', '10:00', 36, 40, 850.00, 'Kolatoli, Hotel Motel Zone', 10021),
(4, 'Dhaka', 'Rangamati', '07:00', '14:00', 40, 40, 650.00, 'Sayedabad, Jatrabari', 10022),
(4, 'Dhaka', 'Rangamati', '16:00', '23:00', 40, 40, 650.00, 'Sayedabad, Jatrabari', 10023),
(4, 'Rangamati', 'Dhaka', '08:00', '15:00', 40, 40, 650.00, 'Rangamati Bus Stand, Reserve Bazar', 10024),
(4, 'Rangamati', 'Dhaka', '17:00', '00:00', 37, 40, 650.00, 'Rangamati Bus Stand, Reserve Bazar', 10025);

-- Sample seat initialization for new trips (showing pattern for trip 10006)
-- You would repeat this pattern for all other trips (10007-10025)
INSERT INTO seat_info (trip_no, seat_number, status) VALUES
-- Trip 10006 (Sylhet to Dhaka)
(10006, 'A1', 'available'), (10006, 'A2', 'available'), (10006, 'A3', 'available'), (10006, 'A4', 'available'),
(10006, 'B1', 'available'), (10006, 'B2', 'available'), (10006, 'B3', 'available'), (10006, 'B4', 'available'),
(10006, 'C1', 'available'), (10006, 'C2', 'available'), (10006, 'C3', 'available'), (10006, 'C4', 'available'),
(10006, 'D1', 'available'), (10006, 'D2', 'available'), (10006, 'D3', 'available'), (10006, 'D4', 'available'),
(10006, 'E1', 'available'), (10006, 'E2', 'available'), (10006, 'E3', 'available'), (10006, 'E4', 'available'),
(10006, 'F1', 'available'), (10006, 'F2', 'available'), (10006, 'F3', 'available'), (10006, 'F4', 'available'),
(10006, 'G1', 'available'), (10006, 'G2', 'available'), (10006, 'G3', 'available'), (10006, 'G4', 'available'),
(10006, 'H1', 'available'), (10006, 'H2', 'available'), (10006, 'H3', 'available'), (10006, 'H4', 'available'),
(10006, 'I1', 'available'), (10006, 'I2', 'available'), (10006, 'I3', 'available'), (10006, 'I4', 'available'),
(10006, 'J1', 'available'), (10006, 'J2', 'available'), (10006, 'J3', 'available'), (10006, 'J4', 'available');

-- Sample bookings for demonstration
INSERT INTO bookings (trip_no, passenger_name, mobile, gender, selected_seats, boarding_point, payment_method, transaction_id, total_fare, status) VALUES
(10001, 'Abdul Karim', '01712345678', 'Male', 'A1,A2', 'Motijheel', 'bKash', 'BKA12345678', 900.00, 'confirmed'),
(10003, 'Fatema Begum', '01812345679', 'Female', 'C3', 'Sadarghat', 'Nagad', 'NAG98765432', 350.00, 'confirmed'),
(10010, 'Rafiq Hossain', '01912345680', 'Male', 'B1,B2', 'GEC Circle', 'Rocket', 'ROC11223344', 700.00, 'confirmed'),
(10018, 'Shahana Akter', '01612345681', 'Female', 'D1', 'Motijheel', 'bKash', 'BKA55667788', 850.00, 'pending'),
(10014, 'Kamal Uddin', '01512345682', 'Male', 'E1,E2,E3', 'Barishal Bus Terminal', 'Card', 'CARD99887766', 840.00, 'confirmed');

-- Update seat statuses for booked seats
UPDATE seat_info SET status = 'booked' WHERE trip_no = 10001 AND seat_number IN ('A1', 'A2');
UPDATE seat_info SET status = 'booked' WHERE trip_no = 10003 AND seat_number = 'C3';
UPDATE seat_info SET status = 'booked' WHERE trip_no = 10010 AND seat_number IN ('B1', 'B2');
UPDATE seat_info SET status = 'booked' WHERE trip_no = 10018 AND seat_number = 'D1';
UPDATE seat_info SET status = 'booked' WHERE trip_no = 10014 AND seat_number IN ('E1', 'E2', 'E3');

-- Update available seats count
UPDATE bus_lists SET available_seats = available_seats - 2 WHERE trip_no = 10001;
UPDATE bus_lists SET available_seats = available_seats - 1 WHERE trip_no = 10003;
UPDATE bus_lists SET available_seats = available_seats - 2 WHERE trip_no = 10010;
UPDATE bus_lists SET available_seats = available_seats - 1 WHERE trip_no = 10018;
UPDATE bus_lists SET available_seats = available_seats - 3 WHERE trip_no = 10014;
