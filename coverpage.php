<?php
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือยัง ถ้ายังให้ redirect ไปหน้า login
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["user_role"]) || strtolower($_SESSION["user_role"]) !== 'technology') {
    die("Access denied: Unauthorized user.");
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test2";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
SELECT s.sub_id, s.sub_nameTH, s.sub_section, e.exam_date,s.sub_semester, e.exam_start, e.exam_end, e.exam_room, e.pdf_path, e.exam_status, e.exam_year, e.exam_comment, u.user_firstname, u.user_lastname, u.user_tel
FROM subject s
JOIN exam e ON s.sub_id = e.sub_id
JOIN user u ON s.teach_id = u.user_id
";
$result = $conn->query($sql);

if ($result === false) {
    die("SQL Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cover Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

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
                <a class="nav-link active" href="Technology.php">All Subject</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="coverpage.php">Cover Page</a>
            </li>
        </ul>
    </div>
</aside>

<main class="container mt-5 pt-5">
    <h2>Cover Page</h2>
    <div class="search-bar mb-3">
        <input type="text" class="form-control" id="searchInput" placeholder="Search Subject..." onkeyup="searchUsers()">
    </div>

    <div class="semester-selection mb-3">
        <label for="semesterSelect">Select Semester:</label>
        <select id="semesterSelect" class="form-control" onchange="filterBySemesterAndYear()">
            <option value="">All</option>
            <option value="1">Semester 1</option>
            <option value="2">Semester 2</option>
        </select>
    </div>
    <div class="year-selection mb-3">
        <label for="yearSelect">Select Year:</label>
        <select id="yearSelect" class="form-control" onchange="filterBySemesterAndYear()">
            <option value="">All</option>
            <option value="2024">2024</option>
            <option value="2025">2025</option>
        </select>
    </div>

    
    <!-- Cover Page Table -->
    <table class="table" id="coverTable">
    <thead class="thead-dark">
        <tr>
            <th>ID</th>
            <th>NAME</th>
            <th>EXAM_SEMESTER</th>
            <th>EXAM_DATE</th>
            <th>EXAM_YEAR</th>
            <th>EXAM_START</th>
            <th>EXAM_END</th>
            <th>EXAM_ROOM</th>
            <th>COMMENT</th>
            <th style="width: 100px;">PRINT PAGE</th> <!-- เพิ่มความกว้างที่นี่ -->
        </tr>
    </thead>
    <tbody id="coverTableBody">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr id='row_" . htmlspecialchars($row['sub_id']) . "'>";
                echo "<td>" . htmlspecialchars($row['sub_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['sub_nameTH']) . "</td>";
                echo "<td>" . htmlspecialchars($row['sub_semester']) . "</td>";
                echo "<td>" . htmlspecialchars($row['exam_date']) . "</td>";
                echo "<td>" . htmlspecialchars($row['exam_year']) . "</td>";
                echo "<td>" . htmlspecialchars($row['exam_start']) . "</td>";
                echo "<td>" . htmlspecialchars($row['exam_end']) . "</td>";
                echo "<td>" . htmlspecialchars($row['exam_room']) . "</td>";
                echo "<td>
                    <button class='btn btn-success' onclick='viewComment(\"" . htmlspecialchars($row["exam_comment"]) . "\")'>View Comment</button>
                </td>";
                echo "<td style='width: 100px;'> <!-- เพิ่มความกว้างที่นี่ -->
                    <button class='btn btn-info' onclick='window.location.href=\"print_page.php?sub_id=" . htmlspecialchars($row['sub_id']) . "\"'>
                        <i class='fas fa-print'></i> 
                    </button>
                </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='10'>No subjects found</td></tr>";
        }
        ?>
    </tbody>
</table>

</main>

<!-- Comment Modal -->
<div class="modal fade" id="commentModal" tabindex="-1" role="dialog" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentModalLabel">Comment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="commentText"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



<!-- Script Section -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
// Function to view comment
function viewComment(comment) {
    document.getElementById('commentText').innerText = comment;
    $('#commentModal').modal('show');
}

// Function to filter by semester and year
function filterBySemesterAndYear() {
    const semesterFilter = document.getElementById("semesterSelect").value;
    const yearFilter = document.getElementById("yearSelect").value;
    const table = document.getElementById("coverTable");
    const tr = table.getElementsByTagName("tr");
    
    for (let i = 1; i < tr.length; i++) {
        const tdSemester = tr[i].getElementsByTagName("td")[2];
        const tdYear = tr[i].getElementsByTagName("td")[4];

        const semesterMatches = semesterFilter ? tdSemester.innerText === semesterFilter : true;
        const yearMatches = yearFilter ? tdYear.innerText === yearFilter : true;

        tr[i].style.display = semesterMatches && yearMatches ? "" : "none";
    }
}
</script>
</body>
</html>