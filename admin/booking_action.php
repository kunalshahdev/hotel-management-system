<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_admin();

$action = $_GET['action'] ?? '';
$id = intval($_GET['id'] ?? 0);

if (!$id || !$action) {
    redirect('bookings.php');
}

$booking = db_fetch("SELECT * FROM bookings WHERE id = ?", 'i', [$id]);
if (!$booking) {
    flash('danger', 'Booking not found.');
    redirect('bookings.php');
}

switch ($action) {
    case 'checkin':
        if ($booking['status'] === 'Confirmed') {
            db_query("UPDATE bookings SET status = 'Checked-In', checked_in_at = NOW() WHERE id = ?", 'i', [$id]);
            db_query("UPDATE rooms SET status = 'Occupied' WHERE id = ?", 'i', [$booking['room_id']]);
            flash('success', 'Guest checked in successfully.');
        }
        break;

    case 'checkout':
        if ($booking['status'] === 'Checked-In') {
            db_query("UPDATE bookings SET status = 'Checked-Out', checked_out_at = NOW() WHERE id = ?", 'i', [$id]);
            db_query("UPDATE rooms SET status = 'Available' WHERE id = ?", 'i', [$booking['room_id']]);

            // Auto create payment record if not exists
            $existing = db_value("SELECT COUNT(*) FROM payments WHERE booking_id = ?", 'i', [$id]);
            if (!$existing) {
                db_query(
                    "INSERT INTO payments (booking_id, amount, payment_method, payment_date, status) VALUES (?, ?, 'Cash', CURDATE(), 'Paid')",
                    'id', [$id, $booking['total_amount']]
                );
            }
            flash('success', 'Guest checked out successfully. Payment recorded.');
        }
        break;

    case 'cancel':
        if ($booking['status'] === 'Confirmed') {
            db_query("UPDATE bookings SET status = 'Cancelled' WHERE id = ?", 'i', [$id]);
            db_query("UPDATE rooms SET status = 'Available' WHERE id = ?", 'i', [$booking['room_id']]);
            flash('warning', 'Booking cancelled.');
        }
        break;
}

redirect('bookings.php');
