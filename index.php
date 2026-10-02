<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Skill Assessment & Certification Portal</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f4f7fb;
            color: #1e293b;
        }

        /* Navbar */
        .navbar {
            background: #2563eb;
            color: white;
            padding: 18px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 22px;
            font-weight: bold;
        }

        .nav-buttons {
            display: flex;
            gap: 12px;
        }

        .nav-buttons a {
            text-decoration: none;
            color: white;
            padding: 10px 18px;
            border-radius: 7px;
            font-size: 14px;
            transition: 0.3s;
        }

        .student-login {
            background: white;
            color: #2563eb !important;
        }

        .admin-login {
            border: 1px solid white;
        }

        .nav-buttons a:hover {
            opacity: 0.85;
        }

        /* Hero */
        .hero {
            min-height: 520px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 70px 7%;
            gap: 50px;
            background: linear-gradient(135deg, #eff6ff, #ffffff);
        }

        .hero-content {
            max-width: 650px;
        }

        .hero-content h1 {
            font-size: 48px;
            line-height: 1.15;
            margin-bottom: 20px;
            color: #0f172a;
        }

        .hero-content h1 span {
            color: #2563eb;
        }

        .hero-content p {
            font-size: 18px;
            line-height: 1.7;
            color: #64748b;
            margin-bottom: 30px;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
        }

        .primary-button,
        .secondary-button {
            text-decoration: none;
            padding: 14px 25px;
            border-radius: 8px;
            font-weight: bold;
            display: inline-block;
            transition: 0.3s;
        }

        .primary-button {
            background: #2563eb;
            color: white;
        }

        .secondary-button {
            background: white;
            color: #2563eb;
            border: 1px solid #2563eb;
        }

        .primary-button:hover,
        .secondary-button:hover {
            transform: translateY(-2px);
        }

        /* Hero Card */
        .hero-card {
            width: 360px;
            background: white;
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0 15px 40px rgba(15, 23, 42, 0.12);
        }

        .hero-card-icon {
            font-size: 55px;
            margin-bottom: 20px;
        }

        .hero-card h2 {
            margin-bottom: 12px;
            color: #0f172a;
        }

        .hero-card p {
            color: #64748b;
            line-height: 1.6;
        }

        /* Features */
        .features {
            padding: 70px 7%;
            text-align: center;
        }

        .section-title {
            font-size: 32px;
            margin-bottom: 10px;
            color: #0f172a;
        }

        .section-description {
            color: #64748b;
            margin-bottom: 40px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 22px;
        }

        .feature-card {
            background: white;
            padding: 30px 20px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(15, 23, 42, 0.07);
            transition: 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 38px;
            margin-bottom: 15px;
        }

        .feature-card h3 {
            margin-bottom: 10px;
            color: #0f172a;
        }

        .feature-card p {
            color: #64748b;
            line-height: 1.5;
            font-size: 14px;
        }

        /* Footer */
        footer {
            background: #0f172a;
            color: #cbd5e1;
            text-align: center;
            padding: 25px;
            margin-top: 20px;
        }

        footer strong {
            color: white;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .hero {
                flex-direction: column;
                text-align: center;
            }

            .hero-buttons {
                justify-content: center;
            }

            .feature-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .hero-card {
                width: 100%;
                max-width: 400px;
            }
        }

        @media (max-width: 600px) {
            .navbar {
                flex-direction: column;
                gap: 15px;
            }

            .hero-content h1 {
                font-size: 36px;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }

            .hero-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            Student Skill Portal
        </div>

        <div class="nav-buttons">
            <a href="auth/login.php" class="student-login">
                Student Login
            </a>

            <a href="admin/login.php" class="admin-login">
                Admin Login
            </a>
        </div>
    </nav>


    <!-- Hero Section -->
    <section class="hero">

        <div class="hero-content">

            <h1>
                Assess Your Skills.<br>
                <span>Build Your Future.</span>
            </h1>

            <p>
                A smart student skill assessment and certification
                platform that helps students evaluate their knowledge,
                track performance and earn digital certificates.
            </p>

            <div class="hero-buttons">

                <a href="auth/login.php" class="primary-button">
                    Get Started
                </a>

                <a href="auth/register.php" class="secondary-button">
                    Create Account
                </a>

            </div>

        </div>


        <div class="hero-card">

            <div class="hero-card-icon">
                🎓
            </div>

            <h2>
                Learn. Assess. Achieve.
            </h2>

            <p>
                Take skill-based assessments, receive instant results,
                monitor your progress and showcase your achievements
                with digital certificates.
            </p>

        </div>

    </section>


    <!-- Features -->
    <section class="features">

        <h2 class="section-title">
            Everything You Need
        </h2>

        <p class="section-description">
            A complete platform for student skill evaluation and certification.
        </p>


        <div class="feature-grid">

            <div class="feature-card">

                <div class="feature-icon">
                    📝
                </div>

                <h3>
                    Skill Assessments
                </h3>

                <p>
                    Take assessments designed to evaluate
                    your knowledge across different skills.
                </p>

            </div>


            <div class="feature-card">

                <div class="feature-icon">
                    📊
                </div>

                <h3>
                    Performance Tracking
                </h3>

                <p>
                    View your scores and track your assessment
                    performance over time.
                </p>

            </div>


            <div class="feature-card">

                <div class="feature-icon">
                    🏆
                </div>

                <h3>
                    Digital Certificates
                </h3>

                <p>
                    Earn certificates when you successfully
                    complete skill assessments.
                </p>

            </div>


            <div class="feature-card">

                <div class="feature-icon">
                    🔐
                </div>

                <h3>
                    Certificate Verification
                </h3>

                <p>
                    Verify certificates using a unique
                    certificate number and verification code.
                </p>

            </div>

        </div>

    </section>


    <!-- Footer -->
    <footer>

        <p>
            © 2026 <strong>Student Skill Assessment & Certification Portal</strong>
        </p>

        <p>
            Built with PHP, MySQL, HTML, CSS & JavaScript
        </p>

    </footer>

</body>
</html>