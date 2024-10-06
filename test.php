<?php
session_start();

// Check if the user is logged in, if not redirect to login
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

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch users information from the database
$sql = "SELECT user_id, user_firstname, user_lastname FROM user";
$resultUsers = $conn->query($sql);

// Fetch existing exams
$sql = "SELECT s.sub_id, e.exam_id, s.sub_nameEN, e.exam_date, e.exam_room, e.exam_start, e.exam_end, 
               u.user_firstname, u.user_lastname
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
                    <h5 class="modal-title" id="addExamModalLabel">Add Subject</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addExamForm">
                        <div class="form-group">
                            <label for="sub_nameEN">Subject Name</label>
                            <input type="text" class="form-control" id="sub_nameEN" required>
                        </div>
                        <div class="form-group">
                            <label for="exam_date">Date</label>
                            <input type="date" class="form-control" id="exam_date" required>
                        </div>
                        <div class="form-group">
                            <label for="exam_room">Room</label>
                            <input type="text" class="form-control" id="exam_room" required>
                        </div>
                        <div class="form-group">
                            <label for="exam_start">Start Time</label>
                            <input type="time" class="form-control" id="exam_start" required>
                        </div>
                        <div class="form-group">
                            <label for="exam_end">End Time</label>
                            <input type="time" class="form-control" id="exam_end" required>
                        </div>
                        <div class="form-group">
                            <label for="teacher_id">Teacher</label>
                            <select class="form-control" id="teacher_id" required>
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
                        <button type="submit" class="btn btn-primary">Add Subject</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <table class="table table-bordered table-hover" id="examTable">
        <thead class="thead-light">
            <tr>
                <th>Subject Name</th>
                <th>Date</th>
                <th>Room</th>
                <th>Time</th>
                <th>Teacher Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($resultExams->num_rows > 0) {
                while($row = $resultExams->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['sub_nameEN']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['exam_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['exam_room']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['exam_start']) . " - " . htmlspecialchars($row['exam_end']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['user_firstname']) . " " . htmlspecialchars($row['user_lastname']) . "</td>";
                    echo "<td>";
                    echo "<a href='#' class='btn btn-warning btn-sm'>Edit</a>";
                    echo "<a href='delete_exam.php?delete_exam=" . htmlspecialchars($row['exam_id']) . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this exam?\");'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No exams found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</main>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="searchUsers.js"></script>
    <script src="add.js"></script>
    <script src="Edit_user.js"></script>
    </body>
</html>

<?php
$conn->close();
?>