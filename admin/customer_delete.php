<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_admin();

$id = intval($_GET['id'] ?? 0);
if ($id) {
    $active = db_value("SELECT COUNT(*) FROM bookings WHERE customer_id = ? AND status IN ('Confirmed','Checked-In')", 'i', [$id]);
    if ($active > 0) {
        flash('danger', 'Cannot delete customer with active bookings.');
    } else {
        db_query("DELETE FROM customers WHERE id = ?", 'i', [$id]);
        flash('success', 'Customer deleted successfully.');
    }
}
redirect('customers.php');
