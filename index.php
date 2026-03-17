<?php
$pageTitle = 'Home';
require_once 'includes/customer_header.php';

$featuredRooms = db_fetch_all("SELECT * FROM rooms WHERE status = 'Available' ORDER BY price DESC LIMIT 4");
$totalRooms = db_value("SELECT COUNT(*) FROM rooms");
$happyGuests = db_value("SELECT COUNT(DISTINCT customer_id) FROM bookings WHERE status = 'Checked-Out'");

$typeGradients = [
    'Standard' => 'linear-gradient(135deg, #3498DB, #2471A3)',
    'Deluxe'   => 'linear-gradient(135deg, #DC143C, #B01030)',
    'Suite'    => 'linear-gradient(135deg, #003893, #002266)',
    'Family'   => 'linear-gradient(135deg, #2ECC71, #27AE60)',
];
?>


<section class="c-hero">
    <div class="c-hero-content">
        <div class="c-hero-tag">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            Nepal's Premium Hospitality
        </div>
        <h1>Nepal's Finest <span>Hotel Experience</span></h1>
        <p>From the heart of the Himalayas — premium rooms, warm Nepali hospitality, and seamless booking. Welcome to NepStay.</p>
        <div class="c-hero-actions">
            <a href="customer/rooms.php" class="c-btn c-btn-accent c-btn-lg">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Browse Rooms
            </a>
            <?php if (!$isLoggedIn): ?>
            <a href="register.php" class="c-btn c-btn-outline-light c-btn-lg">Create Account</a>
            <?php endif; ?>
        </div>
        <div class="c-hero-stats">
            <div class="c-hero-stat">
                <div class="c-hero-stat-value"><?php echo $totalRooms; ?>+</div>
                <div class="c-hero-stat-label">Premium Rooms</div>
            </div>
            <div class="c-hero-stat">
                <div class="c-hero-stat-value"><?php echo max($happyGuests, 50); ?>+</div>
                <div class="c-hero-stat-label">Happy Guests</div>
            </div>
            <div class="c-hero-stat">
                <div class="c-hero-stat-value">4.8</div>
                <div class="c-hero-stat-label">Guest Rating</div>
            </div>
        </div>
    </div>
</section>

<section class="c-section">
    <div class="c-section-header">
        <h2>Featured Rooms</h2>
        <p>Hand-picked rooms designed for comfort and luxury</p>
    </div>

    <div class="c-rooms-grid">
        <?php foreach ($featuredRooms as $room): ?>
        <div class="c-room-card">
            <div class="c-room-img" style="background: <?php echo $typeGradients[$room['type']] ?? $typeGradients['Standard']; ?>">
                <span class="c-room-img-inner"><?php echo e($room['type']); ?></span>
                <div class="c-room-badge">
                    <span class="badge badge-success"><span class="badge-dot"></span> Available</span>
                </div>
                <span class="c-room-type-badge"><?php echo e($room['type']); ?></span>
            </div>
            <div class="c-room-info">
                <div class="c-room-name">Room <?php echo e($room['room_number']); ?> — <?php echo e($room['type']); ?></div>
                <div class="c-room-features">
                    <span class="c-room-feature">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
                        Floor <?php echo e($room['floor']); ?>
                    </span>
                    <span class="c-room-feature">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        <?php echo e($room['max_guests']); ?> Guests
                    </span>
                </div>
                <?php if ($room['amenities']): ?>
                <div class="c-room-amenities">
                    <?php foreach (array_slice(explode(',', $room['amenities']), 0, 3) as $a): ?>
                    <span class="c-amenity-tag"><?php echo e(trim($a)); ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <div class="c-room-footer">
                    <div class="c-room-price"><?php echo format_currency($room['price']); ?> <span>/night</span></div>
                    <a href="customer/book.php?room_id=<?php echo $room['id']; ?>" class="c-btn c-btn-accent">Book Now</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div style="text-align:center;margin-top:32px;">
        <a href="customer/rooms.php" class="c-btn c-btn-primary c-btn-lg">View All Rooms →</a>
    </div>
</section>

<section class="c-section" style="background: var(--surface); padding: 80px 24px;">
    <div style="max-width:1200px;margin:0 auto;">
        <div class="c-section-header">
            <h2>Why Choose NepStay?</h2>
            <p>Born in the Himalayas, built for travelers who seek authentic Nepali hospitality</p>
        </div>

        <div class="c-features-grid">
            <div class="c-feature-card">
                <div class="c-feature-icon" style="background: rgba(220,20,60,0.1); color: var(--accent);">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h3>Instant Booking</h3>
                <p>Book rooms across Nepal in seconds — from Kathmandu to Pokhara, Chitwan to Lumbini. No delays, just confirm and go.</p>
            </div>
            <div class="c-feature-card">
                <div class="c-feature-icon" style="background: rgba(52,152,219,0.1); color: var(--info);">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3>Secure & Safe</h3>
                <p>Your data and payments are protected with industry-standard security measures.</p>
            </div>
            <div class="c-feature-card">
                <div class="c-feature-icon" style="background: rgba(46,204,113,0.1); color: var(--success);">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <h3>Himalayan Comfort</h3>
                <p>Rooms with breathtaking mountain views, warm interiors, and world-class amenities rooted in Nepali tradition.</p>
            </div>
            <div class="c-feature-card">
                <div class="c-feature-icon" style="background: rgba(155,89,182,0.1); color: #9B59B6;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                </div>
                <h3>Nepali Warmth 24/7</h3>
                <p>Our atithi devo bhava spirit means round-the-clock care. Need help? Our Nepali team is always here for you.</p>
            </div>
        </div>
    </div>
</section>

<div style="max-width:1200px;margin:0 auto;padding:0 24px;">
    <div class="c-cta">
        <h2>Experience Nepal Like Never Before</h2>
        <p>Join thousands of travelers who trust NepStay for their Himalayan adventures.</p>
        <?php if ($isLoggedIn): ?>
        <a href="customer/rooms.php" class="c-btn c-btn-accent c-btn-lg">Browse Rooms</a>
        <?php else: ?>
        <a href="register.php" class="c-btn c-btn-accent c-btn-lg">Get Started Free</a>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/customer_footer.php'; ?>