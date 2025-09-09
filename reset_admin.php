<?php
include('./conn/conn.php'); // Ensure your database connection is correct

$username = "admin"; 
$password = "Admin123"; 

// Hash the password correctly
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Delete old admin user if it exists
$stmt = $pdo->prepare("DELETE FROM users WHERE username = ?");
$stmt->execute([$username]);

// Insert new admin user
$stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
if ($stmt->execute([$username, $hashed_password])) {
    echo "✅ Admin user has been reset successfully!";
} else {
    echo "❌ Error: Could not insert admin user.";
}
?>
