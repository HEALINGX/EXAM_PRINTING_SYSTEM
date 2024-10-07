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

$user_id = $conn->real_escape_string($_GET['edit_user']);

// Fetch user information
$sql = "SELECT * FROM User WHERE user_id = '$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get updated values from the form
    $firstName = $conn->real_escape_string($_POST['firstName']);
    $lastName = $conn->real_escape_string($_POST['lastName']);
    $tel = $conn->real_escape_string($_POST['tel']);
    $role = $conn->real_escape_string($_POST['role']);
    $email = $conn->real_escape_string($_POST['email']);
    
    // Update user information
    $sql = "UPDATE User SET user_firstname='$firstName', user_lastname='$lastName', user_tel='$tel', user_role='$role', user_email='$email' WHERE user_id='$user_id'";
    
    if ($conn->query($sql) === TRUE) {
        echo "User updated successfully";
        header("Location: index.php"); 
        exit();
    } else {
        echo "Error updating user: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
</head>
<body>
    <h2>Edit User</h2>
    <form method="POST">
        <div>
            <label for="firstName">First Name:</label>
            <input type="text" name="firstName" id="firstName" value="<?php echo htmlspecialchars($user['user_firstname']); ?>" required>
        </div>
        <div>
            <label for="lastName">Last Name:</label>
            <input type="text" name="lastName" id="lastName" value="<?php echo htmlspecialchars($user['user_lastname']); ?>" required>
        </div>
        <div>
            <label for="tel">Telephone:</label>
            <input type="text" name="tel" id="tel" value="<?php echo htmlspecialchars($user['user_tel']); ?>" required>
        </div>
        <div>
            <label for="role">Role:</label>
            <select name="role" id="role" required>
                <option value="admin" <?php echo ($user['user_role'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                <option value="teacher" <?php echo ($user['user_role'] == 'Teacher') ? 'selected' : ''; ?>>Teacher</option>
                <option value="student" <?php echo ($user['user_role'] == 'ExamTech') ? 'selected' : ''; ?>>Student</option>
                <option value="technology" <?php echo ($user['user_role'] == 'Technology') ? 'selected' : ''; ?>>Technology</option>
            </select>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['user_email']); ?>" required>
        </div>
        <button type="submit">Update User</button>
    </form>
</body>
</html>