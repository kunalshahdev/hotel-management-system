<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (isset($_SESSION['user_id'])) {
    redirect('admin/index.php');
}
if (isset($_SESSION['customer_id'])) {
    redirect('customer/dashboard.php');
}

$error = '';
$loginType = $_GET['type'] ?? 'customer'; // 'customer' or 'admin'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $type     = $_POST['login_type'] ?? 'customer';
 
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        if ($type === 'admin') {
            $user = attempt_login($email, $password);
            if ($user) {
                login_user($user);
                redirect('admin/index.php');
            } else {
                $error = 'Invalid admin credentials.';
            }
        } else {
            $customer = attempt_customer_login($email, $password);
            if ($customer) {
                login_customer($customer);
                redirect('customer/dashboard.php');
            } else {
                $error = 'Invalid email or password. Don\'t have an account? <a href="register.php">Register here</a>.';
            }
        }
    }
    $loginType = $type;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — NepStay</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .login-tabs {
            display: flex;
            background: var(--surface);
            border-radius: var(--radius);
            padding: 4px;
            margin-bottom: var(--sp-lg);
        }
        .login-tab {
            flex: 1;
            padding: 10px;
            text-align: center;
            border-radius: var(--radius-sm);
            font-size: 0.88rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition-fast);
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-family: inherit;
        }
        .login-tab.active {
            background: var(--card);
            color: var(--primary);
            box-shadow: var(--shadow-sm);
            font-weight: 600;
        }
        .login-tab:hover:not(.active) { color: var(--text); }
        .alert a { color: var(--accent); text-decoration: underline; }
    </style>
</head>
<body>
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <div class="logo-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/>
                    <path d="M9 9h1"/><path d="M9 13h1"/><path d="M9 17h1"/>
                </svg>
            </div>
            <h2>NepStay</h2>
            <p>Sign in to your account</p>
        </div>

        <!-- Login Type Tabs -->
        <div class="login-tabs" id="loginTabs">
            <button type="button" class="login-tab <?php echo $loginType === 'customer' ? 'active' : ''; ?>" data-type="customer">Guest</button>
            <button type="button" class="login-tab <?php echo $loginType === 'admin' ? 'active' : ''; ?>" data-type="admin">Admin</button>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger" style="margin-bottom: var(--sp-md);">
            <span class="alert-text"><?php echo $error; ?></span>
        </div>
        <?php endif; ?>

        <form method="POST" autocomplete="on" id="loginForm">
            <input type="hidden" name="login_type" id="loginTypeInput" value="<?php echo e($loginType); ?>">

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="your@email.com" value="<?php echo e($_POST['email'] ?? ''); ?>" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn btn-accent btn-block btn-lg" style="margin-top: var(--sp-lg);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                Sign In
            </button>
        </form>

        <div class="auth-footer">
            <p>Don't have an account? <a href="register.php" style="color:var(--accent);font-weight:500;">Register</a></p>
            <p style="margin-top:6px;font-size:0.78rem;color:var(--text-light);">Admin: admin@nepstay.com.np / admin123</p>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.login-tab').forEach(function(tab) {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.login-tab').forEach(function(t) { t.classList.remove('active'); });
        this.classList.add('active');
        document.getElementById('loginTypeInput').value = this.getAttribute('data-type');
    });
});
</script>
</body>
</html>