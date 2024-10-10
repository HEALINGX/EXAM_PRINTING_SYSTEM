<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    die("Unauthorized user.");
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test2";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request is valid
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sub_id']) && isset($_POST['status'])) {
    $sub_id = $_POST['sub_id'];
    $status = $_POST['status'];

    // Update exam status in the database
    $sql = "UPDATE exam SET exam_status = ? WHERE sub_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("si", $status, $sub_id);
    if ($stmt->execute()) {
        echo "Exam status updated successfully.";
    } else {
        echo "Error updating status: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
