<?php
require_once '../includes/header.php';

$rooms = db_fetch_all("SELECT * FROM rooms ORDER BY floor ASC, room_number ASC");

$todayCheckins = db_fetch_all("
    SELECT b.*, c.name AS customer_name, r.room_number, r.type AS room_type
    FROM bookings b
    JOIN customers c ON b.customer_id = c.id
    JOIN rooms r ON b.room_id = r.id
    WHERE b.check_in = CURDATE() AND b.status IN ('Confirmed','Checked-In')
    ORDER BY b.status DESC
");

$todayCheckouts = db_fetch_all("
    SELECT b.*, c.name AS customer_name, r.room_number, r.type AS room_type
    FROM bookings b
    JOIN customers c ON b.customer_id = c.id
    JOIN rooms r ON b.room_id = r.id
    WHERE b.check_out = CURDATE() AND b.status = 'Checked-In'
    ORDER BY r.room_number
");

$counts = [
    'Available' => 0, 'Occupied' => 0, 'Reserved' => 0, 'Maintenance' => 0
];
foreach ($rooms as $r) {
    $counts[$r['status']] = ($counts[$r['status']] ?? 0) + 1;
}
?>

<div class="page-header">
    <div>
        <h2>Front Desk</h2>
        <p class="subtitle">Quick room availability and today's activity</p>
    </div>
    <a href="booking_form.php" class="btn btn-accent">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Quick Booking
    </a>
</div>

<div class="stats-grid">
    <div class="stat-card stat-success">
        <div class="stat-content">
            <h4>Available</h4>
            <div class="stat-value"><?php echo $counts['Available']; ?></div>
        </div>
    </div>
    <div class="stat-card stat-danger">
        <div class="stat-content">
            <h4>Occupied</h4>
            <div class="stat-value"><?php echo $counts['Occupied']; ?></div>
        </div>
    </div>
    <div class="stat-card stat-info">
        <div class="stat-content">
            <h4>Reserved</h4>
            <div class="stat-value"><?php echo $counts['Reserved']; ?></div>
        </div>
    </div>
    <div class="stat-card stat-warning">
        <div class="stat-content">
            <h4>Maintenance</h4>
            <div class="stat-value"><?php echo $counts['Maintenance']; ?></div>
        </div>
    </div>
</div>

<div class="card mb-lg">
    <div class="card-header">
        <h3>Room Map</h3>
        <div class="legend" style="margin:0;">
            <div class="legend-item"><div class="legend-dot" style="background:var(--success);"></div> Available</div>
            <div class="legend-item"><div class="legend-dot" style="background:var(--danger);"></div> Occupied</div>
            <div class="legend-item"><div class="legend-dot" style="background:var(--info);"></div> Reserved</div>
            <div class="legend-item"><div class="legend-dot" style="background:var(--warning);"></div> Maintenance</div>
        </div>
    </div>
    <div class="card-body">
        <div class="availability-grid">
            <?php foreach ($rooms as $r):
                $statusClass = strtolower($r['status']);
            ?>
            <div class="room-tile <?php echo $statusClass; ?>">
                <span class="room-tile-number"><?php echo e($r['room_number']); ?></span>
                <span class="room-tile-type"><?php echo e($r['type']); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="grid-2">
    <!-- Today's Check-ins -->
    <div class="table-container">
        <div class="table-header">
            <h3>Today's Check-ins (<?php echo count($todayCheckins); ?>)</h3>
        </div>
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($todayCheckins)): ?>
                <tr><td colspan="4"><div class="empty-state"><p>No check-ins today.</p></div></td></tr>
                <?php else: ?>
                    <?php foreach ($todayCheckins as $b): ?>
                    <tr>
                        <td class="text-bold"><?php echo e($b['customer_name']); ?></td>
                        <td><?php echo e($b['room_number']); ?> <span class="text-muted"><?php echo e($b['room_type']); ?></span></td>
                        <td><span class="badge <?php echo badge_class($b['status']); ?>"><span class="badge-dot"></span> <?php echo e($b['status']); ?></span></td>
                        <td>
                            <?php if ($b['status'] === 'Confirmed'): ?>
                            <a href="booking_action.php?action=checkin&id=<?php echo $b['id']; ?>" class="btn btn-success btn-sm">Check-In</a>
                            <?php else: ?>
                            <span class="text-muted">Done</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Today's Check-outs -->
    <div class="table-container">
        <div class="table-header">
            <h3>Today's Check-outs (<?php echo count($todayCheckouts); ?>)</h3>
        </div>
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($todayCheckouts)): ?>
                <tr><td colspan="4"><div class="empty-state"><p>No check-outs today.</p></div></td></tr>
                <?php else: ?>
                    <?php foreach ($todayCheckouts as $b): ?>
                    <tr>
                        <td class="text-bold"><?php echo e($b['customer_name']); ?></td>
                        <td><?php echo e($b['room_number']); ?></td>
                        <td class="text-bold"><?php echo format_currency($b['total_amount']); ?></td>
                        <td>
                            <a href="booking_action.php?action=checkout&id=<?php echo $b['id']; ?>" class="btn btn-primary btn-sm">Check-Out</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
