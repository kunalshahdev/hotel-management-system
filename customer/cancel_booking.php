<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_customer();

$customer = current_customer();
$id = intval($_GET['id'] ?? 0);

if ($id) {
    // Verify booking belongs to this customer and is cancellable
    $booking = db_fetch("SELECT * FROM bookings WHERE id = ? AND customer_id = ? AND status = 'Confirmed'", 'ii', [$id, $customer['id']]);
    if ($booking) {
        db_query("UPDATE bookings SET status = 'Cancelled' WHERE id = ?", 'i', [$id]);
        db_query("UPDATE rooms SET status = 'Available' WHERE id = ?", 'i', [$booking['room_id']]);
        flash('success', 'Booking ' . booking_id($id) . ' has been cancelled.');
    } else {
        flash('danger', 'Unable to cancel this booking.');
    }
}

redirect('bookings.php');
