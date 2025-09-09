<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log-In</title>
    <style>
body {
    font-family: Arial, sans-serif;
    background-image: url('./School.jpg'); /* Make sure the path is correct */
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    height: 100vh;
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
}

.log-in {
    background: white;
    padding: 20px;
    border-radius: 20px; /* Reduced for better design */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    width: 350px; /* Slightly wider */
    text-align: center;
}

.log-in h2 {
    margin-bottom: 20px;
}

.log-in label {
    display: block;
    margin-top: 10px;
    text-align: left;
}

.log-in input {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.log-in button {
    margin-top: 20px;
    padding: 10px 15px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: 0.3s;
}

.log-in button:hover {
    background-color: #003b80;
    transform: scale(1.05);
}

.error {
    color: red;
    margin-top: 10px;
}

    </style>
</head>
<body>
    <?php
    session_start();

    // Database connection
    $host = "localhost"; // XAMPP default is localhost
    $dbname = "qr_attendance_db"; // Replace with your database name
    $username = "root"; // XAMPP default username
    $password = ""; // XAMPP default password

    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("❌ Connection failed: " . $conn->connect_error);
    }

    // Handle login form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
        $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');

        // Fetch user from the database
        $stmt = $conn->prepare("SELECT user_id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verify the hashed password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                header("Location: index.php"); // Redirect to the dashboard
                exit();
            } else {
                $error_message = "Incorrect password!";
            }
        } else {
            $error_message = "User not found!";
        }
    }
    ?>
    <div class="log-in">
        <h2>Login</h2>
        <?php if (!empty($error_message)): ?>
            <p class="error"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
