<?php
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือยัง ถ้ายังให้ redirect ไปหน้า login
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

$sql = "
SELECT s.sub_semester,e.exam_year,s.sub_id, s.sub_nameEN, e.exam_date, e.exam_start, e.exam_end, e.exam_room, e.pdf_path, e.exam_status 
FROM subject s
JOIN exam e ON s.sub_id = e.sub_id
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
    <link rel="stylesheet" href="styles.css"> 
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
                <a class="nav-link active" href="#">All Subject</a>
            </li>
        </ul>
    </div>
</aside>

<!-- Main Content -->
<main class="container mt-5 pt-10">
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
                <th>EXAM_DATE</th>
                <th>EXAM_START</th>
                <th>EXAM_END</th>
                <th>EXAM_ROOM</th>
                <th>Exam SEMESTER</th>
                <th>Exam YEAR</th>
                <th>Exam File</th>
                <th>Download</th>
                <th>Status</th>   
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr id='row_" . htmlspecialchars($row['sub_id']) . "'>";
                    echo "<td>" . htmlspecialchars($row['sub_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['sub_nameEN']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['exam_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['exam_start']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['exam_end']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['exam_room']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['sub_semester']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['exam_year']) . "</td>";
    
                    echo "<td>";
                    if ($row['pdf_path']) {
                        echo "<a href='uploads/" . htmlspecialchars($row['pdf_path']) . "' target='_blank' class='btn btn-primary' style='background-color: #6f42c1; border-color: #6f42c1;'>View File</a>";
                    } else {
                        echo "No file uploaded";
                    }
                    echo "</td>";

                    // ใช้ pdf_path ที่บันทึกในฐานข้อมูล
                    $file_path = htmlspecialchars($row['pdf_path']);
                    // กำหนดเส้นทางที่อยู่ของไฟล์
                    $full_path = __DIR__ . "/uploads/" . $file_path; 
                    if ($file_path && file_exists($full_path)) { // ตรวจสอบว่าไฟล์มีอยู่จริง
                        echo "<td><a href='uploads/" . $file_path . "' class='btn btn-primary' download onclick='updateStatus(\"" . $row["sub_id"] . "\")'>Download</a></td>";
                    } else {
                        echo "<td>No file uploaded</td>";
                    }

                    // แสดงสถานะจากฐานข้อมูล
                    echo "<td>" . htmlspecialchars($row['exam_status']) . "</td>"; 
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No subjects found</td></tr>"; 
            }
            ?>
        </tbody>
    </table>

</main>

<script>
function filterBySemesterAndYear() {
    const semesterSelect = document.getElementById("semesterSelect");
    const selectedSemester = semesterSelect.value;
    
    const yearSelect = document.getElementById("yearSelect");
    const selectedYear = yearSelect.value;
    
    const rows = document.querySelectorAll("#userTable tbody tr");

    rows.forEach(row => {
        const semesterCell = row.cells[6]; // คอลัมน์ 'Exam SEMESTER' (index 6)
        const yearCell = row.cells[7]; // คอลัมน์ 'Exam YEAR' (index 7)

        const semester = semesterCell ? semesterCell.textContent.trim() : '';
        const year = yearCell ? yearCell.textContent.trim() : '';

        if ((selectedSemester === "" || semester === selectedSemester) && 
            (selectedYear === "" || year === selectedYear)) {
            row.style.display = ""; // แสดงแถว
        } else {
            row.style.display = "none"; // ซ่อนแถว
        }
    });
}
</script>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="search.js"></script>

<script>
function updateStatus(subId) {
    $.ajax({
        type: "POST",
        url: "update_status.php",
        data: { sub_id: subId },
        success: function(response) {
            console.log(response); // แสดงข้อความตอบกลับใน console
            // อัปเดตสถานะในตาราง
            var row = $('#row_' + subId);
            row.find('td:last').text('Printed'); // เปลี่ยนสถานะในคอลัมน์ Status
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
}
</script>

</body>
</html>

<?php
$conn->close();
?>