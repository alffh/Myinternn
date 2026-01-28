<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include 'db_connect.php'; 

echo "<h2>Status Diagnostik Login</h2>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['loginUsername']);
    $password = $_POST['loginPassword'];

    echo "1. Mencari user: <b>$username</b>...<br>";

    // Guna 'user_id' mengikut screenshot database anda
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "2. User dijumpai! Role: <b>" . $user['role'] . "</b><br>";

        // Cuba kedua-dua cara: Hash dan Plain Text
        $is_valid = false;
        if (password_verify($password, $user['password'])) {
            $is_valid = true;
            echo "3. Password sah (Hash Match).<br>";
        } elseif ($password === $user['password']) {
            $is_valid = true;
            echo "3. Password sah (Plain Text Match).<br>";
        }

        if ($is_valid) {
            // Set session guna 'user_id' ikut screenshot DB anda
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username'];

            echo "<br><b style='color:green;'>LOGIN BERJAYA!</b><br>";
            echo "Klik pautan di bawah untuk masuk:<br>";
            
            // Link manual supaya kita tahu fail itu wujud atau tidak
            if ($user['role'] == 'applicant') echo "<a href='student_dashboard.php'>Masuk Student Dashboard</a>";
            elseif ($user['role'] == 'company') echo "<a href='company_dashboard.php'>Masuk Company Dashboard</a>";
            elseif ($user['role'] == 'lecturer') echo "<a href='lecturer_dashboard.php'>Masuk Lecturer Dashboard</a>";
            
        } else {
            echo "<br><b style='color:red;'>RALAT: Password tidak sepadan!</b>";
        }
    } else {
        echo "<br><b style='color:red;'>RALAT: Username '$username' tidak wujud dalam table users.</b>";
    }
} else {
    echo "Sila akses melalui borang login.";
}
?>