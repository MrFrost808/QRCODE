<?php
include("../conn/conn.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['student_no'], $_POST['tbl_student_id'], $_POST['student_name'], $_POST['course_year'])) {
        $studentNo = $_POST['student_no'];
        $studentId = $_POST['tbl_student_id'];
        $studentName = $_POST['student_name'];
        $studentCourse = $_POST['course_year'];

        try {
            // Corrected SQL syntax
            $stmt = $pdo->prepare("UPDATE tbl_student 
                                   SET student_no = :student_no, 
                                       student_name = :student_name, 
                                       course_year = :course_year 
                                   WHERE tbl_student_id = :tbl_student_id");

            // Corrected parameter bindings
            $stmt->bindParam(":student_no", $studentNo, PDO::PARAM_STR);
            $stmt->bindParam(":student_name", $studentName, PDO::PARAM_STR);
            $stmt->bindParam(":course_year", $studentCourse, PDO::PARAM_STR);
            $stmt->bindParam(":tbl_student_id", $studentId, PDO::PARAM_STR);

            $stmt->execute();

            // Redirect after successful update
            header("Location: http://localhost/QRCODE/masterlist.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "
            <script>
                alert('Please fill in all fields!');
                window.location.href = 'http://localhost/QRCODE/masterlist.php';
            </script>
        ";
    }
}
?>
