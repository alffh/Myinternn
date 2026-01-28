<?php
include 'db_connect.php'; 
session_start();

if (!isset($_SESSION['student_id'])) {
    die("Access Denied");
}

$student_id = $_SESSION['student_id'];

$s = $conn->query("SELECT * FROM students WHERE student_id = '$student_id'")->fetch_assoc();


$company_data = $conn->query("SELECT e.*, c.company_name FROM evaluations e JOIN companies c ON e.company_id = c.company_id WHERE e.student_id = '$student_id' AND e.evaluator_type = 'company'")->fetch_assoc();
$lecturer_data = $conn->query("SELECT * FROM evaluations WHERE student_id = '$student_id' AND evaluator_type = 'lecturer'")->fetch_assoc();

function getGrade($mark) {
    if ($mark >= 80) return 'A'; 
    if ($mark >= 70) return 'B';
    if ($mark >= 60) return 'C'; 
    if ($mark >= 50) return 'D';
    return 'G';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official_Slip_<?= $student_id ?></title>
    <link rel="stylesheet" href="style_result_slip.css">
</head>
<body>

    <a href="#" onclick="window.print()" class="btn-download">🖨️ DOWNLOAD / PRINT PDF</a>

    <div class="slip-page">
        <div class="header">
            <h2>MYINTERN MANAGEMENT SYSTEM</h2>
            <h3>OFFICIAL INTERNSHIP RESULT SLIP</h3>
        </div>

        <div class="student-detail">
            <div>NAME</div><div>: <?= strtoupper($s['student_name']) ?></div>
            <div>PROGRAMME</div><div>: <?= strtoupper($s['programme']) ?></div>
            <div>STUDENT ID</div><div>: <?= $s['student_id'] ?></div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>MODULE</th>
                    <th>ASSESSMENT TYPE</th>
                    <th align="center">MARK</th>
                    <th align="center">GRADE</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>IND-1</td><td>Industry: Technical Skills</td><td align="center"><?= $company_data['skill_score'] ?></td><td align="center"><?= getGrade($company_data['skill_score']) ?></td></tr>
                <tr><td>IND-2</td><td>Industry: Discipline</td><td align="center"><?= $company_data['discipline_score'] ?></td><td align="center"><?= getGrade($company_data['discipline_score']) ?></td></tr>
                <tr><td>ACA-1</td><td>Academic: Logbook</td><td align="center"><?= $lecturer_data['skill_score'] ?></td><td align="center"><?= getGrade($lecturer_data['skill_score']) ?></td></tr>
                <tr><td>ACA-2</td><td>Academic: Presentation</td><td align="center"><?= $lecturer_data['attendance_score'] ?></td><td align="center"><?= getGrade($lecturer_data['attendance_score']) ?></td></tr>
            </tbody>
        </table>

        <?php 
        $final = ($company_data['final_score'] + $lecturer_data['final_score']) / 2;
        ?>
        <div class="total-box">
            <strong>FINAL AGGREGATE: <?= number_format($final, 2) ?>%</strong><br>
            <span style="color: green;">STATUS: LULUS (PASS)</span>
        </div>

        <div style="margin-top: 100px; display: flex; justify-content: space-between;">
            <div style="text-align: center; border-top: 1px solid #000; width: 200px;">Student Signature</div>
            <div style="text-align: center; border-top: 1px solid #000; width: 200px;">Registrar Signature</div>
        </div>
    </div>

</body>
</html>