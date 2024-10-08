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
SELECT s.sub_id, s.sub_nameTH, s.sub_section, e.exam_date,e.exam_semester, e.exam_start, e.exam_end, e.exam_room, e.pdf_path, e.exam_status, e.exam_year, e.exam_comment, u.user_firstname, u.user_lastname, u.user_tel
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
    <link rel="stylesheet" href="adminstyles.css">
</head>
<body>

<!-- Navbar -->
<header class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="#">Exam Printing</a>
    <button class="btn btn-danger ml-auto" onclick="location.href='logout.php'">Logout</button>
</header>

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
                <th>PRINT PAGE</th>
            </tr>
        </thead>
        <tbody id="coverTableBody">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr id='row_" . htmlspecialchars($row['sub_id']) . "'>";
                    echo "<td>" . htmlspecialchars($row['sub_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['sub_nameTH']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['exam_semester']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['exam_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['exam_year']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['exam_start']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['exam_end']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['exam_room']) . "</td>";
                    echo "<td>
                        <button class='btn btn-info' onclick='viewComment(\"" . htmlspecialchars($row["exam_comment"]) . "\")'>View Comment</button>
                    </td>";
                    echo "<td>
                        <button class='btn btn-info' onclick='window.location.href=\"print_page.php?sub_id=" . htmlspecialchars($row['sub_id']) . "\"'>View Print Page</button>
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

<!-- Print Page Modal -->
<div class="modal fade" id="printPageModal" tabindex="-1" role="dialog" aria-labelledby="printPageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printPageModalLabel">Print Page</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="printPageText"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printContent()">Print</button>
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

// Function to view print page
function viewPrintPage(subName, subId, examDate, examStart, examEnd, examRoom, userFirstname, userLastName, userTel, subSection) {
    const printPageText = `
        คณะวิทยาศาสตร์
        การสอบวิชา ${subName}                                                          รหัสวิชา ${subId}
        สอบวันที่ ${examDate} เวลา ${examStart} – ${examEnd} น.
        ห้องสอบ ${examRoom} เลขประจำซอง.............................................
        จำนวนนักศึกษา.............................................คน
        ซองนี้มีข้อสอบ..............................................ชุด      นศ.คณะ...............................................ตอน ${subSection}
        ข้อสอบสำรอง.....................ชุด
        อุปกรณ์ที่ใช้หรือคำแนะนำผู้คุมสอบเพิ่มเติม
        ............................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................................
        .......................................................................................................................................................................................
        ผู้ออกข้อสอบ ${userFirstname} ${userLastName} 
        ห้องทำงาน...........................................................โทรศัพท์มือถือ ${userTel}

        จำนวนนักศึกษาที่เข้าสอบ...............คน จำนวนนักศึกษาที่ขาดสอบ...........คน คือ
                รหัสนักศึกษา................................................
                ชื่อ-สกุล......................................................
                1................................................................................ผู้คุมสอบ
                2................................................................................ผู้คุมสอบ
                3................................................................................ผู้คุมสอบ
                หมายเหตุ..........................................................................................................
    `;
    document.getElementById('printPageText').innerText = printPageText;
    $('#printPageModal').modal('show');
}

// Function to print content
function printContent() {
    const printPageText = document.getElementById('printPageText').innerText;
    const newWindow = window.open('', '_blank');
    newWindow.document.write(`<pre>${printPageText}</pre>`);
    newWindow.document.close();
    newWindow.print();
}

// Function to search subjects
function searchUsers() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById("coverTable");
    const tr = table.getElementsByTagName("tr");
    
    for (let i = 1; i < tr.length; i++) {
        const td = tr[i].getElementsByTagName("td")[1];
        if (td) {
            const txtValue = td.textContent || td.innerText;
            tr[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? "" : "none";
        }       
    }
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
