<?php
include('./conn/conn.php'); // Ensure database connection

$username = "admin"; // Set admin username
$password = "Admin123"; // Set admin password

// Hash the password for security
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if the admin already exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$result = $stmt->fetch();

if ($result) {
    echo "⚠️ Admin user already exists!";
} else {
    // Insert the admin user
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
    
    if ($stmt->execute([$username, $hashed_password])) {
        echo "✅ Admin user created successfully!";
    } else {
        echo "❌ Error: Could not insert admin user.";
    }
}
?>
