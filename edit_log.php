<?php
date_default_timezone_set('Asia/Kuala_Lumpur');
include 'db_connect.php'; 
session_start();

// 1. Session & Role Security
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$logbook_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 2. Fetch log data (Must be 'pending' and belong to this student)
$query = $conn->prepare("SELECT * FROM logbook WHERE logbook_id = ? AND student_id = (SELECT student_id FROM students WHERE user_id = ?) AND status = 'pending'");
$query->bind_param("ii", $logbook_id, $user_id);
$query->execute();
$result = $query->get_result();
$log = $result->fetch_assoc();

// If log not found or not pending, redirect back
if (!$log) {
    header("Location: logbook.php");
    exit();
}

// 3. Update logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_log'])) {
    $activities = $_POST['activities'];
    $hours = $_POST['hours'];

    $update = $conn->prepare("UPDATE logbook SET activities = ?, hours_spent = ? WHERE logbook_id = ?");
    $update->bind_param("sdi", $activities, $hours, $logbook_id);

    if ($update->execute()) {
        header("Location: logbook.php?msg=updated");
        exit();
    } else {
        $message = "<div class='status-pill' style='background:#ffebee; color:#e74c3c; display:block; text-align:center; margin-bottom:20px;'>❌ Error: Could not update the entry.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Log Entry | myIntern</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/applicant.css">
    <style>
        /* Specific tweaks to ensure form consistency */
        .log-form label { display: block; font-weight: 600; margin-bottom: 8px; color: #555; font-size: 0.9rem; }
        .log-form input, .log-form textarea { 
            width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd; margin-bottom: 20px; font-family: 'Inter', sans-serif;
        }
        .log-form input:disabled { background: #f0f0f0; color: #888; cursor: not-allowed; border: 1px solid #eee; }
        .btn-flex { display: grid; grid-template-columns: 1fr 2fr; gap: 15px; margin-top: 10px; }
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
    <h1>Edit Log Entry</h1>
    <p>Modifying activities for: <strong><?php echo date('d M Y', strtotime($log['log_date'])); ?></strong></p>
</div>

<div class="main-container" style="display: block; max-width: 800px;">
    <div class="card">
        <?php echo $message; ?>

        <form method="POST" class="log-form">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label>Log Date</label>
                    <input type="text" value="<?php echo date('d M Y', strtotime($log['log_date'])); ?>" disabled>
                </div>
                <div>
                    <label>Hours Spent</label>
                    <input type="number" step="0.5" name="hours" value="<?php echo $log['hours_spent']; ?>" required>
                </div>
            </div>

            <label>Detailed Activity Description</label>
            <textarea name="activities" rows="8" placeholder="Describe your tasks and learning..." required><?php echo htmlspecialchars($log['activities']); ?></textarea>

            <div class="btn-flex">
                <a href="logbook.php" class="btn" style="background: #eee; color: #555; text-decoration: none; line-height: 2.5;">Cancel</a>
                <button type="submit" name="update_log" class="btn btn-primary">Update Entry</button>
            </div>
        </form>
    </div>
</div>

<footer class="main-footer" style="margin-top: 50px;">
    <div class="footer-bottom">
        <p>© 2026 myIntern Platform. All rights reserved.</p>
    </div>
</footer>

</body>
</html>