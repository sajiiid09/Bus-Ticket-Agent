<?php
/**
 * Homepage - Bus Search and Booking
 */
require_once '../includes/head.php';
require_once '../app/repositories/BusRepository.php';

$busRepo = new BusRepository();
$buses = $busRepo->getAllBuses();
?>

<?php require_once '../includes/navbar.php'; ?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1 class="hero-title">Book Your Bus Ticket Online</h1>
        <p class="hero-subtitle">Safe, convenient, and affordable bus travel across Bangladesh</p>
    </div>
</section>

<!-- Search Form -->
<div class="search-form-container">
    <form method="POST" action="search_result.php" class="search-form">
        <div class="search-form-row">
            <div class="form-group">
                <label for="from">From City</label>
                <input type="text" id="from" name="from_city" placeholder="e.g., Dhaka" required>
            </div>
            <div class="form-group">
                <label for="to">To City</label>
                <input type="text" id="to" name="to_city" placeholder="e.g., Sylhet" required>
            </div>
            <div class="form-group">
                <label for="date">Journey Date</label>
                <input type="date" id="date" name="journey_date" required>
            </div>
        </div>
        <button type="submit" class="btn-search">Search Buses</button>
    </form>
</div>

<!-- Trip Progress Indicator -->
<section class="container" style="margin: 60px auto;">
    <div class="progress-steps">
        <div class="step active">
            <div class="step-number">1</div>
            <div class="step-label">Search</div>
        </div>
        <div class="step">
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

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <h2 class="section-title">Why Choose BusTicket?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fa fa-lock"></i></div>
                <h3 class="feature-title">Safe Payment</h3>
                <p class="feature-description">Secure online payment with encryption and fraud protection</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa fa-clock-o"></i></div>
                <h3 class="feature-title">24/7 Access</h3>
                <p class="feature-description">Book tickets anytime, anywhere from your device</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa fa-undo"></i></div>
                <h3 class="feature-title">Easy Cancellation</h3>
                <p class="feature-description">Cancel or modify your booking with full refund policy</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa fa-check"></i></div>
                <h3 class="feature-title">Instant Confirmation</h3>
                <p class="feature-description">Get instant booking confirmation via email and SMS</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa fa-phone"></i></div>
                <h3 class="feature-title">Support</h3>
                <p class="feature-description">24/7 customer support for all your queries</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fa fa-star"></i></div>
                <h3 class="feature-title">Best Prices</h3>
                <p class="feature-description">Compare and book at the best prices available</p>
            </div>
        </div>
    </div>
</section>

<!-- Bus Operators Section -->
<section class="operators-section" id="operators">
    <div class="container">
        <h2 class="section-title">Top Bus Operators</h2>
        <div class="operators-carousel">
            <?php foreach ($buses as $bus): ?>
            <div class="operator-card">
                <div class="operator-logo"><i class="fa fa-bus"></i></div>
                <div class="operator-name"><?php echo htmlspecialchars($bus['company']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Routes Section -->
<section class="routes-section">
    <div class="container">
        <h2 class="section-title">Popular Routes</h2>
        <div class="routes-container">
            <div class="route-card">
                <div class="route-cities">Dhaka <span class="route-arrow">→</span> Sylhet</div>
                <p class="route-description">10+ buses daily</p>
            </div>
            <div class="route-card">
                <div class="route-cities">Dhaka <span class="route-arrow">→</span> Chattogram</div>
                <p class="route-description">15+ buses daily</p>
            </div>
            <div class="route-card">
                <div class="route-cities">Dhaka <span class="route-arrow">→</span> Barishal</div>
                <p class="route-description">8+ buses daily</p>
            </div>
            <div class="route-card">
                <div class="route-cities">Dhaka <span class="route-arrow">→</span> Khulna</div>
                <p class="route-description">6+ buses daily</p>
            </div>
        </div>
    </div>
</section>

<!-- Payment Methods -->
<section class="payment-section">
    <p style="margin-bottom: 20px; font-weight: 600; color: var(--text-primary);">We Accept Multiple Payment Methods</p>
    <div class="payment-methods">
        <div class="payment-badge"><i class="fa fa-money"></i> Cash on Booking</div>
        <div class="payment-badge"><i class="fa fa-credit-card"></i> Credit Card</div>
        <div class="payment-badge"><i class="fa fa-mobile"></i> bKash</div>
        <div class="payment-badge"><i class="fa fa-mobile"></i> Rocket</div>
        <div class="payment-badge"><i class="fa fa-mobile"></i> Nagad</div>
    </div>
</section>

<?php require_once '../includes/modals.php'; ?>
<?php require_once '../includes/footer.php'; ?>

<script>
// Navbar shrink effect
$(window).scroll(function() {
    if ($(document).scrollTop() > 50) {
        $('#main-navbar').addClass('shrink');
    } else {
        $('#main-navbar').removeClass('shrink');
    }
});

// Smooth scrolling for anchor links
$('a[href*="#"]').on('click', function (e) {
    let target = $(this.getAttribute('href'));
    if (target.length) {
        e.preventDefault();
        $('html, body').stop().animate({
            scrollTop: target.offset().top - 80
        }, 800);
    }
});
</script>
