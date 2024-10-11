<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "exam_system";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to log data
function logData($message) {
    $logFile = 'log.txt'; // Specify log file location
    $currentDateTime = date('Y-m-d H:i:s');
    $logMessage = "[$currentDateTime] $message" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from form
    $sub_nameEN = $conn->real_escape_string($_POST['sub_nameEN']);
    $sub_nameTH = $conn->real_escape_string($_POST['sub_nameTH']);
    $sub_semester = $conn->real_escape_string($_POST['sub_semester']);
    $sub_section = $conn->real_escape_string($_POST['sub_section']);
    $sub_department = $conn->real_escape_string($_POST['sub_department']);
    $sub_detail = $conn->real_escape_string($_POST['sub_detail']);
    $exam_date = $conn->real_escape_string($_POST['exam_date']);
    $exam_room = $conn->real_escape_string($_POST['exam_room']);
    $exam_year = $conn->real_escape_string($_POST['exam_room']);
    $exam_start = $conn->real_escape_string($_POST['exam_start']);
    $exam_end = $conn->real_escape_string($_POST['exam_end']);
    $teacher_id = $conn->real_escape_string($_POST['teacher_id']);

    // Log form data
    logData("Form Data: sub_nameEN=$sub_nameEN, sub_nameTH=$sub_nameTH, sub_semester=$sub_semester, sub_section=$sub_section, sub_department=$sub_department, sub_detail=$sub_detail, exam_date=$exam_date, exam_room=$exam_room, exam_start=$exam_start, exam_end=$exam_end, teacher_id=$teacher_id");

    // Insert subject
    $insertSubject = "INSERT INTO subject (sub_nameEN, sub_nameTH, sub_semester, sub_section, sub_department, sub_detail, teach_id) 
                      VALUES ('$sub_nameEN', '$sub_nameTH', '$sub_semester', '$sub_section', '$sub_department', '$sub_detail', '$teacher_id')";

    if ($conn->query($insertSubject) === TRUE) {
        // Get the last inserted subject ID
        $sub_id = $conn->insert_id;

        // Log subject insertion success
        logData("Subject inserted successfully with ID $sub_id");

        // Insert exam
        $insertExam = "INSERT INTO exam (sub_id, exam_date, exam_start, exam_end, exam_room,exam_year, exam_status) 
                        VALUES ('$sub_id', '$exam_date', '$exam_start', '$exam_end', '$exam_room','$exam_year', 'Scheduled')";

        if ($conn->query($insertExam) === TRUE) {
            // Log exam insertion success
            logData("Exam inserted successfully for subject ID $sub_id");

            // Redirect to the dashboard or show success message
            header("Location: examtech.php");
            exit();
        } else {
            // Log exam insertion error
            logData("Error inserting exam: " . $conn->error);
            echo "Error: " . $insertExam . "<br>" . $conn->error;
        }
    } else {
        // Log subject insertion error
        logData("Error inserting subject: " . $conn->error);
        echo "Error: " . $insertSubject . "<br>" . $conn->error;
    }
}

$conn->close();
?>