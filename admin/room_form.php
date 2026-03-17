<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_admin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$room = null;

if ($id) {
    $room = db_fetch("SELECT * FROM rooms WHERE id = ?", 'i', [$id]);
    if (!$room) {
        flash('danger', 'Room not found.');
        redirect('rooms.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_number = trim($_POST['room_number'] ?? '');
    $type        = $_POST['type'] ?? 'Standard';
    $floor       = intval($_POST['floor'] ?? 1);
    $price       = floatval($_POST['price'] ?? 0);
    $max_guests  = intval($_POST['max_guests'] ?? 2);
    $status      = $_POST['status'] ?? 'Available';
    $description = trim($_POST['description'] ?? '');
    $amenities   = trim($_POST['amenities'] ?? '');

    if (empty($room_number) || $price <= 0) {
        flash('danger', 'Room number and price are required.');
    } else {
        if ($id) {
            // Update
            db_query(
                "UPDATE rooms SET room_number=?, type=?, floor=?, price=?, max_guests=?, status=?, description=?, amenities=? WHERE id=?",
                'ssiidsssi',
                [$room_number, $type, $floor, $price, $max_guests, $status, $description, $amenities, $id]
            );
            flash('success', "Room $room_number updated successfully.");
        } else {
            // Insert
            db_query(
                "INSERT INTO rooms (room_number, type, floor, price, max_guests, status, description, amenities) VALUES (?,?,?,?,?,?,?,?)",
                'ssidisss',
                [$room_number, $type, $floor, $price, $max_guests, $status, $description, $amenities]
            );
            flash('success', "Room $room_number added successfully.");
        }
        redirect('rooms.php');
    }
}

require_once '../includes/header.php';
?>

<div class="page-header">
    <div>
        <h2><?php echo $id ? 'Edit Room' : 'Add Room'; ?></h2>
        <div class="breadcrumb">
            <a href="index.php">Dashboard</a> <span class="sep">›</span>
            <a href="rooms.php">Rooms</a> <span class="sep">›</span>
            <span><?php echo $id ? 'Edit' : 'Add'; ?></span>
        </div>
    </div>
</div>

<div class="card form-card">
    <div class="card-header">
        <h3>Room Details</h3>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="room_number">Room Number <span class="required">*</span></label>
                    <input type="text" name="room_number" id="room_number" class="form-control" placeholder="e.g. 101" value="<?php echo e($room['room_number'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="type">Room Type <span class="required">*</span></label>
                    <select name="type" id="type" class="form-control">
                        <?php foreach (['Standard','Deluxe','Suite','Family'] as $t): ?>
                        <option value="<?php echo $t; ?>" <?php echo ($room['type'] ?? '') === $t ? 'selected' : ''; ?>><?php echo $t; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row-3">
                <div class="form-group">
                    <label for="floor">Floor</label>
                    <input type="number" name="floor" id="floor" class="form-control" min="1" value="<?php echo e($room['floor'] ?? 1); ?>">
                </div>
                <div class="form-group">
                    <label for="price">Price per Night (₹) <span class="required">*</span></label>
                    <input type="number" name="price" id="price" step="0.01" class="form-control" placeholder="0.00" value="<?php echo e($room['price'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="max_guests">Max Guests</label>
                    <input type="number" name="max_guests" id="max_guests" class="form-control" min="1" value="<?php echo e($room['max_guests'] ?? 2); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control">
                    <?php foreach (['Available','Occupied','Maintenance','Reserved'] as $s): ?>
                    <option value="<?php echo $s; ?>" <?php echo ($room['status'] ?? 'Available') === $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control" placeholder="Room description..."><?php echo e($room['description'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="amenities">Amenities</label>
                <input type="text" name="amenities" id="amenities" class="form-control" placeholder="WiFi, TV, AC, Mini Bar..." value="<?php echo e($room['amenities'] ?? ''); ?>">
                <span class="form-hint">Comma-separated list of amenities</span>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-accent">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    <?php echo $id ? 'Update Room' : 'Add Room'; ?>
                </button>
                <a href="rooms.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
