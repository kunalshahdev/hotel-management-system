<?php
require_once '../includes/header.php';

$statusFilter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$where = [];
$params = [];
$types = '';

if ($statusFilter && $statusFilter !== 'all') {
    $where[] = "b.status = ?";
    $params[] = $statusFilter;
    $types .= 's';
}
if ($search) {
    $where[] = "(c.name LIKE ? OR r.room_number LIKE ? OR b.id LIKE ?)";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= 'sss';
}

$sql = "SELECT b.*, c.name AS customer_name, c.phone AS customer_phone, r.room_number, r.type AS room_type
        FROM bookings b
        JOIN customers c ON b.customer_id = c.id
        JOIN rooms r ON b.room_id = r.id";
if (!empty($where)) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY b.created_at DESC";

$bookings = db_fetch_all($sql, $types, $params);

$allCount       = db_value("SELECT COUNT(*) FROM bookings");
$confirmedCount = db_value("SELECT COUNT(*) FROM bookings WHERE status = 'Confirmed'");
$checkedInCount = db_value("SELECT COUNT(*) FROM bookings WHERE status = 'Checked-In'");
$checkedOutCount= db_value("SELECT COUNT(*) FROM bookings WHERE status = 'Checked-Out'");
$cancelledCount = db_value("SELECT COUNT(*) FROM bookings WHERE status = 'Cancelled'");
?>

<div class="page-header">
    <div>
        <h2>Bookings</h2>
        <p class="subtitle">Manage reservations and check-ins</p>
    </div>
    <a href="booking_form.php" class="btn btn-accent">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Booking
    </a>
</div>

<div class="card" style="margin-bottom: var(--sp-md);">
    <div class="card-body" style="padding: 12px var(--sp-lg);">
        <form method="GET" class="filter-bar">
            <select name="status" class="form-control">
                <option value="all">All Status (<?php echo $allCount; ?>)</option>
                <option value="Confirmed" <?php echo $statusFilter === 'Confirmed' ? 'selected' : ''; ?>>Confirmed (<?php echo $confirmedCount; ?>)</option>
                <option value="Checked-In" <?php echo $statusFilter === 'Checked-In' ? 'selected' : ''; ?>>Checked-In (<?php echo $checkedInCount; ?>)</option>
                <option value="Checked-Out" <?php echo $statusFilter === 'Checked-Out' ? 'selected' : ''; ?>>Checked-Out (<?php echo $checkedOutCount; ?>)</option>
                <option value="Cancelled" <?php echo $statusFilter === 'Cancelled' ? 'selected' : ''; ?>>Cancelled (<?php echo $cancelledCount; ?>)</option>
            </select>
            <input type="text" name="search" class="form-control" placeholder="Search guest, room..." value="<?php echo e($search); ?>">
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <?php if ($statusFilter || $search): ?>
            <a href="bookings.php" class="btn btn-outline btn-sm">Clear</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="table-container">
    <div class="table-scroll">
        <table class="data-table" id="bookingsTable">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Guest</th>
                    <th>Room</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Guests</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($bookings)): ?>
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
                            <h3>No bookings found</h3>
                            <p>No bookings match the current filters.</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($bookings as $b): ?>
                <tr data-status="<?php echo e($b['status']); ?>">
                    <td class="text-bold"><?php echo booking_id($b['id']); ?></td>
                    <td>
                        <?php echo e($b['customer_name']); ?>
                        <div class="text-muted"><?php echo e($b['customer_phone']); ?></div>
                    </td>
                    <td>
                        <?php echo e($b['room_number']); ?>
                        <span class="text-muted"><?php echo e($b['room_type']); ?></span>
                    </td>
                    <td><?php echo format_date($b['check_in']); ?></td>
                    <td><?php echo format_date($b['check_out']); ?></td>
                    <td><?php echo $b['guests']; ?></td>
                    <td class="text-bold"><?php echo format_currency($b['total_amount']); ?></td>
                    <td><span class="badge <?php echo badge_class($b['status']); ?>"><span class="badge-dot"></span> <?php echo e($b['status']); ?></span></td>
                    <td>
                        <div class="btn-group">
                            <?php if ($b['status'] === 'Confirmed'): ?>
                            <a href="booking_action.php?action=checkin&id=<?php echo $b['id']; ?>" class="btn btn-success btn-sm">Check-In</a>
                            <a href="booking_action.php?action=cancel&id=<?php echo $b['id']; ?>" class="btn btn-outline btn-sm" data-confirm="Cancel this booking?" style="color:var(--danger);">Cancel</a>
                            <?php elseif ($b['status'] === 'Checked-In'): ?>
                            <a href="booking_action.php?action=checkout&id=<?php echo $b['id']; ?>" class="btn btn-primary btn-sm">Check-Out</a>
                            <?php else: ?>
                            <span class="text-muted">—</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
