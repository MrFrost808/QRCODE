
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Attendance System</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- Data Table -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />

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

        .student-container {
            height: 90%;
            width: 90%;
            border-radius: 20px;
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.8);
        }

        .student-container > div {
            box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
            border-radius: 10px;
            padding: 30px;
            max-height: 500px;
            overflow-y: auto;
        }

        .title {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        table.dataTable thead > tr > th.sorting, table.dataTable thead > tr > th.sorting_asc, table.dataTable thead > tr > th.sorting_desc, table.dataTable thead > tr > th.sorting_asc_disabled, table.dataTable thead > tr > th.sorting_desc_disabled, table.dataTable thead > tr > td.sorting, table.dataTable thead > tr > td.sorting_asc, table.dataTable thead > tr > td.sorting_desc, table.dataTable thead > tr > td.sorting_asc_disabled, table.dataTable thead > tr > td.sorting_desc_disabled {
            text-align: center;
        }

    </style>
</head>
<body>


</div>



    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand ml-4" href="#">QR Code Attendance System</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">

                    <a class="nav-link" href="./masterlist.php">List of Students</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="./Statistics.php">Statistics</a>
                </li>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./printAttendance.php">History</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
            <li class="nav-item mr-3">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item mr-3">
                    <a class="nav-link" href="Log-In.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="main">
        
        <div class="student-container">
            <div class="student-list">
                <div class="title">
                    <h4>List of Students</h4>
                    <button class="btn btn-dark" data-toggle="modal" data-target="#addStudentModal">Add Student</button>
                </div>
                <hr>
                <div class="table-container table-responsive">
                    <table class="table text-center table-sm" id="studentTable">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Student No.</th>
                                <th scope="col">Name</th>
                                <th scope="col">Course</th>
                                <th scope="col">Year</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php 
                               
                               include('./conn/conn.php'); // Include your database connection file

                               try {
                                   // Prepare and execute the query
                                   $stmt = $pdo->prepare("SELECT tbl_student_id, student_no, student_name, course, year_level, generated_code FROM tbl_student");
                                   // Replace tbl_student with your table name
                                   $stmt->execute();
                               
                                   // Fetch all data as an associative array
                                   $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                               
                                   // If no data is found, initialize $result as an empty array to avoid errors
                                   if (!$result) {
                                       $result = [];
                                   }
                               } catch (PDOException $e) {
                                   die("Error fetching data: " . $e->getMessage());
                               }
                               
                
                                foreach ($result as $row) {
                                    $studentID = $row["tbl_student_id"];
                                    $studentNo = $row["student_no"];
                                    $studentName = $row["student_name"];
                                    $studentCourse = $row["course"];
                                    $studentYear = $row["year_level"];
                                    $qrCode = $row["generated_code"];
                                ?>

                                <tr>
                                    <th scope="row"><?= htmlspecialchars($studentID) ?></th>

                                    <td id="studentNo-<?= $studentID ?>"><?= $studentNo ?></td>
                                    <td id="studentName-<?= $studentID ?>"><?= $studentName ?></td>
                                    <td id="studentCourse-<?= $studentID ?>"><?= $studentCourse ?></td>
                                    <td id="studentYear-<?= $studentID ?>"><?= $studentYear ?></td>
                                    <td>
                                        <div class="action-button">
                                            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#qrCodeModal<?= $studentID ?>"><img src="https://cdn-icons-png.flaticon.com/512/1341/1341632.png" alt="" width="16"></button>

                                            <!-- QR Modal -->
                                            <div class="modal fade" id="qrCodeModal<?= $studentID ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"><?= $studentName ?>'s QR Code</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= $qrCode ?>" alt="" width="300">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <button class="btn btn-secondary btn-sm" onclick="updateStudent(<?= $studentID ?>)">&#128393;</button>
                                            <button class="btn btn-danger btn-sm" onclick="deleteStudent(<?= $studentID ?>)">&#10006;</button>
                                        </div>
                                    </td>
                                </tr>

                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addStudentModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="addStudent" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <li class="nav-item">
                <div class="modal-header">
                    
                    <h5 class="modal-title" id="addStudent">Add Student</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="./endpoint/add-student.php" method="POST">
                    <div class="form-group">
                            <label for="studentNo">Student No:</label>
                            <input type="text" class="form-control" id="studentNo" name="student_no">
                        </div>
                        <div class="form-group">
                            <label for="studentName">Full Name:</label>
                            <input type="text" class="form-control" id="studentName" name="student_name">
                        </div>
                        <div class="form-group">
                        <label for="studentCourse">Course:</label>
                        <input type="text" class="form-control" id="studentCourse" name="course">
                    </div>
                    <div class="form-group">
                        <label for="studentYear">Year:</label>
                        <select class="form-control" id="studentYear" name="year_level">
                            <option value="1st Year">1st Year</option>
                            <option value="2nd Year">2nd Year</option>
                            <option value="3rd Year">3rd Year</option>
                            <option value="4th Year">4th Year</option>
                        </select>
                    </div>
                        <button type="button" class="btn btn-secondary form-control qr-generator" onclick="generateQrCode()">Generate QR Code</button>

                        <div class="qr-con text-center" style="display: none;">
                            <input type="hidden" class="form-control" id="generatedCode" name="generated_code">
                            <p>Take a pic with your qr code.</p>
                            <img class="mb-4" src="" id="qrImg" alt="">
                        </div>
                        <div class="modal-footer modal-close" style="display: none;">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-dark">Add List</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Modal -->
    <div class="modal fade" id="updateStudentModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="updateStudent" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStudent">Update Student</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="./endpoint/update-student.php" method="POST">
                        <input type="hidden" class="form-control" id="updateStudentId" name="tbl_student_id">
                        <div class="form-group">
                            <label for="updateStudentNo">Student No:</label>
                            <input type="text" class="form-control" id="updateStudentNo" name="student_no">
                        </div>
                        <div class="form-group">
                            <label for="updateStudentName">Full Name:</label>
                            <input type="text" class="form-control" id="updateStudentName" name="student_name">
                        </div>
                        <div class="form-group">
                            <label for="updateStudentCourse">Course and Section:</label>
                            <input type="text" class="form-control" id="updateStudentCourse" name="course_year">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-dark">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

    <!-- Data Table -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>

    <script>
 $(document).ready(function () {
    var table = $('#studentTable').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "lengthMenu": [10],
        "language": {
            "search": "Search Student: "
        }
    });

    // Custom search function
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        var searchValue = $('#studentTable_filter input').val().toLowerCase();
        var studentName = data[2].toLowerCase(); // ✅ Column index for "Name" (adjust if necessary)

        // ✅ Show only names that start with the search letter
        if (studentName.startsWith(searchValue) || searchValue === "") {
            return true;
        }
        return false;
    });

    // ✅ Re-run search when user types
    $('#studentTable_filter input').on('keyup', function () {
        table.draw();
    });
});



        function updateStudent(id) {
    $("#updateStudentModal").modal("show");

    let updateStudentId = $("#studentID-" + id).text().trim();
    let updateStudentNo = $("#studentNo-" + id).text().trim();
    let updateStudentName = $("#studentName-" + id).text().trim();
    let updateStudentCourse = $("#studentCourse-" + id).text().trim();
    let updateStudentYear = $("#studentYear_level-" + id).text().trim();

    $("#updateStudentId").val(updateStudentId);
    $("#updateStudentNo").val(updateStudentNo);
    $("#updateStudentName").val(updateStudentName);
    $("#updateStudentCourse").val(updateStudentCourse);
    $("#updateStudentYear").val(updateStudentYear);
}


        function deleteStudent(id) {
            if (confirm("Do you want to delete this student?")) {
                window.location = "./endpoint/delete-student.php?student=" + id;
            }
        }

        function generateRandomCode(length) {
            const characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            let randomString = '';

            for (let i = 0; i < length; i++) {
                const randomIndex = Math.floor(Math.random() * characters.length);
                randomString += characters.charAt(randomIndex);
            }

            return randomString;
        }

        function generateQrCode() {
            const qrImg = document.getElementById('qrImg');

            let text = generateRandomCode(10);
            $("#generatedCode").val(text);

            if (text === "") {
                alert("Please enter text to generate a QR code.");
                return;
            } else {
                const apiUrl = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(text)}`;

                qrImg.src = apiUrl;
                document.getElementById('studentNo').style.pointerEvents = 'none';

                document.getElementById('studentName').style.pointerEvents = 'none';
                document.getElementById('studentCourse').style.pointerEvents = 'none';
                document.getElementById('studentYear').style.pointerEvents = 'none';
                document.querySelector('.modal-close').style.display = '';
                document.querySelector('.qr-con').style.display = '';
                document.querySelector('.qr-generator').style.display = 'none';
            }
        }
    </script>
</body>
</html>