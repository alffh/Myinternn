<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


$lec_stmt = $conn->prepare("SELECT lecturer_name, programme_code FROM lecturers WHERE user_id = ?");
$lec_stmt->bind_param("i", $user_id);
$lec_stmt->execute();
$lec_data = $lec_stmt->get_result()->fetch_assoc();

$lecturer_name = $lec_data['lecturer_name'] ?? "Lecturer";
$assigned_programme = $lec_data['programme_code'] ?? "";
$search_term = "%" . $assigned_programme . "%";


$query = "SELECT s.*, ia.application_status 
          FROM students s
          LEFT JOIN internship_applications ia ON s.student_id = ia.student_id
          WHERE s.programme LIKE ? 
          ORDER BY s.student_name ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $search_term);
$stmt->execute();
$student_result = $stmt->get_result();

$count_query = "SELECT COUNT(*) as total FROM students WHERE programme LIKE ?";
$c_stmt = $conn->prepare($count_query);
$c_stmt->bind_param("s", $search_term);
$c_stmt->execute();
$total_students = $c_stmt->get_result()->fetch_assoc()['total'];

$pending_query = "SELECT COUNT(*) as total FROM internship_applications ia 
                  JOIN students s ON ia.student_id = s.student_id 
                  WHERE s.programme LIKE ? AND ia.application_status = 'pending'";
$p_stmt = $conn->prepare($pending_query);
$p_stmt->bind_param("s", $search_term);
$p_stmt->execute();
$pending = $p_stmt->get_result()->fetch_assoc()['total'];

$approved_query = "SELECT COUNT(*) as total FROM internship_applications ia 
                   JOIN students s ON ia.student_id = s.student_id 
                   WHERE s.programme LIKE ? AND ia.application_status = 'approved'";
$a_stmt = $conn->prepare($approved_query);
$a_stmt->bind_param("s", $search_term);
$a_stmt->execute();
$completed = $a_stmt->get_result()->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard - myIntern</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/lecturer_dashboard.css">
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

<div class="main-content">
    </div>
    <div class="dashboard-header">
        <div class="container">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Lecturer'); ?>!</h1>
            <p>Monitor your assigned students and manage internship evaluations.</p>
        </div>
    </div>

    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <p>Supervised</p>
                <h2><?php echo $total_students; ?></h2>
            </div>
            <div class="stat-card" style="border-color: var(--warning);">
                <p>Pending Apps</p>
                <h2><?php echo $pending; ?></h2>
            </div>
            <div class="stat-card" style="border-color: var(--success);">
                <p>Approved</p>
                <h2><?php echo $completed; ?></h2>
            </div>
        </div>

        <div class="action-row">
            <a href="lecturer_evaluation.php" class="action-card">
                <span class="action-icon">📊</span>
                <div>
                    <div style="font-weight: 700; color: #1a1a1a;">Evaluation Review</div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">View industry marks & feedback</div>
                </div>
            </a>
            <a href="lecturer_report.php" class="action-card">
                <span class="action-icon">📜</span>
                <div>
                    <div style="font-weight: 700; color: #1a1a1a;">Final Reports</div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">Generate completion certificates</div>
                </div>
            </a>
        </div>

        <div class="table-card">
            <h3>Student Monitoring</h3>
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Programme</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($student_result && $student_result->num_rows > 0): ?>
                        <?php while($row = $student_result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['student_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['programme'] ?? 'N/A'); ?></td>
                            <td>
                                <?php 
                                    $app_status = $row['application_status'] ?? '';
                                    if ($app_status == 'approved') {
                                        $displayStatus = "Applied"; $badgeClass = "bg-applied";
                                    } elseif ($app_status == 'pending') {
                                        $displayStatus = "Pending"; $badgeClass = "bg-pending";
                                    } else {
                                        $displayStatus = "Not Applied"; $badgeClass = "bg-notapplied";
                                    }
                                ?>
                                <span class="status-pill <?php echo $badgeClass; ?>">
                                    <?php echo $displayStatus; ?>
                                </span>
                            </td>
                            <td>
                                <a href="view_student.php?id=<?php echo $row['student_id']; ?>" class="btn-view">🔍 View Detail</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center; padding: 40px; color: #999;">No students found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

  <footer style="text-align: center; padding: 40px 0; color: #bbb; font-size: 0.8rem;">
            &copy; 2026 myIntern System. All rights reserved.
        </footer>
    </div>

</body>
</html>