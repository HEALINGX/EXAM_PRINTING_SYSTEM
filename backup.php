<?php
session_start();

// ตรวจสอบสิทธิ์การเข้าถึงของผู้ใช้
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["user_role"]) || strtolower($_SESSION["user_role"]) !== 'examtech') {
    die("Access denied: Unauthorized user.");
}

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "printing_exam";

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['exam_id'])) {
    $exam_id = intval($_GET['exam_id']);

    // ค้นหาข้อมูลจากตาราง exam
    $sql = "
    SELECT s.sub_nameEN, s.sub_detail, e.exam_date, e.pdf_path 
    FROM subject s
    JOIN exam e ON s.sub_id = e.sub_id
    WHERE e.exam_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $exam_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // ตรวจสอบว่ามีไฟล์ pdf_path อยู่หรือไม่
        if (!empty($row['pdf_path'])) {
            // เพิ่มข้อมูลลงในตาราง backup
            $insert_sql = "
            INSERT INTO backup (exam_id, sub_nameEN, sub_detail, exam_date, pdf_path)
            VALUES (?, ?, ?, ?, ?)";

            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("issss", $exam_id, $row['sub_nameEN'], $row['sub_detail'], $row['exam_date'], $row['pdf_path']);
            
            if ($insert_stmt->execute()) {
                // อัปเดตข้อมูลในตาราง exam โดยลบเฉพาะค่าใน pdf_path
                $update_sql = "UPDATE exam SET pdf_path = NULL WHERE exam_id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("i", $exam_id);
                if ($update_stmt->execute()) {
                    // ส่งกลับไปที่ Tech_view_exam.php พร้อมพารามิเตอร์
                    header("Location: Tech_view_exam.php?backup_success=1");
                    exit();
                } else {
                    header("Location: Tech_view_exam.php?error=" . urlencode("Error updating data: " . $conn->error));
                    exit();
                }
            } else {
                header("Location: Tech_view_exam.php?error=" . urlencode("Error inserting backup: " . $conn->error));
                exit();
            }
        } else {
            header("Location: Tech_view_exam.php?error=" . urlencode("No file available for backup."));
            exit();
        }
    } else {
        header("Location: Tech_view_exam.php?error=" . urlencode("Exam not found."));
        exit();
    }
} else {
    header("Location: Tech_view_exam.php?error=" . urlencode("Invalid request."));
    exit();
}

$conn->close();
?>
