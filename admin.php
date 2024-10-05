<?php
session_start();

// Check if user is logged in and has admin role
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== 'Admin') {
    header("Location: login.php"); // Redirect to login if not authorized
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome to the Admin Dashboard</h1>
    <p>Hello, <?php echo $_SESSION["email"]; ?>!</p>
    <a href="logout.php">Logout</a>
    <!-- Add more admin functionalities here -->
</body>
</html>
