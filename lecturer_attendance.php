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

$query = "SELECT s.student_id, s.student_name, s.student_number, s.programme, 
          COUNT(a.attendance_id) as total_days
          FROM students s
          INNER JOIN internship_applications ia ON s.student_id = ia.student_id
          LEFT JOIN attendance a ON s.student_id = a.student_id
          WHERE ia.application_status = 'approved' 
          AND s.programme LIKE ? 
          GROUP BY s.student_id, s.student_name, s.student_number, s.programme
          ORDER BY s.student_name ASC";

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
    <title>Attendance Summary - myIntern</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="css/lecturer_attendance.css">
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

    <div class="dashboard-header">
        <div class="container">
            <h1 style="margin: 0;">Active Internship Attendance</h1>
            <p style="opacity: 0.9;">Monitoring students with Approved Placement status</p>
        </div>
    </div>

    <div class="container content-section">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
            <div class="student-card">
                <div class="student-info">
                    <h3><?php echo htmlspecialchars($row['student_name']); ?></h3>
                    <p><?php echo htmlspecialchars($row['student_number']); ?> • <?php echo htmlspecialchars($row['programme']); ?></p>
                </div>
                
                <div class="stats-box">
                    <div class="days-count">
                        <small>Total Days</small>
                        <?php echo $row['total_days']; ?>
                    </div>
                    <a href="attendance_history.php?id=<?php echo $row['student_id']; ?>" class="btn-detail">
                        View History
                    </a>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" width="80" style="opacity: 0.2; margin-bottom: 20px;">
                <p>No students have an approved internship placement yet.</p>
            </div>
        <?php endif; ?>

        <footer style="text-align: center; padding: 40px 0; color: #bbb; font-size: 0.8rem;">
            &copy; 2026 myIntern System. All rights reserved.
        </footer>
    </div>

</body>
</html>