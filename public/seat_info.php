<?php
/**
 * Seat Selection Page - choose seats before booking
 */
require_once '../app/repositories/BusRepository.php';
require_once '../app/services/ReservationService.php';

/**
 * Build the reusable seat-selection markup so both the dedicated
 * seat page and any legacy modal/AJAX requests share the same HTML.
 */
function renderSeatSelectionMarkup(array $trip, int $trip_no, array $available_seats, array $reserved_seats, array $boarding_points, bool $wrapContainer = true): string {
    ob_start();

    if ($wrapContainer) {
        echo '<div class="container" id="seatSelectionContent">';
    }
    ?>
    <div class="trip-info-bar">
        <span class="trip-name"><?php echo htmlspecialchars($trip['bus_name']); ?></span>
        <span class="trip-time"><?php echo date('H:i', strtotime($trip['departure_time'])); ?> - <?php echo date('H:i', strtotime($trip['arrival_time'])); ?></span>
        <span class="trip-route"><?php echo htmlspecialchars($trip['route_from']); ?> to <?php echo htmlspecialchars($trip['route_to']); ?></span>
    </div>

    <div class="seat-layout">
        <div class="seat-grid-wrapper">
            <div class="seat-legend">
                <div class="legend-item"><span class="seat-legend-available"></span> Available</div>
                <div class="legend-item"><span class="seat-legend-reserved"></span> Reserved</div>
                <div class="legend-item"><span class="seat-legend-selected"></span> Selected</div>
            </div>

            <form id="seatForm" class="seat-grid-container">
                <div class="seat-grid">
                    <?php
                    foreach (range('A', 'J') as $row) {
                        for ($col = 1; $col <= 4; $col++) {
                            $seat_number = $row . $col;
                            $is_reserved = in_array($seat_number, $reserved_seats, true);
                            $is_available = in_array($seat_number, $available_seats, true);
                            $seat_class = 'seat';

                            if ($is_reserved) {
                                $seat_class .= ' reserved';
                            } elseif ($is_available) {
                                $seat_class .= ' available';
                            } else {
                                $seat_class .= ' unavailable';
                            }
                            ?>
                            <input type="checkbox" class="<?php echo $seat_class; ?>" id="seat_<?php echo $seat_number; ?>" name="selected_seats[]" value="<?php echo $seat_number; ?>" <?php echo $is_reserved ? 'disabled' : ''; ?> data-seat-number="<?php echo $seat_number; ?>">
                            <label for="seat_<?php echo $seat_number; ?>" class="seat-label" title="<?php echo $seat_number; ?>"><?php echo $seat_number; ?></label>
                            <?php
                        }
                    }
                    ?>
                </div>

                <div class="boarding-point-section">
                    <label for="boarding_point">Select Boarding Point:</label>
                    <select id="boarding_point" name="boarding_point" required>
                        <option value="">-- Select a boarding point --</option>
                        <?php foreach ($boarding_points as $point): ?>
                            <option value="<?php echo htmlspecialchars($point); ?>"><?php echo htmlspecialchars($point); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="seat-summary">
                    <div class="summary-item">
                        <span class="summary-label">Selected Seats:</span>
                        <span class="summary-value" id="selectedSeatsDisplay">None</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Fare (per seat):</span>
                        <span class="summary-value">৳<span id="farePerSeat"><?php echo number_format($trip['fare'], 0); ?></span></span>
                    </div>
                    <div class="summary-item total">
                        <span class="summary-label">Total Fare:</span>
                        <span class="summary-value">৳<span id="totalFare">0</span></span>
                    </div>
                </div>

                <input type="hidden" name="trip_no" value="<?php echo $trip_no; ?>">
                <input type="hidden" id="tripFarePerSeat" value="<?php echo htmlspecialchars($trip['fare']); ?>">

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="window.history.back();">Back to Results</button>
                    <button type="button" class="btn btn-primary" id="btnContinueToBooking" onclick="proceedToBooking(<?php echo $trip_no; ?>)">Continue to Booking</button>
                </div>
            </form>
        </div>
    </div>
    <?php

    if ($wrapContainer) {
        echo '</div>';
    }

    return ob_get_clean();
}

$isAjaxRequest = $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'load_seats';
$trip_no = $isAjaxRequest ? intval($_POST['trip_no'] ?? 0) : intval($_GET['trip'] ?? 0);

$busRepo = new BusRepository();
$resService = new ReservationService();

$trip = $trip_no ? $busRepo->getTripDetails($trip_no) : null;

if (!$trip) {
    if ($isAjaxRequest) {
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Unable to load seat information at this moment. Please refresh and try again.'
        ]);
        exit;
    }

    header('Location: index.php');
    exit;
}

$available_seats = $resService->getAvailableSeats($trip_no);
$reserved_seats = $resService->getReservedSeats($trip_no);
$boarding_points = array_filter(array_map('trim', explode(',', (string) $trip['boarding_points'])));
if (empty($boarding_points)) {
    $boarding_points = [$trip['route_from']];
}

$seatSelectionMarkup = renderSeatSelectionMarkup($trip, $trip_no, $available_seats, $reserved_seats, $boarding_points, !$isAjaxRequest);

if ($isAjaxRequest) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'html' => $seatSelectionMarkup,
        'trip' => [
            'bus_name' => $trip['bus_name'],
            'company' => $trip['company'],
            'route_from' => $trip['route_from'],
            'route_to' => $trip['route_to'],
            'departure_time' => $trip['departure_time'],
            'arrival_time' => $trip['arrival_time'],
            'fare' => $trip['fare'],
        ],
        'available' => $available_seats,
        'reserved' => $reserved_seats,
    ]);
    exit;
}

require_once '../includes/head.php';
require_once '../includes/navbar.php';
?>

<section class="seat-header">
    <div class="container">
        <h1 class="page-title">Choose Your Seats</h1>
        <p class="page-subtitle">Select seats for <?php echo htmlspecialchars($trip['bus_name']); ?> travelling from <?php echo htmlspecialchars($trip['route_from']); ?> to <?php echo htmlspecialchars($trip['route_to']); ?>.</p>
    </div>
</section>

<section class="container" style="margin: 40px auto;">
    <div class="progress-steps">
        <div class="step done">
            <div class="step-number"><i class="fa fa-check"></i></div>
            <div class="step-label">Search</div>
        </div>
        <div class="step done">
            <div class="step-number"><i class="fa fa-check"></i></div>
            <div class="step-label">Select Bus</div>
        </div>
        <div class="step active">
            <div class="step-number">3</div>
            <div class="step-label">Choose Seats</div>
        </div>
        <div class="step">
            <div class="step-number">4</div>
            <div class="step-label">Book & Pay</div>
        </div>
        <div class="step">
            <div class="step-number">5</div>
            <div class="step-label">Get Ticket</div>
        </div>
    </div>
</section>

<section class="seat-selection-page">
    <?php echo $seatSelectionMarkup; ?>
</section>

<?php require_once '../includes/footer.php'; ?>

<script>
    $(function() {
        if (typeof initializeSeatSelectionUI === 'function') {
            initializeSeatSelectionUI();
        }
    });
</script>
