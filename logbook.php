<?php
session_start();
require 'db_connect.php'; 

// 1. Session & Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// 2. Fetch Student Profile
$stmt = $conn->prepare("SELECT * FROM students WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) { die("Student profile not found."); }
$student_id = $student['student_id'];

// 3. Check for Approved Placement (Restriction Logic)
$status_query = "SELECT application_status FROM internship_applications WHERE student_id = ? AND application_status = 'approved' LIMIT 1";
$status_stmt = $conn->prepare($status_query);
$status_stmt->bind_param("i", $student_id);
$status_stmt->execute();
$is_secured = ($status_stmt->get_result()->num_rows > 0);

// 4. Handle Delete Action
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    $del_stmt = $conn->prepare("DELETE FROM logbook WHERE logbook_id = ? AND student_id = ? AND status = 'pending'");
    $del_stmt->bind_param("ii", $del_id, $student_id);
    if ($del_stmt->execute()) {
        $message = "<div class='status-pill approved' style='display:block; text-align:center; margin-bottom:20px;'>✅ Entry deleted successfully!</div>";
    }
}

// 5. Handle New Log Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_log']) && $is_secured) {
    $log_date = $_POST['log_date'];
    $activities = $_POST['activities'];
    $hours = $_POST['hours'];

    $ins_stmt = $conn->prepare("INSERT INTO logbook (student_id, log_date, activities, hours_spent, status) VALUES (?, ?, ?, ?, 'pending')");
    $ins_stmt->bind_param("issd", $student_id, $log_date, $activities, $hours);
    
    if($ins_stmt->execute()) { 
        $message = "<div class='status-pill approved' style='display:block; text-align:center; margin-bottom:20px;'>✅ Log entry added successfully!</div>"; 
    }
}

// 6. Fetch Log History
$history = $conn->prepare("SELECT * FROM logbook WHERE student_id = ? ORDER BY log_date DESC");
$history->bind_param("i", $student_id);
$history->execute();
$logs = $history->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Logbook | myIntern</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/applicant.css">
    <style>
        /* Specific tweaks for logbook layout */
        .log-form label { display: block; font-weight: 700; margin-bottom: 10px; font-size: 0.85rem; color: #555; text-transform: uppercase; letter-spacing: 0.5px; }
        .log-form input, .log-form textarea { 
            width: 100%; padding: 14px; border: 2px solid #f1f1f1; border-radius: 12px; margin-bottom: 20px; font-family: 'Inter', sans-serif; background: #fafafa; transition: 0.3s;
        }
        .log-form input:focus, .log-form textarea:focus { border-color: var(--primary); background: white; outline: none; box-shadow: 0 0 0 4px #f3f0ff; }
        
        /* Table Styles from your updated CSS */
        .data-table { width: 100%; border-collapse: separate; border-spacing: 0 10px; margin-top: 20px; }
        .data-table th { padding: 15px 20px; background: #f3f0ff; color: var(--primary); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 800; text-align: left; }
        .data-table th:first-child { border-radius: 10px 0 0 10px; }
        .data-table th:last-child { border-radius: 0 10px 10px 0; }
        .data-table td { padding: 20px; background: white; border-bottom: 1px solid #f1f1f1; vertical-align: middle; }
        .data-table tr:hover td { background: #fcfaff; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <a href="applicant.php" class="nav-brand">
            <div>mi</div> myIntern
        </a>
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
    <h1>Weekly Logbook</h1>
    <p>Record your daily internship activities and progress here.</p>
</div>

<div class="main-container" style="display: block; max-width: 1000px;"> 
    <?php if (!$is_secured): ?>
        <div class="card" style="text-align: center; padding: 80px 40px; border: 2px dashed #f3f0ff;">
            <div style="font-size: 60px; margin-bottom: 20px;">🔒</div>
            <h2 style="color: var(--primary); margin-bottom: 10px;">Access Restricted</h2>
            <p style="color: #666;">You must have an <strong>Approved</strong> internship placement to begin recording your logs.</p>
            <a href="applicant.php" class="btn btn-primary" style="width: auto; margin-top: 25px; padding: 12px 40px;">Return Home</a>
        </div>
    <?php else: ?>
        <div class="card">
            <?php echo $message; ?>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h3 style="margin:0; font-weight: 800; color: var(--primary);">📝 New Entry</h3>
                <button onclick="window.print()" class="btn" style="width:auto; padding: 8px 20px; background:#f3f0ff; color:var(--primary); font-size: 0.85rem; font-weight: 700;">
                    Download PDF
                </button>
            </div>
            
            <form method="POST" class="log-form">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label>Select Date</label>
                        <input type="date" name="log_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div>
                        <label>Hours Spent</label>
                        <input type="number" step="0.5" name="hours" placeholder="e.g. 8" required>
                    </div>
                </div>
                <label>Activity Description</label>
                <textarea name="activities" rows="4" placeholder="Briefly describe what you did today..." required></textarea>
                <button type="submit" name="add_log" class="btn btn-primary">Submit Daily Log</button>
            </form>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 20px; font-weight: 800; color: var(--primary);">📋 Submission History</h3>
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Activities</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($logs->num_rows > 0): ?>
                            <?php while($row = $logs->fetch_assoc()): ?>
                            <tr>
                                <td style="white-space: nowrap;"><strong><?php echo date('d M Y', strtotime($row['log_date'])); ?></strong></td>
                                <td style="font-size: 0.9rem; color: #444;"><?php echo nl2br(htmlspecialchars($row['activities'])); ?></td>
                                <td><span class="status-pill <?php echo $row['status']; ?>"><?php echo strtoupper($row['status']); ?></span></td>
                                <td style="white-space: nowrap;">
                                    <?php if($row['status'] == 'pending'): ?>
                                        <a href="edit_log.php?id=<?php echo $row['logbook_id']; ?>" style="color:var(--primary); font-weight:700; text-decoration:none; margin-right:15px; font-size:0.85rem;">Edit</a>
                                        <a href="logbook.php?delete_id=<?php echo $row['logbook_id']; ?>" 
                                           style="color:#ea4335; font-weight:700; text-decoration:none; font-size:0.85rem;" 
                                           onclick="return confirm('Delete this entry?')">Delete</a>
                                    <?php else: ?>
                                        <span style="color:#aaa; font-size: 0.8rem; font-weight: 600;">Locked</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align:center; color:#999; padding: 40px;">No entries found yet. Start by adding one above!</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<footer class="main-footer">
    <div class="footer-bottom">
        <p>© 2026 myIntern Platform. All rights reserved.</p>
    </div>
</footer>

</body>
</html>