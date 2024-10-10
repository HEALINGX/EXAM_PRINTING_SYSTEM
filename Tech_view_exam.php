<?php
session_start();

// ตรวจสอบสิทธิ์การเข้าถึงของผู้ใช้
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["user_role"]) || strtolower($_SESSION["user_role"]) !== 'examtech') {
    die("Access denied: Unauthorized user.");
}

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test2";

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตัวแปรเก็บข้อความแจ้งเตือน
$alert_message = '';
$alert_class = '';

// เช็คค่าพารามิเตอร์จาก URL
if (isset($_GET['backup_success'])) {
    $alert_message = "Backup successful and PDF path cleared!";
    $alert_class = "alert-success";
} elseif (isset($_GET['error'])) {
    $alert_message = "Error: " . htmlspecialchars($_GET['error']);
    $alert_class = "alert-danger";
}

// คำสั่ง SQL ที่ใช้ในการดึงข้อมูล
$sql = "
SELECT s.sub_id, s.sub_nameEN, s.sub_semester, e.exam_date, e.exam_year, e.exam_status, e.exam_id, e.pdf_path AS exam_pdf, b.pdf_path AS backup_pdf
FROM subject s
JOIN exam e ON s.sub_id = e.sub_id
LEFT JOIN backup b ON e.exam_id = b.exam_id
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
    <title>Exam Tech.</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="adminstyles.css"> 
</head>
<body>

<!-- Navbar -->
<header class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="#">Exam Printing</a>
    <button class="btn btn-danger ml-auto" onclick="location.href='logout.php'">Logout</button>
</header>

<aside class="sidebar bg-dark text-white">
    <div class="p-4">
        <h4>Dashboard</h4>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="examtech.php" onclick="setActive(this)">Manage Subject</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="Tech_view_exam.php" onclick="setActive(this)">View Exam/Back up</a>
            </li>
        </ul>
    </div>
</aside>
<!-- Alert Message -->
<div class="container mt-5 pt-5">
    <?php if (!empty($alert_message)) : ?>
        <div class="alert <?php echo htmlspecialchars($alert_class); ?>" role="alert">
            <?php echo htmlspecialchars($alert_message); ?>
        </div>
    <?php endif; ?>
</div>

<!-- Main Content -->
<main class="container mt-5 pt-5" style="margin-left: 400px;">
    <h2>Download Exam File</h2>

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

    <table class="table" id="userTable">
    <thead class="thead-dark">
        <tr>
            <th>ID</th>
            <th>NAME</th>
            <th>EXAM_SEMESTER</th>
            <th>EXAM_DATE</th>
            <th>EXAM_YEAR</th>
            <th>Exam Status</th>
            <th>ACTION</th>
            <th>EXAM FILE</th>
        </tr>
    </thead>
    <tbody id="examTableBody">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr id='row_" . htmlspecialchars($row['sub_id']) . "'>";
                echo "<td>" . htmlspecialchars($row['sub_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['sub_nameEN']) . "</td>";
                echo "<td>" . htmlspecialchars($row['sub_semester']) . "</td>";
                echo "<td>" . htmlspecialchars($row['exam_date']) . "</td>";
                echo "<td>" . htmlspecialchars($row['exam_year']) . "</td>";
                echo "<td>" . htmlspecialchars($row['exam_status']) . "</td>";
                echo "<td>";
                
                if (!empty($row['exam_pdf'])) {
                    echo " <a href='backup.php?exam_id=" . htmlspecialchars($row['exam_id']) . "' class='btn btn-danger'>Backup</a>";
                } else {
                    echo "<span class='text-danger'>No file available</span>";
                }
                echo "</td>";
                
                // เพิ่มคอลัมน์ EXAM FILE สำหรับการดึงข้อมูลจาก backup
                echo "<td>";
                if (!empty($row['backup_pdf'])) {
                    echo "<a href='uploads/" . htmlspecialchars($row['backup_pdf']) . "' target='_blank' class='btn btn-info'>View Exam</a>";
                } else {
                    echo "<span class='text-danger'>No backup available</span>";
                }
                echo "</td>";
                
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No exams found.</td></tr>"; // ปรับ colspan ให้ครอบคลุมคอลัมน์ทั้งหมด
        }
        ?>
    </tbody>
</table>

</main>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
function searchUsers() {
    var input = document.getElementById("searchInput");
    var filter = input.value.toUpperCase();
    var table = document.getElementById("userTable");
    var tr = table.getElementsByTagName("tr");

    for (var i = 1; i < tr.length; i++) {
        tr[i].style.display = "none";
        var td = tr[i].getElementsByTagName("td");
        if (td.length > 0) {
            var txtValue = td[1].textContent || td[1].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            }
        }
    }
}

function filterBySemesterAndYear() {
    var semesterSelect = document.getElementById("semesterSelect");
    var yearSelect = document.getElementById("yearSelect");
    var semesterFilter = semesterSelect.value;
    var yearFilter = yearSelect.value;
    var table = document.getElementById("userTable");
    var tr = table.getElementsByTagName("tr");

    for (var i = 1; i < tr.length; i++) {
        tr[i].style.display = "none";
        var td = tr[i].getElementsByTagName("td");
        if (td.length > 0) {
            var semesterMatch = (semesterFilter === "" || td[2].innerText === semesterFilter);
            var yearMatch = (yearFilter === "" || td[4].innerText === yearFilter);
            if (semesterMatch && yearMatch) {
                tr[i].style.display = "";
            }
        }
    }
}
</script>

</body>
</html>
