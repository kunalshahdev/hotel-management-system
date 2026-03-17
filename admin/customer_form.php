<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_admin();

$id = intval($_GET['id'] ?? 0);
$customer = null;

if ($id) {
    $customer = db_fetch("SELECT * FROM customers WHERE id = ?", 'i', [$id]);
    if (!$customer) {
        flash('danger', 'Customer not found.');
        redirect('customers.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name      = trim($_POST['name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $address   = trim($_POST['address'] ?? '');
    $id_proof  = $_POST['id_proof'] ?? 'National ID';
    $id_number = trim($_POST['id_number'] ?? '');

    if (empty($name) || empty($phone)) {
        flash('danger', 'Name and phone are required.');
    } else {
        if ($id) {
            db_query(
                "UPDATE customers SET name=?, email=?, phone=?, address=?, id_proof=?, id_number=? WHERE id=?",
                'ssssssi',
                [$name, $email, $phone, $address, $id_proof, $id_number, $id]
            );
            flash('success', 'Customer updated successfully.');
        } else {
            db_query(
                "INSERT INTO customers (name, email, phone, address, id_proof, id_number) VALUES (?,?,?,?,?,?)",
                'ssssss',
                [$name, $email, $phone, $address, $id_proof, $id_number]
            );
            flash('success', 'Customer added successfully.');
        }
        redirect('customers.php');
    }
}

require_once '../includes/header.php';
?>

<div class="page-header">
    <div>
        <h2><?php echo $id ? 'Edit Customer' : 'Add Customer'; ?></h2>
        <div class="breadcrumb">
            <a href="index.php">Dashboard</a> <span class="sep">›</span>
            <a href="customers.php">Customers</a> <span class="sep">›</span>
            <span><?php echo $id ? 'Edit' : 'Add'; ?></span>
        </div>
    </div>
</div>

<div class="card form-card">
    <div class="card-header">
        <h3>Customer Information</h3>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Full Name <span class="required">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="John Doe" value="<?php echo e($customer['name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number <span class="required">*</span></label>
                    <input type="text" name="phone" id="phone" class="form-control" placeholder="+91 98765 43210" value="<?php echo e($customer['phone'] ?? ''); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="john@email.com" value="<?php echo e($customer['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea name="address" id="address" class="form-control" placeholder="Full address..."><?php echo e($customer['address'] ?? ''); ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="id_proof">ID Proof Type</label>
                    <select name="id_proof" id="id_proof" class="form-control">
                        <?php foreach (['Passport','Driving License','National ID','Aadhar','Other'] as $p): ?>
                        <option value="<?php echo $p; ?>" <?php echo ($customer['id_proof'] ?? 'National ID') === $p ? 'selected' : ''; ?>><?php echo $p; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="id_number">ID Number</label>
                    <input type="text" name="id_number" id="id_number" class="form-control" placeholder="ID document number" value="<?php echo e($customer['id_number'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-accent">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    <?php echo $id ? 'Update Customer' : 'Add Customer'; ?>
                </button>
                <a href="customers.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
