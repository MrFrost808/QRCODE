
<!DOCTYPE html>
<html lang="en">
<head>
<script src="scanner.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Attendance System</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap');

        * {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
    background: 
        linear-gradient(to bottom, rgba(255,255,255,0.15) 0%, rgba(0,0,0,0.15) 100%), 
        url('./School.jpg');
    background-blend-mode: multiply, multiply;
    background-attachment: fixed;
    background-repeat: no-repeat;
    background-size: cover;
}


        .main {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 91.5vh;
        }

        .attendance-container {
            height: 90%;
            width: 90%;
            border-radius: 20px;
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.8);
        }

        .attendance-container > div {
            box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
            border-radius: 10px;
            padding: 30px;
        }

        .attendance-container > div:last-child {
            width: 64%;
            margin-left: auto;
        }
/* Ensure the table row is relative for positioning */
.attendance-row {
    position: relative;
}

/* Hide the delete button by default */
.delete-btn {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    display: none;
    background-color: red;
    color: white;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
    border-radius: 5px;
}

/* Show delete button when hovering over a row */
.attendance-row:hover .delete-btn {
    display: block;
}

.delete-btn:hover {
    background-color: darkred;
}

    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand ml-4" href="#">QR Code Attendance System</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="./index.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
    <a class="nav-link" href="masterlist.php" data-toggle="modal" data-target="#adminLoginModal">Admin</a>
</li>


            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item mr-3">
                    <a class="nav-link" href="Log-In.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
<!-- Admin Login Modal -->
<div class="modal fade" id="adminLoginModal" tabindex="-1" aria-labelledby="adminLoginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adminLoginModalLabel">Admin Login</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="adminLoginForm">
                    <div class="form-group">
                        <label for="adminUsername">Username</label>
                        <input type="text" class="form-control" id="adminUsername" required>
                    </div>
                    <div class="form-group">
                        <label for="adminPassword">Password</label>
                        <input type="password" class="form-control" id="adminPassword" required>
                    </div>
                    <p id="loginError" class="text-danger" style="display:none;">Invalid username or password!</p>
                    <button type="submit" class="btn btn-dark btn-block">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>

    <div class="main">
        
        <div class="attendance-container row">
            <div class="qr-container col-4">
                <div class="scanner-con">
                    <h5 class="text-center">Scan your QR Code here for your attedance</h5>
                    <video id="interactive" class="viewport" width="100%">
                </div>

                <div class="qr-detected-container" style="display: none;">
                    <form action="./endpoint/add-attendance.php" method="POST">
                        <h4 class="text-center">Student QR Detected!</h4>
                        <input type="hidden" id="detected-qr-code" name="qr_code">
                        
                    </form>
                </div>
            </div>

            <div class="attendance-list">
                <h4>List of Present Students</h4>
                <div class="table-container table-responsive">
                    <table class="table text-center table-sm" id="attendanceTable">
                        <thead class="thead-dark">
                            <tr>
                            <th scope="col">#</th>
                            <th scope="col">Student No</th>
                            <th scope="col">Name</th>
                            <th scope="col">Course</th>
                            <th scope="col">Year</th>
                            <th scope="col">Time In</th>
                            <th scope="col">Time Out</th>

                            </tr>
                        </thead>
                        <tbody>

                        <?php
include('./conn/conn.php');

