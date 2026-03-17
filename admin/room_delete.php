<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_admin();

$id = intval($_GET['id'] ?? 0);
if ($id) {
    // Check if room has active bookings
    $active = db_value("SELECT COUNT(*) FROM bookings WHERE room_id = ? AND status IN ('Confirmed','Checked-In')", 'i', [$id]);
    if ($active > 0) {
        flash('danger', 'Cannot delete room with active bookings.');
    } else {
        db_query("DELETE FROM rooms WHERE id = ?", 'i', [$id]);
        flash('success', 'Room deleted successfully.');
    }
}
redirect('rooms.php');
