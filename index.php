<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "exam_system";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

   
    $stmt = $conn->prepare("SELECT user_id, user_password, user_role FROM User WHERE user_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result(); 
    $stmt->bind_result($user_id, $hashed_password, $user_role);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && md5($password) == $hashed_password) {
       
        $_SESSION["user_id"] = $user_id;
        $_SESSION["email"] = $email;
        $_SESSION["user_role"] = $user_role;

        
        switch ($user_role) {
            case 'Admin':
                header("Location: admin.php");
                break;
            case 'Teacher':
                header("Location: teacher.php");
                break;
            case 'Technology':
                header("Location: Technology.php");
                break;
            case 'ExamTech':
                header("Location: examtech.php");
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
    <link rel="stylesheet" href="loginstyles.css"> 
    <title>Login</title>
</head>
<body>
    <form action="index.php" method="post">
        <h2>Sign In</h2>
        <div>
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Sign In</button>
    </form>
</body>
</html>