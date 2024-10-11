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

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบการส่งข้อมูล
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $conn->real_escape_string($_POST['firstName']);
    $lastName = $conn->real_escape_string($_POST['lastName']);
    $tel = $conn->real_escape_string($_POST['tel']);
    $role = $conn->real_escape_string($_POST['role']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = md5($conn->real_escape_string($_POST['password'])); 

    // สร้าง SQL สำหรับเพิ่มผู้ใช้ใหม่
    $sql = "INSERT INTO User (user_firstname, user_lastname, user_tel, user_role, user_email, user_password) VALUES ('$firstName', '$lastName', '$tel', '$role', '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        $user_id = $conn->insert_id; // รับ user_id ที่เพิ่งเพิ่ม

        // ถ้า role เป็น 'teacher' ให้เพิ่ม user_id ไปยังตาราง teacher
        if ($role === 'Teacher') {
            $insertTeacherSql = "INSERT INTO teacher (user_id) VALUES ('$user_id')";
            if ($conn->query($insertTeacherSql) === TRUE) {
                echo "New teacher created successfully with user_id: $user_id";
            } else {
                echo "Error adding teacher: " . $conn->error;
            }
        } else {
            echo "New user created successfully with user_id: $user_id";
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