try {
    // Join `tbl_attendance` with `tbl_student` to fetch relevant data
    $stmt = $pdo->prepare("
        SELECT 
            a.tbl_attendance_id, 
            s.student_no,
            s.student_name, 
            s.course,
            s.year_level, 
            a.time_in,
            a.time_out
        FROM tbl_attendance a
        JOIN tbl_student s ON a.tbl_student_id = s.tbl_student_id
    ");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $count = 1;

    // Check if there are results
    if (!empty($results)) {
        foreach ($results as $row) {
            $attendanceID = $row["tbl_attendance_id"];
            $studentNo = $row["student_no"];
            $studentName = $row["student_name"];
            $studentCourse = $row["course"];
            $studentYear = $row["year_level"];
            $timeIn = $row["time_in"];
            $timeOut = $row["time_out"];
?>
            <tr class="attendance-row" data-id="<?= $attendanceID ?>">
                <th scope="row"><?= $count++ ?></th>
                <td><?= htmlspecialchars($studentNo) ?></td>
                <td><?= htmlspecialchars($studentName) ?></td>
                <td><?= htmlspecialchars($studentCourse) ?></td>
                <td><?= htmlspecialchars($studentYear) ?></td>
                <td><?= htmlspecialchars($timeIn) ?></td>
                <td><?= htmlspecialchars($timeOut) ?></td>

                <!-- Hidden Delete Button -->
                <td class="delete-container">
                    <button class="delete-btn" onclick="deleteAttendance(<?= $attendanceID; ?>)">❌</button>
                </td>
            </tr>
<?php
        }
    } else {
        echo "<tr><td colspan='6' class='text-center'>No attendance records found.</td></tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='6' class='text-danger text-center'>Error fetching attendance: " . $e->getMessage() . "</td></tr>";
}
?>



                        </tbody>
                    </table>
                </div>
            </div>
        
        </div>

    </div>
    

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

    <!-- instascan Js -->
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    



    <script>
document.getElementById("adminLoginForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent page refresh

    // Get input values
    let username = document.getElementById("adminUsername").value;
    let password = document.getElementById("adminPassword").value;

    // Check credentials (Replace with actual AJAX or database check)
    if (username === "admin" && password === "admin123") {  
        window.location.href = "masterlist.php"; // ✅ Redirect to Admin Panel
    } else {
        document.getElementById("loginError").style.display = "block"; // Show error message
    }
});
</script>

    <script>

let scanner;

function startScanner() {
    scanner = new Instascan.Scanner({ video: document.getElementById('interactive') });

    scanner.addListener('scan', function (content) {
        console.log("Scanned QR Code:", content); // Debugging output

        // Send QR code to the backend automatically
        $.ajax({
            url: "endpoint/add-attendance.php",
            type: "POST",
            data: { qr_code: content },
            success: function (response) {
                console.log("Server Response:", response);
                location.reload(); // Reload page to update the attendance list
            },
            error: function () {
                console.log("Error sending QR code to the server.");
            }
        });

        scanner.stop();
        document.querySelector(".qr-detected-container").style.display = '';
        document.querySelector(".scanner-con").style.display = 'none';
    });

    Instascan.Camera.getCameras()
        .then(function (cameras) {
            if (cameras.length > 0) {
                scanner.start(cameras[0]);
            } else {
                document.getElementById('interactive').outerHTML = "<p class='text-danger text-center'>No camera detected. Please check your device.</p>";
            }
        })
        .catch(function (err) {
            document.getElementById('interactive').outerHTML = "<p class='text-danger text-center'>Camera access error: " + err + "</p>";
        });
}


        document.addEventListener('DOMContentLoaded', startScanner);

        function deleteAttendance(id) {
    if (confirm("Do you want to remove this attendance?")) {
        $.ajax({
            url: "./endpoint/delete-attendance.php",
            type: "POST",
            data: { attendance_id: id },
            success: function (response) {
                alert("Attendance removed successfully.");
                location.reload();
            },
            error: function () {
                alert("Error removing attendance.");
            }
        });
    }
}



function markTimeOut(attendanceID) {
    if (confirm("Mark time out for this attendance?")) {
        $.ajax({
            url: "./endpoint/mark-timeout.php",
            type: "POST",
            data: { attendance_id: attendanceID },
            success: function (response) {
                alert(response);
                location.reload();
            },
            error: function () {
                alert("Error marking time out.");
            }
        });
    }
}


    </script>
    <script src="https://unpkg.com/html5-qrcode"></script>
</body>
</html>