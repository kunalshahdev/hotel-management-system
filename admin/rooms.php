<?php
require_once '../includes/header.php';

$statusFilter = $_GET['status'] ?? '';
$typeFilter   = $_GET['type'] ?? '';

$where = [];
$params = [];
$types = '';

if ($statusFilter && $statusFilter !== 'all') {
    $where[] = "status = ?";
    $params[] = $statusFilter;
    $types .= 's';
}
if ($typeFilter && $typeFilter !== 'all') {
    $where[] = "type = ?";
    $params[] = $typeFilter;
    $types .= 's';
}

$sql = "SELECT * FROM rooms";
if (!empty($where)) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY room_number ASC";

$rooms = db_fetch_all($sql, $types, $params);

$typeGradients = [
    'Standard' => 'linear-gradient(135deg, #3498DB, #2471A3)',
    'Deluxe'   => 'linear-gradient(135deg, #DC143C, #B01030)',
    'Suite'    => 'linear-gradient(135deg, #003893, #002266)',
    'Family'   => 'linear-gradient(135deg, #2ECC71, #27AE60)',
];
?>

<div class="page-header">
    <div>
        <h2>Rooms</h2>
        <p class="subtitle">Manage hotel rooms and availability</p>
    </div>
    <a href="room_form.php" class="btn btn-accent">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Room
    </a>
</div>

<div class="card" style="margin-bottom: var(--sp-md);">
    <div class="card-body" style="padding: 12px var(--sp-lg);">
        <form method="GET" class="filter-bar">
            <select name="status" class="form-control">
                <option value="all">All Statuses</option>
                <option value="Available" <?php echo $statusFilter === 'Available' ? 'selected' : ''; ?>>Available</option>
                <option value="Occupied" <?php echo $statusFilter === 'Occupied' ? 'selected' : ''; ?>>Occupied</option>
                <option value="Reserved" <?php echo $statusFilter === 'Reserved' ? 'selected' : ''; ?>>Reserved</option>
                <option value="Maintenance" <?php echo $statusFilter === 'Maintenance' ? 'selected' : ''; ?>>Maintenance</option>
            </select>
            <select name="type" class="form-control">
                <option value="all">All Types</option>
                <option value="Standard" <?php echo $typeFilter === 'Standard' ? 'selected' : ''; ?>>Standard</option>
                <option value="Deluxe" <?php echo $typeFilter === 'Deluxe' ? 'selected' : ''; ?>>Deluxe</option>
                <option value="Suite" <?php echo $typeFilter === 'Suite' ? 'selected' : ''; ?>>Suite</option>
                <option value="Family" <?php echo $typeFilter === 'Family' ? 'selected' : ''; ?>>Family</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Filter
            </button>
            <?php if ($statusFilter || $typeFilter): ?>
            <a href="rooms.php" class="btn btn-outline btn-sm">Clear</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if (empty($rooms)): ?>
<div class="card">
    <div class="empty-state">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 20v-8a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v8"/><path d="M4 10V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4"/></svg>
        <h3>No rooms found</h3>
        <p>No rooms match the selected filters, or no rooms have been added yet.</p>
        <a href="room_form.php" class="btn btn-accent btn-sm mt-md">Add First Room</a>
    </div>
</div>
<?php else: ?>
<div class="rooms-grid">
    <?php foreach ($rooms as $room): ?>
    <div class="room-card">
        <div class="room-card-top" style="background: <?php echo $typeGradients[$room['type']] ?? $typeGradients['Standard']; ?>">
            <span class="room-card-type"><?php echo e($room['type']); ?></span>
            <div class="room-card-badge">
                <span class="badge <?php echo badge_class($room['status']); ?>"><span class="badge-dot"></span> <?php echo e($room['status']); ?></span>
            </div>
        </div>
        <div class="room-card-body">
            <div class="room-card-number">Room <?php echo e($room['room_number']); ?></div>
            <div class="room-card-details">
                <span class="room-card-detail">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
                    Floor <?php echo e($room['floor']); ?>
                </span>
                <span class="room-card-detail">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    <?php echo e($room['max_guests']); ?> Guests
                </span>
            </div>
            <div class="room-card-price"><?php echo format_currency($room['price']); ?> <span>/night</span></div>
            <div class="room-card-actions">
                <a href="room_form.php?id=<?php echo $room['id']; ?>" class="btn btn-outline btn-sm" style="flex:1;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit
                </a>
                <a href="room_delete.php?id=<?php echo $room['id']; ?>" class="btn btn-outline btn-sm" style="color:var(--danger);" data-confirm="Delete room <?php echo e($room['room_number']); ?>?">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
