<?php
// 1. MESTI panggil session_start & db_connect di baris teratas
session_start();
include 'db_connect.php';

// 2. Semak akses: Pastikan hanya lecturer yang sah
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: login.php");
    exit();
}

// 3. LOGIK AUTO-SYNC: Dapatkan kod kursus pensyarah yang sedang login
$user_id = $_SESSION['user_id'];
$lec_stmt = $conn->prepare("SELECT programme_code FROM lecturers WHERE user_id = ?");
$lec_stmt->bind_param("i", $user_id);
$lec_stmt->execute();
$lec_result = $lec_stmt->get_result()->fetch_assoc();

$assigned_programme = $lec_result['programme_code'] ?? '';
$search_term = "%" . $assigned_programme . "%";

// 4. Query Laporan: Ganti 'overall_score' kepada 'final_score'
$query = "SELECT s.student_id, s.student_name, s.student_number, s.programme, 
          e.evaluation_id, e.final_score 
          FROM students s 
          LEFT JOIN evaluations e ON s.student_id = e.student_id 
          WHERE s.programme LIKE ? 
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
    <title>Final Internship Reports - myIntern</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="css/lecturer_report.css">
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
            <a href="lecturer_dashboard.php" class="back-btn">← Back to Dashboard</a>
            <h1 style="margin: 0;">Final Internship Reports</h1>
            <p style="opacity: 0.9;">Generate and download student completion reports after evaluation.</p>
        </div>
    </div>

    <div class="container report-section">
        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>Student Information</th>
                        <th>Internship Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: #1a1a1a;"><?php echo htmlspecialchars($row['student_name']); ?></div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($row['student_number']); ?></div>
                            </td>
                            <td>
                                <?php if($row['evaluation_id']): ?>
                                    <span class="status-ready">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>
                                        Ready (Score: <?php echo $row['final_score']; ?>%)
                                    </span>
                                <?php else: ?>
                                    <span class="status-wait">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/></svg>
                                        Pending Evaluation
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <?php if($row['evaluation_id']): ?>
                                    <a href="generate_report.php?id=<?php echo $row['student_id']; ?>" class="btn-download">
                                        📄 Download Report
                                    </a>
                                <?php else: ?>
                                    <span class="btn-disabled">Not Ready</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 50px; color: #999;">No students found in the database.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <footer style="text-align: center; padding: 40px 0; color: #bbb; font-size: 0.8rem;">
            &copy; 2026 myIntern System. All rights reserved.
        </footer>
    </div>

</body>
</html>