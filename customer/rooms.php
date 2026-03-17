<?php
$pageTitle = 'Browse Rooms';
require_once '../includes/customer_header.php';

$typeFilter = $_GET['type'] ?? '';
$priceSort  = $_GET['sort'] ?? 'asc';
$checkIn    = $_GET['check_in'] ?? '';
$checkOut   = $_GET['check_out'] ?? '';

$where = ["status = 'Available'"];
$params = [];
$types = '';

if ($typeFilter && $typeFilter !== 'all') {
    $where[] = "type = ?";
    $params[] = $typeFilter;
    $types .= 's';
}

$sql = "SELECT * FROM rooms WHERE " . implode(' AND ', $where);
$sql .= " ORDER BY price " . ($priceSort === 'desc' ? 'DESC' : 'ASC');

$rooms = db_fetch_all($sql, $types, $params);

$typeGradients = [
    'Standard' => 'linear-gradient(135deg, #3498DB, #2471A3)',
    'Deluxe'   => 'linear-gradient(135deg, #DC143C, #B01030)',
    'Suite'    => 'linear-gradient(135deg, #003893, #002266)',
    'Family'   => 'linear-gradient(135deg, #2ECC71, #27AE60)',
];
?>

<div class="c-page">
    <div class="c-page-header">
        <h1>Available Rooms</h1>
        <p>Find your perfect room from our collection</p>
    </div>

    <!-- Filter Bar -->
    <div class="c-filter-bar">
        <div class="c-filter-group">
            <label>Room Type</label>
            <select onchange="applyFilters()" id="filterType" class="form-control">
                <option value="all">All Types</option>
                <option value="Standard" <?php echo $typeFilter === 'Standard' ? 'selected' : ''; ?>>Standard</option>
                <option value="Deluxe" <?php echo $typeFilter === 'Deluxe' ? 'selected' : ''; ?>>Deluxe</option>
                <option value="Suite" <?php echo $typeFilter === 'Suite' ? 'selected' : ''; ?>>Suite</option>
                <option value="Family" <?php echo $typeFilter === 'Family' ? 'selected' : ''; ?>>Family</option>
            </select>
        </div>
        <div class="c-filter-group">
            <label>Check-in</label>
            <input type="date" id="filterCheckIn" class="form-control" value="<?php echo e($checkIn); ?>">
        </div>
        <div class="c-filter-group">
            <label>Check-out</label>
            <input type="date" id="filterCheckOut" class="form-control" value="<?php echo e($checkOut); ?>">
        </div>
        <div class="c-filter-group">
            <label>Price</label>
            <select onchange="applyFilters()" id="filterSort" class="form-control">
                <option value="asc" <?php echo $priceSort === 'asc' ? 'selected' : ''; ?>>Low to High</option>
                <option value="desc" <?php echo $priceSort === 'desc' ? 'selected' : ''; ?>>High to Low</option>
            </select>
        </div>
        <div class="c-filter-group" style="flex:0;">
            <label>&nbsp;</label>
            <button onclick="applyFilters()" class="c-btn c-btn-accent">Search</button>
        </div>
    </div>

    <!-- Results -->
    <p style="margin-bottom:16px;color:var(--text-muted);font-size:0.9rem;"><?php echo count($rooms); ?> room<?php echo count($rooms) !== 1 ? 's' : ''; ?> available</p>

    <?php if (empty($rooms)): ?>
    <div class="card" style="padding:60px 24px;text-align:center;">
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 20v-8a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v8"/><path d="M4 10V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4"/></svg>
            <h3>No rooms available</h3>
            <p>Try adjusting your filters or check back later.</p>
        </div>
    </div>
    <?php else: ?>
    <div class="c-rooms-grid">
        <?php foreach ($rooms as $room): ?>
        <div class="c-room-card">
            <div class="c-room-img" style="background: <?php echo $typeGradients[$room['type']] ?? $typeGradients['Standard']; ?>">
                <span class="c-room-img-inner"><?php echo e($room['type']); ?></span>
                <div class="c-room-badge">
                    <span class="badge badge-success"><span class="badge-dot"></span> Available</span>
                </div>
                <span class="c-room-type-badge"><?php echo e($room['type']); ?></span>
            </div>
            <div class="c-room-info">
                <div class="c-room-name">Room <?php echo e($room['room_number']); ?> — <?php echo e($room['type']); ?></div>
                <?php if ($room['description']): ?>
                <p style="font-size:0.85rem;color:var(--text-muted);margin-bottom:8px;"><?php echo e(substr($room['description'], 0, 80)); ?></p>
                <?php endif; ?>
                <div class="c-room-features">
                    <span class="c-room-feature">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
                        Floor <?php echo e($room['floor']); ?>
                    </span>
                    <span class="c-room-feature">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        Max <?php echo e($room['max_guests']); ?> Guests
                    </span>
                </div>
                <?php if ($room['amenities']): ?>
                <div class="c-room-amenities">
                    <?php foreach (explode(',', $room['amenities']) as $a): ?>
                    <span class="c-amenity-tag"><?php echo e(trim($a)); ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <div class="c-room-footer">
                    <div class="c-room-price"><?php echo format_currency($room['price']); ?> <span>/night</span></div>
                    <?php
                    $bookUrl = "book.php?room_id=" . $room['id'];
                    if ($checkIn) $bookUrl .= "&check_in=" . urlencode($checkIn);
                    if ($checkOut) $bookUrl .= "&check_out=" . urlencode($checkOut);
                    ?>
                    <a href="<?php echo $bookUrl; ?>" class="c-btn c-btn-accent">Book Now</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
function applyFilters() {
    var type = document.getElementById('filterType').value;
    var sort = document.getElementById('filterSort').value;
    var checkIn = document.getElementById('filterCheckIn').value;
    var checkOut = document.getElementById('filterCheckOut').value;
    var params = [];
    if (type !== 'all') params.push('type=' + type);
    if (sort) params.push('sort=' + sort);
    if (checkIn) params.push('check_in=' + checkIn);
    if (checkOut) params.push('check_out=' + checkOut);
    window.location.href = 'rooms.php' + (params.length ? '?' + params.join('&') : '');
}
</script>

<?php require_once '../includes/customer_footer.php'; ?>
