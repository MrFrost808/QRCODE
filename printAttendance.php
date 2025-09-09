<?php
include('./conn/conn.php'); // Ensure this connects to your database

// Get selected filter type (default is "daily")
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'daily';

// Pagination settings
$limit = 10;  // Records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;  // Current page
$offset = ($page - 1) * $limit;  // Offset for SQL query

// Set the date condition based on the filter type
switch ($filter) {
    case 'weekly':
        $dateCondition = "time_in >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        break;
    case 'monthly':
        $dateCondition = "time_in >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        break;
    case 'annually':
        $dateCondition = "YEAR(time_in) = YEAR(CURDATE())";
        break;
    case 'semester':
        $dateCondition = "(MONTH(time_in) BETWEEN 6 AND 10 AND YEAR(time_in) = YEAR(CURDATE())) 
                          OR 
                          (MONTH(time_in) BETWEEN 11 AND 5 AND YEAR(time_in) = YEAR(CURDATE()) - 1)";
        break;
    default:
        $dateCondition = "DATE(time_in) = CURDATE()";
}

// Main query for fetching records
switch ($filter) {
    case 'weekly':
        $query = "SELECT * FROM tbl_attendance_archive WHERE $dateCondition ORDER BY time_in DESC LIMIT $limit OFFSET $offset";
        break;
    case 'monthly':
        $query = "SELECT * FROM tbl_attendance_archive WHERE $dateCondition ORDER BY time_in DESC LIMIT $limit OFFSET $offset";
        break;
    case 'annually':
        $query = "SELECT * FROM tbl_attendance_archive WHERE $dateCondition ORDER BY time_in DESC LIMIT $limit OFFSET $offset";
        break;
    case 'semester':
        $query = "SELECT * FROM tbl_attendance_archive WHERE $dateCondition ORDER BY time_in DESC LIMIT $limit OFFSET $offset";
        break;
    default:
        $query = "SELECT * FROM tbl_attendance_archive WHERE $dateCondition ORDER BY time_in DESC LIMIT $limit OFFSET $offset";
}

$stmt = $pdo->prepare($query);
$stmt->execute();
$attendances = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total number of records for pagination
$totalQuery = "SELECT COUNT(*) FROM tbl_attendance_archive WHERE $dateCondition";
$totalStmt = $pdo->prepare($totalQuery);
$totalStmt->execute();
$totalRecords = $totalStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Attendance Records</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        a {
            margin-bottom: 20px; 
        }           
        .print-container {
            max-width: 900px;
            margin: auto;
        }

        .btn-print {
            margin-bottom: 20px;
        }

        @media print {
            .btn-print {
                display: none;
            }
        }
        #reader { 
        display: none !important; 
        width: 0; 
        height: 0; 
        overflow: hidden;
    }
    </style>
</head>
<body>

    <div class="print-container">
        <h2 class="text-center">Archived Attendance Records</h2>

        <!-- Filter Selection -->
        <form method="GET" class="mb-3">
            <label for="filter">Filter By:</label>
            <select name="filter" id="filter" class="form-control" onchange="this.form.submit()">
                <option value="daily" <?= ($filter == 'daily') ? 'selected' : '' ?>>Daily</option>
                <option value="weekly" <?= ($filter == 'weekly') ? 'selected' : '' ?>>Weekly</option>
                <option value="monthly" <?= ($filter == 'monthly') ? 'selected' : '' ?>>Monthly</option>
                <option value="annually" <?= ($filter == 'annually') ? 'selected' : '' ?>>Annually</option>
                <option value="semester" <?= ($filter == 'semester') ? 'selected' : '' ?>>Semester</option>
            </select>
        </form>

        <button class="btn btn-primary btn-print" onclick="window.print()">Print Attendance</button>
        <a class="btn btn-primary" href="masterlist.php">Back</a>

        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Student No</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($attendances)): ?>
                    <?php $count = ($page - 1) * $limit + 1; ?>
                    <?php foreach ($attendances as $row): ?>
                        <tr>
                            <td><?= $count++; ?></td>
                            <td><?= htmlspecialchars($row['student_no']) ?></td>
                            <td><?= htmlspecialchars($row['student_name']) ?></td>
                            <td><?= htmlspecialchars($row['course']) ?></td>
                            <td><?= htmlspecialchars($row['year_level']) ?></td>
                            <td><?= htmlspecialchars($row['time_in']) ?></td>
                            <td><?= htmlspecialchars($row['time_out']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center">No archived records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination Links -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($page == 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?filter=<?= $filter ?>&page=1" aria-label="First">
                        <span aria-hidden="true">&laquo;&laquo;</span>
                    </a>
                </li>
                <li class="page-item <?= ($page == 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?filter=<?= $filter ?>&page=<?= $page - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <li class="page-item <?= ($page == $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?filter=<?= $filter ?>&page=<?= $page + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
                <li class="page-item <?= ($page == $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?filter=<?= $filter ?>&page=<?= $totalPages ?>" aria-label="Last">
                        <span aria-hidden="true">&raquo;&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

</body>
</html>
