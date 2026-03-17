<?php
require_once 'includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_destroy();
header('Location: /Hotel_Reservation_System/login.php');
exit;