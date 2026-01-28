<?php
session_start();
require 'db_connect.php'; 

// 1. Check Session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$ad_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($ad_id === 0) {
    die("Invalid Advertisement ID.");
}

// 2. Get Student ID
$stmt = $conn->prepare("SELECT student_id FROM students WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student_id = $stmt->get_result()->fetch_assoc()['student_id'];

// 3. Get Advertisement & Company Details
$query = "SELECT a.*, c.company_name, c.company_address, c.industry_type 
          FROM internship_ads a 
          JOIN companies c ON a.company_id = c.company_id 
          WHERE a.ad_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $ad_id);
$stmt->execute();
$ad = $stmt->get_result()->fetch_assoc();

if (!$ad) {
    die("Internship advertisement not found.");
}

// 4. LOGIC CHECK 1: Already secured a placement elsewhere?
$secured_query = "SELECT application_id FROM internship_applications 
                  WHERE student_id = ? AND application_status = 'approved'";
$stmt = $conn->prepare($secured_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$is_secured = $stmt->get_result()->num_rows > 0;

// 5. LOGIC CHECK 2: Already applied for THIS specific ad?
$applied_query = "SELECT application_id FROM internship_applications 
                  WHERE student_id = ? AND ad_id = ?";
$stmt = $conn->prepare($applied_query);
$stmt->bind_param("ii", $student_id, $ad_id);
$stmt->execute();
$already_applied = $stmt->get_result()->num_rows > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Internship | myIntern</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/internship_details.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <a href="applicant.php" class="nav-brand">
            <div>mi</div> <span>myIntern</span>
        </a>
        <ul class="nav-menu">
            <li><a href="applicant.php">Home</a></li>
            <li class="dropdown">
                <a href="#">Internship Tools ▼</a>
                <div class="dropdown-content">
                    <a href="attendance.php">🕒 Clock In/Out</a>
                    <a href="logbook.php">📖 Weekly Logbook</a>
                </div>
            </li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="logout.php" style="color:#ea4335; font-weight:600;">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="header-section">
    <h1>Internship Details</h1>
    <p>Read the requirements carefully before applying</p>
</div>

<div class="container">
    <a href="applicant.php" class="back-link">← Back to Dashboard</a>
    
    <div class="card">
        <div class="company-meta">🏢 <?php echo htmlspecialchars($ad['company_name']); ?></div>
        <h2 style="font-size: 1.8rem; margin-bottom: 5px;"><?php echo htmlspecialchars($ad['title']); ?></h2>
        <p style="color: #666; margin-bottom: 30px;">📍 <?php echo htmlspecialchars($ad['industry_type']); ?> | <?php echo htmlspecialchars($ad['company_address']); ?></p>

        <div class="section">
            <div class="section-title">Job Description</div>
            <div class="section-content">
                <?php echo nl2br(htmlspecialchars($ad['description'])); ?>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Requirements</div>
            <div class="section-content">
                <?php echo nl2br(htmlspecialchars($ad['requirements'] ?? 'No specific requirements listed.')); ?>
            </div>
        </div>

        <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">

        <?php if ($is_secured): ?>
            <div class="alert alert-error">You have already secured a placement. You cannot apply for other internships.</div>
            <button class="btn-disabled">Application Restricted</button>
        <?php elseif ($already_applied): ?>
            <div class="alert alert-info">You have already submitted an application for this position.</div>
            <button class="btn-disabled">Already Applied</button>
        <?php else: ?>
            <a href="apply_form.php?id=<?php echo $ad_id; ?>" class="btn-apply">Apply for this Internship</a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>