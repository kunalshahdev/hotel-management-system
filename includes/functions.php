<?php

function format_currency($amount) {
    return 'NPR ' . number_format((float)$amount, 2);
}

function format_date($date, $format = 'd M Y') {
    if (!$date) return '—';
    return date($format, strtotime($date));
}

function format_datetime($datetime) {
    if (!$datetime) return '—';
    return date('d M Y, h:i A', strtotime($datetime));
}

function badge_class($status) {
    $map = [
        'Available'   => 'badge-success',
        'Occupied'    => 'badge-danger',
        'Maintenance' => 'badge-warning',
        'Reserved'    => 'badge-info',
        'Confirmed'   => 'badge-info',
        'Checked-In'  => 'badge-success',
        'Checked-Out' => 'badge-muted',
        'Cancelled'   => 'badge-danger',
        'Paid'        => 'badge-success',
        'Pending'     => 'badge-warning',
        'Refunded'    => 'badge-danger',
    ];
    return $map[$status] ?? 'badge-muted';
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function flash($type = null, $message = null) {
    if ($type && $message) {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
        return;
    }
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function room_type_color($type) {
    $map = [
        'Standard' => '#3498DB',
        'Deluxe'   => '#DC143C',
        'Suite'    => '#003893',
        'Family'   => '#2ECC71',
    ];
    return $map[$type] ?? '#7F8C9B';
}

function calc_nights($check_in, $check_out) {
    $d1 = new DateTime($check_in);
    $d2 = new DateTime($check_out);
    return max(1, $d2->diff($d1)->days);
}

function booking_id($id) {
    return 'BK' . str_pad($id, 4, '0', STR_PAD_LEFT);
}

function is_page($page) {
    return basename($_SERVER['PHP_SELF']) === $page;
}

function nav_active($page) {
    return is_page($page) ? 'active' : '';
}

