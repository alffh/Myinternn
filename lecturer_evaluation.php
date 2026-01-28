<?php
session_start();

include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$lec_stmt = $conn->prepare("SELECT programme_code FROM lecturers WHERE user_id = ?");
$lec_stmt->bind_param("i", $user_id);
$lec_stmt->execute();
$lec_result = $lec_stmt->get_result()->fetch_assoc();

$assigned_programme = $lec_result['programme_code'] ?? '';
$search_term = "%" . $assigned_programme . "%";

$query = "SELECT e.*, s.student_name, s.student_number, s.programme, c.company_name 
          FROM evaluations e
          JOIN students s ON e.student_id = s.student_id
          JOIN companies c ON e.company_id = c.company_id
          WHERE s.programme LIKE ? 
          ORDER BY e.submitted_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $search_term);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation Review - myIntern</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/lecturer_evaluation.css">
</head>
<body>

       <nav class="navbar">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <a href="lecturer_dashboard.php" class="nav-brand">
                <svg width="32" height="32" viewBox="0 0 32 32">
                    <rect width="32" height="32" rx="8" fill="#6f42c1"/>
                    <text x="16" y="22" font-size="18" font-weight="bold" fill="white" text-anchor="middle">mi</text>
                </svg>
                <span>myIntern</span>
            </a>
            <ul class="nav-menu">
                <li><a href="lecturer_dashboard.php">Home</a></li>
                <li><a href="lecturer_attendance.php">Attendance</a></li>
                <li><a href="lecturer_logbook.php" >Logbook</a></li>
                <li><a href="lecturer_profile.php">Profile</a></li>
                <li><a href="logout.php" class="btn-logout">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-header no-print">
        <div class="container">
            <a href="lecturer_dashboard.php" class="back-btn">← Back to Dashboard</a>
            <h1 style="margin: 0;">Evaluation Review</h1>
            <p style="opacity: 0.9;">Analyze industry marks and student performance feedback</p>
        </div>
    </div>

    <div class="container eval-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
            <div class="eval-card">
                <div class="eval-header">
                    <div class="student-info">
                        <h3><?php echo htmlspecialchars($row['student_name']); ?></h3>
                        <p>ID: <?php echo htmlspecialchars($row['student_number']); ?></p>
                        <p><strong>🏢 <?php echo htmlspecialchars($row['company_name']); ?></strong></p>
                    </div>
                    <div class="score-circle">
                        <span>Score</span>
                        <?php echo $row['final_score']; ?>%
                    </div>
                </div>
                
                <hr style="border: 0; border-top: 1px solid #eee;">
                
                <div class="feedback-box">
                    <strong style="display: block; margin-bottom: 8px; font-style: normal; color: #1a1a1a;">Industry Feedback:</strong>
                    "<?php echo nl2br(htmlspecialchars($row['feedback'])); ?>"
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button onclick="window.print()" class="btn-pdf no-print">
                        📄 Download Report
                    </button>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="background: white; padding: 60px; border-radius: 20px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
                <p style="color:#999; font-size: 1.1rem;">No student evaluations have been submitted yet.</p>
            </div>
        <?php endif; ?>

        <footer style="text-align: center; padding: 40px 0; color: #bbb; font-size: 0.8rem;" class="no-print">
            &copy; 2026 myIntern System. All rights reserved.
        </footer>
    </div>

</body>
</html>