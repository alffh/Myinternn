<?php
session_start();

include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: login.php");
    exit();
}

$student_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($student_id === 0) {
    die("Error: No student selected.");
}

$user_id = $_SESSION['user_id'];
$lec_stmt = $conn->prepare("SELECT lecturer_name, programme_code FROM lecturers WHERE user_id = ?");
$lec_stmt->bind_param("i", $user_id);
$lec_stmt->execute();
$lec_data = $lec_stmt->get_result()->fetch_assoc();
$assigned_programme = $lec_data['programme_code'] ?? '';
$search_term = "%" . $assigned_programme . "%";

$check_access = $conn->prepare("SELECT student_id FROM students WHERE student_id = ? AND programme LIKE ?");
$check_access->bind_param("is", $student_id, $search_term);
$check_access->execute();

if ($check_access->get_result()->num_rows === 0) {
    die("<div style='padding:50px; text-align:center; font-family:sans-serif;'>
            <h1 style='color:red;'>AKSES DISEKAT!</h1>
            <p>Anda tidak dibenarkan melihat rekod pelajar dari kursus lain.</p>
            <br><a href='lecturer_attendance.php' style='color:blue;'>Kembali ke Senarai Kehadiran</a>
         </div>");
}

$name_stmt = $conn->prepare("SELECT student_name, student_number, programme FROM students WHERE student_id = ?");
$name_stmt->bind_param("i", $student_id);
$name_stmt->execute();
$student = $name_stmt->get_result()->fetch_assoc();

$lecturer_name = $lec_data['lecturer_name'] ?? "Lecturer";

$query = "SELECT * FROM attendance WHERE student_id = ? ORDER BY attendance_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$history = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance History - <?php echo htmlspecialchars($student['student_name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/attendance_history.css">
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
            <a href="lecturer_attendance.php" class="back-btn">← Back to Summary</a>
            <h1 style="margin: 0;">Attendance History</h1>
            <p style="opacity: 0.9;">Detailed record for monitoring student punctuality</p>
        </div>
    </div>

    <div class="container report-container">
        <div class="report-card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;" class="report-header">
                <div class="student-meta">
                    <h2 style="margin:0; color: var(--primary); font-size: 1.5rem;"><?php echo htmlspecialchars($student['student_name']); ?></h2>
                    <div>Student ID: <strong><?php echo htmlspecialchars($student['student_number']); ?></strong></div>
                    <div>Programme: <strong><?php echo htmlspecialchars($student['programme']); ?></strong></div>
                </div>
                <button onclick="window.print()" class="btn-pdf no-print">📄 Export PDF</button>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($history->num_rows > 0): ?>
                        <?php while($row = $history->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo date('d M Y', strtotime($row['attendance_date'])); ?></strong></td>
                            <td><?php echo $row['check_in'] ? date('h:i A', strtotime($row['check_in'])) : '--:--'; ?></td>
                            <td><?php echo $row['check_out'] ? date('h:i A', strtotime($row['check_out'])) : '--:--'; ?></td>
                            <td><span class="status-badge">✓ Present</span></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center; padding: 40px; color: #999;">No attendance records found for this student.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="signature-area">
                <div class="official-stamp">
                    OFFICIAL<br>UNIVERSITY<br>STAMP
                </div>

                <div class="sig-box">
                    <div class="auto-signature"><?php echo htmlspecialchars($lecturer_name); ?></div>
                    <div class="sig-line">Lecturer Signature</div>
                    <div style="font-size: 0.7rem; color: #999; margin-top: 4px;">Verified via myIntern Portal</div>
                </div>
            </div>
        </div>

        <footer style="text-align: center; padding: 40px 0; color: #bbb; font-size: 0.8rem;" class="no-print">
            &copy; 2026 myIntern System. All rights reserved.
        </footer>
    </div>

</body>
</html>