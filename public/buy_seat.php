<?php
/**
 * Booking Form - Passenger information and confirmation
 */
require_once '../includes/head.php';
require_once '../app/repositories/BusRepository.php';
require_once '../app/services/ReservationService.php';

$trip_no = isset($_GET['trip']) ? intval($_GET['trip']) : 0;
$busRepo = new BusRepository();
$trip = $trip_no ? $busRepo->getTripDetails($trip_no) : null;

if (!$trip) {
    header('Location: index.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passenger_name = $_POST['passenger_name'] ?? '';
    $mobile = $_POST['mobile'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $selected_seats = $_POST['selected_seats'] ?? '';
    $boarding_point = $_POST['boarding_point'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    $total_fare = floatval($_POST['total_fare'] ?? 0);
    
    // Validation
    $errors = [];
    if (empty($passenger_name)) $errors[] = 'Passenger name is required';
    if (empty($mobile) || !preg_match('/^[0-9]{11}$/', $mobile)) $errors[] = 'Valid mobile number is required';
    if (empty($gender)) $errors[] = 'Gender is required';
    if (empty($selected_seats)) $errors[] = 'Seats are required';
    if (empty($boarding_point)) $errors[] = 'Boarding point is required';
    if (empty($payment_method)) $errors[] = 'Payment method is required';
    
    if (empty($errors)) {
        // Create booking
        $resService = new ReservationService();
        $booking_id = $resService->createBooking($trip_no, $passenger_name, $mobile, $gender, $selected_seats, $boarding_point, $payment_method, $total_fare);
        
        // Redirect to payment confirmation
        header('Location: payment_confirm.php?booking=' . $booking_id);
        exit;
    }
}

// Get data from session/GET parameters
$selected_seats = isset($_GET['seats']) ? $_GET['seats'] : '';
$boarding_point = isset($_GET['boarding']) ? $_GET['boarding'] : '';
$total_fare = isset($_GET['fare']) ? floatval($_GET['fare']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_seats = $_POST['selected_seats'] ?? $selected_seats;
    $total_fare = isset($_POST['total_fare']) ? floatval($_POST['total_fare']) : $total_fare;
    $boarding_point = $_POST['boarding_point'] ?? $boarding_point;
}

$seat_list = array_filter(array_map('trim', explode(',', $selected_seats)));
$seat_count = count($seat_list);
?>

<?php require_once '../includes/navbar.php'; ?>

<!-- Header -->
<section class="booking-header">
    <div class="container">
        <h1 class="page-title">Booking Summary & Passenger Details</h1>
    </div>
</section>

<!-- Progress Indicator -->
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
        <div class="step done">
            <div class="step-number"><i class="fa fa-check"></i></div>
            <div class="step-label">Choose Seats</div>
        </div>
        <div class="step active">
            <div class="step-number">4</div>
            <div class="step-label">Book & Pay</div>
        </div>
        <div class="step">
            <div class="step-number">5</div>
            <div class="step-label">Get Ticket</div>
        </div>
    </div>
</section>

<!-- Booking Form -->
<section class="booking-section">
    <div class="container">
        <div class="booking-content">
            <!-- Left: Passenger Form -->
            <div class="booking-form-column">
                <div class="form-card">
                    <h2 class="form-title">Passenger Information</h2>
                    
                    <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" id="bookingForm">
                        <div class="form-group">
                            <label for="passenger_name">Full Name *</label>
                            <input type="text" id="passenger_name" name="passenger_name" class="form-control" placeholder="Enter passenger name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="mobile">Mobile Number *</label>
                            <input type="tel" id="mobile" name="mobile" class="form-control" placeholder="01XXXXXXXXX" pattern="[0-9]{11}" required>
                            <small class="form-text">Format: 11 digits (e.g., 01712345678)</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Gender *</label>
                            <div class="radio-group">
                                <div class="radio-item">
                                    <input type="radio" id="gender_male" name="gender" value="Male" required>
                                    <label for="gender_male">Male</label>
                                </div>
                                <div class="radio-item">
                                    <input type="radio" id="gender_female" name="gender" value="Female" required>
                                    <label for="gender_female">Female</label>
                                </div>
                                <div class="radio-item">
                                    <input type="radio" id="gender_other" name="gender" value="Other" required>
                                    <label for="gender_other">Other</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="boarding_point">Boarding Point *</label>
                            <input type="text" id="boarding_point" name="boarding_point" class="form-control" value="<?php echo htmlspecialchars($boarding_point); ?>" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label>Payment Method *</label>
                            <div class="payment-options">
                                <div class="payment-option">
                                    <input type="radio" id="payment_cash" name="payment_method" value="Cash on Booking" required>
                                    <label for="payment_cash">
                                        <i class="fa fa-money"></i>
                                        <span>Cash on Booking</span>
                                    </label>
                                </div>
                                <div class="payment-option">
                                    <input type="radio" id="payment_card" name="payment_method" value="Credit Card" required>
                                    <label for="payment_card">
                                        <i class="fa fa-credit-card"></i>
                                        <span>Credit Card</span>
                                    </label>
                                </div>
                                <div class="payment-option">
                                    <input type="radio" id="payment_bkash" name="payment_method" value="bKash" required>
                                    <label for="payment_bkash">
                                        <i class="fa fa-mobile"></i>
                                        <span>bKash</span>
                                    </label>
                                </div>
                                <div class="payment-option">
                                    <input type="radio" id="payment_rocket" name="payment_method" value="Rocket" required>
                                    <label for="payment_rocket">
                                        <i class="fa fa-mobile"></i>
                                        <span>Rocket</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden fields for data passing -->
                        <input type="hidden" name="trip_no" value="<?php echo $trip_no; ?>">
                        <input type="hidden" name="selected_seats" id="hidden_selected_seats" value="<?php echo htmlspecialchars($selected_seats); ?>">
                        <input type="hidden" name="total_fare" id="hidden_total_fare" value="<?php echo htmlspecialchars(number_format($total_fare, 2, '.', '')); ?>">
                    </form>
                </div>
            </div>
            
            <!-- Right: Booking Summary -->
            <div class="booking-summary-column">
                <div class="summary-card">
                    <h2 class="summary-title">Booking Summary</h2>
                    
                    <!-- Trip Details -->
                    <div class="summary-section">
                        <h3 class="summary-section-title">Trip Details</h3>
                        <div class="summary-item">
                            <span class="item-label">Bus Name:</span>
                            <span class="item-value"><?php echo htmlspecialchars($trip['bus_name']); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="item-label">Company:</span>
                            <span class="item-value"><?php echo htmlspecialchars($trip['company']); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="item-label">Route:</span>
                            <span class="item-value"><?php echo htmlspecialchars($trip['route_from']) . ' to ' . htmlspecialchars($trip['route_to']); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="item-label">Departure:</span>
                            <span class="item-value"><?php echo date('H:i', strtotime($trip['departure_time'])); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="item-label">Arrival:</span>
                            <span class="item-value"><?php echo date('H:i', strtotime($trip['arrival_time'])); ?></span>
                        </div>
                    </div>
                    
                    <!-- Selected Seats -->
                    <div class="summary-section">
                        <h3 class="summary-section-title">Selected Seats</h3>
                        <div class="seats-display" id="seatsDisplay">
                            <?php if ($seat_count > 0): ?>
                                <?php foreach ($seat_list as $seat): ?>
                                    <span class="seat-badge"><?php echo htmlspecialchars($seat); ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-muted">No seats selected yet.</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Fare Breakdown -->
                    <div class="summary-section fare-breakdown">
                        <div class="breakdown-item">
                            <span class="breakdown-label">Seats Selected:</span>
                            <span class="breakdown-value" id="seatCount"><?php echo $seat_count; ?></span>
                        </div>
                        <div class="breakdown-item">
                            <span class="breakdown-label">Fare per Seat:</span>
                            <span class="breakdown-value">৳<?php echo number_format($trip['fare'], 0); ?></span>
                        </div>
                        <div class="breakdown-item">
                            <span class="breakdown-label">Discount:</span>
                            <span class="breakdown-value">৳0</span>
                        </div>
                        <div class="breakdown-item total">
                            <span class="breakdown-label">Total Fare:</span>
                            <span class="breakdown-value">৳<span id="summaryTotalFare"><?php echo number_format($total_fare, 0); ?></span></span>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="summary-actions">
                        <button type="button" class="btn btn-secondary" onclick="history.back()">Back</button>
                        <button type="submit" form="bookingForm" class="btn btn-primary btn-large">Confirm & Proceed to Payment</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>

<style>
/* Booking Header */
.booking-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    padding: 40px 20px;
    margin-top: -60px;
    padding-top: 100px;
}

.page-title {
    font-size: 28px;
    font-weight: 700;
    margin: 0;
}

/* Booking Section */
.booking-section {
    padding: 40px 20px;
    background: var(--neutral-100);
    min-height: calc(100vh - 200px);
}

.booking-content {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
}

/* Form Column */
.booking-form-column {
    display: flex;
    flex-direction: column;
}

.form-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.form-title {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 25px;
    color: var(--text-primary);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 8px;
    font-size: 14px;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid var(--neutral-300);
    border-radius: 6px;
    font-size: 14px;
    transition: all var(--transition-speed) ease;
    background-color: var(--neutral-50);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    background-color: white;
    box-shadow: 0 0 0 3px rgba(7, 157, 73, 0.1);
}

.form-text {
    display: block;
    font-size: 12px;
    color: var(--text-secondary);
    margin-top: 5px;
}

/* Radio Group */
.radio-group {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.radio-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.radio-item input[type="radio"] {
    cursor: pointer;
    accent-color: var(--primary-color);
}

.radio-item label {
    margin: 0;
    cursor: pointer;
    font-weight: 500;
}

/* Payment Options */
.payment-options {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.payment-option {
    position: relative;
}

.payment-option input[type="radio"] {
    display: none;
}

.payment-option label {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
    border: 2px solid var(--neutral-300);
    border-radius: 6px;
    cursor: pointer;
    transition: all var(--transition-speed) ease;
    background-color: var(--neutral-50);
    margin: 0;
    font-weight: 500;
}

.payment-option input[type="radio"]:checked + label {
    border-color: var(--primary-color);
    background-color: rgba(7, 157, 73, 0.05);
    color: var(--primary-color);
}

.payment-option label i {
    font-size: 18px;
}

/* Alert */
.alert {
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.alert-danger {
    background-color: #ffebee;
    border: 1px solid #ef5350;
    color: #c62828;
}

.alert ul {
    margin: 0;
    padding-left: 20px;
}

/* Summary Column */
.booking-summary-column {
    position: relative;
}

.summary-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    position: sticky;
    top: 80px;
}

.summary-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 20px;
    color: var(--text-primary);
}

.summary-section {
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--neutral-200);
}

.summary-section-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--primary-color);
    text-transform: uppercase;
    margin-bottom: 12px;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    font-size: 13px;
}

