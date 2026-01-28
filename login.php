<?php
include 'db_connect.php';
session_start();

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'];
    $password_input = isset($_POST['password']) ? $_POST['password'] : "";

    if ($role == 'student') {
        $student_number = $_POST['student_number'];
        $stmt = $conn->prepare("SELECT users.* FROM users 
                                JOIN students ON users.user_id = students.user_id 
                                WHERE students.student_number = ? AND users.role = 'applicant'");
        $stmt->bind_param("s", $student_number);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password_input, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = 'applicant';
            header("Location: applicant.php");
            exit();
        } else { $error = "❌ Invalid Student Number or Password!"; }
    }

    else if ($role == 'lecturer') {
        $course_code = $_POST['course_code'];
        $stmt = $conn->prepare("SELECT * FROM lecturers WHERE programme_code = ?");
        $stmt->bind_param("s", $course_code);
        $stmt->execute();
        $lecturer = $stmt->get_result()->fetch_assoc();

        // Nota: Sebaiknya gunakan password_verify juga di sini untuk keselamatan
        if ($lecturer && $password_input == $lecturer['password']) {
            $_SESSION['user_id'] = $lecturer['user_id'];
            $_SESSION['role'] = 'lecturer';
            $_SESSION['lecturer_name'] = $lecturer['lecturer_name'];
            $_SESSION['programme_code'] = $lecturer['programme_code'];
            header("Location: lecturer_dashboard.php");
            exit();
        } else { $error = "❌ Invalid password for " . htmlspecialchars($course_code); }
    }

    else if ($role == 'company') {
        $email = $_POST['email_company'];
        $stmt = $conn->prepare("SELECT users.user_id, users.password, companies.company_id 
                                FROM users 
                                LEFT JOIN companies ON users.user_id = companies.user_id 
                                WHERE users.email = ? AND users.role = 'company'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password_input, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = 'company';
            $_SESSION['company_id'] = $user['company_id']; 
            header("Location: company_dashboard.php");
            exit();
        } else { $error = "❌ Invalid Company Email or Password!"; }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | UiTM x myIntern</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

<div class="login-wrapper">
    <div class="video-side">
        <iframe src="https://www.youtube.com/embed/b5DpQPYtxuk?autoplay=1&mute=1&loop=1&playlist=b5DpQPYtxuk&controls=0&showinfo=0&rel=0&modestbranding=1" frameborder="0"></iframe>
        <div class="video-overlay"></div>
        <div class="video-content">
            <h2>MyIntern</h2>
            <p>One goal, one vision. Driving student excellence through quality industrial training.</p>
        </div>
    </div>

    <div class="form-side">
        <div class="brand-header">
            <div class="logo-box">mi</div>
            <h1>UiTM x myIntern</h1>
        </div>

        <?php if($error) echo "<div class='error-msg'>$error</div>"; ?>

        <div class="tabs">
            <button class="tab-btn active" onclick="showForm('student', this)">Student</button>
            <button class="tab-btn" onclick="showForm('lecturer', this)">Lecturer</button>
            <button class="tab-btn" onclick="showForm('company', this)">Company</button>
        </div>

        <form method="POST">
            <input type="hidden" name="role" id="role-input" value="student">

            <div id="student-form" class="form-section active">
                <label>Student Number (Matrix)</label>
                <input type="text" name="student_number" placeholder="e.g. 2023123456">
            </div>

            <div id="lecturer-form" class="form-section">
                <label>Lecturer Identity</label>
                <select name="course_code">
                    <option value="" disabled selected>Identify yourself</option>
                    <option value="CS240">Dr. Siti Khadijah binti Hassan (CS240)</option>
                    <option value="IM262">Ts. Norliana binti Ahmad (IM262)</option>
                    <option value="AC220">Ms. Aisyah Sofea binti Azman (AC220)</option>
                    <option value="BA232">Ms. Farah Nabila binti Rahman (BA232)</option>
                    <option value="CS241">Dr. Amirah Izzati binti Salleh (CS241)</option>
                    <option value="BA242">Dr. Amin bin Aman (BA242)</option>
                    <option value="IC210">Dr. Hasreeq bin Omar (IC210)</option>
                </select>
            </div>

            <div id="company-form" class="form-section">
                <label>Employer Email</label>
                <input type="email" name="email_company" placeholder="hr@company.com">
            </div>

            <label>Password / Access Code</label>
            <input type="password" name="password" placeholder="••••••••" required>

            <button type="submit" class="btn-login">Sign In to Portal</button>
        </form>

        <div class="register-text" id="register-area">
            Don't have an account? <a href="register.php" id="reg-link">Register Here</a>
        </div>
    </div>
</div>

<script>
    function showForm(role, btn) {
        document.getElementById('role-input').value = role;
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('.form-section').forEach(f => f.classList.remove('active'));
        document.getElementById(role + '-form').classList.add('active');

        const regArea = document.getElementById('register-area');
        if (role === 'lecturer') {
            regArea.style.display = 'none';
        } else {
            regArea.style.display = 'block';
        }
    }
</script>

</body>
</html>