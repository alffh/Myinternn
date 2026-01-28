<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = mysqli_real_escape_string($conn, $_POST['id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']); 
'
    $sql = "UPDATE students SET status = '$new_status' WHERE student_id = '$student_id'";

    if ($conn->query($sql)) {
        echo "<script>alert('Status updated successfully!'); window.location.href='lecturer_dashboard.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>