.item-label {
    font-weight: 600;
    color: var(--text-secondary);
}

.item-value {
    color: var(--text-primary);
    font-weight: 500;
}

/* Seats Display */
.seats-display {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.seat-badge {
    background-color: var(--accent-color);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

/* Fare Breakdown */
.fare-breakdown {
    background-color: var(--neutral-100);
    padding: 15px;
    border-radius: 6px;
    border: none;
    margin-bottom: 0;
    padding-bottom: 15px;
}

.breakdown-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 13px;
    margin-bottom: 8px;
}

.breakdown-item.total {
    font-weight: 700;
    font-size: 16px;
    color: var(--primary-color);
    border-top: 1px solid var(--neutral-300);
    padding-top: 10px;
    margin-top: 10px;
}

.breakdown-label {
    font-weight: 600;
    color: var(--text-secondary);
}

.breakdown-value {
    color: var(--text-primary);
}

.breakdown-item.total .breakdown-value {
    color: var(--primary-color);
}

/* Summary Actions */
.summary-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 25px;
}

.btn-large {
    padding: 14px 20px;
    font-size: 15px;
}

/* Responsive */
@media (max-width: 1024px) {
    .booking-content {
        grid-template-columns: 1fr;
    }

    .summary-card {
        position: static;
    }
}

