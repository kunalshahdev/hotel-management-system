<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

require_admin();

$currentUser = current_user();
$currentPage = basename($_SERVER['PHP_SELF']);

$pageTitles = [
    'index.php'           => 'Dashboard',
    'rooms.php'           => 'Rooms',
    'room_form.php'       => isset($_GET['id']) ? 'Edit Room' : 'Add Room',
    'bookings.php'        => 'Bookings',
    'booking_form.php'    => isset($_GET['id']) ? 'Edit Booking' : 'New Booking',
    'customers.php'       => 'Customers',
    'customer_form.php'   => isset($_GET['id']) ? 'Edit Customer' : 'Add Customer',
    'reports.php'         => 'Reports',
    'front_desk.php'      => 'Front Desk',
];
$pageTitle = $pageTitles[$currentPage] ?? 'NepStay';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle); ?> — NepStay HMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Hotel_Reservation_System/assets/css/style.css">
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/>
                <path d="M9 9h1"/><path d="M9 13h1"/><path d="M9 17h1"/>
            </svg>
        </div>
        <div class="brand-text">
            <span class="brand-name">NepStay</span>
            <span class="brand-sub">Hotel Management</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <span class="nav-label">MAIN MENU</span>

            <a href="/Hotel_Reservation_System/admin/index.php" class="nav-item <?php echo nav_active('index.php'); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                <span>Dashboard</span>
            </a>

            <a href="/Hotel_Reservation_System/admin/rooms.php" class="nav-item <?php echo (nav_active('rooms.php') || nav_active('room_form.php')) ? 'active' : ''; ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 20v-8a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v8"/><path d="M4 10V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4"/><path d="M12 4v6"/><path d="M2 18h20"/></svg>
                <span>Rooms</span>
            </a>

            <a href="/Hotel_Reservation_System/admin/bookings.php" class="nav-item <?php echo (nav_active('bookings.php') || nav_active('booking_form.php')) ? 'active' : ''; ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/></svg>
                <span>Bookings</span>
            </a>

            <a href="/Hotel_Reservation_System/admin/customers.php" class="nav-item <?php echo (nav_active('customers.php') || nav_active('customer_form.php')) ? 'active' : ''; ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <span>Customers</span>
            </a>
        </div>

        <div class="nav-section">
            <span class="nav-label">OPERATIONS</span>

            <a href="/Hotel_Reservation_System/admin/front_desk.php" class="nav-item <?php echo nav_active('front_desk.php'); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 20h20"/><path d="M5 20v-4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v4"/><path d="M12 15V9"/><circle cx="12" cy="5" r="2"/></svg>
                <span>Front Desk</span>
            </a>

            <a href="/Hotel_Reservation_System/admin/reports.php" class="nav-item <?php echo nav_active('reports.php'); ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
                <span>Reports</span>
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        <a href="/Hotel_Reservation_System/logout.php" class="nav-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            <span>Logout</span>
        </a>
    </div>
</aside>

<div class="main-wrapper">

    <!-- Topbar -->
    <header class="topbar">
        <div class="topbar-left">
            <button class="topbar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <h1 class="topbar-title"><?php echo e($pageTitle); ?></h1>
        </div>
        <div class="topbar-right">
            <div class="topbar-user" id="userDropdownToggle">
                <div class="topbar-avatar"><?php echo strtoupper(substr($currentUser['name'] ?? 'A', 0, 1)); ?></div>
                <div class="topbar-user-info">
                    <span class="topbar-user-name"><?php echo e($currentUser['name'] ?? 'Admin'); ?></span>
                    <span class="topbar-user-role"><?php echo e(ucfirst($currentUser['role'] ?? 'admin')); ?></span>
                </div>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                <!-- Dropdown -->
                <div class="dropdown-menu" id="userDropdown">
                    <a href="/Hotel_Reservation_System/logout.php" class="dropdown-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    <?php $flash = flash(); if ($flash): ?>
    <div class="alert alert-<?php echo $flash['type']; ?>" data-auto-dismiss>
        <span class="alert-text"><?php echo e($flash['message']); ?></span>
        <button class="alert-close" onclick="this.parentElement.remove()">×</button>
    </div>
    <?php endif; ?>

    <!-- Content wrapper -->
    <main class="content">
