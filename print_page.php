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
$dbname = "test2";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sub_id = $_GET['sub_id'];
$sql = "
SELECT s.sub_id, s.sub_nameTH, s.sub_section, e.exam_date, s.sub_semester, e.exam_start, e.exam_end, e.exam_room, e.pdf_path, e.exam_status, e.exam_year, e.exam_comment, u.user_firstname, u.user_lastname, u.user_tel
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
    body {
        font-family: 'TH SarabunPSK', sans-serif;
    }

    #printPageText {
        width: 100%; /* ขนาด A4 กว้าง */
        height: auto; /* ขนาด A4 สูง */
        padding: 1cm; /* ขอบรอบเนื้อหา */
        margin: 0 auto; /* จัดกึ่งกลางแนวนอน */
        border: 1px solid black;
        font-size: 14px;
        line-height: 1.6;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        justify-content: center; /* จัดให้อยู่กึ่งกลางในแนวตั้ง */
        align-items: center; /* จัดให้อยู่กึ่งกลางในแนวนอน */
        text-align: center; /* จัดข้อความให้อยู่กึ่งกลาง */
    }

    .header-text {
        text-align: center; /* จัดข้อความให้อยู่ตรงกลางแนวนอน */
        font-weight: bold;
        font-size: 40px; /* เพิ่มขนาดตัวอักษรเพื่อให้ดูเด่นขึ้น */
        margin-bottom: 20px;
        width: 100%; /* ทำให้ข้อความขยายเต็มความกว้างของ container */
    }

    .left-section {
        text-align: left;
    }

    .right-section {
        text-align: right;
    }

    .content-section {
        display: flex;
        justify-content: space-between; /* จัดให้องค์ประกอบซ้ายขวาห่างกัน */
        width: 100%;
    }

    .content-section2 {
        display: flex;
        justify-content: center; /* จัดให้องค์ประกอบซ้ายขวาห่างกัน */
        width: 100%;
    }

    @media print {
        body * {
            visibility: hidden;
        }

        #printPageText, #printPageText * {
            visibility: visible;
        }

        #printPageText {
            position: absolute;
            left: 0;
            top: 0;
            margin: auto;
        }

        button {
            display: none; /* ซ่อนปุ่มตอนพิมพ์ */
        }

        /* ปรับขนาดตัวอักษรในโหมดพิมพ์ */
        #printPageText {
            font-size: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .header-text {
            font-size: 20px; /* ขนาดหัวเรื่องใหญ่ขึ้น */
        }
    }
</style>
    <script>
        function printContent() {
            window.print();
        }
    </script>
</head>
<body>

<main class="container mt-5 pt-5">
    <div id="printPageText">
    <div class="header-text">
            <img src="logo.png" alt="Logo" style="max-width: 100px; height: auto; display: block; margin: 0 auto;">
            คณะวิทยาศาสตร์
        </div>

        <div class="content-section">
            <span class="left-section">การสอบวิชา <?php echo htmlspecialchars($row['sub_nameTH']); ?></span>
            <span class="right-section">รหัสวิชา <?php echo htmlspecialchars($row['sub_id']); ?></span>
        </div>

        <div class="content-section">
            <span class="left-section">สอบวันที่ <?php echo htmlspecialchars($row['exam_date']); ?></span>
            <span class="right-section">เวลา <?php echo htmlspecialchars($row['exam_start']); ?> – <?php echo htmlspecialchars($row['exam_end']); ?> น.</span>
        </div>

        <div class="content-section">
            <span class="left-section">ห้องสอบ <?php echo htmlspecialchars($row['exam_room']); ?></span>
            <span class="right-section">เลขประจำซอง........................</span>
        </div>

        <div class="content-section">
            <span class="left-section">จำนวนนักศึกษา................คน</span>
            <span class="right-section">นศ.คณะ................ตอน <?php echo htmlspecialchars($row['sub_section']); ?></span>
        </div>

        <div class="content-section">
            <span class="left-section">ข้อสอบสำรอง.....................ชุด</span>
        </div>

        <div class="content-section">
            <span class="left-section">อุปกรณ์ที่ใช้หรือคำแนะนำผู้คุมสอบเพิ่มเติม</span><br>
            ...................................................................................................................................................................
        </div>

        <div class="content-section">
            <span class="left-section">ผู้ออกข้อสอบ <?php echo htmlspecialchars($row['user_firstname']); ?> <?php echo htmlspecialchars($row['user_lastname']); ?></span>
            <span class="right-section">โทรศัพท์มือถือ <?php echo htmlspecialchars($row['user_tel']); ?></span>
        </div>

        <div class="content-section">
            <span class="left-section">จำนวนนักศึกษาที่เข้าสอบ...............คน</span>
            <span class="right-section">จำนวนนักศึกษาที่ขาดสอบ...........คน</span>
        </div>

        <div class="content-section">
            <span class="left-section">รหัสนักศึกษา................................................</span>
            <span class="right-section">ชื่อ-สกุล......................................................</span>
        </div>

        <div class="content-section">
            <span class="left-section">รหัสนักศึกษา................................................</span>
            <span class="right-section">ชื่อ-สกุล......................................................</span>
        </div>

        <div class="content-section">
            <span class="left-section">รหัสนักศึกษา................................................</span>
            <span class="right-section">ชื่อ-สกุล......................................................</span>
        </div>

        <div class="content-section2">
            1................................................................................ผู้คุมสอบ<br>
            2................................................................................ผู้คุมสอบ<br>
            3................................................................................ผู้คุมสอบ
        </div>

        <div class="content-section2">
            หมายเหตุ..................................................................................
        </div>
    </div>

    <button class="btn btn-primary" onclick="printContent()">Print</button>
</main>

</body>
</html>
