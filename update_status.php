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

// รับค่าจาก AJAX
if (isset($_POST['sub_id'])) {
    $sub_id = $_POST['sub_id'];
    $new_status = 'Printed';

    // อัปเดตสถานะในฐานข้อมูล
    $sql = "UPDATE exam SET exam_status = ? WHERE sub_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $sub_id);
    if ($stmt->execute()) {
        echo "Status updated successfully";
    } else {
        echo "Error updating status: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>
