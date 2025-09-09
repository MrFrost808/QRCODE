<?php
include('./conn/conn.php'); // Ensure database connection

// Fetch unique courses from the database
$courseQuery = "SELECT DISTINCT course FROM tbl_attendance_statistics";
$courseStmt = $pdo->prepare($courseQuery);
$courseStmt->execute();
$coursesList = $courseStmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch unique year levels from the database
$yearQuery = "SELECT DISTINCT year_level FROM tbl_attendance_statistics";
$yearStmt = $pdo->prepare($yearQuery);
$yearStmt->execute();
$yearLevels = $yearStmt->fetchAll(PDO::FETCH_COLUMN);

// Get filter values from request
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'daily';
$selectedCourse = isset($_GET['course']) ? $_GET['course'] : '';
$selectedYear = isset($_GET['year']) ? $_GET['year'] : '';

$dateCondition = "";
switch ($filter) {
    case 'weekly':
        $dateCondition = "YEARWEEK(record_date, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'monthly':
        $dateCondition = "YEAR(record_date) = YEAR(CURDATE()) AND MONTH(record_date) = MONTH(CURDATE())";
        break;
    case 'annually':
        $dateCondition = "YEAR(record_date) = YEAR(CURDATE())";
        break;
    case 'semester':
        $currentMonth = date('m');
        $currentYear = date('Y');
        if ($currentMonth >= 6 && $currentMonth <= 11) {
            $dateCondition = "record_date BETWEEN '$currentYear-06-01' AND '$currentYear-11-30'";
        } else {
            $dateCondition = "(record_date BETWEEN '$currentYear-01-01' AND '$currentYear-05-31')";
        }
        break;
    default:
        $dateCondition = "record_date = CURDATE()";
        break;
}

// Additional conditions for course and year level
$courseCondition = $selectedCourse ? " AND course LIKE '$selectedCourse%'" : "";
$yearCondition = $selectedYear ? " AND year_level = '$selectedYear'" : "";

// Fetch attendance statistics
$query = "SELECT record_date, course, year_level, SUM(attendance_count) as attendance_count 
          FROM tbl_attendance_statistics 
          WHERE $dateCondition $courseCondition $yearCondition 
          GROUP BY record_date, course, year_level 
          ORDER BY record_date ASC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for Chart.js
$dates = [];
$courseWiseData = [];
$courses = [];

foreach ($stats as $row) {
    $date = $row['record_date'];
    $course = $row['course'];

    if (!in_array($date, $dates)) {
        $dates[] = $date;
    }
    if (!in_array($course, $courses)) {
        $courses[] = $course;
    }

    $courseWiseData[$course][$date] = $row['attendance_count'];
}

// Ensure every course has values for every date (fill missing values with 0)
foreach ($courses as $course) {
    foreach ($dates as $date) {
        if (!isset($courseWiseData[$course][$date])) {
            $courseWiseData[$course][$date] = 0;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Attendance System</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(to bottom, rgba(255,255,255,0.15) 0%, rgba(0,0,0,0.15) 100%), 
                        url('./School.jpg');
            background-blend-mode: multiply, multiply;
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-size: cover;
        }
        .container { margin-top: 30px; }
        .card { background: rgba(255, 255, 255, 0.9); border-radius: 10px; padding: 20px; }
        h2 { text-align: center; margin-bottom: 20px; font-weight: bold; }
        canvas { width: 100% !important; height: 400px !important; }
        @media print {
            .btn-print, .form-control { display: none; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand ml-4" href="#">QR Code Attendance System</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a class="nav-link" href="./masterlist.php">List of Students</a></li>
                <li class="nav-item active"><a class="nav-link" href="./Statistics.php">Statistics</a></li>
                <li class="nav-item"><a class="nav-link" href="./printAttendance.php">History</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <h2>Attendance Statistics</h2>
            <form method="GET" class="mb-3">
                <label for="filter">Filter By:</label>
                <select name="filter" id="filter" class="form-control" onchange="this.form.submit()">
                    <option value="daily" <?= $filter == 'daily' ? 'selected' : '' ?>>Daily</option>
                    <option value="weekly" <?= $filter == 'weekly' ? 'selected' : '' ?>>Weekly</option>
                    <option value="monthly" <?= $filter == 'monthly' ? 'selected' : '' ?>>Monthly</option>
                    <option value="annually" <?= $filter == 'annually' ? 'selected' : '' ?>>Annually</option>
                    <option value="semester" <?= $filter == 'semester' ? 'selected' : '' ?>>Semester</option>
                </select>
                <label for="course">Course:</label>
                <select name="course" id="course" class="form-control" onchange="this.form.submit()">
                    <option value="">All</option>
                    <?php foreach ($coursesList as $course): ?>
                        <option value="<?= $course ?>" <?= $selectedCourse == $course ? 'selected' : '' ?>><?= $course ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="year">Year Level:</label>
                <select name="year" id="year" class="form-control" onchange="this.form.submit()">
                    <option value="">All</option>
                    <?php foreach ($yearLevels as $year): ?>
                        <option value="<?= $year ?>" <?= $selectedYear == $year ? 'selected' : '' ?>><?= $year ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
            <canvas id="attendanceChart"></canvas>
            <button class="btn btn-primary btn-print" onclick="window.print()">Print</button>
        </div>
    </div>

    <script>
    var ctx = document.getElementById('attendanceChart').getContext('2d');
    var attendanceChart = new Chart(ctx, {
        type: 'bar', // Change from 'line' to 'bar' to create a bar chart
        data: {
            labels: <?= json_encode($dates) ?>,
            datasets: <?= json_encode(array_map(function($course) use ($courseWiseData, $dates) {
                return [
                    'label' => $course,
                    'data' => array_map(function($date) use ($courseWiseData, $course) {
                        return $courseWiseData[$course][$date];
                    }, $dates),
                    'backgroundColor' => '#' . substr(md5(rand()), 0, 6), // Bar color
                    'borderColor' => '#' . substr(md5(rand()), 0, 6), // Border color
                    'borderWidth' => 1
                ];
            }, $courses)) ?>
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Attendance Count'
                    },
                    min: 0
                }
            }
        }
    });
</script>
</body>
</html>
