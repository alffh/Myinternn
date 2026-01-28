<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT lecturer_name, programme_code, email, profile_pix FROM lecturers WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$lecturer = $result->fetch_assoc();

$profile_img = !empty($lecturer['profile_pix']) ? "uploads/profile/" . $lecturer['profile_pix'] : "img/default_avatar.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | myIntern</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/lecturer_dashboard.css">
    <link rel="stylesheet" href="css/lecturer_profile.css">
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
            <li><a href="lecturer_dashboard.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'lecturer_dashboard.php') ? 'class="active"' : ''; ?>>Home</a></li>
            <li><a href="lecturer_attendance.php">Attendance</a></li>
            <li><a href="lecturer_logbook.php">Logbook</a></li>
            <li><a href="lecturer_profile.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'lecturer_profile.php') ? 'class="active"' : ''; ?>>Profile</a></li>
            <li><a href="logout.php" class="btn-logout">Logout</a></li>
        </ul>
    </div>
</nav>

    <div class="dashboard-header">
        <div class="container">
            <h1>Lecturer Profile</h1>
            <p>Manage your personal information and account settings</p>
        </div>
    </div>

    <div class="container">
        <div class="profile-card">
            <div class="profile-left">
                <div class="avatar-container">
                    <img src="<?php echo $profile_img; ?>" alt="Profile Picture">
                </div>
            </div>
            
            <div class="profile-right">
                <div class="info-group">
                    <label>Full Name</label>
                    <p><?php echo htmlspecialchars($lecturer['lecturer_name']); ?></p>
                </div>

                <div class="info-group">
                    <label>Programme Code</label>
                    <p class="prog-code"><?php echo htmlspecialchars($lecturer['programme_code']); ?></p>
                </div>

                <div class="info-group">
                    <label>Email Address</label>
                    <p><?php echo htmlspecialchars($lecturer['email']); ?></p>
                </div>

                <div class="profile-actions">
                    <a href="edit_profile.php" class="btn-edit-main">Edit Profile Details</a>
                </div>
            </div>
        </div>
    </div>

    <footer style="text-align: center; padding: 40px 0; color: #bbb; font-size: 0.8rem;">
        &copy; 2026 myIntern System. All rights reserved.
    </footer>

</body>
</html>