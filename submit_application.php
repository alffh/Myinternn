<?php
session_start();
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $ad_id = intval($_POST['ad_id']);
    
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $university = mysqli_real_escape_string($conn, $_POST['university']);
    $programme = mysqli_real_escape_string($conn, $_POST['programme']);
    
    $cgpa = floatval($_POST['cgpa']); 

    $stu_query = $conn->prepare("SELECT student_id FROM students WHERE user_id = ?");
    $stu_query->bind_param("i", $user_id);
    $stu_query->execute();
    $student_data = $stu_query->get_result()->fetch_assoc();
    
    if (!$student_data) {
        die("Student profile not found.");
    }
    $student_id = $student_data['student_id'];

    $update_stu = $conn->prepare("UPDATE students SET phone = ?, university = ?, cgpa = ?, programme = ? WHERE student_id = ?");
    $update_stu->bind_param("ssdsi", $phone, $university, $cgpa, $programme, $student_id);
    
    if (!$update_stu->execute()) {
        die("Update profile failed: " . $conn->error);
    }


    $check_stmt = $conn->prepare("SELECT application_id FROM internship_applications WHERE student_id = ? AND ad_id = ?");
    $check_stmt->bind_param("ii", $student_id, $ad_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        echo "<script>alert('You have already applied for this position.'); window.location.href='applicant.php';</script>";
    } else {
       
        $insert = $conn->prepare("INSERT INTO internship_applications (student_id, ad_id, application_status, applied_at) VALUES (?, ?, 'pending', NOW())");
        $insert->bind_param("ii", $student_id, $ad_id);

        if ($insert->execute()) {
            echo "<script>alert('Application submitted successfully!'); window.location.href='applicant.php';</script>";
        } else {
            echo "<script>alert('Error submitting application.'); window.location.href='applicant.php';</script>";
        }
    }
}
?>