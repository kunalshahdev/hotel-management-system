<?php
$pageTitle = 'Book Room';
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

if (!is_customer_logged_in()) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    flash('info', 'Please sign in or register to book a room.');
    redirect('/Hotel_Reservation_System/login.php');
}

$customer = current_customer();
$room_id = intval($_GET['room_id'] ?? 0);
$room = null;

if ($room_id) {
    $room = db_fetch("SELECT * FROM rooms WHERE id = ? AND status = 'Available'", 'i', [$room_id]);
}

if (!$room) {
    flash('danger', 'Room not available or not found.');
    redirect('rooms.php');
}

$checkIn  = $_GET['check_in'] ?? date('Y-m-d');
$checkOut = $_GET['check_out'] ?? date('Y-m-d', strtotime('+1 day'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $check_in  = $_POST['check_in'] ?? '';
    $check_out = $_POST['check_out'] ?? '';
    $guests    = intval($_POST['guests'] ?? 1);
    $notes     = trim($_POST['notes'] ?? '');

    if (!$check_in || !$check_out || strtotime($check_out) <= strtotime($check_in)) {
        flash('danger', 'Please select valid check-in and check-out dates.');
    } elseif ($guests < 1 || $guests > $room['max_guests']) {
        flash('danger', 'Guests must be between 1 and ' . $room['max_guests'] . '.');
    } else {
        $nights = calc_nights($check_in, $check_out);
        $total = $room['price'] * $nights;

        db_query(
            "INSERT INTO bookings (customer_id, room_id, check_in, check_out, guests, total_amount, notes, status) VALUES (?,?,?,?,?,?,?,'Confirmed')",
            'iissids',
            [$customer['id'], $room['id'], $check_in, $check_out, $guests, $total, $notes]
        );
        // Mark room as reserved
        db_query("UPDATE rooms SET status = 'Reserved' WHERE id = ?", 'i', [$room['id']]);

        flash('success', 'Room booked successfully! Your booking ID is ' . booking_id(db_insert_id()) . '.');
        redirect('bookings.php');
    }

    $checkIn = $check_in;
    $checkOut = $check_out;
}

$typeGradients = [
    'Standard' => 'linear-gradient(135deg, #3498DB, #2471A3)',
    'Deluxe'   => 'linear-gradient(135deg, #DC143C, #B01030)',
    'Suite'    => 'linear-gradient(135deg, #003893, #002266)',
    'Family'   => 'linear-gradient(135deg, #2ECC71, #27AE60)',
];

require_once '../includes/customer_header.php';
$nights = calc_nights($checkIn, $checkOut);
$total  = $room['price'] * $nights;
?>

<div class="c-page">
    <div class="c-page-header">
        <h1>Book Room <?php echo e($room['room_number']); ?></h1>
        <p>Complete your reservation</p>
    </div>

    <div class="c-booking-card">
        <!-- Room Info -->
        <div class="c-booking-header">
            <div class="c-booking-room-badge" style="background: <?php echo $typeGradients[$room['type']] ?? '#3498DB'; ?>; font-size: 0.7rem;">
                <?php echo e($room['type']); ?>
            </div>
            <div class="c-booking-room-info">
                <h3>Room <?php echo e($room['room_number']); ?> — <?php echo e($room['type']); ?></h3>
                <p>Floor <?php echo e($room['floor']); ?> · Max <?php echo e($room['max_guests']); ?> guests · <?php echo format_currency($room['price']); ?>/night</p>
            </div>
        </div>

        <!-- Booking Form -->
        <div class="c-booking-body">
            <form method="POST">
                <input type="hidden" id="bookPricePerNight" value="<?php echo $room['price']; ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label for="bookCheckIn">Check-in Date <span class="required">*</span></label>
                        <input type="date" name="check_in" id="bookCheckIn" class="form-control" value="<?php echo e($checkIn); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="bookCheckOut">Check-out Date <span class="required">*</span></label>
                        <input type="date" name="check_out" id="bookCheckOut" class="form-control" value="<?php echo e($checkOut); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="guests">Number of Guests <span class="required">*</span></label>
                    <select name="guests" id="guests" class="form-control">
                        <?php for ($g = 1; $g <= $room['max_guests']; $g++): ?>
                        <option value="<?php echo $g; ?>" <?php echo $g == 1 ? 'selected' : ''; ?>><?php echo $g; ?> Guest<?php echo $g > 1 ? 's' : ''; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="notes">Special Requests (optional)</label>
                    <textarea name="notes" id="notes" class="form-control" placeholder="Any special requests? Early check-in, extra pillows, etc."></textarea>
                </div>

                <!-- Price Summary -->
                <div class="c-booking-summary">
                    <div class="c-summary-row">
                        <span>Room Rate</span>
                        <span><?php echo format_currency($room['price']); ?> / night</span>
                    </div>
                    <div class="c-summary-row">
                        <span>Duration</span>
                        <span><span id="bookNights"><?php echo $nights; ?></span> night(s)</span>
                    </div>
                    <div class="c-summary-row total">
                        <span>Total Amount</span>
                        <span class="c-summary-amount" id="bookTotal"><?php echo format_currency($total); ?></span>
                    </div>
                </div>

                <div class="form-actions" style="border:none;padding-top:24px;">
                    <button type="submit" class="c-btn c-btn-accent c-btn-lg" style="flex:1;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        Confirm Booking
                    </button>
                    <a href="rooms.php" class="c-btn c-btn-primary" style="padding:14px 24px;">Back to Rooms</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/customer_footer.php'; ?>
