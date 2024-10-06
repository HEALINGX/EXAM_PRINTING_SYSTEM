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


$items_per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $items_per_page;


$sql = "SELECT s.sub_id, s.sub_nameEN, e.exam_date, e.exam_start, e.exam_end, e.exam_room 
        FROM subject s, exam e 
        WHERE s.sub_id = e.sub_id 
        LIMIT $items_per_page OFFSET $offset";
$result = $conn->query($sql);


$total_sql = "SELECT COUNT(*) as total FROM subject s, exam e WHERE s.sub_id = e.sub_id";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_items = $total_row['total'];
$total_pages = ceil($total_items / $items_per_page);


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

    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

</main>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="search.js"></script>
  
    </script>
    
</body>
</html>


<?php
$conn->close();
?>