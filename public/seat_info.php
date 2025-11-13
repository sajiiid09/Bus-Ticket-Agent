<?php
/**
 * Seat Selection Modal - Load available seats and handle selection
 * AJAX endpoint for loading seat grid
 */
require_once '../app/repositories/BusRepository.php';
require_once '../app/services/ReservationService.php';

header('Content-Type: application/json');

$trip_no = isset($_POST['trip_no']) ? intval($_POST['trip_no']) : 0;
$action = isset($_POST['action']) ? $_POST['action'] : 'load_seats';

if ($action === 'load_seats' && $trip_no) {
    $busRepo = new BusRepository();
    $resService = new ReservationService();
    
    $trip = $busRepo->getTripDetails($trip_no);
    $available_seats = $resService->getAvailableSeats($trip_no);
    $reserved_seats = $resService->getReservedSeats($trip_no);
    
    // If JSON response expected
    if (isset($_GET['format']) && $_GET['format'] === 'json') {
        echo json_encode([
            'available' => $available_seats,
            'reserved' => $reserved_seats,
            'trip' => $trip
        ]);
        exit;
    }
    
    // HTML response for modal
    $html = '';
    $html .= '<div class="seat-selection-container">';
    
    // Trip Info
    $html .= '<div class="trip-info-bar">';
    $html .= '<span class="trip-name">' . htmlspecialchars($trip['bus_name']) . '</span>';
    $html .= '<span class="trip-time">' . date('H:i', strtotime($trip['departure_time'])) . ' - ' . date('H:i', strtotime($trip['arrival_time'])) . '</span>';
    $html .= '<span class="trip-route">' . htmlspecialchars($trip['route_from']) . ' to ' . htmlspecialchars($trip['route_to']) . '</span>';
    $html .= '</div>';
    
    // Seat Legend
    $html .= '<div class="seat-legend">';
    $html .= '<div class="legend-item"><span class="seat-legend-available"></span> Available</div>';
    $html .= '<div class="legend-item"><span class="seat-legend-reserved"></span> Reserved</div>';
    $html .= '<div class="legend-item"><span class="seat-legend-selected"></span> Selected</div>';
    $html .= '</div>';
    
    // Seat Grid
    $html .= '<form id="seatForm" class="seat-grid-container">';
    $html .= '<div class="seat-grid">';
    
    $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
    foreach ($rows as $row) {
        for ($col = 1; $col <= 4; $col++) {
            $seat_number = $row . $col;
            $is_available = in_array($seat_number, $available_seats);
            $is_reserved = in_array($seat_number, $reserved_seats);
            
            $seat_class = $is_reserved ? 'seat reserved' : ($is_available ? 'seat available' : 'seat unavailable');
            $seat_disabled = $is_reserved ? 'disabled' : '';
            
            $html .= '<input type="checkbox" class="' . $seat_class . '" id="seat_' . $seat_number . '" name="selected_seats[]" value="' . $seat_number . '" ' . $seat_disabled . ' data-seat-number="' . $seat_number . '">';
            $html .= '<label for="seat_' . $seat_number . '" class="seat-label" title="' . $seat_number . '">' . $seat_number . '</label>';
        }
    }
    
    $html .= '</div>';
    
    // Boarding Point Selection
    $html .= '<div class="boarding-point-section">';
    $html .= '<label for="boarding_point">Select Boarding Point:</label>';
    $boarding_points = explode(',', $trip['boarding_points']);
    $html .= '<select id="boarding_point" name="boarding_point" required>';
    $html .= '<option value="">-- Select a boarding point --</option>';
    foreach ($boarding_points as $point) {
        $html .= '<option value="' . htmlspecialchars(trim($point)) . '">' . htmlspecialchars(trim($point)) . '</option>';
    }
    $html .= '</select>';
    $html .= '</div>';
    
    // Summary and Buttons
    $html .= '<div class="seat-summary">';
    $html .= '<div class="summary-item">';
    $html .= '<span class="summary-label">Selected Seats:</span>';
    $html .= '<span class="summary-value" id="selectedSeatsDisplay">None</span>';
    $html .= '</div>';
    $html .= '<div class="summary-item">';
    $html .= '<span class="summary-label">Fare (per seat):</span>';
    $html .= '<span class="summary-value">৳<span id="farePerSeat">' . number_format($trip['fare'], 0) . '</span></span>';
    $html .= '</div>';
    $html .= '<div class="summary-item total">';
    $html .= '<span class="summary-label">Total Fare:</span>';
    $html .= '<span class="summary-value">৳<span id="totalFare">0</span></span>';
    $html .= '</div>';
    $html .= '</div>';
    
    $html .= '<input type="hidden" name="trip_no" value="' . $trip_no . '">';
    $html .= '<input type="hidden" id="tripFarePerSeat" value="' . $trip['fare'] . '">';
    
    $html .= '<div class="modal-actions">';
    $html .= '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>';
    $html .= '<button type="button" class="btn btn-primary" id="btnContinueToBooking" onclick="proceedToBooking(' . $trip_no . ')">Continue to Booking</button>';
    $html .= '</div>';
    
    $html .= '</form>';
    $html .= '</div>';
    
    echo $html;
    exit;
}
?>
