<?php
session_start();

// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: test.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "exam_system";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize input data
    $sub_id = $conn->real_escape_string($_POST['sub_id']);
    $sub_nameEN = $conn->real_escape_string($_POST['sub_nameEN']);
    $sub_nameTH = $conn->real_escape_string($_POST['sub_nameTH']);
    $sub_semester = $conn->real_escape_string($_POST['sub_semester']);
    $sub_section = $conn->real_escape_string($_POST['sub_section']);
    $sub_department = $conn->real_escape_string($_POST['sub_department']);
    $sub_detail = $conn->real_escape_string($_POST['sub_detail']);
    $teacher_fullname = isset($_POST['teacher_fullname']) ? $conn->real_escape_string($_POST['teacher_fullname']) : null;
    $exam_date = $conn->real_escape_string($_POST['exam_date']);
    $exam_room = $conn->real_escape_string($_POST['exam_room']);
    $exam_start = $conn->real_escape_string($_POST['exam_start']);
    $exam_end = $conn->real_escape_string($_POST['exam_end']);
    $exam_status = $conn->real_escape_string($_POST['exam_status']); // New field

    // Initialize teach_id to null
    $teach_id = null;

    // If a teacher's full name is provided, look for their user ID
    if ($teacher_fullname !== null) {
        // Fetch teach_id based on the full name
        $query = "SELECT user_id as teach_new_id FROM user WHERE user_role = 'Teacher' AND CONCAT(user_firstname, ' ', user_lastname) = '$teacher_fullname'";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $teach_id = $row['teach_new_id']; // Set the new teach_id
        } else {
            $_SESSION['error'] = "Selected teacher does not exist.";
            header("Location: examtech.php");
            exit();
        }
    }

    // Prepare SQL statements to update both the subject and exam
    $sql_subject = "UPDATE subject SET 
        sub_nameEN = '$sub_nameEN',
        sub_nameTH = '$sub_nameTH',
        sub_semester = '$sub_semester',
        sub_section = '$sub_section',
        sub_department = '$sub_department',
        sub_detail = '$sub_detail'" .
        ($teach_id !== null ? ", teach_id = '$teach_id'" : "") . 
        " WHERE sub_id = '$sub_id'";

    $sql_exam = "UPDATE exam SET 
        exam_date = '$exam_date',
        exam_room = '$exam_room',
        exam_start = '$exam_start',
        exam_end = '$exam_end',
        exam_status = '$exam_status' 
        WHERE sub_id = '$sub_id'"; // assuming exam is linked to subject via sub_id

    // Execute the subject update query
    if ($conn->query($sql_subject) === TRUE) {
        // Execute the exam update query
        if ($conn->query($sql_exam) === TRUE) {
            $_SESSION['message'] = "Subject and exam updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating exam: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "Error updating subject: " . $conn->error;
    }

    // Redirect back to the management page
    header("Location: examtech.php");
    exit();
}

$conn->close();
?>