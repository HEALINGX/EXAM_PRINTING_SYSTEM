<?php 
session_start();

// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "printing_exam";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch users' information from the database
$sql = "SELECT user_id, user_firstname, user_lastname FROM user WHERE user_role = 'Teacher'";
$resultUsers = $conn->query($sql);

// Fetch existing exams
$sql = "SELECT  s.sub_id, e.exam_id, s.sub_nameEN,s.sub_nameTH,s.sub_semester,s.sub_department,s.sub_section,s.sub_detail,
                e.exam_date, e.exam_room, e.exam_status,
                e.exam_start, e.exam_end, u.user_firstname, u.user_lastname
        FROM subject s 
        JOIN exam e ON s.sub_id = e.sub_id 
        JOIN user u ON s.teach_id = u.user_id";

$resultExams = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="adminstyles.css"> 
</head>
<body>

<!-- Navbar -->
<header class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="#">Exam Printing</a>
    <button class="btn btn-danger ml-auto" onclick="location.href='logout.php'">Logout</button>
</header>

<!-- Sidebar -->
<aside class="sidebar bg-dark text-white">
    <div class="p-4">
        <h4>Dashboard</h4>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="#">All Users</a>
            </li>
        </ul>
    </div>
</aside>

<!-- Main Content -->
<main class="container mt-5 pt-10">
    <h2>Exam File Management</h2>
    <div class="search-bar mb-3">
        <input type="text" class="form-control" id="searchInput" placeholder="Search Subject..." onkeyup="searchUsers()">
        <button class="btn btn-primary" data-toggle="modal" data-target="#addExamModal">Add Subject</button>
    </div>

    <!-- Add Exam Modal -->
    <div class="modal fade" id="addExamModal" tabindex="-1" role="dialog" aria-labelledby="addExamModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addExamModalLabel">Add Exam</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addExamForm" action="add_exam.php" method="POST">
                        <div class="form-group">
                            <label for="sub_nameEN">Subject Name (EN)</label>
                            <input type="text" class="form-control" id="sub_nameEN" name="sub_nameEN" required>
                        </div>
                        <div class="form-group">
                            <label for="sub_nameTH">Subject Name (TH)</label>
                            <input type="text" class="form-control" id="sub_nameTH" name="sub_nameTH" required>
                        </div>
                        <div class="form-group">
                            <label for="sub_semester">Semester</label>
                            <input type="text" class="form-control" id="sub_semester" name="sub_semester" required>
                        </div>
                        <div class="form-group">
                            <label for="sub_section">Section</label>
                            <input type="text" class="form-control" id="sub_section" name="sub_section" required>
                        </div>
                        <div class="form-group">
                            <label for="sub_department">Department</label>
                            <input type="text" class="form-control" id="sub_department" name="sub_department" required>
                        </div>
                        <div class="form-group">
                            <label for="sub_detail">Details</label>
                            <textarea class="form-control" id="sub_detail" name="sub_detail" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="exam_date">Exam Date</label>
                            <input type="date" class="form-control" id="exam_date" name="exam_date" required>
                        </div>
                        <div class="form-group">
                            <label for="exam_room">Exam Room</label>
                            <input type="text" class="form-control" id="exam_room" name="exam_room" required>
                        </div>
                        <div class="form-group">
                            <label for="exam_start">Start Time</label>
                            <input type="time" class="form-control" id="exam_start" name="exam_start" required>
                        </div>
                        <div class="form-group">
                            <label for="exam_end">End Time</label>
                            <input type="time" class="form-control" id="exam_end" name="exam_end" required>
                        </div>
                        <div class="form-group">
                            <label for="teacher_id">Teacher</label>
                            <select class="form-control" id="teacher_id" name="teacher_id" required>
                                <option value="" disabled selected>Select Teacher</option>
                                <?php
                                if ($resultUsers->num_rows > 0) {
                                    while ($row = $resultUsers->fetch_assoc()) {
                                        echo "<option value='" . htmlspecialchars($row['user_id']) . "'>" . htmlspecialchars($row['user_firstname'] . ' ' . $row['user_lastname']) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Exam</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<!-- Edit Exam Modal -->
<div class="modal fade" id="EditExamModal" tabindex="-1" role="dialog" aria-labelledby="EditExamModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="EditExamModalLabel">Edit Exam</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editExamForm" action="edit_subject.php" method="POST">
                    <input type="hidden" name="sub_id" id="edit_sub_id" required>
                    
                    <div class="form-group">
                        <label for="edit_sub_nameEN">Subject Name (EN)</label>
                        <input type="text" class="form-control" id="edit_sub_nameEN" name="sub_nameEN" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_sub_nameTH">Subject Name (TH)</label>
                        <input type="text" class="form-control" id="edit_sub_nameTH" name="sub_nameTH" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_sub_semester">Semester</label>
                        <input type="text" class="form-control" id="edit_sub_semester" name="sub_semester" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_sub_section">Section</label>
                        <input type="text" class="form-control" id="edit_sub_section" name="sub_section" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_sub_department">Department</label>
                        <input type="text" class="form-control" id="edit_sub_department" name="sub_department" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_sub_detail">Details</label>
                        <textarea class="form-control" id="edit_sub_detail" name="sub_detail" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_exam_date">Exam Date</label>
                        <input type="date" class="form-control" id="edit_exam_date" name="exam_date" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_exam_room">Exam Room</label>
                        <input type="text" class="form-control" id="edit_exam_room" name="exam_room" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_exam_start">Start Time</label>
                        <input type="time" class="form-control" id="edit_exam_start" name="exam_start" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_exam_end">End Time</label>
                        <input type="time" class="form-control" id="edit_exam_end" name="exam_end" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_exam_status">Exam Status</label>
                        <input type="text" class="form-control" id="edit_exam_status" name="exam_status" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_user_teacher">Teacher Name</label>
                        <input type="text" class="form-control" id="edit_user_teacher" name="user_teacher" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Exam</button>
                </form>
            </div>
        </div>
    </div>
</div>


    <table class="table table-bordered mt-4">
        <thead class="thead-light">
            <tr>
                <th>Subject Name</th>
                <th>Exam Date</th>
                <th>Room</th>
                <th>Time</th>
                <th>Teacher Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="examTableBody">
            <?php
            if ($resultExams->num_rows > 0) {
                while ($row = $resultExams->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['sub_nameEN']) . "</td>
                            <td>" . htmlspecialchars($row['exam_date']) . "</td>
                            <td>" . htmlspecialchars($row['exam_room']) . "</td>
                            <td>" . htmlspecialchars($row['exam_start']) . " - " . htmlspecialchars($row['exam_end']) . "</td>
                            <td>" . htmlspecialchars($row['user_firstname']) . "  " . htmlspecialchars($row['user_lastname']) . "</td>
                            <td>
                                <button class='btn btn-warning' onclick='editExam(" . json_encode($row) . ")'>Edit</button>
                                <a href='delete_subject.php?sub_id=" . htmlspecialchars($row['sub_id']) . "' class='btn btn-danger' onclick='return confirm(`Are you sure you want to delete this Subject?`);'>Delete</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No exams found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</main>

<!-- Footer -->
<footer class="text-center py-4">
    <p>&copy; <?php echo date("Y"); ?> Exam Printing System</p>
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
// Edit Exam Function
// Complete Edit Exam Function
// Edit Exam Function
function editExam(examData) {
    document.getElementById('edit_sub_id').value = examData.sub_id;
    document.getElementById('edit_sub_nameEN').value = examData.sub_nameEN;
    document.getElementById('edit_sub_nameTH').value = examData.sub_nameTH;
    document.getElementById('edit_sub_semester').value = examData.sub_semester;
    document.getElementById('edit_sub_section').value = examData.sub_section;
    document.getElementById('edit_sub_department').value = examData.sub_department;
    document.getElementById('edit_sub_detail').value = examData.sub_detail;
    document.getElementById('edit_exam_date').value = examData.exam_date;
    document.getElementById('edit_exam_room').value = examData.exam_room;
    document.getElementById('edit_exam_start').value = examData.exam_start;
    document.getElementById('edit_exam_end').value = examData.exam_end;
    document.getElementById('edit_exam_status').value = examData.exam_status;
    document.getElementById('edit_user_teacher').value = examData.user_firstname + ' ' + examData.user_lastname;

    // Show the edit modal
    $('#EditExamModal').modal('show');
}



    // Search functionality for exams
    function searchUsers() {
        let input = document.getElementById('searchInput').value.toLowerCase();
        let table = document.getElementById('examTableBody');
        let tr = table.getElementsByTagName('tr');

        for (let i = 0; i < tr.length; i++) {
            let td = tr[i].getElementsByTagName('td')[0]; // Search by subject name
            if (td) {
                let textValue = td.textContent || td.innerText;
                tr[i].style.display = textValue.toLowerCase().indexOf(input) > -1 ? "" : "none";
            }
        }
    }
</script>
</body>
</html>