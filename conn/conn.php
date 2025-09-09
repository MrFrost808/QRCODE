<?php
$host = "localhost"; // XAMPP default is localhost
$dbname = "qr_attendance_db"; // Make sure this matches your database
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP has no password


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
