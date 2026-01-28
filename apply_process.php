<?php
include 'db_connect.php';
session_start();

$_SESSION['student_id'] = $student_data['student_id']; 
$ad_id = $_GET['ad_id'];


// Check if already applied
$check = "SELECT * FROM internship_applications WHERE student_id = '$student_id' AND ad_id = '$ad_id'";
$res = mysqli_query($conn, $check);

if(mysqli_num_rows($res) == 0) {
    $sql = "INSERT INTO internship_applications (student_id, ad_id, application_status) 
            VALUES ('$student_id', '$ad_id', 'pending')";
    mysqli_query($conn, $sql);
    header("Location: my_applications.php?msg=success");
} else {
    header("Location: my_applications.php?msg=exists");
}
?>