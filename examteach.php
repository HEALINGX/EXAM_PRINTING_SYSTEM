<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamTech</title>
</head>
<body>
    <h2>Welcome ExamTech</h2>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION["email"]); ?>!</p>
    <a href="logout.php">Logout</a>
</body>
</html>