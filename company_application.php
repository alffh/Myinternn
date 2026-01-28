<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit();
}

$company_user_id = $_SESSION['user_id'];
$company = $conn->query("SELECT company_id, company_name FROM companies WHERE user_id = '$company_user_id'")->fetch_assoc();
$company_id = $company['company_id'] ?? 0;
$message = "";

// Handle approve/reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'], $_POST['action'])) {
    $application_id = intval($_POST['application_id']);
    $action = $_POST['action'];

    if (in_array($action, ['approved', 'rejected'])) {
        $update = $conn->query("UPDATE internship_applications 
                                SET application_status='$action' 
                                WHERE application_id='$application_id' 
                                  AND ad_id IN (SELECT ad_id FROM internship_ads WHERE company_id='$company_id')");
        if ($update) {
            $message = "✅ Application has been marked as " . ucfirst($action) . ".";
        }
    }
}

// Fetch applications
$applications = $conn->query("
    SELECT ia.*, s.student_name, s.student_number, ia.apply_date, ia.application_status, a.title
    FROM internship_applications ia
    JOIN students s ON ia.student_id = s.student_id
    JOIN internship_ads a ON ia.ad_id = a.ad_id
    WHERE a.company_id='$company_id'
    ORDER BY ia.apply_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Management | <?php echo htmlspecialchars($company['company_name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/company_application.css">
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

    <div class="dashboard-header">
        <div class="container">
            <h1>Applicant Management</h1>
            <p>Review and process student internship requests</p>
        </div>
    </div>

    <div class="container">
        <?php if (!empty($message)) echo "<div class='alert'>$message</div>"; ?>

        <div class="section-card">
            <?php if($applications->num_rows > 0): ?>
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student Details</th>
                            <th>Position Applied</th>
                            <th>Applied Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $applications->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($row['student_name']); ?></strong><br>
                                <small style="color:#666;"><?php echo htmlspecialchars($row['student_number']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo date('d M Y', strtotime($row['apply_date'])); ?></td>
                            <td>
                                <?php
                                    $status = $row['application_status'];
                                    $badge = "badge-pending";
                                    if($status=='approved') $badge='badge-approved';
                                    elseif($status=='rejected') $badge='badge-rejected';
                                ?>
                                <span class="status-badge <?php echo $badge; ?>"><?php echo $status; ?></span>
                            </td>
                            <td>
                                <?php if($status=='pending'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="application_id" value="<?php echo $row['application_id']; ?>">
                                    <button type="submit" name="action" value="approved" class="btn-approve">Approve</button>
                                    <button type="submit" name="action" value="rejected" class="btn-reject">Reject</button>
                                </form>
                                <?php else: ?>
                                    <span style="color:#999; font-size: 0.8rem; font-weight:600;">Decision Made</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div style="text-align:center; padding: 40px; color:#999;">
                    <p>No applications found for your advertisements.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>