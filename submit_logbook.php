<?php
include 'db_connect.php';
session_start();
$student_id = $_SESSION['student_id'] ?? 1;

if(isset($_POST['submit_log'])) {
    $date = $_POST['log_date'];
    $activities = mysqli_real_escape_string($conn, $_POST['activities']);
    
    $sql = "INSERT INTO logbook (student_id, log_date, activities) VALUES ('$student_id', '$date', '$activities')";
    mysqli_query($conn, $sql);
}
?>

<div class="main-content">
    <div class="header"><h1>Daily Logbook</h1></div>
    <div class="table-container">
        <form method="POST">
            <label class="label">Log Date</label>
            <input type="date" name="log_date" class="applicant-card" style="width:100%; margin-bottom:20px;">
            
            <label class="label">Activities Performed</label>
            <textarea name="activities" class="applicant-card" style="width:100%; height:150px;" placeholder="What did you do today?"></textarea>
            
            <button type="submit" name="submit_log" class="btn-apply" style="border:none; cursor:pointer;">Submit Log</button>
        </form>
    </div>
</div>