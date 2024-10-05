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
    $stmt->bind_result($user_id, $hashed_password, $user_role);
    $stmt->fetch();

    if (md5($password) == $hashed_password) {
        // Password is correct, start a new session
        $_SESSION["user_id"] = $user_id;
        $_SESSION["email"] = $email;
        $_SESSION["user_role"] = $user_role;
        
        // Redirect based on user role
        switch ($user_role) {
            case 'Admin':
                header("Location: admin.php");
                break;
            case 'Teacher':
                header("Location: teacher.php");
                break;
            case 'ExamTech':
                header("Location: examTech.php");
                break;
            case 'Technology':
                header("Location: technology.php");
                break; 
            default:
                // Redirect to a default page if role doesn't match
                header("Location: welcome.php");
                break;
        }
        exit(); // Ensure no further code is executed after redirect*/
    } else {
        // Invalid credentials
        echo "Invalid email or password.";
    }

    

    $stmt->close();
}
$conn->close();
?>
