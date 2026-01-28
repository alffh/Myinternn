<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['company_id']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit();
}

$company_id = $_SESSION['company_id'];
$message = "";


$company_query = $conn->query("SELECT company_name FROM companies WHERE company_id = '$company_id'");
$company_data = $company_query->fetch_assoc();
$company_name = $company_data['company_name'] ?? 'Company';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_feedback'])) {
    $feedback = $conn->real_escape_string($_POST['feedback']);
    $status = $_POST['log_status'];
    $log_date = $_POST['log_date'];
    $student_id = $_POST['student_id'];

    $updateSql = "UPDATE logbook SET supervisor_comments = '$feedback', status = '$status' 
                  WHERE student_id = '$student_id' AND log_date = '$log_date'";
    
    if ($conn->query($updateSql)) {
        $message = "✅ Feedback updated successfully!";
    } else {
        $message = "❌ Error: " . $conn->error;
    }
}


$sql = "SELECT l.*, s.student_name, s.programme 
        FROM logbook l
        JOIN students s ON l.student_id = s.student_id
        JOIN internship_applications ap ON s.student_id = ap.student_id
        JOIN internship_ads ad ON ap.ad_id = ad.ad_id
        WHERE ad.company_id = '$company_id' AND ap.application_status = 'approved'
        ORDER BY l.log_date DESC"; 

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logbook Review | myIntern</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/review_logbook.css">
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
        <h1>Logbook Review</h1>
        <p>Monitor daily activities and provide professional feedback to your interns.</p>
    </div>
</div>

<div class="container">
    <?php if($message) echo "<div class='alert'>$message</div>"; ?>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="card">
                <div class="student-header">
                    <div>
                        <h3 style="color: #2d3436;"><?php echo htmlspecialchars($row['student_name']); ?></h3>
                        <p style="color:#999; font-size:0.85rem; margin-top:4px;">🎓 <?php echo htmlspecialchars($row['programme']); ?></p>
                    </div>
                    <span class="date-badge">📅 <?php echo date('d M Y', strtotime($row['log_date'])); ?></span>
                </div>

                <div>
                    <span class="label-text">Intern's Daily Activities</span>
                    <div class="activities-box">
                        <?php echo nl2br(htmlspecialchars($row['activities'])); ?>
                        <div style="margin-top: 15px; padding-top: 12px; border-top: 1px solid #f1f1f1; font-size: 0.85rem; color: #666;">
                            <strong>Duration:</strong> <?php echo $row['hours_spent']; ?> working hours reported.
                        </div>
                    </div>
                </div>

                <div class="feedback-section">
                    <form method="POST">
                        <input type="hidden" name="log_date" value="<?php echo $row['log_date']; ?>">
                        <input type="hidden" name="student_id" value="<?php echo $row['student_id']; ?>">
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                            <span class="label-text">Supervisor's Feedback & Approval</span>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="font-size: 0.8rem; font-weight: 600; color: #555;">Status:</span>
                                <select name="log_status">
                                    <option value="pending" <?php echo ($row['status'] == 'pending') ? 'selected' : ''; ?>>⏳ Pending Review</option>
                                    <option value="approved" <?php echo ($row['status'] == 'approved') ? 'selected' : ''; ?>>✅ Approved</option>
                                    <option value="rejected" <?php echo ($row['status'] == 'rejected') ? 'selected' : ''; ?>>❌ Needs Revision</option>
                                </select>
                            </div>
                        </div>
                        
                        <textarea name="feedback" rows="3" placeholder="Add constructive comments for the student..."><?php echo htmlspecialchars($row['supervisor_comments'] ?? ''); ?></textarea>
                        
                        <div style="text-align: right;">
                            <button type="submit" name="update_feedback" class="btn-save">Save Review</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="card" style="text-align:center; padding:100px 0;">
            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" style="opacity: 0.2; margin-bottom: 20px;"><br>
            <h3 style="color: #bbb; font-weight: 500;">No Logbook Entries</h3>
            <p style="color: #ccc; font-size: 0.9rem;">Your interns haven't submitted any daily logs for review yet.</p>
        </div>
    <?php endif; ?>
</div>

<footer>
    &copy; 2026 myIntern Management System. All rights reserved.
</footer>

</body>
</html>