<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "printing_exam";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบการส่งข้อมูล
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subNameEN = $conn->real_escape_string($_POST['sub_nameEN']);
    $examDate = $conn->real_escape_string($_POST['exam_date']);
    $examRoom = $conn->real_escape_string($_POST['exam_room']);
    $examStart = $conn->real_escape_string($_POST['exam_start']);
    $examEnd = $conn->real_escape_string($_POST['exam_end']);
    $userFirstName = $conn->real_escape_string($_POST['user_firstname']);

    // Assume 'teacher_id' refers to the user ID of the teacher
    $sql = "INSERT INTO exam (sub_id, exam_date, exam_room, exam_start, exam_end, teach_id) VALUES 
            ((SELECT sub_id FROM subject WHERE sub_nameEN='$subNameEN'), '$examDate', '$examRoom', '$examStart', '$examEnd', 
            (SELECT user_id FROM user WHERE user_firstname='$userFirstName'))";

    if ($conn->query($sql) === TRUE) {
        echo "New exam created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
