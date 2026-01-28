<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');
include 'db_connect.php';

// 1. Session & Role Security
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');
$current_time = date('H:i:s');
$message = "";

// 2. Get Student Info
$stmt = $conn->prepare("SELECT student_id, student_name, student_number, internship_status FROM students WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student_data = $stmt->get_result()->fetch_assoc();
$student_id = $student_data['student_id'];

// 3. Check Placement Status
$check_app = $conn->prepare("SELECT COUNT(*) as is_approved FROM internship_applications WHERE student_id = ? AND application_status = 'approved'");
$check_app->bind_param("i", $student_id);
$check_app->execute();
$app_data = $check_app->get_result()->fetch_assoc();

$is_placed = ($student_data['internship_status'] == 'placed' || $app_data['is_approved'] > 0);

// 4. Handle Check-In / Check-Out Actions
if ($is_placed && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'check_in') {
        $check = $conn->prepare("SELECT * FROM attendance WHERE student_id = ? AND attendance_date = ?");
        $check->bind_param("is", $student_id, $today);
        $check->execute();
        
        if ($check->get_result()->num_rows == 0) {
            $late_limit = "09:00:00"; 
            $is_late = (strtotime($current_time) > strtotime($late_limit));
            $log_status = $is_late ? 'LATE' : 'ON TIME';
            
            $sql = $conn->prepare("INSERT INTO attendance (student_id, attendance_date, check_in, attendance_status, status) VALUES (?, ?, ?, 'present', ?)");
            $sql->bind_param("isss", $student_id, $today, $current_time, $log_status);
            $sql->execute();
            $message = "✅ Checked In successfully!";
        }
    } elseif ($_POST['action'] == 'check_out') {
        $sql = $conn->prepare("UPDATE attendance SET check_out = ? WHERE student_id = ? AND attendance_date = ? AND check_out IS NULL");
        $sql->bind_param("sis", $current_time, $student_id, $today);
        $sql->execute();
        $message = "✅ Checked Out successfully!";
    }
}

// 5. Fetch History
$history_query = $conn->prepare("SELECT * FROM attendance WHERE student_id = ? ORDER BY attendance_date DESC");
$history_query->bind_param("i", $student_id);
$history_query->execute();
$history_res = $history_query->get_result();

$todayStatusQuery = $conn->prepare("SELECT * FROM attendance WHERE student_id = ? AND attendance_date = ?");
$todayStatusQuery->bind_param("is", $student_id, $today);
$todayStatusQuery->execute();
$todayStatus = $todayStatusQuery->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance | myIntern</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/applicant.css">
    <style>
        /* Specific tweaks for Attendance Page */
        .welcome-hero { background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; padding: 60px 20px 100px; text-align: center; }
        .welcome-hero h1 { font-size: 2.2rem; margin-bottom: 10px; }
        
        /* Enlarged Table Styling */
        .attendance-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .attendance-table th { 
            text-align: left; padding: 20px; color: #888; text-transform: uppercase; 
            letter-spacing: 1px; font-weight: 700; font-size: 0.9rem; border-bottom: 2px solid #f0f0f0;
        }
        .attendance-table td { 
            padding: 25px 20px; font-size: 1.1rem; border-bottom: 1px solid #f8f9fc; color: #333; 
        }
        .attendance-table tr:hover { background: #fcfaff; }

        /* Clock Styling */
        .live-clock { font-size: 3.5rem; font-weight: 800; color: var(--primary); margin: 10px 0; letter-spacing: -2px; }
        
        /* Button layout */
        .btn-group { display: flex; gap: 15px; justify-content: center; margin-top: 20px; }
        .btn-att { padding: 18px 45px; font-size: 1.1rem; font-weight: 700; width: auto; min-width: 200px; }
        .btn-success { background: #34a853; color: white; }
        .btn-danger { background: #ea4335; color: white; }
        .btn-att:disabled { background: #eee !important; color: #aaa; cursor: not-allowed; transform: none !important; box-shadow: none !important; }

        .status-pill.late { background: #fff3cd; color: #856404; }
        .status-pill.ontime { background: #d4edda; color: #155724; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <a href="applicant.php" class="nav-brand"><div>mi</div> myIntern</a>
        <ul class="nav-menu">
            <li><a href="applicant.php">Home</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle">Internship Tools <span class="arrow">▾</span></a>
                <div class="dropdown-content">
                    <a href="attendance.php">🕒 Clock In / Out</a>
                    <a href="logbook.php">📖 Weekly Logbook</a>
                    <a href="view_evaluation.php">📝 My Result</a>
                </div>
            </li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="logout.php" style="color:#ea4335;">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="welcome-hero">
    <h1>Daily Attendance</h1>
    <p><?php echo htmlspecialchars($student_data['student_name']); ?> | ID: <?php echo htmlspecialchars($student_data['student_number']); ?></p>
</div>

<div class="main-container" style="display: block; max-width: 1000px;">
    <div class="card" style="text-align: center; padding: 50px 30px;">
        <div id="live-clock" class="live-clock">00:00:00</div>
        <p style="color: #666; font-size: 1.1rem; font-weight: 500; margin-bottom: 30px;"><?php echo date('l, d F Y'); ?></p>

        <?php if($message) echo "<p style='color:var(--primary); font-weight:700; margin-bottom:20px;'>$message</p>"; ?>

        <?php if(!$is_placed): ?>
            <div class="card" style="background: #fff5f5; border: 1px solid #fed7d7; color: #c53030; margin: 0 auto; max-width: 500px;">
                🔒 Attendance Restricted<br>
                <small>Approved internship placement required.</small>
            </div>
        <?php else: ?>
            <form method="POST" class="btn-group">
                <button type="submit" name="action" value="check_in" class="btn btn-att btn-success" <?php echo ($todayStatus && $todayStatus['check_in']) ? 'disabled' : ''; ?>>
                    Clock In
                </button>
                <button type="submit" name="action" value="check_out" class="btn btn-att btn-danger" <?php echo (!$todayStatus || !$todayStatus['check_in'] || $todayStatus['check_out']) ? 'disabled' : ''; ?>>
                    Clock Out
                </button>
            </form>
        <?php endif; ?>
    </div>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h3 style="margin:0; font-weight: 800; color: var(--primary);">📋 Attendance History</h3>
            <button onclick="window.print()" class="btn" style="width: auto; background: #f3f0ff; color: var(--primary); padding: 10px 20px;">
                📄 Export Report
            </button>
        </div>

        <div style="overflow-x: auto;">
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($history_res->num_rows > 0): ?>
                        <?php while($row = $history_res->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo date('d M Y', strtotime($row['attendance_date'])); ?></strong></td>
                            <td><?php echo $row['check_in'] ? date('h:i A', strtotime($row['check_in'])) : '--'; ?></td>
                            <td><?php echo $row['check_out'] ? date('h:i A', strtotime($row['check_out'])) : '<span style="color:#aaa;">--</span>'; ?></td>
                            <td>
                                <span class="status-pill <?php echo ($row['status'] == 'LATE') ? 'late' : 'approved'; ?>">
                                    <?php echo $row['status'] ?: 'PRESENT'; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center; padding:50px; color:#aaa;">No records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<footer class="main-footer" style="background: white; padding: 30px; border-top: 1px solid #eee; text-align: center; margin-top: 50px;">
    <p style="color: #777; font-size: 0.9rem;">© 2026 myIntern Platform. All rights reserved.</p>
</footer>

<script>
    function updateClock() {
        const now = new Date();
        document.getElementById('live-clock').textContent = now.toLocaleTimeString('en-MY', {
            hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>

</body>
</html>