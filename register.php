<?php
include 'db_connect.php';
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role']; 
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone    = mysqli_real_escape_string($conn, $_POST['phone']);

    $conn->begin_transaction();

    try {
        $sql_user = "INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, ?, 'active')";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("ssss", $username, $email, $password, $role);
        $stmt_user->execute();
        $new_user_id = $conn->insert_id;

        if ($role == 'applicant') {
            $student_number = mysqli_real_escape_string($conn, $_POST['student_number']);
            $programme  = mysqli_real_escape_string($conn, $_POST['programme']);
            
            $sql_prof = "INSERT INTO students (user_id, student_name, student_number, programme, phone) VALUES (?, ?, ?, ?, ?)";
            $stmt_prof = $conn->prepare($sql_prof);
            $stmt_prof->bind_param("issss", $new_user_id, $fullname, $student_number, $programme, $phone);

        } elseif ($role == 'company') {
            $address  = mysqli_real_escape_string($conn, $_POST['address']);
            $industry = mysqli_real_escape_string($conn, $_POST['industry']);
            
            $sql_prof = "INSERT INTO companies (user_id, company_name, company_address, industry_type, company_email, company_phone) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_prof = $conn->prepare($sql_prof);
            $stmt_prof->bind_param("isssss", $new_user_id, $fullname, $address, $industry, $email, $phone);
        }

        $stmt_prof->execute();
        $conn->commit();
        $message = "<div class='success-msg'>Registration Successful! <a href='login.php' style='font-weight:bold; color:inherit;'>Login here</a></div>";

    } catch (Exception $e) {
        $conn->rollback();
        $message = "<div class='error-msg'>Registration Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | UiTM x myIntern</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/register.css">
</head>
<body>

<div class="reg-wrapper">
    <div class="video-side">
        <iframe src="https://www.youtube.com/embed/YLoXyfaXxKE?autoplay=1&mute=1&loop=1&playlist=YLoXyfaXxKE&controls=0&showinfo=0&rel=0&modestbranding=1" frameborder="0"></iframe>
        <div class="video-overlay"></div>
        <div class="video-content">
            <h2>UiTM di Hatiku</h2>
            <p>Sertai rangkaian praktikal terbesar untuk pelajar UiTM dan rakan industri.</p>
        </div>
    </div>

    <div class="form-side">
        <div class="brand-header">
            <div class="logo-box">mi</div>
            <h1>UiTM x myIntern</h1>
        </div>

        <h2>Create Account</h2>
        <?php echo $message; ?>

        <form method="POST" id="regForm">
            <label>Select Account Type</label>
            <select name="role" id="roleSelect" required onchange="toggleFields()">
                <option value="" disabled selected>Who are you?</option>
                <option value="applicant">Student</option>
                <option value="company">Company / Employer</option>
            </select>

            <label>Username</label>
            <input type="text" name="username" required placeholder="Choose a username">

            <label>Login Email</label>
            <input type="email" name="email" required placeholder="example@email.com">

            <label>Password</label>
            <input type="password" name="password" required placeholder="••••••••">

            <label>Phone Number</label>
            <input type="text" name="phone" required placeholder="012-3456789">

            <div id="studentFields" class="dynamic-fields">
                <label>Full Name (as per IC)</label>
                <input type="text" name="fullname_student" placeholder="Ahmad Bin Zaki">

                <label>Student Number (Matrix)</label>
                <input type="text" name="student_number" placeholder="2023123456">

                <label>Programme Code</label>
                <select name="programme">
                    <option value="" disabled selected>Select your programme</option>
                    <option value="CS240">CS240 - Information Technology</option>
                    <option value="IM262">IM262 - Information Management</option>
                    <option value="AC220">AC220 - Accountancy</option>
                    <option value="BA232">BA232 - Business Admin</option>
                    <option value="CS241">CS241 - Statistics</option>
                    <option value="BA242">BA242 - Finance</option>
                    <option value="IC210">IC210 - Islamic Banking</option>
                </select>
            </div>

            <div id="companyFields" class="dynamic-fields">
                <label>Company Name</label>
                <input type="text" name="fullname_company" placeholder="Intel Technology Sdn Bhd">

                <label>Industry Type</label>
                <input type="text" name="industry" placeholder="e.g. IT, Manufacturing, Finance">

                <label>Company Address</label>
                <textarea name="address" placeholder="Full office address"></textarea>
            </div>

            <input type="hidden" name="fullname" id="finalFullname">
            <button type="submit" class="btn-reg">Register Account</button>
        </form>

        <div style="text-align:center; margin-top:20px; font-size:0.9rem;">
            Already have an account? <a href="login.php" style="color:var(--uitm-purple); font-weight:700; text-decoration:none;">Login here</a>
        </div>
    </div>
</div>

<script>
    function toggleFields() {
        const role = document.getElementById('roleSelect').value;
        const studentFields = document.getElementById('studentFields');
        const companyFields = document.getElementById('companyFields');

        studentFields.classList.remove('show');
        companyFields.classList.remove('show');

        // Reset required fields
        document.getElementsByName('fullname_student')[0].required = false;
        document.getElementsByName('student_number')[0].required = false;
        document.getElementsByName('fullname_company')[0].required = false;

        if (role === 'applicant') {
            studentFields.classList.add('show');
            document.getElementsByName('fullname_student')[0].required = true;
            document.getElementsByName('student_number')[0].required = true;
        } else if (role === 'company') {
            companyFields.classList.add('show');
            document.getElementsByName('fullname_company')[0].required = true;
        }
    }

    document.getElementById('regForm').onsubmit = function() {
        const role = document.getElementById('roleSelect').value;
        const sName = document.getElementsByName('fullname_student')[0].value;
        const cName = document.getElementsByName('fullname_company')[0].value;
        
        document.getElementById('finalFullname').value = (role === 'applicant') ? sName : cName;
    };
</script>

</body>
</html>