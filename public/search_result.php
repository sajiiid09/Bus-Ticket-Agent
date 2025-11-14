<?php
/**
 * Search Results Page - Display available buses for selected route
 */
require_once '../includes/head.php';
require_once '../app/repositories/BusRepository.php';

$busRepo = new BusRepository();
$from_city = isset($_POST['from_city']) ? $_POST['from_city'] : '';
$to_city = isset($_POST['to_city']) ? $_POST['to_city'] : '';
$journey_date = isset($_POST['journey_date']) ? $_POST['journey_date'] : '';

$trips = [];
if ($from_city && $to_city) {
    $trips = $busRepo->searchTrips($from_city, $to_city);
}
?>

<?php require_once '../includes/navbar.php'; ?>

<!-- Results Header -->
<section class="results-header">
    <div class="container">
        <div class="results-title">
            <h1 class="page-title">Available Buses</h1>
            <div class="results-meta">
                <span class="route-text"><strong><?php echo htmlspecialchars($from_city); ?></strong> to <strong><?php echo htmlspecialchars($to_city); ?></strong></span>
                <span class="date-text"> • <?php echo $journey_date ? date('D, M d, Y', strtotime($journey_date)) : 'Any Date'; ?></span>
            </div>
        </div>
        <div class="results-info">
            <p><?php echo count($trips); ?> buses available</p>
        </div>
    </div>
</section>

<!-- Progress Indicator -->
<section class="container" style="margin: 40px auto;">
    <div class="progress-steps">
        <div class="step done">
            <div class="step-number"><i class="fa fa-check"></i></div>
            <div class="step-label">Search</div>
        </div>
        <div class="step active">
            <div class="step-number">2</div>
            <div class="step-label">Select Bus</div>
        </div>
        <div class="step">
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

<!-- Bus Listings -->
<section class="results-section">
    <div class="container">
        <?php if (count($trips) > 0): ?>
            <div class="buses-list">
                <?php foreach ($trips as $trip): ?>
                <div class="bus-card">
                    <div class="bus-card-top">
                        <div class="bus-info">
                            <h3 class="bus-name"><?php echo htmlspecialchars($trip['bus_name']); ?></h3>
                            <p class="bus-company"><?php echo htmlspecialchars($trip['company']); ?></p>
                        </div>
                        <div class="bus-badge">
                            <span class="badge-coach">AC Coach</span>
                        </div>
                    </div>
                    
                    <div class="bus-card-middle">
                        <div class="time-row">
                            <div class="time-item">
                                <div class="time"><?php echo date('H:i', strtotime($trip['departure_time'])); ?></div>
                                <div class="location"><?php echo htmlspecialchars($trip['route_from']); ?></div>
                            </div>
                            <div class="duration">
                                <div class="line"></div>
                                <div class="duration-text">~8h</div>
                            </div>
                            <div class="time-item">
                                <div class="time"><?php echo date('H:i', strtotime($trip['arrival_time'])); ?></div>
                                <div class="location"><?php echo htmlspecialchars($trip['route_to']); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bus-card-bottom">
                        <div class="seats-info">
                            <span class="seats-available"><?php echo $trip['available_seats']; ?> seats available</span>
                        </div>
                        <div class="fare-section">
                            <span class="fare-label">From</span>
                            <span class="fare-amount">৳<?php echo number_format($trip['fare'], 0); ?></span>
                        </div>
                        <a class="btn-select-bus" href="seat_info.php?trip=<?php echo $trip['trip_no']; ?>">
                            Select Bus
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <div class="no-results-icon"><i class="fa fa-search"></i></div>
                <h2>No Buses Found</h2>
                <p>Sorry, no buses are available for this route. Please try another search.</p>
                <a href="index.php" class="btn btn-primary">Back to Search</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Static Route Map for reference -->
<section class="route-map-section" id="route-map">
    <div class="container">
        <div class="route-map-card">
            <div>
                <h2>Popular Route Map</h2>
                <p>Preview the coverage map for major intercity routes before finalizing your booking.</p>
            </div>
            <div class="route-map-preview">
                <img src="placeholder.svg" alt="Routes map preview" />
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>

<style>
/* Results Header Styling */
.results-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    padding: 40px 20px;
    margin-top: -60px;
    padding-top: 100px;
}

.results-title {
    margin-bottom: 15px;
}

.page-title {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 8px;
}

.results-meta {
    font-size: 16px;
    opacity: 0.95;
}

.route-text, .date-text {
    font-weight: 500;
}

.results-info {
    margin-top: 10px;
    font-size: 14px;
    opacity: 0.9;
}

/* Bus Card Styling */
.results-section {
    padding: 40px 20px;
    background: var(--neutral-100);
}

.buses-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
    max-width: 1000px;
    margin: 0 auto;
}

.bus-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all var(--transition-speed) ease;
    border-left: 4px solid var(--primary-color);
}

.bus-card:hover {
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.bus-card-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--neutral-200);
}

.bus-info h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
}

.bus-company {
    font-size: 13px;
    color: var(--text-secondary);
    margin: 3px 0 0 0;
}

.badge-coach {
    display: inline-block;
    background-color: #e8f5e9;
    color: var(--primary-color);
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.bus-card-middle {
    margin-bottom: 20px;
}

.time-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
}

.time-item {
    flex: 1;
}

.time {
    font-size: 20px;
    font-weight: 700;
    color: var(--text-primary);
}

.location {
    font-size: 13px;
    color: var(--text-secondary);
    margin-top: 4px;
}

.duration {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.line {
    width: 40px;
    height: 2px;
    background-color: var(--neutral-300);
}

.duration-text {
    font-size: 12px;
    color: var(--text-secondary);
    white-space: nowrap;
}

.bus-card-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
}

.seats-info {
    flex: 1;
}

.seats-available {
    font-size: 13px;
    color: var(--success);
    font-weight: 600;
}

.fare-section {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    min-width: 100px;
}

.fare-label {
    font-size: 11px;
    color: var(--text-secondary);
    text-transform: uppercase;
}

.fare-amount {
    font-size: 24px;
    font-weight: 700;
    color: var(--primary-color);
}

.btn-select-bus {
    background-color: var(--primary-color);
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-speed) ease;
    white-space: nowrap;
}

.btn-select-bus:hover {
    background-color: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(7, 157, 73, 0.3);
}

/* No Results */
.no-results {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: var(--border-radius);
}

.no-results-icon {
    font-size: 64px;
    color: var(--neutral-300);
    margin-bottom: 20px;
}

.no-results h2 {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 12px;
}

.no-results p {
    color: var(--text-secondary);
    margin-bottom: 30px;
}

/* Progress Steps - Done State */
.step.done .step-number {
    background-color: var(--success);
    color: white;
}

@media (max-width: 768px) {
    .page-title {
        font-size: 24px;
    }

    .results-meta {
        font-size: 14px;
    }

    .time-row {
        gap: 10px;
    }

    .bus-card-bottom {
        flex-wrap: wrap;
        gap: 15px;
    }

    .btn-select-bus {
        width: 100%;
    }

    .fare-section {
        order: -1;
        width: 100%;
        align-items: flex-start;
    }
}
</style>
