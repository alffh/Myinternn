<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['lecturer', 'applicant'])) {
    header("Location: login.php");
    exit();
}

$student_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($student_id === 0) { die("Error: No student selected."); }

$user_id = $_SESSION['user_id'];
$current_role = $_SESSION['role'];

if ($current_role === 'lecturer') {
    $lec_stmt = $conn->prepare("SELECT lecturer_name, programme_code FROM lecturers WHERE user_id = ?");
    $lec_stmt->bind_param("i", $user_id);
    $lec_stmt->execute();
    $lec_data = $lec_stmt->get_result()->fetch_assoc();
    $lecturer_name = $lec_data['lecturer_name'] ?? "Lecturer";
    $assigned_programme = $lec_data['programme_code'] ?? 'Coordinator';
} else {
    $check_own_id = $conn->prepare("SELECT student_id FROM students WHERE user_id = ?");
    $check_own_id->bind_param("i", $user_id);
    $check_own_id->execute();
    $own_data = $check_own_id->get_result()->fetch_assoc();
    if (!$own_data || $own_data['student_id'] != $student_id) { die("Access Denied."); }
    $lecturer_name = "ACADEMIC COORDINATOR";
    $assigned_programme = "Industrial Training Division";
}

$query = "SELECT s.*, e.final_score, c.company_name 
          FROM students s 
          LEFT JOIN evaluations e ON s.student_id = e.student_id 
          LEFT JOIN internship_applications ia ON s.student_id = ia.student_id AND ia.application_status = 'approved'
          LEFT JOIN internship_ads ad ON ia.ad_id = ad.ad_id
          LEFT JOIN companies c ON ad.company_id = c.company_id
          WHERE s.student_id = ? LIMIT 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Premium Certificate - <?php echo htmlspecialchars($data['student_name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Great+Vibes&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/generate_report.css">
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn btn-main">PRINT CERTIFICATE</button>
        <li><a href="company_dashboard.php">Back to dashboard</a></li>
</div>

    

    <div class="cert-wrapper">
        <div class="certificate">
            <div class="mi-logo"><span>mi</span> myIntern</div>

            <h1>CERTIFICATE</h1>
            <div class="sub-heading">of Industrial Completion</div>

            <p class="certify-text">This is to officially certify that</p>
            <div class="student-name"><?php echo htmlspecialchars($data['student_name']); ?></div>
            
            <p class="details-text">
                Student ID: <strong><?php echo htmlspecialchars($data['student_number']); ?></strong><br>
                Has successfully completed the Professional Internship Program at<br>
                <span class="company-name"><?php echo htmlspecialchars($data['company_name'] ?? 'The Assigned Organization'); ?></span>
            </p>

            <div class="grade-badge">
                FINAL GRADE: <?php echo $data['final_score'] ? $data['final_score'] . '%' : 'DISTINCTION'; ?>
            </div>

            <div class="cert-footer">
                <div class="signature-area">
                    <div class="sig-name"><?php echo htmlspecialchars($lecturer_name); ?></div>
                    <div class="sig-line">
                        Program Coordinator<br>
                        <?php echo htmlspecialchars($assigned_programme); ?>
                    </div>
                </div>

                <div class="official-seal">
                    DIGITAL SEAL<br>AUTHENTICATED<br>BY myIntern
                </div>

                <div class="signature-area">
                    <div class="sig-name" style="font-size: 1.5rem; margin-bottom: 5px;"><?php echo date('d F Y'); ?></div>
                    <div class="sig-line">Date Issued</div>
                </div>
            </div>
            
            <p style="position: absolute; bottom: 10px; left: 0; right: 0; font-size: 0.6rem; color: #bbb; font-family: sans-serif;">
                This certificate is electronically generated and remains property of the University.
            </p>
        </div>
    </div>

</body>
</html>