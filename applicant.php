<?php
session_start();
require 'db_connect.php'; 

// 1. Session & Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'applicant') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Fetch Student Profile
$stmt = $conn->prepare("SELECT * FROM students WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) { die("Student profile not found."); }

$student_id = $student['student_id'];
$display_name = $student['student_name'];

// 3. Check for Approved Placement
$secured_query = "SELECT ap.application_id, ap.application_status, c.company_name, ad.title, ad.ad_id 
                  FROM internship_applications ap 
                  JOIN internship_ads ad ON ap.ad_id = ad.ad_id 
                  JOIN companies c ON ad.company_id = c.company_id 
                  WHERE ap.student_id = ? AND ap.application_status = 'approved' 
                  LIMIT 1";
$secured_stmt = $conn->prepare($secured_query);
$secured_stmt->bind_param("i", $student_id);
$secured_stmt->execute();
$approved_app = $secured_stmt->get_result()->fetch_assoc();
$is_secured = ($approved_app) ? true : false;

// 4. Fetch Score for Certificate
$eval_stmt = $conn->prepare("SELECT final_score FROM evaluations WHERE student_id = ?");
$eval_stmt->bind_param("i", $student_id);
$eval_stmt->execute();
$eval_data = $eval_stmt->get_result()->fetch_assoc();
$final_mark = $eval_data['final_score'] ?? null;

// 5. Search Logic (only used if not secured)
$search = $_GET['search'] ?? '';
$search_param = "%$search%";
$ads_query = "SELECT a.*, c.company_name FROM internship_ads a 
              JOIN companies c ON a.company_id = c.company_id
              WHERE (a.title LIKE ? OR c.company_name LIKE ?) AND a.ad_status = 'active'
              ORDER BY a.posted_at DESC";
$ads_stmt = $conn->prepare($ads_query);
$ads_stmt->bind_param("ss", $search_param, $search_param);
$ads_stmt->execute();
$ads_result = $ads_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | myIntern</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/applicant.css">
    <style>
        .motivation-carousel {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white !important;
            text-align: center;
            position: relative;
            overflow: hidden;
            min-height: 180px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border: none;
        }
        .motivation-slide { display: none; animation: fadeIn 0.8s ease; }
        .motivation-slide.active { display: block; }
        .quote-icon { font-size: 3rem; opacity: 0.2; position: absolute; top: 10px; left: 20px; }
        .carousel-dots { margin-top: 15px; }
        .dot { height: 8px; width: 8px; background: rgba(255,255,255,0.4); border-radius: 50%; display: inline-block; margin: 0 5px; transition: 0.3s; }
        .dot.active { background: white; width: 20px; border-radius: 10px; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <a href="applicant.php" class="nav-brand"><div>mi</div> myIntern</a>
        <ul class="nav-menu">
            <li><a href="applicant.php">Home</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle">Internship Tools <span class="arrow">▾</span></a>
                <div class="dropdown-content">
                    <a href="attendance.php">🕒 Clock In / Out</a>
                    <a href="logbook.php">📖 Weekly Logbook</a>
                    <a href="view_evaluation.php">📝 My Result</a>
                </div>
            </li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="logout.php" style="color:#ea4335;">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="welcome-hero">
    <h1>Welcome back, <?php echo htmlspecialchars($display_name); ?>!</h1>
    <p><?php echo htmlspecialchars($student['programme']); ?> | ID: <?php echo htmlspecialchars($student['student_number']); ?></p>
</div>

<div class="main-container">
    <div class="content-left">
        
        <?php if ($is_secured): ?>
            <div class="card motivation-carousel">
                <div class="carousel-container">
                    <div class="motivation-slide active">
                        <span class="quote-icon">“</span>
                        <p>The only way to do great work is to love what you do.</p>
                        <small>- Steve Jobs</small>
                    </div>
                    <div class="motivation-slide">
                        <span class="quote-icon">“</span>
                        <p>Success is the sum of small efforts, repeated day in and day out.</p>
                        <small>- Robert Collier</small>
                    </div>
                    <div class="motivation-slide">
                        <span class="quote-icon">“</span>
                        <p>Your internship is the foundation of your future career.</p>
                        <small>- myIntern Team</small>
                    </div>
                </div>
                <div class="carousel-dots">
                    <span class="dot active"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </div>
            </div>

            <div class="card">
                <h3 style="margin-bottom: 20px;">Placement Status</h3>
                <div class="placement-info">
                    <div style="font-size: 50px; background: #f3f0ff; padding: 15px; border-radius: 12px;">🏢</div>
                    <div>
                        <h2 style="color: var(--primary); margin-bottom: 5px;"><?php echo htmlspecialchars($approved_app['company_name']); ?></h2>
                        <p style="color: #666; margin-bottom: 10px;"><?php echo htmlspecialchars($approved_app['title']); ?></p>
                        <span class="status-pill approved">SECURED</span>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="card">
                <h3 style="margin-bottom: 20px;">Find Your Internship</h3>
                <form method="GET" style="margin-bottom: 25px;">
                    <input type="text" name="search" placeholder="Search position or company..." value="<?php echo htmlspecialchars($search); ?>" 
                           style="width: 100%; padding: 15px; border-radius: 12px; border: 1px solid #ddd; outline: none; transition: 0.3s;">
                </form>

                <div class="ad-list">
                    <?php if($ads_result->num_rows > 0): ?>
                        <?php while($ad = $ads_result->fetch_assoc()): ?>
                            <div class="ad-item">
                                <div>
                                    <h4 style="color: #333; margin-bottom: 5px;"><?php echo htmlspecialchars($ad['title']); ?></h4>
                                    <p style="font-size: 0.9rem; color: #777;"><?php echo htmlspecialchars($ad['company_name']); ?></p>
                                </div>
                                <a href="internship_details.php?id=<?php echo $ad['ad_id']; ?>" style="color: var(--primary); font-weight: 700; text-decoration: none;">Details →</a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #999; padding: 20px;">No internship advertisements found.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="content-right">
        <div class="card" style="text-align: center;">
            <div style="font-size: 50px; margin-bottom: 10px;">🎓</div>
            <h3>Digital Certificate</h3>
            <p style="font-size: 0.85rem; color: #777; margin-bottom: 20px; padding: 0 10px;">
                <?php echo ($final_mark) ? "Congratulations! Your certificate is ready." : "Available after your final supervisor evaluation."; ?>
            </p>
            
            <?php if ($final_mark): ?>
                <a href="generate_report.php?id=<?php echo $student_id; ?>" class="btn btn-primary">Download Now</a>
            <?php else: ?>
                <button class="btn" style="background: #eee; color: #aaa; cursor: not-allowed;" disabled>Not Available Yet</button>
            <?php endif; ?>
        </div>

        <div class="card">
            <h4 style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">Quick Tools</h4>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <a href="attendance.php" class="btn" style="background: #f3f0ff; color: var(--primary); text-align: left; padding-left: 20px;">🕒 Attendance</a>
                <a href="logbook.php" class="btn" style="background: #f3f0ff; color: var(--primary); text-align: left; padding-left: 20px;">📖 Weekly Logbook</a>
                <a href="view_evaluation.php" class="btn" style="background: #f3f0ff; color: var(--primary); text-align: left; padding-left: 20px;">📝 My Result</a>
            </div>
        </div>
    </div>
</div>

<footer class="main-footer">
    <div class="footer-bottom">
        <p>© 2026 myIntern Platform. All rights reserved.</p>
    </div>
</footer>

<script>
    // Carousel Logic
    let currentSlide = 0;
    const slides = document.querySelectorAll('.motivation-slide');
    const dots = document.querySelectorAll('.dot');

    function showSlide(index) {
        if(slides.length === 0) return;
        slides.forEach(s => s.classList.remove('active'));
        dots.forEach(d => d.classList.remove('active'));
        
        slides[index].classList.add('active');
        dots[index].classList.add('active');
    }

    function nextSlide() {
        if(slides.length === 0) return;
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }

    // Initialize carousel if slides exist
    if(slides.length > 0) {
        setInterval(nextSlide, 5000);
    }
</script>

</body>
</html>
