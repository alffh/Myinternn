<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header("Location: login.php");
    exit();
}

$ad_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

if ($ad_id === 0) {
    die("Invalid Advertisement ID.");
}

$stmt = $conn->prepare("SELECT * FROM students WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

$ad_stmt = $conn->prepare("SELECT title FROM internship_ads WHERE ad_id = ?");
$ad_stmt->bind_param("i", $ad_id);
$ad_stmt->execute();
$ad_info = $ad_stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply Internship | myIntern</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/apply_form.css">
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
    <h1>Application Form</h1>
    <p>Submit your application to start your journey</p>
</div>

<div class="container">
    <div class="form-card">
        <div class="apply-target">
            <p>Applying for position:</p>
            <h3><?php echo htmlspecialchars($ad_info['title']); ?></h3>
        </div>

        <form action="submit_application.php" method="POST">
            <input type="hidden" name="ad_id" value="<?php echo $ad_id; ?>">

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>" placeholder="e.g. 0123456789" required>
            </div>

            <div class="info-grid">
                <div class="form-group">
                    <label>University</label>
                    <input type="text" name="university" value="<?php echo htmlspecialchars($student['university']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Current CGPA</label>
                    <input type="number" step="0.01" name="cgpa" value="<?php echo htmlspecialchars($student['cgpa']); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Programme / Course</label>
                <input type="text" name="programme" value="<?php echo htmlspecialchars($student['programme']); ?>" required>
            </div>

            <div class="form-group">
                <label>Cover Letter / Additional Info</label>
                <textarea name="additional_info" rows="6" placeholder="Share your skills, experiences, and why you are interested in this internship..."></textarea>
            </div>

            <button type="submit" class="btn-submit">Confirm & Submit Application</button>
            <a href="internship_details.php?id=<?php echo $ad_id; ?>" class="btn-cancel">Cancel and Go Back</a>
        </form>
    </div>
</div>

<footer style="text-align: center; padding: 30px 0; color: #bbb; font-size: 0.85rem;">
    &copy; 2026 myIntern System. All rights reserved.
</footer>

</body>
</html>
