<?php
session_start();
include 'db_connect.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: login.php");
    exit();
}

$student_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($student_id == 0) {
    die("Invalid Student ID.");
}

$user_id = $_SESSION['user_id'];
$lec_stmt = $conn->prepare("SELECT programme_code FROM lecturers WHERE user_id = ?");
$lec_stmt->bind_param("i", $user_id);
$lec_stmt->execute();
$assigned_programme = $lec_stmt->get_result()->fetch_assoc()['programme_code'];

$search_term = "%" . $assigned_programme . "%";

$check_access = $conn->prepare("SELECT student_id FROM students WHERE student_id = ? AND programme LIKE ?");
$check_access->bind_param("is", $student_id, $search_term);
$check_access->execute();

if ($check_access->get_result()->num_rows === 0) {
    die("<div style='padding:50px; text-align:center; font-family:sans-serif;'>
            <h1 style='color:red;'>AKSES DISEKAT!</h1>
            <p>Anda tidak dibenarkan melihat rekod pelajar dari kursus lain.</p>
            <br><a href='lecturer_dashboard.php' style='color:blue;'>Kembali ke Dashboard</a>
         </div>");
}

$stmt_student = $conn->prepare("SELECT student_name, student_number, programme FROM students WHERE student_id = ?");
$stmt_student->bind_param("i", $student_id);
$stmt_student->execute();
$student = $stmt_student->get_result()->fetch_assoc();

$query = "SELECT * FROM logbook WHERE student_id = ? ORDER BY log_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$logs = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logbook - <?php echo htmlspecialchars($student['student_name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="css/logbook_history.css">
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
            <a href="lecturer_logbook.php" class="back-btn">← Back to Logbook Summary</a>
            <h1 style="margin: 0;">Weekly Logbook Activities</h1>
            <p style="opacity: 0.9;"><?php echo htmlspecialchars($student['student_name']); ?> (<?php echo htmlspecialchars($student['student_number']); ?>)</p>
        </div>
    </div>

    <div class="container log-container">
        <?php if ($logs->num_rows > 0): ?>
            <?php while($row = $logs->fetch_assoc()): ?>
            <div class="log-entry">
                <span class="log-date">📅 <?php echo date('d M Y', strtotime($row['log_date'])); ?></span>
                
                <span class="status-badge <?php echo ($row['status'] == 'approved') ? 'bg-approved' : 'bg-pending'; ?>">
                    <?php echo ucfirst($row['status']); ?>
                </span>

                <div class="activities-box">
                    <strong style="color: #1a1a1a; display: block; margin-bottom: 5px;">Daily Activities:</strong>
                    <?php echo nl2br(htmlspecialchars($row['activities'])); ?>
                </div>

                <div class="hours-tag">
                    ⏱️ Time Spent: <strong><?php echo $row['hours_spent']; ?> Hours</strong>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="background: white; padding: 50px; border-radius: 20px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
                <p style="color:#999; font-size: 1.1rem;">No logbook entries found for this student.</p>
            </div>
        <?php endif; ?>

        <footer style="text-align: center; padding: 40px 0; color: #bbb; font-size: 0.8rem;" class="no-print">
            &copy; 2026 myIntern System. All rights reserved.
        </footer>
    </div>

</body>
</html>