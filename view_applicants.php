<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['company_id']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit();
}

$company_id = $_SESSION['company_id'];

if (isset($_GET['status']) && isset($_GET['app_id'])) {
    $new_status = $_GET['status']; 
    $app_id = $conn->real_escape_string($_GET['app_id']);
    
    $conn->begin_transaction();
    try {
        $update_app = "UPDATE internship_applications SET application_status = '$new_status' WHERE application_id = '$app_id'";
        $conn->query($update_app);

        if ($new_status === 'approved') {
            $get_stu = $conn->query("SELECT student_id FROM internship_applications WHERE application_id = '$app_id'")->fetch_assoc();
            if ($get_stu) {
                $sid = $get_stu['student_id'];
                $update_stu = "UPDATE students SET internship_status = 'placed' WHERE student_id = '$sid'";
                $conn->query($update_stu);
            }
        }
        $conn->commit();
        echo "<script>alert('Application updated to $new_status'); window.location.href='view_applicants.php';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Error updating application.');</script>";
    }
}

$sql = "SELECT a.application_id, a.application_status, 
               s.student_id, s.student_name, s.university, s.programme, s.cgpa, s.phone,
               ad.title as job_title
        FROM internship_applications a
        JOIN students s ON a.student_id = s.student_id
        JOIN internship_ads ad ON a.ad_id = ad.ad_id
        WHERE ad.company_id = '$company_id'
        ORDER BY a.application_id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Applicants | myIntern</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/view_applicants.css">
</head>
<body>

<nav class="navbar">
     <div class="nav-container">
        <a href="company_dashboard.php" class="nav-brand"><div>mi</div>myIntern</a>
        <ul class="nav-menu">
            <li><a href="company_dashboard.php">Dashboard</a></li>
            <li><a href="view_applicants.php">Applicants</a></li>
            <li class="dropdown">
                <a href="#" class="active">Manage Interns ▼</a>
                <div class="dropdown-content">
                    <a href="monitor_attendance.php">🕒 Attendance Monitoring</a>
                    <a href="review_logbook.php">📖 Logbook Review</a>
                    <a href="evaluate_student.php">⭐ Student Evaluation</a>
                </div>
            </li>
            <li><a href="logout.php" class="btn-logout">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="dashboard-header">
    <div class="header-container">
        <h1>Applicant Management</h1>
        <p>Review student qualifications and manage internship placements.</p>
    </div>
</div>

<div class="container">
    <div class="card">
        <h3 style="margin-bottom: 25px; color: #333;">Recent Applications</h3>
        
        <table>
            <thead>
                <tr>
                    <th>Student Info</th>
                    <th>Academic Background</th>
                    <th>Position Applied</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div style="font-weight: 700; font-size: 1rem; color: #2d3436;"><?php echo htmlspecialchars($row['student_name']); ?></div>
                            <div style="color: #636e72; font-size: 0.8rem; margin-top: 4px;">📞 <?php echo htmlspecialchars($row['phone'] ?? 'N/A'); ?></div>
                        </td>
                        <td class="edu-info">
                            <div style="margin-bottom: 3px;"><b>Uni:</b> <?php echo htmlspecialchars($row['university']); ?></div>
                            <div><b>CGPA:</b> 
                                <span style="color:#34a853; font-weight:700;">
                                    <?php echo !empty($row['cgpa']) ? htmlspecialchars($row['cgpa']) : '0.00'; ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: #6f42c1;"><?php echo htmlspecialchars($row['job_title']); ?></div>
                        </td>
                        <td>
                            <span class="badge <?php echo $row['application_status']; ?>">
                                <?php echo $row['application_status']; ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($row['application_status'] == 'pending'): ?>
                                <div class="btn-group">
                                    <a href="?status=approved&app_id=<?php echo $row['application_id']; ?>" 
                                       class="btn btn-approve" 
                                       onclick="return confirm('Confirm Approve? Student will be placed.')">Approve</a>
                                    
                                    <a href="?status=rejected&app_id=<?php echo $row['application_id']; ?>" 
                                       class="btn btn-reject" 
                                       onclick="return confirm('Reject this application?')">Reject</a>
                                </div>
                            <?php else: ?>
                                <span style="color:#bbb; font-style:italic; font-size:0.85rem;">Processed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="empty-state">
                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" style="opacity: 0.2; margin-bottom: 15px;"><br>
                            No new applications received at the moment.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<footer style="text-align: center; padding: 20px 0 40px 0; color: #bbb; font-size: 0.85rem;">
    &copy; 2026 myIntern Management System. All rights reserved.
</footer>

</body>
</html>