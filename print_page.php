<?php
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือยัง ถ้ายังให้ redirect ไปหน้า login
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["user_role"]) || strtolower($_SESSION["user_role"]) !== 'technology') {
    die("Access denied: Unauthorized user.");
}

// ตรวจสอบว่ามีการส่งข้อมูลมาจาก URL หรือไม่
if (!isset($_GET['sub_id'])) {
    die("Invalid request.");
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

$sub_id = $_GET['sub_id'];
$sql = "
SELECT s.sub_id, s.sub_nameTH, s.sub_section, e.exam_date, e.exam_semester, e.exam_start, e.exam_end, e.exam_comment, e.exam_room, e.pdf_path, e.exam_status, e.exam_year, u.user_firstname, u.user_lastname, u.user_tel
FROM subject s
JOIN exam e ON s.sub_id = e.sub_id
JOIN user u ON s.teach_id = u.user_id
WHERE s.sub_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $sub_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No subject found");
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Style for print page content */
        #printPageText {
            width: 100%; /* Full width */
            max-width: 210mm; /* A4 width */
            margin: 0 auto; /* Center alignment */
            padding: 20mm; /* Padding for content */
            font-size: 12px; /* Adjust font size */
            line-height: 1.5; /* Improve line spacing */
            overflow-wrap: break-word; /* Handle long words */
        }

        /* Center the image */
        .image-container {
            text-align: center; /* Center alignment */
            margin-bottom: 20px; /* Add some space below the image */
        }

        /* Media query for print */
        @media print {
            body {
                margin: 0; /* Remove default margin */
            }

            body * {
                visibility: hidden; /* Hide everything except for the modal */
            }

            #printPageText, #printPageText * {
                visibility: visible; /* Show only the print content */
            }

            #printPageText {
                position: absolute;
                left: 0;
                top: 0;
                width: 210mm; /* A4 width */
                height: 297mm; /* A4 height */
                box-sizing: border-box; /* Include padding in width/height */
            }

            /* Adjust styles for printed content */
            pre {
                white-space: pre-wrap; /* Preserve whitespace */
                word-wrap: break-word; /* Handle long words */
                margin: 0; /* Remove default margins */
                font-size: 12px; /* Maintain font size */
                line-height: 1.6; /* Improved line spacing for print */
            }
        }
    </style>
</head>
<body>

<main class="container mt-5 pt-5">
    <div id="printPageText">
        <pre>
                                                                                             คณะวิทยาศาสตร์
        การสอบวิชา <?php echo htmlspecialchars($row['sub_nameTH']); ?>                                                รหัสวิชา <?php echo htmlspecialchars($row['sub_id']); ?>
        สอบวันที่ <?php echo htmlspecialchars($row['exam_date']); ?> เวลา <?php echo htmlspecialchars($row['exam_start']); ?> – <?php echo htmlspecialchars($row['exam_end']); ?> น.
        ห้องสอบ <?php echo htmlspecialchars($row['exam_room']); ?> เลขประจำซอง........................
        จำนวนนักศึกษา................คน
        ซองนี้มีข้อสอบ................ชุด                                     นศ.คณะ................ตอน <?php echo htmlspecialchars($row['sub_section']); ?>
        ข้อสอบสำรอง.....................ชุด
        อุปกรณ์ที่ใช้หรือคำแนะนำผู้คุมสอบเพิ่มเติม
        .......................................................................
        .......................................................................
        ผู้ออกข้อสอบ <?php echo htmlspecialchars($row['user_firstname']); ?> <?php echo htmlspecialchars($row['user_lastname']); ?> 
        ห้องทำงาน...........................................................
        โทรศัพท์มือถือ <?php echo htmlspecialchars($row['user_tel']); ?>

        จำนวนนักศึกษาที่เข้าสอบ...............คน          จำนวนนักศึกษาที่ขาดสอบ...........คน คือ
                รหัสนักศึกษา................................................
                ชื่อ-สกุล......................................................
                1................................................................................ผู้คุมสอบ
                2................................................................................ผู้คุมสอบ
                3................................................................................ผู้คุมสอบ
                หมายเหตุ  <?php echo htmlspecialchars($row['exam_comment']); ?> 
        </pre>
    </div>

    <button class="btn btn-primary" onclick="printContent()">Print</button>
</main>

<!-- Script Section -->
<script>
function printContent() {
    const printContents = document.getElementById('printPageText').innerHTML;
    const newWindow = window.open('', '_blank');
    newWindow.document.write(`
        <html>
        <head>
            <title>Print Page</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20mm; /* Adjust padding for print */
                }
                pre {
                    white-space: pre-wrap; /* Preserve whitespace */
                    word-wrap: break-word; /* Handle long words */
                    font-size: 12px; /* Maintain font size */
                    line-height: 1.6; /* Improved line spacing for print */
                }
                .image-container {
                    text-align: center; /* Center alignment */
                }
            </style>
        </head>
        <body>
            <div class="image-container">
                <img src="logo.png" alt="Description of image" width="250" height="200">
            </div>
            ${printContents}
        </body>
        </html>
    `);
    newWindow.document.close();
    newWindow.print();
}
</script>
</body>
</html>
