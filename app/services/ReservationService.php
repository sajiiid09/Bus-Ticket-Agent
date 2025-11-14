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
        $this->ensureSeatInventory($trip_no);
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
        $this->ensureSeatInventory($trip_no);
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
        $this->ensureSeatInventory($trip_no);

        $seat_string = $selected_seats;

        $trip_no = $this->db->escape($trip_no);
        $passenger_name = $this->db->escape($passenger_name);
        $mobile = $this->db->escape($mobile);
        $gender = $this->db->escape($gender);
        $selected_seats = $this->db->escape($selected_seats);
        $boarding_point = $this->db->escape($boarding_point);
        $payment_method = $this->db->escape($payment_method);
        $total_fare = $this->db->escape($total_fare);

        $sql = "INSERT INTO bookings (trip_no, passenger_name, mobile, gender, selected_seats, boarding_point, payment_method, total_fare, status)"
                . " VALUES ($trip_no, '$passenger_name', '$mobile', '$gender', '$selected_seats', '$boarding_point', '$payment_method', $total_fare, 'pending')";

        $result = $this->db->query($sql);
        $booking_id = $this->db->getLastId();

        if ($result) {
            $seats = $this->parseSeatList($seat_string);
            if (!empty($seats)) {
                $this->updateSeatStatuses($trip_no, $seats, 'reserved');
                $this->adjustAvailableSeats($trip_no, count($seats));
            }
        }

        return $booking_id;
    }

    public function getBooking($booking_id) {
        $booking_id = $this->db->escape($booking_id);
        $sql = "SELECT * FROM bookings WHERE id = $booking_id";
        $result = $this->db->query($sql);
        return $this->db->fetchAssoc($result);
    }

    public function confirmBooking($booking_id, $transaction_id = null) {
        $booking = $this->getBooking($booking_id);
        if (!$booking) {
            return false;
        }

        $booking_id = $this->db->escape($booking_id);
        $transaction_id = $transaction_id ? $this->db->escape($transaction_id) : null;

        $set_clause = $transaction_id ? "status = 'confirmed', transaction_id = '$transaction_id'" : "status = 'confirmed'";
        $sql = "UPDATE bookings SET $set_clause WHERE id = $booking_id";

        $result = $this->db->query($sql);

        if ($result) {
            $seats = $this->parseSeatList($booking['selected_seats']);
            if (!empty($seats)) {
                $this->updateSeatStatuses($booking['trip_no'], $seats, 'booked');
            }
        }

        return $result;
    }

    private function parseSeatList($seats) {
        if (is_string($seats)) {
            $seats = explode(',', $seats);
        }

        $seats = array_map('trim', $seats);
        return array_filter($seats, function ($seat) {
            return $seat !== '';
        });
    }

    private function updateSeatStatuses($trip_no, array $seats, $status) {
        if (empty($seats)) {
            return;
        }

        $allowed_statuses = ['available', 'reserved', 'booked'];
        if (!in_array($status, $allowed_statuses, true)) {
            return;
        }

        $trip_no = $this->db->escape($trip_no);
        $status = $this->db->escape($status);
        $seat_list = array_map(function ($seat) {
            return "'" . $this->db->escape($seat) . "'";
        }, $seats);

        $seat_in_clause = implode(',', $seat_list);
        $sql = "UPDATE seat_info SET status = '$status' WHERE trip_no = $trip_no AND seat_number IN ($seat_in_clause)";
        $this->db->query($sql);
    }

    private function adjustAvailableSeats($trip_no, $seat_count) {
        $seat_count = intval($seat_count);
        if ($seat_count <= 0) {
            return;
        }

        $trip_no = $this->db->escape($trip_no);
        $sql = "UPDATE bus_lists SET available_seats = GREATEST(0, available_seats - $seat_count) WHERE trip_no = $trip_no";
        $this->db->query($sql);
    }

    private function ensureSeatInventory($trip_no) {
        $trip_no = intval($trip_no);
        if ($trip_no <= 0) {
            return;
        }

        $trip_no_escaped = $this->db->escape($trip_no);
        $result = $this->db->query("SELECT COUNT(*) AS seat_count FROM seat_info WHERE trip_no = $trip_no_escaped");
        $row = $result ? $this->db->fetchAssoc($result) : null;

        if ($row && intval($row['seat_count']) > 0) {
            return;
        }

        $seat_values = [];
        foreach (range('A', 'J') as $row_label) {
            for ($i = 1; $i <= 4; $i++) {
                $seat_number = $row_label . $i;
                $escaped_seat = $this->db->escape($seat_number);
                $seat_values[] = "($trip_no_escaped, '$escaped_seat', 'available')";
            }
        }

        if (!empty($seat_values)) {
            $values_sql = implode(',', $seat_values);
            $this->db->query("INSERT INTO seat_info (trip_no, seat_number, status) VALUES $values_sql");
        }
    }
}
?>
