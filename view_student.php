<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: login.php");
    exit();
}

$student_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($student_id == 0) { die("Invalid Student ID."); }

$query = "SELECT s.*, a.application_status, a.apply_date, ad.title as job_title, c.company_name as applied_company
          FROM students s 
          LEFT JOIN internship_applications a ON s.student_id = a.student_id
          LEFT JOIN internship_ads ad ON a.ad_id = ad.ad_id
          LEFT JOIN companies c ON ad.company_id = c.company_id
          WHERE s.student_id = ? 
          ORDER BY a.application_id DESC LIMIT 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) { die("Student not found."); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student - <?php echo htmlspecialchars($student['student_name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/view_student.css">
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
                <li><a href="lecturer_dashboard.php">Students</a></li>
                <li><a href="lecturer_attendance.php">Attendance</a></li>
                <li><a href="lecturer_logbook.php">Logbook</a></li>
                <li><a href="logout.php" class="btn-logout">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-header">
        <div class="container">
            <a href="lecturer_dashboard.php" class="back-btn">← Back to Dashboard</a>
            <h1 style="margin: 0;">Student Profile Detail</h1>
            <p style="opacity: 0.8;">Viewing full information for internship monitoring</p>
        </div>
    </div>

    <div class="container profile-container">
        <div class="profile-card">
            <div class="profile-flex">
                <img src="uploads/<?php echo htmlspecialchars($student['profile_pic'] ?? 'default.png'); ?>" 
                     onerror="this.src='https://cdn-icons-png.flaticon.com/512/149/149071.png'" 
                     class="profile-pic">
                <div>
                    <h2 style="margin: 0; color: var(--primary);"><?php echo htmlspecialchars($student['student_name']); ?></h2>
                    <p style="margin: 5px 0; color: var(--text-muted);">Student ID: <?php echo htmlspecialchars($student['student_number']); ?></p>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-item"><label>Programme</label><span><?php echo htmlspecialchars($student['programme'] ?? 'N/A'); ?></span></div>
                <div class="info-item"><label>University</label><span><?php echo htmlspecialchars($student['university'] ?? 'N/A'); ?></span></div>
                <div class="info-item"><label>CGPA</label><span style="color: var(--primary);"><?php echo htmlspecialchars($student['cgpa'] ?? '0.00'); ?></span></div>
                <div class="info-item"><label>Contact</label><span><?php echo htmlspecialchars($student['phone'] ?? 'N/A'); ?></span></div>
            </div>

            <h3 style="margin-top: 40px; font-size: 1rem; color: #1a1a1a; border-left: 4px solid var(--primary); padding-left: 10px;">Placement Information</h3>
            
            <?php if ($student['application_status']): ?>
                <div class="status-banner">
                    <div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">Current Placement</div>
                        <strong style="font-size: 1.1rem; color: #1a1a1a;"><?php echo htmlspecialchars($student['applied_company']); ?></strong>
                        <div style="font-size: 0.9rem; margin-top: 3px;"><?php echo htmlspecialchars($student['job_title']); ?></div>
                    </div>
                    <span class="status-badge <?php echo ($student['application_status'] == 'approved') ? 'bg-approved' : 'bg-pending'; ?>">
                        <?php echo htmlspecialchars($student['application_status']); ?>
                    </span>
                </div>
            <?php else: ?>
                <div class="no-placement">
                    Student has not secured a placement yet.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        &copy; 2026 myIntern System. All rights reserved.
    </footer>

</body>
</html>