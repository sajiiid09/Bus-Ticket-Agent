<?php
/**
 * Reservation Service
 * Handles seat reservation and booking logic
 */

require_once __DIR__ . '/../db.php';

class ReservationService {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAvailableSeats($trip_no) {
        $trip_no = $this->db->escape($trip_no);
        $sql = "SELECT * FROM seat_info WHERE trip_no = $trip_no AND status = 'available'";
        $result = $this->db->query($sql);
        $seats = [];
        
        while ($row = $this->db->fetchAssoc($result)) {
            $seats[] = $row['seat_number'];
        }
        
        return $seats;
    }

    public function getReservedSeats($trip_no) {
        $trip_no = $this->db->escape($trip_no);
        $sql = "SELECT * FROM seat_info WHERE trip_no = $trip_no AND status IN ('reserved', 'booked')";
        $result = $this->db->query($sql);
        $seats = [];
        
        while ($row = $this->db->fetchAssoc($result)) {
            $seats[] = $row['seat_number'];
        }
        
        return $seats;
    }

    public function createBooking($trip_no, $passenger_name, $mobile, $gender, $selected_seats, $boarding_point, $payment_method, $total_fare) {
        $trip_no = $this->db->escape($trip_no);
        $passenger_name = $this->db->escape($passenger_name);
        $mobile = $this->db->escape($mobile);
        $gender = $this->db->escape($gender);
        $selected_seats = $this->db->escape($selected_seats);
        $boarding_point = $this->db->escape($boarding_point);
        $payment_method = $this->db->escape($payment_method);
        $total_fare = $this->db->escape($total_fare);

        $sql = "INSERT INTO bookings (trip_no, passenger_name, mobile, gender, selected_seats, boarding_point, payment_method, total_fare, status) 
                VALUES ($trip_no, '$passenger_name', '$mobile', '$gender', '$selected_seats', '$boarding_point', '$payment_method', $total_fare, 'pending')";
        
        $this->db->query($sql);
        return $this->db->getLastId();
    }

    public function getBooking($booking_id) {
        $booking_id = $this->db->escape($booking_id);
        $sql = "SELECT * FROM bookings WHERE id = $booking_id";
        $result = $this->db->query($sql);
        return $this->db->fetchAssoc($result);
    }

    public function confirmBooking($booking_id, $transaction_id = null) {
        $booking_id = $this->db->escape($booking_id);
        $transaction_id = $transaction_id ? $this->db->escape($transaction_id) : null;
        
        $set_clause = $transaction_id ? "status = 'confirmed', transaction_id = '$transaction_id'" : "status = 'confirmed'";
        $sql = "UPDATE bookings SET $set_clause WHERE id = $booking_id";
        
        return $this->db->query($sql);
    }
}
?>