@media (max-width: 768px) {
    .booking-header {
        padding: 80px 20px 40px;
    }

    .page-title {
        font-size: 22px;
    }

    .form-card,
    .summary-card {
        padding: 20px;
    }

    .payment-options {
        grid-template-columns: 1fr;
    }

    .summary-actions {
        flex-direction: column;
    }
}
</style>

<script>
$(document).ready(function() {
    // Get data from sessionStorage
    const selectedSeats = sessionStorage.getItem('selected_seats') || '';
    const boardingPoint = sessionStorage.getItem('boarding_point') || '';
    const totalFare = sessionStorage.getItem('total_fare') || 0;
    
    // Update form fields
    $('#boarding_point').val(boardingPoint);
    $('#hidden_selected_seats').val(selectedSeats);
    $('#hidden_total_fare').val(totalFare);
    
    // Update summary display
    if (selectedSeats) {
        const seats = selectedSeats.split(',');
        const seatsHtml = seats.map(seat => 
            '<span class="seat-badge">' + seat.trim() + '</span>'
        ).join('');
        $('#seatsDisplay').html(seatsHtml);
        $('#seatCount').text(seats.length);
        $('#summaryTotalFare').text(parseInt(totalFare));
    }
    
    // Form validation
    $('#bookingForm').on('submit', function(e) {
        const passengerName = $('#passenger_name').val().trim();
        const mobile = $('#mobile').val().trim();
        const gender = $('input[name="gender"]:checked').val();
        const paymentMethod = $('input[name="payment_method"]:checked').val();
        
        if (!passengerName || !mobile || !gender || !paymentMethod) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return false;
        }
    });
});
</script>
