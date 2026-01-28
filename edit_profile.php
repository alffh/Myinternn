<?php
include 'db_connect.php'; 
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// 1. Fetch current lecturer data
$stmt = $conn->prepare("SELECT * FROM lecturers WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$lecturer = $stmt->get_result()->fetch_assoc();

$profile_img = !empty($lecturer['profile_pix']) ? "uploads/profile/" . $lecturer['profile_pix'] : "img/default_avatar.png";

// 2. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['lecturer_name'];
    $email = $_POST['email'];
    $image_name = $lecturer['profile_pix']; 

    if (!empty($_FILES['profile_pix']['name'])) {
        $target_dir = "uploads/profile/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['profile_pix']['name'], PATHINFO_EXTENSION);
        $image_name = "lecturer_" . $user_id . "_" . time() . "." . $file_ext;
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES['profile_pix']['tmp_name'], $target_file)) {
            // File moved successfully
        }
    }

    $update = $conn->prepare("UPDATE lecturers SET lecturer_name=?, email=?, profile_pix=? WHERE user_id=?");
    $update->bind_param("sssi", $name, $email, $image_name, $user_id);
    
    if ($update->execute()) {
        header("Location: lecturer_profile.php?success=1");
        exit();
    } else {
        $message = "❌ Database update failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | myIntern</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/lecturer_dashboard.css">
    <link rel="stylesheet" href="css/edit_profile.css">
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
                <li><a href="lecturer_dashboard.php">Home</a></li>
                <li><a href="lecturer_attendance.php">Attendance</a></li>
                <li><a href="lecturer_logbook.php">Logbook</a></li>
                <li><a href="lecturer_profile.php" class="active">Profile</a></li>
                <li><a href="logout.php" class="btn-logout">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="main-content">
        <div class="dashboard-header">
            <div class="container">
                <h1>Edit Profile</h1>
                <p>Update your personal information and account settings</p>
            </div>
        </div>

        <div class="container">
            <div class="modern-edit-card">
                <form method="POST" enctype="multipart/form-data">
                    
                    <div class="avatar-edit-section">
                        <div class="avatar-frame">
                            <img src="<?php echo $profile_img; ?>" alt="Profile" id="upload-preview">
                        </div>
                        <label for="profile_pix" class="btn-photo-select">SELECT PHOTO</label>
                        <input type="file" name="profile_pix" id="profile_pix" accept="image/*" style="display:none;">
                        <p class="upload-hint">Auto-updates preview on selection</p>
                    </div>

                    <div class="form-fields">
                        <?php if($message) echo "<p style='color:red; text-align:center; margin-bottom:15px;'>$message</p>"; ?>

                        <div class="input-wrapper">
                            <label>FULL NAME</label>
                            <input type="text" name="lecturer_name" value="<?php echo htmlspecialchars($lecturer['lecturer_name']); ?>" required>
                        </div>

                        <div class="input-wrapper field-locked">
                            <label>PROGRAMME CODE</label>
                            <div class="input-relative">
                                <input type="text" value="<?php echo htmlspecialchars($lecturer['programme_code']); ?>" readonly>
                                <span class="lock-badge">🔒 LOCKED</span>
                            </div>
                        </div>

                        <div class="input-wrapper">
                            <label>EMAIL ADDRESS</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($lecturer['email']); ?>" required>
                        </div>

                        <div class="form-buttons">
                            <button type="submit" class="btn-submit-save">Save Profile Changes</button>
                            <a href="lecturer_profile.php" class="cancel-text">Cancel and Go Back</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <footer style="text-align: center; padding: 40px 0; color: #bbb; font-size: 0.8rem;">
            &copy; 2026 myIntern System. All rights reserved.
        </footer>
    </div>

    <script>
        // Photo Preview Script
        document.getElementById('profile_pix').onchange = function (evt) {
            const [file] = this.files;
            if (file) {
                document.getElementById('upload-preview').src = URL.createObjectURL(file);
            }
        };
    </script>
</body>
</html>