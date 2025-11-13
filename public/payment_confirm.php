<?php
/**
 * Payment Confirmation & Processing Page
 */
require_once '../includes/head.php';
require_once '../app/services/ReservationService.php';
require_once '../app/repositories/BusRepository.php';

$booking_id = isset($_GET['booking']) ? intval($_GET['booking']) : 0;
$resService = new ReservationService();
$busRepo = new BusRepository();

$booking = null;
$trip = null;

// Handle payment confirmation (mock)
$payment_confirmed = false;
$transaction_id = null;

if ($booking_id) {
    $booking = $resService->getBooking($booking_id);
    if ($booking) {
        $trip = $busRepo->getTripDetails($booking['trip_no']);
        $payment_confirmed = $booking['status'] === 'confirmed';
        $transaction_id = $booking['transaction_id'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    // Generate mock transaction ID
    $transaction_id = 'BT' . date('YmdHis') . rand(1000, 9999);
    $resService->confirmBooking($booking_id, $transaction_id);
    $booking['status'] = 'confirmed';
    $booking['transaction_id'] = $transaction_id;
    $payment_confirmed = true;
}

if (!$booking) {
    header('Location: index.php');
    exit;
}
?>

<?php require_once '../includes/navbar.php'; ?>

<!-- Header -->
<section class="payment-header">
    <div class="container">
        <h1 class="page-title">Payment & Confirmation</h1>
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
        <div class="step done">
            <div class="step-number"><i class="fa fa-check"></i></div>
            <div class="step-label">Book & Pay</div>
        </div>
        <div class="step <?php echo $payment_confirmed ? 'done' : 'active'; ?>">
            <div class="step-number"><?php echo $payment_confirmed ? '<i class="fa fa-check"></i>' : '5'; ?></div>
            <div class="step-label">Get Ticket</div>
        </div>
    </div>
</section>

<!-- Payment Section -->
<section class="payment-section">
    <div class="container">
        <?php if ($payment_confirmed): ?>
            <!-- Success State -->
            <div class="success-container">
                <div class="success-icon">
                    <i class="fa fa-check-circle"></i>
                </div>
                <h2 class="success-title">Payment Successful!</h2>
                <p class="success-message">Your booking has been confirmed. Your ticket is ready.</p>
                
                <div class="confirmation-details">
                    <div class="detail-item">
                        <span class="detail-label">Booking Reference:</span>
                        <span class="detail-value">BK<?php echo str_pad($booking_id, 6, '0', STR_PAD_LEFT); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Transaction ID:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($transaction_id); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value"><span class="badge-confirmed">CONFIRMED</span></span>
                    </div>
                </div>
                
                <div class="actions-success">
                    <a href="ticket.php?booking=<?php echo $booking_id; ?>" class="btn btn-primary">Download Ticket</a>
                    <a href="index.php" class="btn btn-secondary">Book Another</a>
                </div>
            </div>
        <?php else: ?>
            <!-- Payment Form -->
            <div class="payment-form-container">
                <div class="payment-left">
                    <div class="payment-card">
                        <h2 class="form-title">Complete Payment</h2>
                        
                        <div class="booking-info">
                            <h3 class="info-title">Booking Details</h3>
                            <div class="info-item">
                                <span class="info-label">Passenger:</span>
                                <span class="info-value"><?php echo htmlspecialchars($booking['passenger_name']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Mobile:</span>
                                <span class="info-value"><?php echo htmlspecialchars($booking['mobile']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Bus:</span>
                                <span class="info-value"><?php echo htmlspecialchars($trip['bus_name']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Seats:</span>
                                <span class="info-value"><?php echo htmlspecialchars($booking['selected_seats']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Boarding Point:</span>
                                <span class="info-value"><?php echo htmlspecialchars($booking['boarding_point']); ?></span>
                            </div>
                        </div>
                        
                        <form method="POST" id="paymentForm">
                            <div class="payment-method-section">
                                <h3 class="info-title">Select Payment Method</h3>
                                
                                <div class="payment-method-item">
                                    <input type="radio" id="method_card" name="payment_gateway" value="card" checked>
                                    <label for="method_card">
                                        <i class="fa fa-credit-card"></i>
                                        <span>Credit/Debit Card</span>
                                    </label>
                                </div>
                                
                                <div class="payment-method-item">
                                    <input type="radio" id="method_bkash" name="payment_gateway" value="bkash">
                                    <label for="method_bkash">
                                        <i class="fa fa-mobile"></i>
                                        <span>bKash</span>
                                    </label>
                                </div>
                                
                                <div class="payment-method-item">
                                    <input type="radio" id="method_rocket" name="payment_gateway" value="rocket">
                                    <label for="method_rocket">
                                        <i class="fa fa-mobile"></i>
                                        <span>Rocket</span>
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Mock Card Form -->
                            <div class="card-form hidden" id="cardFormContainer">
                                <div class="form-group">
                                    <label for="cardNumber">Card Number</label>
                                    <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19">
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="cardExpiry">MM/YY</label>
                                        <input type="text" id="cardExpiry" placeholder="12/25" maxlength="5">
                                    </div>
                                    <div class="form-group">
                                        <label for="cardCVV">CVV</label>
                                        <input type="text" id="cardCVV" placeholder="123" maxlength="3">
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" name="confirm_payment" class="btn btn-primary btn-large">
                                <i class="fa fa-lock"></i> Complete Payment
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Summary Sidebar -->
                <div class="payment-right">
                    <div class="summary-card">
                        <h3 class="summary-title">Amount to Pay</h3>
                        
                        <div class="fare-summary">
                            <div class="fare-item">
                                <span class="fare-label">Total Fare:</span>
                                <span class="fare-value">৳<?php echo number_format($booking['total_fare'], 0); ?></span>
                            </div>
                            <div class="fare-item">
                                <span class="fare-label">Discount:</span>
                                <span class="fare-value">৳0</span>
                            </div>
                            <div class="fare-item total">
                                <span class="fare-label">You Pay:</span>
                                <span class="fare-value">৳<?php echo number_format($booking['total_fare'], 0); ?></span>
                            </div>
                        </div>
                        
                        <div class="security-notice">
                            <i class="fa fa-shield"></i>
                            <span>Your payment is secure and encrypted</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>

<style>
/* Payment Header */
.payment-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    padding: 40px 20px;
    margin-top: -60px;
    padding-top: 100px;
}

/* Payment Section */
.payment-section {
    padding: 40px 20px;
    background: var(--neutral-100);
    min-height: calc(100vh - 200px);
}

/* Success State */
.success-container {
    background: white;
    border-radius: var(--border-radius);
    padding: 60px 40px;
    text-align: center;
    max-width: 600px;
    margin: 0 auto;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.success-icon {
    font-size: 72px;
    color: var(--success);
    margin-bottom: 20px;
    animation: scaleIn 0.5s ease-out;
}

@keyframes scaleIn {
    from {
        transform: scale(0);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

.success-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 12px;
}

.success-message {
    font-size: 16px;
    color: var(--text-secondary);
    margin-bottom: 30px;
}

.confirmation-details {
    background: var(--neutral-100);
    padding: 20px;
    border-radius: 6px;
    margin-bottom: 30px;
    text-align: left;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid var(--neutral-200);
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: var(--text-secondary);
}

.detail-value {
    font-weight: 600;
    color: var(--primary-color);
    font-family: monospace;
}

.badge-confirmed {
    display: inline-block;
    background-color: #e8f5e9;
    color: var(--success);
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 12px;
}

.actions-success {
    display: flex;
    gap: 15px;
    justify-content: center;
}

/* Payment Form */
.payment-form-container {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 30px;
    max-width: 1000px;
    margin: 0 auto;
}

.payment-card {
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

.booking-info {
    background: var(--neutral-100);
    padding: 20px;
    border-radius: 6px;
    margin-bottom: 25px;
}

.info-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--primary-color);
    text-transform: uppercase;
    margin-bottom: 12px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    font-size: 13px;
    border-bottom: 1px solid var(--neutral-200);
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: var(--text-secondary);
}

.info-value {
    color: var(--text-primary);
    font-weight: 500;
}

/* Payment Method Section */
.payment-method-section {
    margin-bottom: 25px;
}

.payment-method-item {
    position: relative;
    margin-bottom: 12px;
}

.payment-method-item input[type="radio"] {
    display: none;
}

.payment-method-item label {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border: 2px solid var(--neutral-300);
    border-radius: 6px;
    cursor: pointer;
    transition: all var(--transition-speed) ease;
    margin: 0;
}

.payment-method-item input[type="radio"]:checked + label {
    border-color: var(--primary-color);
    background-color: rgba(7, 157, 73, 0.05);
}

.payment-method-item i {
    font-size: 20px;
    color: var(--text-secondary);
    width: 30px;
}

.payment-method-item input[type="radio"]:checked + label i {
    color: var(--primary-color);
}

.payment-method-item span {
    font-weight: 500;
    color: var(--text-primary);
}

/* Card Form */
.card-form {
    background: var(--neutral-100);
    padding: 20px;
    border-radius: 6px;
    margin-bottom: 25px;
}

.card-form.hidden {
    display: none;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    font-weight: 600;
    font-size: 13px;
    margin-bottom: 6px;
    color: var(--text-primary);
}

.form-group input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--neutral-300);
    border-radius: 4px;
    font-size: 14px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

/* Payment Right Sidebar */
.payment-right {
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
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 20px;
    color: var(--text-primary);
}

.fare-summary {
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--neutral-200);
}

.fare-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-size: 13px;
}

.fare-item.total {
    font-weight: 700;
    font-size: 16px;
    color: var(--primary-color);
}

.fare-label {
    font-weight: 600;
    color: var(--text-secondary);
}

.fare-value {
    font-weight: 600;
    color: var(--text-primary);
}

.fare-item.total .fare-value {
    color: var(--primary-color);
}

.security-notice {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    background: #e8f5e9;
    border-radius: 4px;
    color: var(--success);
    font-size: 12px;
}

.security-notice i {
    font-size: 16px;
}

.btn-large {
    width: 100%;
    padding: 14px 20px;
}

/* Responsive */
@media (max-width: 1024px) {
    .payment-form-container {
        grid-template-columns: 1fr;
    }

    .summary-card {
        position: static;
    }
}

@media (max-width: 768px) {
    .success-container {
        padding: 40px 20px;
    }

    .success-icon {
        font-size: 56px;
    }

    .success-title {
        font-size: 22px;
    }

    .payment-card {
        padding: 20px;
    }

    .actions-success {
        flex-direction: column;
    }

    .actions-success .btn {
        width: 100%;
    }
}
</style>

<script>
$(document).ready(function() {
    // Toggle payment method form
    $('input[name="payment_gateway"]').on('change', function() {
        if ($(this).val() === 'card') {
            $('#cardFormContainer').removeClass('hidden');
        } else {
            $('#cardFormContainer').addClass('hidden');
        }
    });
    
    // Format card number input
    $('#cardNumber').on('input', function() {
        let value = $(this).val().replace(/\s/g, '');
        let formattedValue = value.replace(/(\d{4})/g, '$1 ').trim();
        $(this).val(formattedValue);
    });
    
    // Format expiry input
    $('#cardExpiry').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.slice(0, 2) + '/' + value.slice(2, 4);
        }
        $(this).val(value);
    });
});
</script>
