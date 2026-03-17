<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$isLoggedIn = is_customer_logged_in();
$customer = $isLoggedIn ? current_customer() : null;
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle ?? 'NepStay'); ?> — NepStay Hotel</title>
    <meta name="description" content="Book premium hotel rooms at the best prices. NepStay — Your perfect stay awaits.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Hotel_Reservation_System/assets/css/style.css">
    <link rel="stylesheet" href="/Hotel_Reservation_System/assets/css/customer.css">
</head>
<body class="customer-body">

<nav class="c-navbar" id="cNavbar">
    <div class="c-container">
        <a href="/Hotel_Reservation_System/index.php" class="c-navbar-brand">
            <div class="c-brand-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/>
                    <path d="M9 9h1"/><path d="M9 13h1"/><path d="M9 17h1"/>
                </svg>
            </div>
            <span>NepStay</span>
        </a>

        <div class="c-nav-links" id="cNavLinks">
            <a href="/Hotel_Reservation_System/index.php" class="c-nav-link <?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">Home</a>
            <a href="/Hotel_Reservation_System/customer/rooms.php" class="c-nav-link <?php echo $currentPage === 'rooms.php' ? 'active' : ''; ?>">Rooms</a>
            <?php if ($isLoggedIn): ?>
            <a href="/Hotel_Reservation_System/customer/dashboard.php" class="c-nav-link <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">My Dashboard</a>
            <a href="/Hotel_Reservation_System/customer/bookings.php" class="c-nav-link <?php echo $currentPage === 'bookings.php' ? 'active' : ''; ?>">My Bookings</a>
            <?php endif; ?>
        </div>

        <div class="c-nav-actions">
            <?php if ($isLoggedIn): ?>
            <div class="c-user-menu" id="cUserMenu">
                <div class="c-user-avatar"><?php echo strtoupper(substr($customer['name'] ?? 'U', 0, 1)); ?></div>
                <span class="c-user-name"><?php echo e($customer['name'] ?? 'Guest'); ?></span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                <div class="c-dropdown" id="cUserDropdown">
                    <a href="/Hotel_Reservation_System/customer/dashboard.php" class="c-dropdown-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        Dashboard
                    </a>
                    <a href="/Hotel_Reservation_System/customer/bookings.php" class="c-dropdown-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
                        My Bookings
                    </a>
                    <div class="c-dropdown-divider"></div>
                    <a href="/Hotel_Reservation_System/logout.php" class="c-dropdown-item c-dropdown-danger">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Logout
                    </a>
                </div>
            </div>
            <?php else: ?>
            <a href="/Hotel_Reservation_System/login.php" class="c-btn c-btn-outline-light">Sign In</a>
            <a href="/Hotel_Reservation_System/register.php" class="c-btn c-btn-accent">Register</a>
            <?php endif; ?>

            <button class="c-menu-toggle" id="cMenuToggle" aria-label="Menu">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
        </div>
    </div>
</nav>
