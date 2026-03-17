<?php

require_once __DIR__ . '/db.php';

function require_admin() {
    if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
        header('Location: /Hotel_Reservation_System/login.php');
        exit;
    }
}

function require_customer() {
    if (!isset($_SESSION['customer_id'])) {
        header('Location: /Hotel_Reservation_System/login.php');
        exit;
    }
}

function current_user() {
    if (!isset($_SESSION['user_id'])) return null;
    return db_fetch("SELECT * FROM users WHERE id = ?", 'i', [$_SESSION['user_id']]);
}

function current_customer() {
    if (!isset($_SESSION['customer_id'])) return null;
    return db_fetch("SELECT * FROM customers WHERE id = ?", 'i', [$_SESSION['customer_id']]);
}

function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function is_customer_logged_in() {
    return isset($_SESSION['customer_id']);
}

function attempt_login($email, $password) {
    $user = db_fetch("SELECT * FROM users WHERE email = ?", 's', [$email]);
    if (!$user) return false;

    if (password_verify($password, $user['password'])) {
        return $user;
    }
    // Plain text fallback (for seed data)
    if ($user['password'] === $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        db_query("UPDATE users SET password = ? WHERE id = ?", 'si', [$hash, $user['id']]);
        return $user;
    }
    return false;
}

function attempt_customer_login($email, $password) {
    $customer = db_fetch("SELECT * FROM customers WHERE email = ?", 's', [$email]);
    if (!$customer) return false;
    if (!$customer['password']) return false;

    if (password_verify($password, $customer['password'])) {
        return $customer;
    }
    // Plain text fallback
    if ($customer['password'] === $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        db_query("UPDATE customers SET password = ? WHERE id = ?", 'si', [$hash, $customer['id']]);
        return $customer;
    }
    return false;
}

function login_user($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
}

function login_customer($customer) {
    $_SESSION['customer_id'] = $customer['id'];
    $_SESSION['customer_name'] = $customer['name'];
    $_SESSION['customer_email'] = $customer['email'];
}

function register_customer($name, $email, $phone, $password) {
    // Check if email already exists
    $existing = db_fetch("SELECT id FROM customers WHERE email = ?", 's', [$email]);
    if ($existing) return ['error' => 'An account with this email already exists.'];

    $hash = password_hash($password, PASSWORD_DEFAULT);
    db_query(
        "INSERT INTO customers (name, email, phone, password) VALUES (?,?,?,?)",
        'ssss',
        [$name, $email, $phone, $hash]
    );
    return ['success' => true, 'id' => db_insert_id()];
}

function logout_user() {
    session_destroy();
    header('Location: /Hotel_Reservation_System/login.php');
    exit;
}
