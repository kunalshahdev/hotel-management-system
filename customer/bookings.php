<?php
$pageTitle = 'My Bookings';
require_once '../includes/customer_header.php';
require_customer();

$customer = current_customer();

$bookings = db_fetch_all("
    SELECT b.*, r.room_number, r.type AS room_type, r.price,
    (SELECT p.status FROM payments p WHERE p.booking_id = b.id LIMIT 1) AS payment_status
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    WHERE b.customer_id = ?
    ORDER BY b.created_at DESC
", 'i', [$customer['id']]);
?>

<div class="c-page">
    <div class="c-page-header">
        <h1>My Bookings</h1>
        <p>View and manage your reservations</p>
    </div>

    <?php if (empty($bookings)): ?>
    <div class="card" style="padding:60px 24px;text-align:center;">
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
            <h3>No bookings yet</h3>
            <p>Browse our rooms and book your perfect stay!</p>
            <a href="rooms.php" class="c-btn c-btn-accent" style="display:inline-flex;margin-top:16px;">Browse Rooms</a>
        </div>
    </div>
    <?php else: ?>

    <!-- Booking Cards -->
    <?php foreach ($bookings as $b):
        $nights = calc_nights($b['check_in'], $b['check_out']);
    ?>
    <div class="card" style="margin-bottom:16px;">
        <div class="card-body" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
            <div style="display:flex;align-items:center;gap:16px;">
                <div style="width:48px;height:48px;border-radius:var(--radius);display:flex;align-items:center;justify-content:center;background:var(--surface);color:var(--accent);font-weight:700;font-size:0.85rem;">
                    <?php echo e($b['room_number']); ?>
                </div>
                <div>
                    <div style="font-weight:600;color:var(--primary);"><?php echo booking_id($b['id']); ?> — Room <?php echo e($b['room_number']); ?></div>
                    <div style="font-size:0.82rem;color:var(--text-muted);">
                        <?php echo e($b['room_type']); ?> · <?php echo $nights; ?> night<?php echo $nights > 1 ? 's' : ''; ?>
                        · <?php echo format_date($b['check_in']); ?> → <?php echo format_date($b['check_out']); ?>
                    </div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
                <div style="text-align:right;">
                    <div style="font-weight:700;color:var(--accent);font-size:1.1rem;"><?php echo format_currency($b['total_amount']); ?></div>
                    <span class="badge <?php echo badge_class($b['status']); ?>"><span class="badge-dot"></span> <?php echo e($b['status']); ?></span>
                </div>
                <?php if ($b['status'] === 'Confirmed'): ?>
                <a href="cancel_booking.php?id=<?php echo $b['id']; ?>" class="btn btn-outline btn-sm" style="color:var(--danger);" data-confirm="Are you sure you want to cancel this booking?">Cancel</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <?php endif; ?>
</div>

<?php require_once '../includes/customer_footer.php'; ?>
