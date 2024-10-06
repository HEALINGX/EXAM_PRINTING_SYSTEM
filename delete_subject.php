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
$dbname = "printing_exam";

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

// Check if sub_id is set
if (isset($_GET['sub_id'])) {
    $sub_id = $conn->real_escape_string($_GET['sub_id']);

    // Log the deletion request
    logData("Request to delete subject with ID: $sub_id");

    // Delete related exams from the database first
    $deleteExams = "DELETE FROM exam WHERE sub_id = '$sub_id'";
    if ($conn->query($deleteExams) === TRUE) {
        logData("Related exams for subject with ID $sub_id deleted successfully");

        // Delete the subject from the database
        $deleteSubject = "DELETE FROM subject WHERE sub_id = '$sub_id'";
        if ($conn->query($deleteSubject) === TRUE) {
            logData("Subject with ID $sub_id deleted successfully");

            // Redirect to the dashboard or show success message
            header("Location: examtech.php");
            exit();
        } else {
            logData("Error deleting subject: " . $conn->error);
            echo "Error deleting subject: " . $conn->error;
        }
    } else {
        logData("Error deleting related exams: " . $conn->error);
        echo "Error deleting related exams: " . $conn->error;
    }
} else {
    logData("No subject ID provided for deletion");
    echo "No subject ID provided.";
}

$conn->close();
?>