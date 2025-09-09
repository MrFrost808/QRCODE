<?php
include('./conn/conn.php');

try {
    // Insert summarized attendance into `tbl_attendance_statistics`
    $stmt = $pdo->prepare("
        INSERT INTO tbl_attendance_statistics (course, year_level, attendance_count, record_date)
        SELECT s.course, s.year_level, COUNT(a.tbl_student_id), DATE(a.time_in)
        FROM tbl_attendance a
        JOIN tbl_student s ON a.tbl_student_id = s.tbl_student_id
        WHERE DATE(a.time_in) < CURDATE()
        GROUP BY s.course, s.year_level, DATE(a.time_in)
    ");
    $stmt->execute();

    // Move detailed records to archive (storing date and time as combined)
    $stmt = $pdo->prepare("
        INSERT INTO tbl_attendance_archive (tbl_student_id, student_no, student_name, course, year_level, time_in, time_out)
        SELECT 
            s.tbl_student_id, 
            s.student_no, 
            s.student_name, 
            s.course, 
            s.year_level, 
            GROUP_CONCAT(a.time_in ORDER BY a.time_in SEPARATOR ', ') AS time_in, 
            GROUP_CONCAT(a.time_out ORDER BY a.time_out SEPARATOR ', ') AS time_out
        FROM tbl_attendance a
        JOIN tbl_student s ON a.tbl_student_id = s.tbl_student_id
        WHERE DATE(a.time_in) < CURDATE()
        GROUP BY s.tbl_student_id, s.student_no, s.student_name, s.course, s.year_level
        ON DUPLICATE KEY UPDATE 
            time_in = VALUES(time_in),
            time_out = VALUES(time_out)
    ");
    $stmt->execute();

    // Delete old records from `tbl_attendance`
    $stmt = $pdo->prepare("DELETE FROM tbl_attendance WHERE DATE(time_in) < CURDATE()");
    $stmt->execute();

    echo "Attendance archived and statistics updated successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
