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

// Fetch users information from the database
$sql = "SELECT s.sub_id,s.sub_nameEN,e.exam_date,e.exam_start,e.exam_end,e.exam_room from subject s,exam e where s.sub_id = e.sub_id;";
$result = $conn->query($sql);

// ตรวจสอบผลลัพธ์การดึงข้อมูล
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
    <h2>Download ExamFile</h2>
    <div class="search-bar mb-3">
        <input type="text" class="form-control" id="searchInput" placeholder="Search users..." onkeyup="searchUsers()">
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
                <th>Download</th>
                <th>Status</th>   <!-- คอลัมน์สถานะ -->
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
                    $file_path = 'exam_files/' . htmlspecialchars($row['sub_id']) . '_exam.pdf'; 
                    echo "<td><a href='" . $file_path . "' class='btn btn-primary' download>Download</a></td>";
                    echo "<td>
                            <select class='form-control' onchange='updateStatus(this, \"" . htmlspecialchars($row['sub_id']) . "\")'>
                                <option value='Not uploaded'>Not uploaded</option>
                                <option value='Uploaded'>Uploaded</option>
                                <option value='Printed'>Printed</option>
                            </select>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No users found</td></tr>"; 
            }
            ?>
        </tbody>
    </table>
</main>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        // ฟังก์ชันสำหรับอัปเดตสถานะเมื่อเลือกจาก dropdown
        function updateStatus(selectElement, subId) {
            var status = selectElement.value; // รับค่าที่เลือกจาก dropdown
            var row = document.getElementById("row_" + subId);
            
            // เปลี่ยนสีของแถวตามสถานะที่เลือก
            if (status === "Uploaded") {
                row.style.backgroundColor = "#d4edda"; // สีเขียวอ่อน
            } else if (status === "Printed") {
                row.style.backgroundColor = "#fff3cd"; // สีเหลืองอ่อน
            } else {
                row.style.backgroundColor = ""; // ไม่มีสีพื้นหลัง
            }
        }
    </script>
    
</body>
</html>


<?php
$conn->close();
?>