<?php
session_start(); // Start the session

// Check if the user is logged in and is a teacher
if (!isset($_SESSION["user_id"]) || strtolower($_SESSION["user_role"]) !== 'teacher') {
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
    $target_file = $target_dir . basename($_FILES["pdf_file"]["name"]);
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the file is a PDF
    if ($file_type !== "pdf") {
        echo "Sorry, only PDF files are allowed.";
    } else {
        // Move the uploaded file to the specified directory
        if (move_uploaded_file($_FILES["pdf_file"]["tmp_name"], $target_file)) {
            // Update the database with the file path
            $sql = "UPDATE exam SET pdf_path = ? WHERE sub_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $target_file, $sub_id);

            if ($stmt->execute()) {
                echo "The file " . basename($_FILES["pdf_file"]["name"]) . " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file: " . $conn->error;
            }

            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

// Fetch user information from the database using user_id from session
$user_id = $_SESSION["user_id"];
$sql = "
SELECT 
    s.teach_id, 
    s.sub_nameEN, 
    s.sub_nameTH, 
    s.sub_id, 
    e.exam_date, 
    e.exam_start, 
    e.exam_end, 
    e.exam_status, 
    e.exam_room,
    e.pdf_path
FROM 
    exam e
JOIN 
    subject s ON s.sub_id = e.sub_id
JOIN 
    teacher t ON t.user_id = s.teach_id
JOIN 
    user u ON u.user_id = t.user_id
WHERE 
    t.user_id = ?
";

// Prepare and bind parameters
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check result
if ($result === false) {
    die("SQL Error: " . $conn->error);
}

// Fetch all rows
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
                <a class="nav-link active" href="#">Subject</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="#">Uploaded Subject</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="#">Unuploaded Subject</a>
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
    <table class="table table-bordered table-hover" id="userTable">
        <thead class="thead-light">
            <tr>
                <th>Subject ID</th>
                <th>Subject Name(TH)</th>
                <th>Subject Name(ENG)</th>
                <th>Exam Date</th>
                <th>Exam Time</th>
                <th>Exam Status</th>
                <th>Exam Room</th>
                <th>Exam File</th>
                <th>Upload File</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (count($rows) > 0) {
                foreach ($rows as $row) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['sub_id']). "</td>";
                    echo "<td>" . htmlspecialchars($row['sub_nameTH']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['sub_nameEN']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['exam_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['exam_start']) . " - " . htmlspecialchars($row['exam_end']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['exam_status']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['exam_room']) . "</td>";
                    echo "<td>";
                    if ($row['pdf_path']) {
                        echo "<a href='" . htmlspecialchars($row['pdf_path']) . "' target='_blank'>View File</a>";
                    } else {
                        echo "No file uploaded";
                    }
                    echo "</td>";
                    echo "<td>
                            <form action='' method='post' enctype='multipart/form-data'>
                                <input type='file' name='pdf_file' accept='application/pdf' required>
                                <input type='hidden' name='sub_id' value='" . htmlspecialchars($row['sub_id']) . "'>
                                <button type='submit' class='btn btn-primary btn-sm'>Upload</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>No subjects found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</main>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="searchSubject.js"></script>
</body>
</html>
