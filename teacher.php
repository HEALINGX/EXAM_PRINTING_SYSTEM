<?php
session_start(); // Start the session

// Check if the user is logged in and is a teacher
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["user_role"]) || strtolower($_SESSION["user_role"]) !== 'teacher') {
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

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["pdf_file"]) && isset($_POST['sub_id'])) {
    $sub_id = $_POST['sub_id'];
    $target_dir = __DIR__ . "/uploads/";
    $file_name = basename($_FILES["pdf_file"]["name"]);
    $target_file = $target_dir . $file_name; // ชื่อไฟล์ที่อัปโหลด
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the file is a PDF
    if ($file_type !== "pdf") {
        echo "Sorry, only PDF files are allowed.";
    } else {
        // Fetch the current file path from the database
        $sql = "SELECT pdf_path FROM exam WHERE sub_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("SQL Error: " . $conn->error);
        }
        $stmt->bind_param("i", $sub_id);
        $stmt->execute();
        $stmt->bind_result($current_file_path);
        $stmt->fetch();
        $stmt->close();

        // If there is a file already uploaded, delete it
        if ($current_file_path) {
            $old_file_path = $target_dir . $current_file_path;
            if (file_exists($old_file_path)) {
                unlink($old_file_path); // Delete the old file
            }
        }

        // Move the uploaded file to the specified directory
        if (move_uploaded_file($_FILES["pdf_file"]["tmp_name"], $target_file)) {
            // Update the database with the new file path and change the status
            $sql = "UPDATE exam SET pdf_path = ?, exam_status = 'Uploaded' WHERE sub_id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                die("SQL Error: " . $conn->error);
            }
            $stmt->bind_param("si", $file_name, $sub_id);

            if ($stmt->execute()) {
                echo "The file " . htmlspecialchars($file_name) . " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file: " . $conn->error;
            }

            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

// Handle comment submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment']) && isset($_POST['sub_id'])) {
    $comment = $_POST['comment'];
    $sub_id = $_POST['sub_id'];

    // Update the comment in the database
    $sql = "UPDATE exam SET exam_comment = ? WHERE sub_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("SQL Error: " . $conn->error);
    }
    $stmt->bind_param("si", $comment, $sub_id);

    if ($stmt->execute()) {
        echo "Comment updated successfully.";
    } else {
        echo "Error updating comment: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch user information from the database using user_id from session
$user_id = $_SESSION["user_id"];
$sql = "
SELECT 
    s.teach_id, 
    s.sub_nameEN, 
    s.sub_nameTH, 
    s.sub_semester,
    s.sub_id, 
    e.exam_date,
    e.exam_year, 
    e.exam_start, 
    e.exam_end, 
    e.exam_status, 
    e.exam_room,
    e.pdf_path,
    e.exam_comment
FROM 
    exam e
JOIN 
    subject s ON s.sub_id = e.sub_id
JOIN 
    teacher t ON t.user_id = s.teach_id
JOIN 
    user u ON u.user_id = t.user_id
WHERE 
    t.user_id = ?";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("SQL Error: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die("SQL Error: " . $conn->error);
}

$rows = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
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
                <a class="nav-link active" href="#">ALL Subject</a>
            </li>
        </ul>
    </div>
</aside>

<!-- Main Content -->
<main class="container mt-5 pt-5">
    <h2>Subject Exam File Management</h2>
    <div class="search-bar mb-3">
        <input type="text" class="form-control" id="searchInput" placeholder="Search subject..." onkeyup="searchSubject()">
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
                <th>Subject ID</th>
                <th>Subject Name(TH)</th>
                <th>Subject Name(ENG)</th>
                <th>Subject Semester</th>
                <th style="width: 50%;">Exam Date</th>
                <th>Exam Year</th>
                <th>Exam Time</th>
                <th>Exam Status</th>
                <th>Exam Room</th>
                <th>Upload File</th>
                <th>Comment</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['sub_id']); ?></td>
                <td><?php echo htmlspecialchars($row['sub_nameTH']); ?></td>
                <td><?php echo htmlspecialchars($row['sub_nameEN']); ?></td>
                <td><?php echo htmlspecialchars($row['sub_semester']); ?></td>
                <td><?php echo htmlspecialchars($row['exam_date']); ?></td>
                <td><?php echo htmlspecialchars($row['exam_year']); ?></td>
                <td><?php echo htmlspecialchars($row['exam_start']) . ' - ' . htmlspecialchars($row['exam_end']); ?></td>
                <td><?php echo htmlspecialchars($row['exam_status']); ?></td>

                <td><?php echo htmlspecialchars($row['exam_room']); ?></td>
                <td>
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="sub_id" value="<?php echo htmlspecialchars($row['sub_id']); ?>">
                        <input type="file" name="pdf_file" required <?php if ($row['exam_status'] === 'Printed') echo 'disabled'; ?>>
                        <button type="submit" class="btn btn-primary <?php if ($row['exam_status'] === 'Printed') echo 'disabled'; ?>" 
                            style="<?php if ($row['exam_status'] === 'Printed') echo 'background-color: grey; border-color: grey; cursor: not-allowed;'; ?>">
                            Upload
                        </button>
                    </form>
                </td>
                <td>
                    <button class="btn btn-warning" onclick="openCommentModal('<?php echo $row['sub_id']; ?>', '<?php echo htmlspecialchars($row['exam_comment']); ?>')">Edit Comment</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</main>

<!-- Modal for editing comment -->
<div class="modal fade" id="commentModal" tabindex="-1" role="dialog" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="commentModalLabel">Edit Comment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="sub_id" id="commentSubId">
                    <textarea name="comment" id="comment" class="form-control" rows="3" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCommentModal(subId, comment) {
    // Set the comment text and subject ID to the modal
    document.getElementById('commentSubId').value = subId;
    document.getElementById('comment').value = comment;

    // Show the modal
    $('#commentModal').modal('show');
}

function searchSubject() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("userTable");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[1]; // Assuming the subject name is in the second column
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }       
    }
}

function filterBySemesterAndYear() {
    var semester = document.getElementById("semesterSelect").value;
    var year = document.getElementById("yearSelect").value;
    var table = document.getElementById("userTable");
    var tr = table.getElementsByTagName("tr");

    for (var i = 0; i < tr.length; i++) {
        var tdSemester = tr[i].getElementsByTagName("td")[3]; // Assuming semester is in the 4th column
        var tdYear = tr[i].getElementsByTagName("td")[5]; // Assuming year is in the 6th column
        if (tdSemester && tdYear) {
            var semesterValue = tdSemester.textContent || tdSemester.innerText;
            var yearValue = tdYear.textContent || tdYear.innerText;
            if ((semester === "" || semesterValue === semester) && (year === "" || yearValue === year)) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}
</script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
