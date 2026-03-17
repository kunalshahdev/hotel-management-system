<?php
require_once '../includes/header.php';

$search = $_GET['search'] ?? '';
$params = [];
$types = '';

$sql = "SELECT c.*, (SELECT COUNT(*) FROM bookings WHERE customer_id = c.id) AS booking_count FROM customers c";
if ($search) {
    $sql .= " WHERE c.name LIKE ? OR c.email LIKE ? OR c.phone LIKE ?";
    $like = "%$search%";
    $params = [$like, $like, $like];
    $types = 'sss';
}
$sql .= " ORDER BY c.created_at DESC";

$customers = db_fetch_all($sql, $types, $params);
?>

<div class="page-header">
    <div>
        <h2>Customers</h2>
        <p class="subtitle">Manage guest records</p>
    </div>
    <a href="customer_form.php" class="btn btn-accent">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Customer
    </a>
</div>

<div class="card" style="margin-bottom: var(--sp-md);">
    <div class="card-body" style="padding: 12px var(--sp-lg);">
        <form method="GET" class="filter-bar">
            <input type="text" name="search" class="form-control" placeholder="Search by name, email, or phone..." value="<?php echo e($search); ?>" style="flex:1;">
            <button type="submit" class="btn btn-primary btn-sm">Search</button>
            <?php if ($search): ?>
            <a href="customers.php" class="btn btn-outline btn-sm">Clear</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="table-container">
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>ID Proof</th>
                    <th>Bookings</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($customers)): ?>
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            <h3>No customers found</h3>
                            <p>Add your first customer to get started.</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($customers as $c): ?>
                <tr>
                    <td class="text-bold"><?php echo e($c['name']); ?></td>
                    <td>
                        <div><?php echo e($c['phone']); ?></div>
                        <div class="text-muted"><?php echo e($c['email'] ?: '—'); ?></div>
                    </td>
                    <td>
                        <?php if ($c['id_number']): ?>
                        <span class="text-muted"><?php echo e($c['id_proof']); ?>:</span> <?php echo e($c['id_number']); ?>
                        <?php else: ?>
                        <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge badge-info"><?php echo $c['booking_count']; ?> bookings</span>
                    </td>
                    <td class="text-muted"><?php echo format_date($c['created_at']); ?></td>
                    <td>
                        <div class="btn-group">
                            <a href="customer_form.php?id=<?php echo $c['id']; ?>" class="btn btn-outline btn-sm">Edit</a>
                            <a href="customer_delete.php?id=<?php echo $c['id']; ?>" class="btn btn-outline btn-sm" style="color:var(--danger);" data-confirm="Delete customer <?php echo e($c['name']); ?>?">Delete</a>
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
