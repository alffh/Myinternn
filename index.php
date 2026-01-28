<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>myIntern - Internship Management System</title>
    <style>
        /* CSS VARIABLES - PURPLE THEME */
        :root {
            --primary: #6f42c1;
            --primary-dark: #5a32a3;
            --primary-light: #f3ebff;
            --primary-gradient: linear-gradient(135deg, #8e44ad, #6f42c1);
            --text-primary: #2d3436;
            --text-secondary: #636e72;
            --bg-secondary: #f8f9fc;
            --white: #ffffff;
        }

        /* RESET & BASE STYLES */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, sans-serif;
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--white);
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* NAVIGATION */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            height: 80px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 10px rgba(111, 66, 193, 0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary);
            text-decoration: none;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            list-style: none;
            gap: 30px;
        }

        .nav-menu a {
            text-decoration: none;
            color: var(--text-secondary);
            font-weight: 500;
            transition: 0.3s;
        }

        .nav-menu a:hover, .nav-menu a.active {
            color: var(--primary);
        }

        .btn-signup {
            background: var(--primary);
            color: white !important;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-signup:hover {
            background: var(--primary-dark);
            box-shadow: 0 4px 15px rgba(111, 66, 193, 0.3);
        }

        /* HERO SECTION */
        .hero {
            padding: 80px 0;
            background: radial-gradient(circle at top right, var(--primary-light), var(--white));
            min-height: 85vh;
            display: flex;
            align-items: center;
        }

        .hero .container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
        }

        .hero-badge {
            display: inline-block;
            padding: 6px 16px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .hero h1 {
            font-size: 3.8rem;
            line-height: 1.1;
            margin-bottom: 20px;
            color: #1a1a1a;
        }

        .gradient-text {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 1.15rem;
            color: var(--text-secondary);
            margin-bottom: 35px;
            max-width: 520px;
        }

        /* HERO ANIMATED VISUAL - ORGANIZED LAYOUT */
        .visual-wrapper {
            position: relative;
            height: 500px;
            width: 100%;
        }

        .floating-card {
            position: absolute;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            padding: 22px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 15px 35px rgba(111, 66, 193, 0.15);
            border: 1px solid rgba(111, 66, 193, 0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            width: 240px;
            z-index: 2;
        }

        /* Improved Positioning */
        .c-1 { top: 10%; right: 15%; animation: floatY 5s ease-in-out infinite; }
        .c-2 { top: 45%; left: 5%; animation: floatY 6s ease-in-out infinite; animation-delay: 0.5s; }
        .c-3 { bottom: 10%; right: 5%; animation: floatY 7s ease-in-out infinite; animation-delay: 1s; }

        .floating-card:hover {
            transform: translateY(-15px) scale(1.05);
            background: white;
            box-shadow: 0 25px 50px rgba(111, 66, 193, 0.25);
            z-index: 10;
        }

        .card-icon {
            font-size: 1.6rem;
            background: var(--primary-light);
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            color: var(--primary);
        }

        .card-info span { display: block; font-size: 0.8rem; color: var(--text-secondary); font-weight: 500; }
        .card-info strong { font-size: 1.2rem; color: var(--primary-dark); display: block; }

        @keyframes floatY {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .main-glow {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 350px;
            height: 350px;
            background: var(--primary-gradient);
            filter: blur(100px);
            opacity: 0.12;
            border-radius: 50%;
            z-index: 1;
        }

        /* FEATURES SECTION */
        .features { padding: 100px 0; background: var(--white); }
        .section-header { text-align: center; margin-bottom: 60px; }
        .section-header h2 { font-size: 2.6rem; color: var(--primary); margin-bottom: 15px; }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .feature-card {
            padding: 45px;
            border-radius: 28px;
            background: var(--white);
            border: 1px solid var(--primary-light);
            transition: 0.4s;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 40px rgba(111, 66, 193, 0.08);
            border-color: var(--primary);
        }

        .feature-icon-box { font-size: 3rem; margin-bottom: 25px; display: block; }

        /* ABOUT SECTION */
        .info-card {
            background: var(--white);
            padding: 40px;
            border-radius: 24px;
            border-left: 6px solid var(--primary);
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
            transition: 0.3s;
        }

        /* CTA SECTION */
        .cta {
            background: var(--primary-gradient);
            padding: 90px 0;
            color: white;
            text-align: center;
        }

        .btn-white {
            display: inline-block;
            margin-top: 30px;
            padding: 16px 45px;
            background: white;
            color: var(--primary);
            text-decoration: none;
            border-radius: 35px;
            font-weight: 700;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .btn-white:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }

        .footer {
            background: var(--primary-light);
            color: var(--text-primary);
            padding: 80px 0 30px;
            border-top: 1px solid rgba(111, 66, 193, 0.1);
        }

        .footer h4 { color: var(--primary); font-weight: 800; margin-bottom: 20px; font-size: 1.2rem; }
        .footer p { color: var(--text-secondary); margin-bottom: 8px; }
        .portal-links { list-style: none; }
        .portal-links li { margin-bottom: 12px; }
        .portal-links a { color: var(--text-secondary); text-decoration: none; transition: 0.3s; font-weight: 500; }
        .portal-links a:hover { color: var(--primary); padding-left: 8px; }

        .footer-bottom {
            margin-top: 60px;
            padding-top: 25px;
            border-top: 1px solid rgba(111, 66, 193, 0.1);
            text-align: center;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="container">
            <a href="#" class="nav-brand">
                <svg width="34" height="34" viewBox="0 0 32 32">
                    <rect width="32" height="32" rx="9" fill="#6f42c1"/>
                    <text x="16" y="22" font-size="16" font-weight="bold" fill="white" text-anchor="middle">mi</text>
                </svg>
                <span>myIntern</span>
            </a>
            <ul class="nav-menu">
                <li><a href="#home">Home</a></li>
                <li><a href="#features">Features</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="login.php" style="color: var(--primary); font-weight: 600;">Login</a></li>
                <li><a href="register.php" class="btn-signup">Sign Up</a></li>
            </ul>
        </div>
    </nav>

    <section class="hero" id="home">
        <div class="container">
            <div class="hero-content">
                <div class="hero-badge">✨ Official Internship Portal</div>
                <h1>Manage Internships <br><span class="gradient-text">With Confidence</span></h1>
                <p class="hero-subtitle">A digital platform designed to streamline applications, attendance, and logbook management for UiTM Machang students and industry partners.</p>
                <a href="register.php" class="btn-signup" style="padding: 18px 45px; font-size: 1.1rem; text-decoration: none; border-radius: 50px;">Get Started Now</a>
            </div>
            
            <div class="hero-visual">
                <div class="visual-wrapper">
                    <div class="floating-card c-1">
                        <div class="card-icon">👥</div>
                        <div class="card-info"><span>Active Students</span><strong>500+ Enrolled</strong></div>
                    </div>
                    <div class="floating-card c-2">
                        <div class="card-icon">🏢</div>
                        <div class="card-info"><span>Partners</span><strong>150+ Companies</strong></div>
                    </div>
                    <div class="floating-card c-3">
                        <div class="card-icon">🏆</div>
                        <div class="card-info"><span>Employment</span><strong>98% Success</strong></div>
                    </div>
                    <div class="main-glow"></div>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="features">
        <div class="container">
            <div class="section-header">
                <h2>Our Core Features</h2>
                <p>Comprehensive tools for students, lecturers, and industry supervisors.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <span class="feature-icon-box">✍️</span>
                    <h3>Digital Logbook</h3>
                    <p>Record daily activities digitally and submit them for lecturer approval with a single click.</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon-box">📍</span>
                    <h3>Attendance Tracking</h3>
                    <p>Smart location-based check-in system to ensure punctuality during internship placement.</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon-box">📊</span>
                    <h3>Performance Monitoring</h3>
                    <p>Track student progress through company evaluations and real-time feedback loops.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="about" style="padding: 100px 0; background: var(--bg-secondary);">
        <div class="container">
            <div class="section-header">
                <h2>About the Program</h2>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                <div class="info-card">
                    <h3>Our Mission</h3>
                    <p>To bridge the gap between academic theory and industrial practice by providing a structured and monitored environment for UiTM students.</p>
                </div>
                <div class="info-card">
                    <h3>Eligibility</h3>
                    <ul style="list-style: none; margin-top: 15px;">
                        <li>💜 Final Year Degree Students</li>
                        <li>💜 Final Semester Diploma Students</li>
                        <li>💜 Completion is mandatory for graduation</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="container">
            <h2 style="font-size: 2.8rem;">Ready to take the next step?</h2>
            <p style="font-size: 1.1rem; opacity: 0.9;">Join myIntern and start managing your industrial training journey professionally.</p>
            <a href="register.php" class="btn-white">Register for Free</a>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div style="display: grid; grid-template-columns: 2fr 1.2fr; gap: 60px;">
                <div>
                    <h4>MYINTERN SYSTEM</h4>
                    <p>Faculty of Information Management & Computer Sciences</p>
                    <p>UiTM Machang, Kelantan Campus</p>
                    <p style="margin-top: 20px; color: var(--primary); font-weight: 700;">
                        📞 +6011 – 3995 2189<br>
                        ✉️ support@myintern.edu.my
                    </p>
                </div>
                <div>
                    <h4>Quick Links</h4>
                    <ul class="portal-links">
                        <li><a href="login.php">Student Dashboard</a></li>
                        <li><a href="lecturer_dashboard.php">Lecturer Portal</a></li>
                        <li><a href="#">Company Registration</a></li>
                        <li><a href="#">Privacy & Terms</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2026 myIntern Platform. All rights reserved. Designed for excellence in Education.</p>
            </div>
        </div>
    </footer>

</body>
</html>