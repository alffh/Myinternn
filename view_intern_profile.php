<?php
include 'db_connect.php';
session_start();

// Kawalan akses: Hanya untuk syarikat yang sah
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company' || !isset($_SESSION['company_id'])) {
    header("Location: login.php");
    exit();
}

$company_id = $_SESSION['company_id'];

// Ambil senarai intern yang telah diluluskan (approved) untuk syarikat ini
$interns_query = "SELECT s.student_id, s.student_name 
                  FROM students s
                  JOIN internship_applications ap ON s.student_id = ap.student_id
                  JOIN internship_ads ad ON ap.ad_id = ad.ad_id
                  WHERE ad.company_id = '$company_id' AND ap.application_status = 'approved'";
$interns_list = $conn->query($interns_query);

$student_id = isset($_GET['id']) ? $_GET['id'] : '';
$student = null;

if (!empty($student_id)) {
    // Menggunakan prepared statement untuk keselamatan data
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intern Profiles | myIntern</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/company_dashboard.css">
    <link rel="stylesheet" href="css/view_intern_profile.css">
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
    <div class="container">
        <h1>Intern Management</h1>
        <p>View and manage profiles of your active interns.</p>
    </div>
</div>

<div class="container main-layout">
    <aside class="intern-sidebar">
        <div class="section-card">
            <h3>Active Interns</h3>
            <div class="intern-list">
                <?php if ($interns_list->num_rows > 0): ?>
                    <?php while($row = $interns_list->fetch_assoc()): ?>
                        <a href="view_intern_profile.php?id=<?php echo $row['student_id']; ?>" 
                           class="intern-item <?php echo ($student_id == $row['student_id']) ? 'active' : ''; ?>">
                           👤 <?php echo htmlspecialchars($row['student_name']); ?>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="empty-txt">No active interns.</p>
                <?php endif; ?>
            </div>
        </div>
    </aside>

    <main class="profile-display">
        <?php if ($student): ?>
            <div class="section-card profile-card">
                <div class="card-header-flex">
                    <div class="avatar-sq"><?php echo strtoupper(substr($student['student_name'], 0, 1)); ?></div>
                    <div>
                        <h2><?php echo htmlspecialchars($student['student_name']); ?></h2>
                        <span class="id-tag">Student ID: <?php echo $student['student_id']; ?></span>
                    </div>
                </div>
                
                <hr class="divider">

                <div class="info-grid">
                    <div class="info-box">
                        <label>Programme</label>
                        <p><?php echo htmlspecialchars($student['programme'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="info-box">
                        <label>Email Address</label>
                        <p><?php echo htmlspecialchars($student['student_email'] ?? 'No Email'); ?></p>
                    </div>
                    <div class="info-box">
                        <label>Phone Number</label>
                        <p><?php echo htmlspecialchars($student['student_phone'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="info-box">
                        <label>University</label>
                        <p>UiTM Cawangan Machang</p>
                    </div>
                </div>

                <div class="action-footer">
                    <?php if (!empty($student['student_email'])): ?>
                        <a href="mailto:<?php echo $student['student_email']; ?>" class="btn-post">
                            Send Email
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>👈 Please select an intern from the list to view their full profile.</p>
            </div>
        <?php endif; ?>
    </main>
</div>

<footer>
    &copy; 2026 myIntern Management System. All rights reserved.
</footer>

</body>
</html>