<?php
session_start();

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT user_id, user_password, user_role FROM User WHERE user_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result(); // Store the result for checking the number of rows
    $stmt->bind_result($user_id, $hashed_password, $user_role);
    $stmt->fetch();

    // Check if user exists and password is correct using md5
    if ($stmt->num_rows > 0 && md5($password) == $hashed_password) {
        // Start a new session
        $_SESSION["user_id"] = $user_id;
        $_SESSION["email"] = $email;

        
        switch ($user_role) {
            case 'Admin':
                header("Location: admin.php");
                break;
            case 'Teacher':
                header("Location: teacher.php");
                break;
            case 'Technology':
                header("Location: teah.php");
                break;
            case 'ExamTech':
                header("Location: examteach.php");
                break;
            default:
                echo "Invalid role.";
        }
        exit;
    } else {
        
        echo "Invalid email or password.";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login Page</h2>
    <form action="index.php" method="post">
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
</body>
</html>
