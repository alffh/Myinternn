<?php
include 'db_connect.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role']!=='company') { header("Location: login.php"); exit(); }
$company_user_id = $_SESSION['user_id'];
$company = $conn->query("SELECT company_id, company_name FROM companies WHERE user_id='$company_user_id'")->fetch_assoc();
$company_id = $company['company_id'] ?? 0;
$message = "";

if ($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['create_ad'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $requirements = $conn->real_escape_string($_POST['requirements']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $ad_status = 'active';
    $posted_at = date('Y-m-d H:i:s');

    $insert = $conn->query("INSERT INTO internship_ads (company_id,title,description,requirements,start_date,end_date,posted_at,ad_status)
                            VALUES ('$company_id','$title','$description','$requirements','$start_date','$end_date','$posted_at','$ad_status')");
    if ($insert) $message="✅ Internship ad created successfully!";
}

$ads = $conn->query("SELECT * FROM internship_ads WHERE company_id='$company_id' ORDER BY posted_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Internship Ads | <?php echo htmlspecialchars($company['company_name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/company_ads.css">
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
        <h1>Manage Internship Advertisements</h1>
        <p>Post new opportunities for talented students</p>
    </div>
</div>

<div class="container">
    <?php if($message) echo "<div style='background:#e8f0fe; padding:15px; border-radius:8px; margin-bottom:20px; border-left:5px solid #4285F4; color:#3367d6; font-weight:600;'>$message</div>"; ?>

    <div class="section-card">
        <h3>Create New Internship Ad</h3>
        <form method="POST">
            <label>Position Title</label>
            <input type="text" name="title" placeholder="e.g. Software Engineer Intern" required>
            
            <label>Job Description</label>
            <textarea name="description" placeholder="Describe the roles and responsibilities..." rows="4" required></textarea>
            
            <label>Requirements</label>
            <textarea name="requirements" placeholder="Skills, CGPA, or course requirements..." rows="3" required></textarea>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label>Internship Start Date</label>
                    <input type="date" name="start_date" required>
                </div>
                <div>
                    <label>Internship End Date</label>
                    <input type="date" name="end_date" required>
                </div>
            </div>
            
            <button type="submit" name="create_ad">Broadcast Internship Ad</button>
        </form>
    </div>

    <div class="section-card">
        <h3>Active Advertisements</h3>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Posted Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($ads->num_rows > 0): ?>
                        <?php while($ad=$ads->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($ad['title']); ?></strong></td>
                            <td><?php echo date('d M', strtotime($ad['start_date'])); ?> - <?php echo date('d M Y', strtotime($ad['end_date'])); ?></td>
                            <td class="status-active"><?php echo ucfirst($ad['ad_status']); ?></td>
                            <td><?php echo date('d M Y', strtotime($ad['posted_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center; padding:30px; color:#999;">No advertisements posted yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>