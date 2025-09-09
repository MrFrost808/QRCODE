<?php
include("../conn/conn.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['student_no'], $_POST['student_name'], $_POST['course'], $_POST['year_level'], $_POST['generated_code'])) {
        $studentNo = $_POST['student_no'];
        $studentName = $_POST['student_name'];
        $studentCourse = $_POST['course'];
        $studentYear = $_POST['year_level'];
        $generatedCode = $_POST['generated_code'];

        try {
            $stmt = $pdo->prepare("INSERT INTO tbl_student (student_no, student_name, course, year_level, generated_code) 
                                   VALUES (:student_no, :student_name, :course, :year_level, :generated_code)");

            $stmt->bindParam(":student_no", $studentNo, PDO::PARAM_STR);
            $stmt->bindParam(":student_name", $studentName, PDO::PARAM_STR);
            $stmt->bindParam(":course", $studentCourse, PDO::PARAM_STR);
            $stmt->bindParam(":year_level", $studentYear, PDO::PARAM_INT);
            $stmt->bindParam(":generated_code", $generatedCode, PDO::PARAM_STR);

            $stmt->execute();
            header("Location: http://localhost/QRCODE/masterlist.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "<script>
                alert('Please fill in all fields!');
                window.location.href = 'http://localhost/QRCODE/masterlist.php';
              </script>";
    }
}
