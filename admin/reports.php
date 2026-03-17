<?php
require_once '../includes/header.php';

$totalRevenue    = db_value("SELECT COALESCE(SUM(amount),0) FROM payments WHERE status = 'Paid'") ?: 0;
$monthRevenue    = db_value("SELECT COALESCE(SUM(amount),0) FROM payments WHERE status = 'Paid' AND MONTH(payment_date) = MONTH(CURDATE()) AND YEAR(payment_date) = YEAR(CURDATE())") ?: 0;
$pendingPayments = db_value("SELECT COALESCE(SUM(total_amount),0) FROM bookings WHERE status IN ('Confirmed','Checked-In') AND id NOT IN (SELECT booking_id FROM payments WHERE status = 'Paid')") ?: 0;

$totalRooms   = db_value("SELECT COUNT(*) FROM rooms") ?: 1;
$occupiedRooms = db_value("SELECT COUNT(*) FROM rooms WHERE status = 'Occupied'") ?: 0;
$reservedRooms = db_value("SELECT COUNT(*) FROM rooms WHERE status = 'Reserved'") ?: 0;
$maintRooms    = db_value("SELECT COUNT(*) FROM rooms WHERE status = 'Maintenance'") ?: 0;
$availRooms    = db_value("SELECT COUNT(*) FROM rooms WHERE status = 'Available'") ?: 0;
$occupancyRate = round(($occupiedRooms / $totalRooms) * 100);

$totalBookings = db_value("SELECT COUNT(*) FROM bookings") ?: 0;
$avgStay = db_value("SELECT ROUND(AVG(DATEDIFF(check_out, check_in)),1) FROM bookings WHERE status IN ('Checked-Out','Checked-In')") ?: 0;

$roomTypeRevenue = db_fetch_all("
    SELECT r.type, COUNT(b.id) as bookings, COALESCE(SUM(b.total_amount),0) as revenue
    FROM rooms r
    LEFT JOIN bookings b ON r.id = b.room_id AND b.status != 'Cancelled'
    GROUP BY r.type
    ORDER BY revenue DESC
");

$recentPayments = db_fetch_all("
    SELECT p.*, b.id AS booking_id, c.name AS customer_name, r.room_number
    FROM payments p
    JOIN bookings b ON p.booking_id = b.id
    JOIN customers c ON b.customer_id = c.id
    JOIN rooms r ON b.room_id = r.id
    ORDER BY p.payment_date DESC
    LIMIT 10
");

$maxRevenue = 1;
foreach ($roomTypeRevenue as $rt) {
    if ($rt['revenue'] > $maxRevenue) $maxRevenue = $rt['revenue'];
}
?>

<div class="page-header">
    <div>
        <h2>Reports</h2>
        <p class="subtitle">Revenue, occupancy and booking analytics</p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card stat-success">
        <div class="stat-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="stat-content">
            <h4>Total Revenue</h4>
            <div class="stat-value"><?php echo format_currency($totalRevenue); ?></div>
        </div>
    </div>
    <div class="stat-card stat-info">
        <div class="stat-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
        </div>
        <div class="stat-content">
            <h4>This Month</h4>
            <div class="stat-value"><?php echo format_currency($monthRevenue); ?></div>
        </div>
    </div>
    <div class="stat-card stat-warning">
        <div class="stat-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="stat-content">
            <h4>Pending</h4>
            <div class="stat-value"><?php echo format_currency($pendingPayments); ?></div>
        </div>
    </div>
    <div class="stat-card stat-accent">
        <div class="stat-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
        </div>
        <div class="stat-content">
            <h4>Avg Stay</h4>
            <div class="stat-value"><?php echo $avgStay; ?> <span style="font-size:0.75rem;font-weight:400;">nights</span></div>
        </div>
    </div>
</div>

<div class="grid-2">
    <!-- Occupancy Overview -->
    <div class="card">
        <div class="card-header"><h3>Room Occupancy</h3></div>
        <div class="card-body">
            <!-- Progress Bars -->
            <div style="margin-bottom: 20px;">
                <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                    <span style="font-size:0.82rem;font-weight:500;">Occupancy Rate</span>
                    <span style="font-size:0.82rem;font-weight:600;color:var(--accent);"><?php echo $occupancyRate; ?>%</span>
                </div>
                <div style="height:10px;background:var(--surface);border-radius:8px;overflow:hidden;">
                    <div style="height:100%;width:<?php echo $occupancyRate; ?>%;background:var(--accent);border-radius:8px;transition:width 0.5s ease;"></div>
                </div>
            </div>

            <div class="legend">
                <div class="legend-item"><div class="legend-dot" style="background:var(--danger);"></div> Occupied (<?php echo $occupiedRooms; ?>)</div>
                <div class="legend-item"><div class="legend-dot" style="background:var(--info);"></div> Reserved (<?php echo $reservedRooms; ?>)</div>
                <div class="legend-item"><div class="legend-dot" style="background:var(--success);"></div> Available (<?php echo $availRooms; ?>)</div>
                <div class="legend-item"><div class="legend-dot" style="background:var(--warning);"></div> Maintenance (<?php echo $maintRooms; ?>)</div>
            </div>
        </div>
    </div>

    <!-- Revenue by Room Type -->
    <div class="card">
        <div class="card-header"><h3>Revenue by Room Type</h3></div>
        <div class="card-body">
            <div class="bar-chart">
                <?php $colors = ['#3498DB','#E8734A','#9B59B6','#2ECC71']; $i = 0; ?>
                <?php foreach ($roomTypeRevenue as $rt): ?>
                <div class="bar-chart-item">
                    <div class="bar-chart-value"><?php echo format_currency($rt['revenue']); ?></div>
                    <div class="bar-chart-bar" style="height:<?php echo max(4, ($rt['revenue']/$maxRevenue)*140); ?>px;background:<?php echo $colors[$i % 4]; ?>;"></div>
                    <div class="bar-chart-label"><?php echo e($rt['type']); ?><br><span style="font-size:0.65rem;"><?php echo $rt['bookings']; ?> bookings</span></div>
                </div>
                <?php $i++; endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="table-container mt-lg">
    <div class="table-header">
        <h3>Recent Payments</h3>
    </div>
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Booking</th>
                    <th>Guest</th>
                    <th>Room</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($recentPayments)): ?>
            <tr><td colspan="7"><div class="empty-state"><p>No payments recorded yet.</p></div></td></tr>
            <?php else: ?>
                <?php foreach ($recentPayments as $p): ?>
                <tr>
                    <td class="text-bold"><?php echo booking_id($p['booking_id']); ?></td>
                    <td><?php echo e($p['customer_name']); ?></td>
                    <td><?php echo e($p['room_number']); ?></td>
                    <td class="text-bold"><?php echo format_currency($p['amount']); ?></td>
                    <td><?php echo e($p['payment_method']); ?></td>
                    <td><?php echo format_date($p['payment_date']); ?></td>
                    <td><span class="badge <?php echo badge_class($p['status']); ?>"><span class="badge-dot"></span> <?php echo e($p['status']); ?></span></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
