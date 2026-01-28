<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company' || !isset($_SESSION['company_id'])) {
    header("Location: login.php");
    exit();
}

$company_id = $_SESSION['company_id'];

$stmt = $conn->prepare("SELECT company_name FROM companies WHERE company_id = ?");
$stmt->bind_param("i", $company_id);
$stmt->execute();
$company = $stmt->get_result()->fetch_assoc();
$company_name = $company['company_name'] ?? 'Company';

$active_ads = $conn->query("SELECT COUNT(*) as total FROM internship_ads WHERE company_id = '$company_id' AND ad_status = 'active'")->fetch_assoc()['total'];
$total_apps = $conn->query("SELECT COUNT(*) as total FROM internship_applications ap JOIN internship_ads ad ON ap.ad_id = ad.ad_id WHERE ad.company_id = '$company_id'")->fetch_assoc()['total'];
$approved = $conn->query("SELECT COUNT(*) as total FROM internship_applications ap JOIN internship_ads ad ON ap.ad_id = ad.ad_id WHERE ad.company_id = '$company_id' AND ap.application_status = 'approved'")->fetch_assoc()['total'];

$ads_stmt = $conn->prepare("SELECT * FROM internship_ads WHERE company_id = ? ORDER BY posted_at DESC");
$ads_stmt->bind_param("i", $company_id);
$ads_stmt->execute();
$ads_list = $ads_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Dashboard | myIntern</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="css/company_dashboard.css?v=1.1">
</head>
<body>

   <nav class="navbar">
     <div class="nav-container">
        <a href="company_dashboard.php" class="nav-brand"><div>mi</div>myIntern</a>
        <ul class="nav-menu">
            <li><a href="company_dashboard.php">Dashboard</a></li>
            <li><a href="view_intern_profile.php">Applicants</a></li>
            <li class="dropdown">
                <a href="#" class="active">Manage Interns ▼</a>
                <div class="dropdown-content">
                    <a href="monitor_attendance.php">🕒 Attendance Monitoring</a>
                    <a href="review_logbook.php">📖 Logbook Review</a>
                    <a href="evaluate_student.php">⭐ Student Evaluation</a>
                </div>
            <li><a href="logout.php" class="btn-logout">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="header-section">
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($company_name); ?>!</h1>
        <p>Monitor your postings and manage your active interns here.</p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <h3><?php echo $active_ads; ?></h3>
        <p>Active Postings</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $total_apps; ?></h3>
        <p>New Applicants</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $approved; ?></h3>
        <p>Active Interns</p>
    </div>
</div>

<div class="main-container">
    <div class="section-card">
        <div class="section-header">
            <h2>📢 Your Internship Postings</h2>
            <a href="company_ads.php" class="btn-post">
                <strong>+</strong> Post New Ad
            </a>
        </div>

        <?php if ($ads_list->num_rows > 0): ?>
     <?php while($ad = $ads_list->fetch_assoc()): ?>
    <div class="ad-row">
        <div class="ad-info">
            <h4><?php echo htmlspecialchars($ad['title']); ?></h4>
            <span>📅 Posted on <?php echo date('d M Y', strtotime($ad['posted_at'])); ?></span>
        </div>
        <a href="view_applicants.php?ad_id=<?php echo $ad['ad_id']; ?>" class="btn-manage">View Applications</a>
    </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <p>You haven't posted any internship opportunities yet.</p>
                <a href="company_ads.php">Create your first post now &rarr;</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer>
    &copy; 2026 myIntern Management System. All rights reserved.
</footer>

</body>
</html>