<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_admin();

$id = intval($_GET['id'] ?? 0);
$booking = null;

if ($id) {
    $booking = db_fetch("SELECT * FROM bookings WHERE id = ?", 'i', [$id]);
    if (!$booking) {
        flash('danger', 'Booking not found.');
        redirect('bookings.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = intval($_POST['customer_id'] ?? 0);
    $room_id     = intval($_POST['room_id'] ?? 0);
    $check_in    = $_POST['check_in'] ?? '';
    $check_out   = $_POST['check_out'] ?? '';
    $guests      = intval($_POST['guests'] ?? 1);
    $notes       = trim($_POST['notes'] ?? '');

    if (!$customer_id || !$room_id || !$check_in || !$check_out) {
        flash('danger', 'Please fill in all required fields.');
    } else {
        // Calculate total
        $room = db_fetch("SELECT * FROM rooms WHERE id = ?", 'i', [$room_id]);
        $nights = calc_nights($check_in, $check_out);
        $total = $room ? $room['price'] * $nights : 0;

        if ($id) {
            db_query(
                "UPDATE bookings SET customer_id=?, room_id=?, check_in=?, check_out=?, guests=?, total_amount=?, notes=? WHERE id=?",
                'iissidsi',
                [$customer_id, $room_id, $check_in, $check_out, $guests, $total, $notes, $id]
            );
            flash('success', 'Booking updated successfully.');
        } else {
            db_query(
                "INSERT INTO bookings (customer_id, room_id, check_in, check_out, guests, total_amount, notes, status) VALUES (?,?,?,?,?,?,?,'Confirmed')",
                'iissids',
                [$customer_id, $room_id, $check_in, $check_out, $guests, $total, $notes]
            );
            // Mark room as reserved
            db_query("UPDATE rooms SET status = 'Reserved' WHERE id = ?", 'i', [$room_id]);
            flash('success', 'Booking created successfully.');
        }
        redirect('bookings.php');
    }
}

$customers = db_fetch_all("SELECT * FROM customers ORDER BY name ASC");
$rooms = db_fetch_all("SELECT * FROM rooms WHERE status IN ('Available','Reserved') ORDER BY room_number ASC");
if ($booking) {
    // Also include the currently booked room
    $currentRoom = db_fetch("SELECT * FROM rooms WHERE id = ?", 'i', [$booking['room_id']]);
    $roomIds = array_column($rooms, 'id');
    if ($currentRoom && !in_array($currentRoom['id'], $roomIds)) {
        $rooms[] = $currentRoom;
    }
}

require_once '../includes/header.php';
?>

<div class="page-header">
    <div>
        <h2><?php echo $id ? 'Edit Booking' : 'New Booking'; ?></h2>
        <div class="breadcrumb">
            <a href="index.php">Dashboard</a> <span class="sep">›</span>
            <a href="bookings.php">Bookings</a> <span class="sep">›</span>
            <span><?php echo $id ? 'Edit' : 'New'; ?></span>
        </div>
    </div>
</div>

<div class="card form-card">
    <div class="card-header">
        <h3>Booking Details</h3>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="customer_id">Customer <span class="required">*</span></label>
                    <select name="customer_id" id="customer_id" class="form-control" required>
                        <option value="">Select customer...</option>
                        <?php foreach ($customers as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo ($booking['customer_id'] ?? '') == $c['id'] ? 'selected' : ''; ?>>
                            <?php echo e($c['name']); ?> (<?php echo e($c['phone']); ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="room_id">Room <span class="required">*</span></label>
                    <select name="room_id" id="room_id" class="form-control" required>
                        <option value="">Select room...</option>
                        <?php foreach ($rooms as $r): ?>
                        <option value="<?php echo $r['id']; ?>" <?php echo ($booking['room_id'] ?? '') == $r['id'] ? 'selected' : ''; ?>>
                            Room <?php echo e($r['room_number']); ?> — <?php echo e($r['type']); ?> (<?php echo format_currency($r['price']); ?>/night)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row-3">
                <div class="form-group">
                    <label for="check_in">Check-in Date <span class="required">*</span></label>
                    <input type="date" name="check_in" id="check_in" class="form-control" value="<?php echo e($booking['check_in'] ?? date('Y-m-d')); ?>" required>
                </div>
                <div class="form-group">
                    <label for="check_out">Check-out Date <span class="required">*</span></label>
                    <input type="date" name="check_out" id="check_out" class="form-control" value="<?php echo e($booking['check_out'] ?? date('Y-m-d', strtotime('+1 day'))); ?>" required>
                </div>
                <div class="form-group">
                    <label for="guests">Guests</label>
                    <input type="number" name="guests" id="guests" class="form-control" min="1" value="<?php echo e($booking['guests'] ?? 1); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea name="notes" id="notes" class="form-control" placeholder="Special requests, notes..."><?php echo e($booking['notes'] ?? ''); ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-accent">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    <?php echo $id ? 'Update Booking' : 'Create Booking'; ?>
                </button>
                <a href="bookings.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
