<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "exam_system";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (isset($_GET['delete_user'])) {
    $user_id = $conn->real_escape_string($_GET['delete_user']);

    $sql = "DELETE FROM User WHERE user_id = '$user_id'";

    if ($conn->query($sql) === TRUE) {
        echo "User deleted successfully";
        header("Location: admin.php"); 
        exit();
    } else {
        echo "Error deleting user: " . $conn->error;
    }
}

$conn->close();
?>