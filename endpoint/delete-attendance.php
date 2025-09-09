<?php
include('../conn/conn.php'); // Adjust the path if necessary

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attendance_id'])) {
    $attendanceID = $_POST['attendance_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM tbl_attendance WHERE tbl_attendance_id = :attendance_id");
        $stmt->bindParam(':attendance_id', $attendanceID, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "success"; // This message is sent back to AJAX
        } else {
            echo "error"; // No rows were deleted
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request!";
}
?>
