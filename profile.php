<?php
date_default_timezone_set('Asia/Kuala_Lumpur');
include 'db_connect.php'; 
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

$studentQuery = $conn->prepare("SELECT * FROM students WHERE user_id = ?");
$studentQuery->bind_param("i", $user_id);
$studentQuery->execute();
$student = $studentQuery->get_result()->fetch_assoc();

if (!$student) {
    die("Error: Profile not found.");
}

$student_id = $student['student_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $student_name = $_POST['student_name'];
    $phone = $_POST['phone'];
    $programme = $_POST['programme'];
    $cgpa = $_POST['cgpa'];
    $university = "UNIVERSITI TEKNOLOGI MARA CAWANGAN MACHANG"; 

    $profile_pic = $student['profile_pic']; 
    if (!empty($_FILES['profile_img']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        
        $file_ext = strtolower(pathinfo($_FILES["profile_img"]["name"], PATHINFO_EXTENSION));
        $file_name = "user_" . $user_id . "_" . time() . "." . $file_ext;
        $target_file = $target_dir . $file_name;

        $allowed = array("jpg", "jpeg", "png", "webp");
        if (in_array($file_ext, $allowed)) {
            if (move_uploaded_file($_FILES["profile_img"]["tmp_name"], $target_file)) {
                $profile_pic = $file_name;
            }
        }
    }

    $update_sql = $conn->prepare("UPDATE students SET student_name = ?, phone = ?, university = ?, programme = ?, cgpa = ?, profile_pic = ? WHERE student_id = ?");
    $update_sql->bind_param("ssssdsi", $student_name, $phone, $university, $programme, $cgpa, $profile_pic, $student_id);

    if ($update_sql->execute()) {
        $message = "✅ Profile updated successfully!";
        $studentQuery->execute();
        $student = $studentQuery->get_result()->fetch_assoc();
    }
}

$display_img = !empty($student['profile_pic']) ? 'uploads/' . $student['profile_pic'] : 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | myIntern</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/applicant.css">
    <style>
        /* Profile-specific styles that aren't in applicant.css */
        .profile-pic-container { text-align: center; margin-bottom: 30px; }
        .profile-pic-preview { 
            width: 140px; height: 140px; border-radius: 50%; object-fit: cover; 
            border: 5px solid #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 15px; 
        }
        .upload-btn-wrapper { position: relative; overflow: hidden; display: inline-block; }
        .btn-upload { 
            border: 2px solid var(--primary); color: var(--primary); background: white; 
            padding: 8px 24px; border-radius: 30px; font-weight: 700; cursor: pointer; font-size: 0.85rem; transition: 0.3s;
        }
        .btn-upload:hover { background: var(--primary); color: white; }
        .upload-btn-wrapper input[type=file] { position: absolute; left: 0; top: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
        
        .profile-form label { display: block; font-weight: 600; margin: 20px 0 8px; font-size: 0.85rem; color: #555; }
        .profile-form input { 
            width: 100%; padding: 14px; border: 1.5px solid #eef2f6; border-radius: 12px; 
            background: #f8fafc; font-size: 1rem; transition: 0.3s; font-family: 'Inter', sans-serif;
        }
        .profile-form input:focus { border-color: var(--primary); background: white; outline: none; }
        .profile-form input:disabled { background: #f0f0f0; cursor: not-allowed; color: #888; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <a href="applicant.php" class="nav-brand"><div>mi</div> myIntern</a>
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
            <li><a href="profile.php" style="color: var(--primary); font-weight: bold;">Profile</a></li>
            <li><a href="logout.php" style="color:#ea4335;">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="welcome-hero">
    <h1>Profile Settings</h1>
    <p>Keep your information up to date for potential employers.</p>
</div>

<div class="main-container" style="display: block; max-width: 800px;">
    <?php if($message): ?>
        <div style="background:white; padding:15px; border-radius:12px; margin-bottom:20px; border-left:5px solid var(--primary); text-align:center; font-weight:600; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <form method="POST" enctype="multipart/form-data" class="profile-form">
            <div class="profile-pic-container">
                <img src="<?php echo $display_img; ?>" id="preview" class="profile-pic-preview">
                <br>
                <div class="upload-btn-wrapper">
                    <button type="button" class="btn-upload">Change Photo</button>
                    <input type="file" name="profile_img" accept="image/*" onchange="previewImage(event)">
                </div>
            </div>

            <label>Full Name</label>
            <input type="text" name="student_name" value="<?php echo htmlspecialchars($student['student_name']); ?>" required>

            <label>Student Number (Locked)</label>
            <input type="text" value="<?php echo htmlspecialchars($student['student_number']); ?>" disabled>

            <label>Phone Number</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>">

            <label>University</label>
            <input type="text" value="UNIVERSITI TEKNOLOGI MARA CAWANGAN MACHANG" disabled style="font-weight:bold;">

            <label>Programme Code</label>
            <input type="text" name="programme" value="<?php echo htmlspecialchars($student['programme']); ?>">

            <label>Latest CGPA</label>
            <input type="number" step="0.01" name="cgpa" value="<?php echo htmlspecialchars($student['cgpa']); ?>">

            <button type="submit" name="update_profile" class="btn btn-primary" style="margin-top: 30px;">Save Profile Changes</button>
        </form>
    </div>
</div>

<footer class="main-footer" style="margin-top: 50px;">
    <div class="footer-bottom">
        <p>© 2026 myIntern Platform. All rights reserved.</p>
    </div>
</footer>

<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('preview');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

</body>
</html>