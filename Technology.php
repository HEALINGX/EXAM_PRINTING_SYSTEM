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
$dbname = "printing_exam";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
SELECT s.sub_id, s.sub_nameEN, s.sub_semester, e.exam_date, e.exam_start, e.exam_end, e.exam_room, e.pdf_path, e.exam_status, e.exam_year,e.exam_comment 
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
            <li class="nav-item">
                <a class="nav-link" href="coverpage.php">Cover Page</a>
            </li>
        </ul>
    </div>
</aside>

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
                <th>EXAM_START</th>
                <th>EXAM_END</th>
                <th>EXAM_ROOM</th>
                <th>COMMENT</th>
                <th>Exam File</th>
                <th>Download</th>
                <th>STATUS</th>   
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
                    echo "<td>" . htmlspecialchars($row['exam_start']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['exam_end']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['exam_room']) . "</td>";
                    echo "<td> <button class='btn btn-info' onclick='viewComment(\"" . htmlspecialchars($row["exam_comment"]) . "\")'>View Comment</button> </td>";
                    echo "<td>";
                    if ($row['pdf_path']) {
                        echo "<a href='uploads/" . htmlspecialchars($row['pdf_path']) . "' target='_blank' class='btn btn-primary' style='background-color: #6f42c1; border-color: #6f42c1;'>View File</a>";
                    } else {
                        echo "No file uploaded";
                    }
                    echo "</td>";

                    $file_path = htmlspecialchars($row['pdf_path']);
                    $full_path = __DIR__ . "/uploads/" . $file_path; 
                    if ($file_path && file_exists($full_path)) { 
                        echo "<td><a href='uploads/" . $file_path . "' class='btn btn-primary' download>Download</a></td>";
                    } else {
                        echo "<td>No file uploaded</td>";
                    }

                    // แสดงสถานะจากฐานข้อมูล
                    echo "<td>
                        <select onchange='updateStatus(\"" . htmlspecialchars($row["sub_id"]) . "\", this.value)'>
                            <option value=''>Select Status</option>
                            <option value='Not Uploaded' " . ($row['exam_status'] === 'Not Uploaded' ? 'selected' : '') . ">Not Uploaded</option>
                            <option value='Uploaded' " . ($row['exam_status'] === 'Uploaded' ? 'selected' : '') . ">Uploaded</option>
                            <option value='Printed' " . ($row['exam_status'] === 'Printed' ? 'selected' : '') . ">Printed</option>
                        </select>
                    </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No subjects found</td></tr>"; 
            }
            ?>
        </tbody>
    </table>
</main>

<!-- Modal สำหรับแสดงคอมเมนต์ -->
<div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentModalLabel">Exam Comment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="commentContent">
                <!-- คอมเมนต์จะมาแสดงตรงนี้ -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="search.js"></script>

<script>
function updateStatus(subId, status) {
    if (!status) {
        alert("Please select a status.");
        return;
    }

    $.ajax({
        type: "POST",
        url: "update_status.php",
        data: { sub_id: subId, status: status },
        success: function(response) {
            alert(response); // แสดงข้อความยืนยันเมื่ออัปเดตสำเร็จ
            console.log(response);
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
}

function filterBySemesterAndYear() {
    let semesterSelect = document.getElementById("semesterSelect");
    let yearSelect = document.getElementById("yearSelect");
    let semester = semesterSelect.value;
    let year = yearSelect.value;
    let table = document.getElementById("examTableBody");
    let tr = table.getElementsByTagName("tr");

    for (let i = 0; i < tr.length; i++) {
        let tdSemester = tr[i].getElementsByTagName("td")[2];
        let tdYear = tr[i].getElementsByTagName("td")[4];
        let display = true;

        if (semester && tdSemester.innerHTML !== semester) {
            display = false;
        }

        if (year && tdYear.innerHTML !== year) {
            display = false;
        }

        tr[i].style.display = display ? "" : "none";
    }
}

function viewComment(comment) {
    document.getElementById("commentContent").textContent = comment;
    $('#commentModal').modal('show');
}

// Function to show tab
function showTab(tabId, element) {
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.style.display = 'none';
    });
    document.getElementById(tabId).style.display = 'block';

    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
    });
    element.classList.add('active');
}

</script>

</body>
</html>

<?php
$conn->close();
?>
