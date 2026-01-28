<?php
include 'db_connect.php'; 
session_start();

if (!isset($_SESSION['company_id']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $company_id = $_SESSION['company_id'];
    $title = $_POST['title'];
    $loc_input = $_POST['location'];
    $desc_input = $_POST['description'];
    
    $final_description = "Location: " . $loc_input . "\n\nDetails: " . $desc_input;

    $stmt = $conn->prepare("INSERT INTO internship_ads (company_id, title, description, posted_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $company_id, $title, $final_description);

    if ($stmt->execute()) {
        echo "<script>alert('Advertisement Posted Successfully!'); window.location.href='company_dashboard.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post New Internship | myIntern</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/post_ad.css">
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

<div class="form-container">
    <h2>📢 Post New Internship</h2>
    <form action="post_ad.php" method="POST">
        <div class="form-group">
            <label>Position Title</label>
            <input type="text" name="title" placeholder="e.g. Software Engineer Intern" required>
        </div>
        <div class="form-group">
            <label>Work Location</label>
            <input type="text" name="location" placeholder="e.g. Shah Alam, Selangor" required>
        </div>
        <div class="form-group">
            <label>Job Description</label>
            <textarea name="description" placeholder="Describe the role, requirements and benefits..." required></textarea>
        </div>
        <button type="submit" class="btn-submit">Publish Advertisement</button>
    </form>
    <a href="company_dashboard.php" class="back-link">&larr; Cancel and go back</a>
</div>

</body>
</html>