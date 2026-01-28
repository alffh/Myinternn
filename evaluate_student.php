<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit();
}

$company_user_id = $_SESSION['user_id'];
$company_query = $conn->query("SELECT company_id, company_name FROM companies WHERE user_id = '$company_user_id'");
$company_data = $company_query->fetch_assoc();
$company_id = $company_data['company_id'];
$company_name = $company_data['company_name'] ?? 'Company';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_evaluation'])) {
    $student_id = $_POST['student_id'];
    $attendance_score = (int)$_POST['attendance_score'];
    $skill_score = (int)$_POST['skill_score'];
    $discipline_score = (int)$_POST['discipline_score'];
    $feedback = $conn->real_escape_string($_POST['final_feedback']);
    
    $total_score_calc = ($attendance_score + $skill_score + $discipline_score) / 3;

    $checkEval = $conn->query("SELECT * FROM evaluations WHERE student_id = '$student_id'");
    
    if($checkEval->num_rows > 0) {
        $sql = "UPDATE evaluations SET 
                attendance_score='$attendance_score', 
                skill_score='$skill_score', 
                discipline_score='$discipline_score', 
                final_score='$total_score_calc', 
                feedback='$feedback',
                submitted_at = NOW()
                WHERE student_id = '$student_id'";
    } else {
        $sql = "INSERT INTO evaluations (student_id, company_id, attendance_score, skill_score, discipline_score, final_score, feedback, submitted_at) 
                VALUES ('$student_id', '$company_id', '$attendance_score', '$skill_score', '$discipline_score', '$total_score_calc', '$feedback', NOW())";
    }

    if ($conn->query($sql)) {
        $message = "✅ Evaluation submitted successfully for the intern!";
    }
}

$students = $conn->query("
    SELECT s.student_id, s.student_name, s.programme 
    FROM students s
    JOIN internship_applications ap ON s.student_id = ap.student_id
    JOIN internship_ads ad ON ap.ad_id = ad.ad_id
    WHERE ad.company_id = '$company_id' AND ap.application_status = 'approved'
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Evaluation | myIntern</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/evaluate_student.css">
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
    <h1>Intern Performance Evaluation</h1>
    <p>Submit final assessments for interns at <strong><?php echo htmlspecialchars($company_name); ?></strong></p>
</div>



<div class="container">
    <?php if($message) echo "<div class='alert'>$message</div>"; ?>

    <div class="card">
        <form method="POST">
            <div class="form-group">
                <label>Select Intern</label>
                <select name="student_id" required>
                    <option value="">-- Choose a Student --</option>
                    <?php while($row = $students->fetch_assoc()): ?>
                        <option value="<?php echo $row['student_id']; ?>">
                            <?php echo htmlspecialchars($row['student_name']); ?> (<?php echo htmlspecialchars($row['programme']); ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
                <p class="helper-text">Only approved interns are listed here.</p>
            </div>

            <div class="score-grid">
                <div class="form-group">
                    <label>Attendance (0-100)</label>
                    <input type="number" name="attendance_score" min="0" max="100" placeholder="e.g. 95" required>
                </div>
                <div class="form-group">
                    <label>Work Skills (0-100)</label>
                    <input type="number" name="skill_score" min="0" max="100" placeholder="e.g. 88" required>
                </div>
                <div class="form-group">
                    <label>Discipline (0-100)</label>
                    <input type="number" name="discipline_score" min="0" max="100" placeholder="e.g. 100" required>
                </div>
            </div>

            <div class="form-group">
                <label>Supervisor's Overall Feedback</label>
                <textarea name="final_feedback" rows="5" placeholder="Provide constructive feedback on technical growth and workplace conduct..." required></textarea>
            </div>

            <button type="submit" name="submit_evaluation" class="btn-submit">Finalize & Submit Evaluation</button>
        </form>
    </div>
</div>

<footer>
    &copy; 2026 myIntern Management System.
</footer>

</body>
</html>