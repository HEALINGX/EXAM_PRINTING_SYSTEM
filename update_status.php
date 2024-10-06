<?php
session_start();

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION["user_id"])) {
    die("Access denied.");
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "printing_exam";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sub_id']) && isset($_POST['exam_status'])) {
    $sub_id = $_POST['sub_id'];
    $exam_status = $_POST['exam_status'];

    // อัปเดตสถานะในฐานข้อมูล
    $sql = "UPDATE exam SET exam_status = ? WHERE sub_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("SQL Error: " . $conn->error);
    }
    $stmt->bind_param("si", $exam_status, $sub_id);
    if ($stmt->execute()) {
        echo "Status updated successfully.";
    } else {
        echo "Error updating status: " . $conn->error;
    }
    $stmt->close();
}

$conn->close();
?>