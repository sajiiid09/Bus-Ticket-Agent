<?php
/**
 * Printable Ticket Page
 */
require_once __DIR__ . '/../includes/head.php';
require_once __DIR__ . '/../app/services/ReservationService.php';
require_once __DIR__ . '/../app/repositories/BusRepository.php';

$booking_id = isset($_GET['booking']) ? intval($_GET['booking']) : 0;
$resService = new ReservationService();
$busRepo = new BusRepository();

$booking = null;
$trip = null;

if ($booking_id) {
    $booking = $resService->getBooking($booking_id);
    if ($booking) {
        $trip = $busRepo->getTripDetails($booking['trip_no']);
    }
}

if (!$booking || $booking['status'] !== 'confirmed') {
    header('Location: index.php');
    exit;
}

$seats_array = array_filter(array_map('trim', explode(',', $booking['selected_seats'])));
?>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<section class="ticket-section-wrapper">
    <div class="container">
        <div class="ticket-container">
            <div class="ticket-header">
                <div class="logo">BusTicket</div>
                <div class="ticket-number">Ticket #BK<?php echo str_pad($booking_id, 6, '0', STR_PAD_LEFT); ?></div>
            </div>

            <div class="ticket-body">
                <div class="ticket-row">
                    <div class="ticket-section">
                        <div class="ticket-field">
                            <span class="field-label">Passenger Name</span>
                            <span class="field-value"><?php echo htmlspecialchars($booking['passenger_name']); ?></span>
                        </div>
                    </div>
                    <div class="ticket-section">
                        <div class="ticket-field">
                            <span class="field-label">Mobile</span>
                            <span class="field-value"><?php echo htmlspecialchars($booking['mobile']); ?></span>
                        </div>
                    </div>
                </div>

                <div class="ticket-row">
                    <div class="ticket-section">
                        <div class="ticket-field">
                            <span class="field-label">Bus Name</span>
                            <span class="field-value"><?php echo htmlspecialchars($trip['bus_name']); ?></span>
                        </div>
                    </div>
                    <div class="ticket-section">
                        <div class="ticket-field">
                            <span class="field-label">Company</span>
                            <span class="field-value"><?php echo htmlspecialchars($trip['company']); ?></span>
                        </div>
                    </div>
                </div>

                <div class="ticket-row">
                    <div class="route-section">
                        <div class="location-box">
                            <div class="time-large"><?php echo date('H:i', strtotime($trip['departure_time'])); ?></div>
                            <div class="location-name"><?php echo htmlspecialchars($trip['route_from']); ?></div>
                        </div>
                        <div class="separator">
                            <div class="separator-line"></div>
                            <div style="font-size: 12px;">~8 hours</div>
                            <div class="separator-line"></div>
                        </div>
                        <div class="location-box">
                            <div class="time-large"><?php echo date('H:i', strtotime($trip['arrival_time'])); ?></div>
                            <div class="location-name"><?php echo htmlspecialchars($trip['route_to']); ?></div>
                        </div>
                    </div>
                </div>

                <div class="ticket-row">
                    <div class="ticket-section">
                        <div class="ticket-field">
                            <span class="field-label">Selected Seats</span>
                            <div class="seats-display">
                                <?php foreach ($seats_array as $seat): ?>
                                    <span class="seat-badge"><?php echo htmlspecialchars($seat); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="ticket-section">
                        <div class="ticket-field">
                            <span class="field-label">Boarding Point</span>
                            <span class="field-value"><?php echo htmlspecialchars($booking['boarding_point']); ?></span>
                        </div>
                    </div>
                </div>

                <div class="ticket-row">
                    <div class="fare-section">
                        <div class="fare-items">
                            <div class="fare-item">
                                <div class="field-label">Payment Method</div>
                                <div class="field-value"><?php echo htmlspecialchars($booking['payment_method']); ?></div>
                            </div>
                            <div class="fare-item">
                                <div class="field-label">Transaction ID</div>
                                <div class="field-value"><?php echo htmlspecialchars($booking['transaction_id']); ?></div>
                            </div>
                            <div class="fare-item">
                                <div class="field-label">Total Fare</div>
                                <div class="fare-amount">৳<?php echo number_format($booking['total_fare'], 0); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ticket-footer">
                <p>Thank you for booking with BusTicket! Please carry this ticket with you on the day of travel.</p>
                <p>Booking Date: <?php echo date('M d, Y H:i', strtotime($booking['created_at'])); ?></p>
            </div>
        </div>

        <div class="print-button">
            <button class="btn btn-print" onclick="window.print()"><i class="fa fa-print"></i> Print Ticket</button>
            <a href="index.php" class="btn btn-home">Book Another</a>
        </div>
    </div>
</section>

<style>
body {
    font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.ticket-section-wrapper {
    padding: 60px 0;
    background: var(--neutral-100);
}

.ticket-container {
    max-width: 900px;
    margin: 0 auto;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
}

.ticket-header {
    background: linear-gradient(135deg, #079d49 0%, #067638 100%);
    color: white;
    padding: 30px;
    text-align: center;
}

.ticket-body {
    padding: 30px;
}

.ticket-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
    padding-bottom: 30px;
    border-bottom: 2px dashed #e0e0e0;
}

.ticket-row:last-child {
    border-bottom: none;
}

.ticket-section {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.ticket-field {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.field-label {
    font-size: 12px;
    font-weight: 700;
    color: #666;
    text-transform: uppercase;
}

.field-value {
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

.route-section {
    grid-column: 1 / -1;
    background: #f9f9f9;
    padding: 20px;
    border-radius: 6px;
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    gap: 20px;
    align-items: center;
}

.location-box {
    text-align: center;
}

.time-large {
    font-size: 24px;
    font-weight: 700;
    color: #333;
}

.location-name {
    font-size: 14px;
    color: #666;
    margin-top: 5px;
}

.separator {
    text-align: center;
    color: #ccc;
}

.separator-line {
    width: 40px;
    height: 2px;
    background: #ccc;
    margin: 10px auto;
}

.seats-display {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}

.seat-badge {
    background: #079d49;
    color: white;
    padding: 8px 14px;
    border-radius: 4px;
    font-weight: 600;
    font-size: 13px;
}

.fare-section {
    grid-column: 1 / -1;
    background: #e8f5e9;
    padding: 20px;
    border-radius: 6px;
    border-left: 4px solid #079d49;
}

.fare-items {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.fare-amount {
    font-size: 18px;
    font-weight: 700;
    color: #079d49;
}

.ticket-footer {
    background: #f5f5f5;
    padding: 20px 30px;
    text-align: center;
    font-size: 12px;
    color: #666;
    border-top: 1px solid #e0e0e0;
}

.print-button {
    display: flex;
    justify-content: center;
    gap: 15px;
    padding: 20px;
}

.btn-print {
    background: #079d49;
    color: white;
}

.btn-home {
    background: #e0e0e0;
    color: #333;
}

@media print {
    nav,
    .print-button,
    .footer,
    .progress-steps { display: none !important; }

    body {
        background: white;
        padding: 0;
    }

    .ticket-section-wrapper {
        padding: 0;
    }

    .ticket-container {
        box-shadow: none;
        margin: 0;
    }
}

@media (max-width: 600px) {
    .ticket-row {
        grid-template-columns: 1fr;
    }

    .route-section {
        grid-template-columns: 1fr;
    }

    .separator-line {
        display: none;
    }

    .fare-items {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
