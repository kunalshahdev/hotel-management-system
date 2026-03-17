<?php
require_once '../includes/header.php';

$totalRooms    = db_value("SELECT COUNT(*) FROM rooms");
$availableRooms = db_value("SELECT COUNT(*) FROM rooms WHERE status = 'Available'");
$activeBookings = db_value("SELECT COUNT(*) FROM bookings WHERE status IN ('Confirmed','Checked-In')");
$totalRevenue  = db_value("SELECT COALESCE(SUM(amount),0) FROM payments WHERE status = 'Paid'");
$totalCustomers = db_value("SELECT COUNT(*) FROM customers");
$occupiedRooms = db_value("SELECT COUNT(*) FROM rooms WHERE status = 'Occupied'");

$recentBookings = db_fetch_all("
    SELECT b.*, c.name AS customer_name, r.room_number, r.type AS room_type
    FROM bookings b
    JOIN customers c ON b.customer_id = c.id
    JOIN rooms r ON b.room_id = r.id
    ORDER BY b.created_at DESC
    LIMIT 8
");

$occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;

$confirmedCount  = db_value("SELECT COUNT(*) FROM bookings WHERE status = 'Confirmed'");
$checkedInCount  = db_value("SELECT COUNT(*) FROM bookings WHERE status = 'Checked-In'");
$checkedOutCount = db_value("SELECT COUNT(*) FROM bookings WHERE status = 'Checked-Out'");
$cancelledCount  = db_value("SELECT COUNT(*) FROM bookings WHERE status = 'Cancelled'");
?>

<div class="stats-grid">
    <div class="stat-card stat-accent">
        <div class="stat-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 20v-8a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v8"/><path d="M4 10V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4"/><path d="M12 4v6"/><path d="M2 18h20"/></svg>
        </div>
        <div class="stat-content">
            <h4>Total Rooms</h4>
            <div class="stat-value"><?php echo $totalRooms; ?></div>
        </div>
    </div>
    <div class="stat-card stat-success">
        <div class="stat-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
        </div>
        <div class="stat-content">
            <h4>Active Bookings</h4>
            <div class="stat-value"><?php echo $activeBookings; ?></div>
        </div>
    </div>
    <div class="stat-card stat-info">
        <div class="stat-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div class="stat-content">
            <h4>Available</h4>
            <div class="stat-value"><?php echo $availableRooms; ?></div>
        </div>
    </div>
    <div class="stat-card stat-warning">
        <div class="stat-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="stat-content">
            <h4>Revenue</h4>
            <div class="stat-value"><?php echo format_currency($totalRevenue); ?></div>
        </div>
    </div>
</div>

<div class="grid-2">
    <!-- Recent Bookings -->
    <div class="table-container" style="grid-column: 1 / -1;">
        <div class="table-header">
            <h3>Recent Bookings</h3>
            <a href="booking_form.php" class="btn btn-accent btn-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                New Booking
            </a>
        </div>
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($recentBookings)): ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <p>No bookings yet</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentBookings as $b): ?>
                    <tr>
                        <td class="text-bold"><?php echo booking_id($b['id']); ?></td>
                        <td><?php echo e($b['customer_name']); ?></td>
                        <td><?php echo e($b['room_number']); ?> <span class="text-muted"><?php echo e($b['room_type']); ?></span></td>
                        <td><?php echo format_date($b['check_in']); ?></td>
                        <td><?php echo format_date($b['check_out']); ?></td>
                        <td class="text-bold"><?php echo format_currency($b['total_amount']); ?></td>
                        <td><span class="badge <?php echo badge_class($b['status']); ?>"><span class="badge-dot"></span> <?php echo e($b['status']); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="stats-grid mt-lg">
    <div class="stat-card stat-info">
        <div class="stat-content">
            <h4>Confirmed</h4>
            <div class="stat-value"><?php echo $confirmedCount; ?></div>
        </div>
    </div>
    <div class="stat-card stat-success">
        <div class="stat-content">
            <h4>Checked-In</h4>
            <div class="stat-value"><?php echo $checkedInCount; ?></div>
        </div>
    </div>
    <div class="stat-card stat-accent">
        <div class="stat-content">
            <h4>Checked-Out</h4>
            <div class="stat-value"><?php echo $checkedOutCount; ?></div>
        </div>
    </div>
    <div class="stat-card stat-danger">
        <div class="stat-content">
            <h4>Cancelled</h4>
            <div class="stat-value"><?php echo $cancelledCount; ?></div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
