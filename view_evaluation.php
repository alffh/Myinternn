<?php
date_default_timezone_set('Asia/Kuala_Lumpur');
include 'db_connect.php'; 
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$studentQuery = $conn->prepare("SELECT student_id FROM students WHERE user_id = ?");
$studentQuery->bind_param("i", $user_id);
$studentQuery->execute();
$studentResult = $studentQuery->get_result()->fetch_assoc();

if (!$studentResult) {
    die("Error: Student profile not found.");
}

$student_id = $studentResult['student_id'];

$sql = "SELECT evaluations.*, students.student_name, students.programme, companies.company_name 
        FROM evaluations 
        JOIN students ON evaluations.student_id = students.student_id
        JOIN companies ON evaluations.company_id = companies.company_id
        WHERE evaluations.student_id = ? 
        ORDER BY evaluations.submitted_at DESC LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$data = ($result->num_rows > 0) ? $result->fetch_assoc() : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internship Result - myIntern</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/view_evaluation.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-container">
            <a href="applicant.php" class="nav-brand"><div>mi</div> myIntern</a>
            <ul class="nav-menu">
                <li><a href="applicant.php">Home</a></li>
                <li class="dropdown">
                    <a href="#">Internship Tools ▼</a>
                    <div class="dropdown-content">
                        <a href="attendance.php">🕒 Clock In/Out</a>
                        <a href="logbook.php">📖 Weekly Logbook</a>
                        <a href="view_evaluation.php" style="color: #6f42c1; font-weight: bold;">📝 My Result</a>
                    </div>
                </li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php" style="color:#ea4335;">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-header no-print">
        <div class="container">
            <h1>Evaluation Result</h1>
            <p>View and download your internship performance report</p>
        </div>
    </div>

    <div class="container result-container">
        <div class="section-card">
            <?php if ($data): ?>
                <div class="slip-header">
                    <h2 style="margin:0; color: #1a1a1a;">INTERNSHIP PERFORMANCE SLIP</h2>
                    <p style="color:#777; font-size: 0.9rem;">Industrial Training Official Evaluation</p>
                </div>

                <table class="info-table">
                    <tr>
                        <td width="30%"><strong>Student Name</strong></td>
                        <td>: <?php echo strtoupper($data['student_name']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Programme</strong></td>
                        <td>: <?php echo $data['programme']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Host Company</strong></td>
                        <td>: <?php echo $data['company_name']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Issue Date</strong></td>
                        <td>: <?php echo date('d F Y', strtotime($data['submitted_at'])); ?></td>
                    </tr>
                </table>

                <table class="grade-table">
                    <thead>
                        <tr>
                            <th>Assessment Item</th>
                            <th style="text-align:center;">Score (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Attendance & Punctuality</td>
                            <td align="center"><strong><?php echo $data['attendance_score']; ?></strong></td>
                        </tr>
                        <tr>
                            <td>Technical Skills & Quality of Work</td>
                            <td align="center"><strong><?php echo $data['skill_score']; ?></strong></td>
                        </tr>
                        <tr>
                            <td>Professional Ethics & Discipline</td>
                            <td align="center"><strong><?php echo $data['discipline_score']; ?></strong></td>
                        </tr>
                    </tbody>
                </table>

                <div class="final-score-box">
                    FINAL GRADE: <?php echo number_format($data['final_score'], 1); ?>%
                    <div style="font-size: 15px; margin-top: 5px; font-weight: 600; color: #555;">
                        RESULT: <?php echo ($data['final_score'] >= 50) ? 'PASSED' : 'FAILED'; ?>
                    </div>
                </div>

                <div style="margin-top: 30px;">
                    <p style="font-weight: 600; color: #555; margin-bottom: 10px;">Supervisor's Feedback:</p>
                    <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; font-style: italic; border-left: 4px solid var(--primary);">
                        "<?php echo nl2br(htmlspecialchars($data['feedback'] ?? 'No feedback provided.')); ?>"
                    </div>
                </div>

                <div class="signature-area" style="margin-top: 50px; display: flex; justify-content: space-between; align-items: flex-end;">
                    <div style="font-size: 0.75rem; color: #999;">
                        Verification ID:<br>
                        #<?php echo strtoupper(substr(md5($data['evaluation_id']), 0, 10)); ?>
                    </div>
                    <div style="text-align: center;">
                        <div class="digital-stamp">
                            Digitally Verified<br>
                            <span style="font-size: 0.7rem;"><?php echo $data['company_name']; ?></span>
                        </div>
                        <p style="font-size: 0.8rem; margin-top: 10px; color: #666;">
                            System Generated Slip<br>No Signature Required
                        </p>
                    </div>
                </div>

                <div style="text-align: center;" class="no-print">
                    <button onclick="window.print()" class="btn-print">Download as PDF / Print Slip</button>
                </div>

            <?php else: ?>
                <div style="text-align:center; padding: 40px;">
                    <div style="font-size: 50px; margin-bottom: 20px;">⏳</div>
                    <h3>Result Not Available</h3>
                    <p style="color: #777;">Your supervisor has not submitted your evaluation yet. Please check again later.</p>
                    <a href="applicant.php" class="btn-print">Back to Dashboard</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer class="no-print" style="text-align: center; padding: 40px 0; color: #bbb; font-size: 0.8rem;">
        &copy; 2026 myIntern System. All rights reserved.
    </footer>

</body>
</html>