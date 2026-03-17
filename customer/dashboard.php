<?php
$pageTitle = 'My Dashboard';
require_once '../includes/customer_header.php';
require_customer();

$customer = current_customer();

$totalBookings = db_value("SELECT COUNT(*) FROM bookings WHERE customer_id = ?", 'i', [$customer['id']]);
$activeBookings = db_value("SELECT COUNT(*) FROM bookings WHERE customer_id = ? AND status IN ('Confirmed','Checked-In')", 'i', [$customer['id']]);
$totalSpent = db_value("SELECT COALESCE(SUM(amount),0) FROM payments p JOIN bookings b ON p.booking_id = b.id WHERE b.customer_id = ? AND p.status = 'Paid'", 'i', [$customer['id']]);

$recentBookings = db_fetch_all("
    SELECT b.*, r.room_number, r.type AS room_type, r.price
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    WHERE b.customer_id = ?
    ORDER BY b.created_at DESC
    LIMIT 5
", 'i', [$customer['id']]);
?>

<div class="c-page">
    <div class="c-page-header">
        <h1>Welcome back, <?php echo e($customer['name']); ?>! 👋</h1>
        <p>Here's your booking overview</p>
    </div>

    <!-- Stats -->
    <div class="c-dash-grid">
        <div class="c-dash-card">
            <div class="c-dash-icon" style="background: rgba(232,115,74,0.1); color: var(--accent);">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
            </div>
            <div>
                <h4>Total Bookings</h4>
                <div class="c-dash-value"><?php echo $totalBookings; ?></div>
            </div>
        </div>
        <div class="c-dash-card">
            <div class="c-dash-icon" style="background: var(--success-bg); color: var(--success);">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div>
                <h4>Active Bookings</h4>
                <div class="c-dash-value"><?php echo $activeBookings; ?></div>
            </div>
        </div>
        <div class="c-dash-card">
            <div class="c-dash-icon" style="background: var(--info-bg); color: var(--info);">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div>
                <h4>Total Spent</h4>
                <div class="c-dash-value"><?php echo format_currency($totalSpent); ?></div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div style="display:flex;gap:12px;margin-bottom:32px;flex-wrap:wrap;">
        <a href="rooms.php" class="c-btn c-btn-accent">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Browse Rooms
        </a>
        <a href="bookings.php" class="c-btn c-btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
            My Bookings
        </a>
    </div>

    <!-- Recent Bookings -->
    <div class="table-container">
        <div class="table-header">
            <h3>Recent Bookings</h3>
        </div>
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
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
                        <td colspan="6">
                            <div class="empty-state">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
                                <h3>No bookings yet</h3>
                                <p>Browse our rooms and make your first booking!</p>
                                <a href="rooms.php" class="c-btn c-btn-accent" style="display:inline-flex;margin-top:12px;">Browse Rooms</a>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentBookings as $b): ?>
                    <tr>
                        <td class="text-bold"><?php echo booking_id($b['id']); ?></td>
                        <td>Room <?php echo e($b['room_number']); ?> <span class="text-muted"><?php echo e($b['room_type']); ?></span></td>
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

<?php require_once '../includes/customer_footer.php'; ?>
