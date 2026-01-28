<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['company_id']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit();
}

$company_id = $_SESSION['company_id'];

$company_query = $conn->prepare("SELECT company_name FROM companies WHERE company_id = ?");
$company_query->bind_param("i", $company_id);
$company_query->execute();
$company_data = $company_query->get_result()->fetch_assoc();
$company_name = $company_data['company_name'] ?? 'Company';

$sql = "SELECT a.*, s.student_name, s.programme, ad.title as job_title
        FROM attendance a
        JOIN students s ON a.student_id = s.student_id
        JOIN internship_applications ap ON s.student_id = ap.student_id
        JOIN internship_ads ad ON ap.ad_id = ad.ad_id
        WHERE ad.company_id = ? AND ap.application_status = 'approved'
        ORDER BY a.attendance_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Monitoring | myIntern</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/monitor_attendance.css">
</head>
<body>

<nav class="navbar">
     <div class="nav-container">
        <a href="company_dashboard.php" class="nav-brand"><div>mi</div>myIntern</a>
        <ul class="nav-menu">
            <li><a href="company_dashboard.php">Dashboard</a></li>
            <li><a href="view_applicants.php">Applicants</a></li>
            <li class="dropdown">
                <a href="#" class="active">Manage Interns ▼</a>
                <div class="dropdown-content">
                    <a href="monitor_attendance.php">🕒 Attendance Monitoring</a>
                    <a href="review_logbook.php">📖 Logbook Review</a>
                    <a href="evaluate_student.php">⭐ Student Evaluation</a>
                </div>
            </li>
            <li><a href="logout.php" class="btn-logout">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="header-section">
    <div class="nav-container">
        <h1>Attendance Monitoring</h1>
        <p>Viewing records for <?php echo htmlspecialchars($company_name); ?></p>
    </div>
</div>

<div class="container">
    <div class="card">
        <div class="header-flex">
            <div>
                <h3 style="color: #2d3436; font-size: 1.25rem;">Daily Attendance Log</h3>
                <p style="color:#888; font-size: 0.85rem; margin-top: 5px;">Summary of intern check-in and check-out activities.</p>
            </div>
            <button onclick="window.print()" class="btn-pdf">
                📄 Export to PDF
            </button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Student Details</th>
                    <th>Date</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th style="text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: #2d3436; font-size: 0.95rem;"><?php echo htmlspecialchars($row['student_name']); ?></div>
                            <div style="color:#999; font-size:0.75rem; margin-top: 4px;">🎓 <?php echo htmlspecialchars($row['programme']); ?></div>
                        </td>
                        <td style="color: #555; font-weight: 500;"><?php echo date('d M Y', strtotime($row['attendance_date'])); ?></td>
                        <td style="font-weight: 700; color: #34a853;"><?php echo date('h:i A', strtotime($row['check_in'])); ?></td>
                        <td style="font-weight: 700; color: #636e72;">
                            <?php echo ($row['check_out']) ? date('h:i A', strtotime($row['check_out'])) : '--:--'; ?>
                        </td>
                        <td style="text-align: center;">
                            <span class="status-badge <?php echo strtolower($row['attendance_status']); ?>">
                                <?php echo htmlspecialchars($row['attendance_status']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding:100px 0;">
                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" style="opacity: 0.2; margin-bottom: 20px;"><br>
                            <p style="color: #999; font-weight: 500;">No attendance records found for your interns.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<footer>
    &copy; 2026 myIntern Management System. All rights reserved.
</footer>

</body>
</html>