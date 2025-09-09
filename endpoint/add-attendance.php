<?php
date_default_timezone_set('Asia/Manila');
include("../conn/conn.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['qr_code'])) {
        $qrCode = $_POST['qr_code'];

        // Find student ID based on QR code
        $selectStmt = $pdo->prepare("SELECT tbl_student_id FROM tbl_student WHERE generated_code = :generated_code");
        $selectStmt->bindParam(":generated_code", $qrCode, PDO::PARAM_STR);
        $selectStmt->execute();
        $result = $selectStmt->fetch();

        if ($result) {
            $studentID = $result["tbl_student_id"];
            $currentTime = date("Y-m-d H:i:s"); // Current timestamp

            try {
                // Check if there's an existing record for today
                $checkStmt = $pdo->prepare("
                    SELECT tbl_attendance_id, time_in, time_out 
                    FROM tbl_attendance 
                    WHERE tbl_student_id = :tbl_student_id 
                    AND DATE(time_in) = CURDATE()
                ");
                $checkStmt->bindParam(":tbl_student_id", $studentID, PDO::PARAM_INT);
                $checkStmt->execute();
                $existingRecord = $checkStmt->fetch();

                if ($existingRecord) {
                    $attendanceID = $existingRecord["tbl_attendance_id"];
                    $timeInList = $existingRecord["time_in"];
                    $timeOutList = $existingRecord["time_out"];

                    // Convert stored values into arrays
                    $timeInArray = explode(", ", $timeInList);
                    $timeOutArray = ($timeOutList != NULL) ? explode(", ", $timeOutList) : [];

                    if (count($timeInArray) > count($timeOutArray)) {
                        // If there's an unmatched time_in, add a time_out
                        $timeOutArray[] = $currentTime;
                    } else {
                        // Otherwise, add a new time_in
                        $timeInArray[] = $currentTime;
                    }

                    // Convert arrays back to comma-separated strings
                    $updatedTimeIn = implode(", ", $timeInArray);
                    $updatedTimeOut = implode(", ", $timeOutArray);

                    // Update attendance record
                    $updateStmt = $pdo->prepare("
                        UPDATE tbl_attendance 
                        SET time_in = :time_in, time_out = :time_out 
                        WHERE tbl_attendance_id = :attendance_id
                    ");
                    $updateStmt->bindParam(":time_in", $updatedTimeIn, PDO::PARAM_STR);
                    $updateStmt->bindParam(":time_out", $updatedTimeOut, PDO::PARAM_STR);
                    $updateStmt->bindParam(":attendance_id", $attendanceID, PDO::PARAM_INT);
                    $updateStmt->execute();
                } else {
                    // First scan of the day: Insert new record
                    $insertStmt = $pdo->prepare("
                        INSERT INTO tbl_attendance (tbl_student_id, time_in, time_out) 
                        VALUES (:tbl_student_id, :time_in, NULL)
                    ");
                    $insertStmt->bindParam(":tbl_student_id", $studentID, PDO::PARAM_INT);
                    $insertStmt->bindParam(":time_in", $currentTime, PDO::PARAM_STR);
                    $insertStmt->execute();
                }

                // Redirect back to index page
                header("Location: http://localhost/QRCODE/index.php");
                exit();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            echo "No student found for the provided QR Code.";
        }
    } else {
        echo "
            <script>
                alert('Please fill in all fields!');
                window.location.href = 'http://localhost/QRCODE/index.php';
            </script>
        ";
    }
}