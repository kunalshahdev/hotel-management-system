<?php

if (defined('DB_INCLUDED')) return;
define('DB_INCLUDED', true);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'hotel_reservation');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die('<div style="padding:40px;font-family:sans-serif;color:#E74C3C;">
        <h2>Database Connection Failed</h2>
        <p>' . $conn->connect_error . '</p>
    </div>');
}
$conn->set_charset('utf8mb4');

function db_query($sql, $types = '', $params = []) {
    global $conn;
    if (empty($types)) {
        return $conn->query($sql);
    }
    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result === false) {
        $affected = $stmt->affected_rows;
        $insert_id = $stmt->insert_id;
        $stmt->close();
        // Return an object with affected_rows and insert_id for INSERT/UPDATE/DELETE
        $r = new stdClass();
        $r->affected_rows = $affected;
        $r->insert_id = $insert_id;
        return $r;
    }
    $stmt->close();
    return $result;
}

function db_fetch($sql, $types = '', $params = []) {
    $result = db_query($sql, $types, $params);
    if ($result instanceof mysqli_result) {
        return $result->fetch_assoc();
    }
    return null;
}

function db_fetch_all($sql, $types = '', $params = []) {
    $result = db_query($sql, $types, $params);
    if ($result instanceof mysqli_result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

function db_value($sql, $types = '', $params = []) {
    $row = db_fetch($sql, $types, $params);
    return $row ? reset($row) : null;
}

function db_insert_id() {
    global $conn;
    return $conn->insert_id;
}

function db_escape($str) {
    global $conn;
    return $conn->real_escape_string($str);
}
