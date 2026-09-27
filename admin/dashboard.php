<?php

require_once "../includes/auth.php";
require_once "../config/database.php";

requireAdmin();

$total_students = 0;
$total_skills = 0;
$total_assessments = 0;
$total_results = 0;
$total_certificates = 0;

try {

    $stmt = $pdo->query("
        SELECT COUNT(*) 
        FROM users 
        WHERE role = 'student'
    ");
    $total_students = $stmt->fetchColumn();

    $stmt = $pdo->query("
        SELECT COUNT(*) 
        FROM skills 
        WHERE is_active = 1
    ");
    $total_skills = $stmt->fetchColumn();

    $stmt = $pdo->query("
        SELECT COUNT(*) 
        FROM assessments 
        WHERE is_active = 1
    ");
    $total_assessments = $stmt->fetchColumn();

    $stmt = $pdo->query("
        SELECT COUNT(*) 
        FROM results
    ");
    $total_results = $stmt->fetchColumn();

    $stmt = $pdo->query("
        SELECT COUNT(*) 
        FROM certificates
    ");
    $total_certificates = $stmt->fetchColumn();

} catch (PDOException $e) {

    $error_message = "Unable to load dashboard statistics.";

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard | Student Skill Portal</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #111827;
        }

        .navbar {
            background: #1d4ed8;
            color: white;
            padding: 18px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h2 {
            margin: 0;
            font-size: 21px;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .admin-name {
            font-size: 14px;
        }

        .logout {
            color: white;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.15);
            padding: 9px 16px;
            border-radius: 6px;
        }

        .logout:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .welcome {
            background: white;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
            margin-bottom: 25px;
        }

        .welcome h1 {
            margin: 0 0 10px;
            font-size: 30px;
        }

        .welcome p {
            margin: 0;
            color: #6b7280;
            line-height: 1.6;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 18px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
        }

        .stat-icon {
            font-size: 28px;
            margin-bottom: 12px;
        }

        .stat-card h3 {
            margin: 0;
            font-size: 30px;
            color: #2563eb;
        }

        .stat-card p {
            margin: 6px 0 0;
            color: #6b7280;
            font-size: 14px;
        }

        .section-title {
            margin: 0 0 18px;
            font-size: 23px;
        }

        .modules {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .module {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease;
        }

        .module:hover {
            transform: translateY(-3px);
        }

        .module-icon {
            font-size: 32px;
            margin-bottom: 12px;
        }

        .module h3 {
            margin: 0 0 8px;
        }

        .module p {
            color: #6b7280;
            line-height: 1.5;
            min-height: 45px;
        }

        .module a {
            display: inline-block;
            margin-top: 8px;
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
        }

        .module a:hover {
            text-decoration: underline;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        @media (max-width: 1000px) {

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .modules {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 650px) {

            .navbar {
                padding: 15px 20px;
            }

            .admin-info {
                gap: 8px;
            }

            .admin-name {
                display: none;
            }

            .container {
                margin-top: 25px;
            }

            .stats,
            .modules {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>

<nav class="navbar">

    <h2>Student Skill Portal</h2>

    <div class="admin-info">

        <span class="admin-name">
            👤 <?php echo htmlspecialchars($_SESSION["full_name"]); ?>
        </span>

        <a href="../auth/logout.php" class="logout">
            Logout
        </a>

    </div>

</nav>

<div class="container">

    <div class="welcome">

        <h1>Admin Dashboard</h1>

        <p>
            Manage students, skills, assessments, results, and certificates
            from one centralized dashboard.
        </p>

    </div>

    <?php if (isset($error_message)): ?>

        <div class="error">
            <?php echo htmlspecialchars($error_message); ?>
        </div>

    <?php endif; ?>

    <div class="stats">

        <div class="stat-card">

            <div class="stat-icon">👨‍🎓</div>

            <h3>
                <?php echo $total_students; ?>
            </h3>

            <p>Students</p>

        </div>

        <div class="stat-card">

            <div class="stat-icon">📚</div>

            <h3>
                <?php echo $total_skills; ?>
            </h3>

            <p>Active Skills</p>

        </div>

        <div class="stat-card">

            <div class="stat-icon">📝</div>

            <h3>
                <?php echo $total_assessments; ?>
            </h3>

            <p>Assessments</p>

        </div>

        <div class="stat-card">

            <div class="stat-icon">📊</div>

            <h3>
                <?php echo $total_results; ?>
            </h3>

            <p>Results</p>

        </div>

        <div class="stat-card">

            <div class="stat-icon">🏆</div>

            <h3>
                <?php echo $total_certificates; ?>
            </h3>

            <p>Certificates</p>

        </div>

    </div>

    <h2 class="section-title">
        Management Modules
    </h2>

    <div class="modules">

        <div class="module">

            <div class="module-icon">👨‍🎓</div>

            <h3>Manage Students</h3>

            <p>
                View registered students and manage their accounts.
            </p>

            <a href="students.php">
                Manage Students →
            </a>

        </div>

        <div class="module">

            <div class="module-icon">📚</div>

            <h3>Manage Skills</h3>

            <p>
                Add, edit, and manage technical skills available on the portal.
            </p>

            <a href="skills.php">
                Manage Skills →
            </a>

        </div>

        <div class="module">

            <div class="module-icon">📝</div>

            <h3>Assessments</h3>

            <p>
                Create and manage assessments for different skills.
            </p>

            <a href="assessments.php">
                Manage Assessments →
            </a>

        </div>

        <div class="module">

            <div class="module-icon">❓</div>

            <h3>Questions</h3>

            <p>
                Add and manage questions used in assessments.
            </p>

            <a href="questions.php">
                Manage Questions →
            </a>

        </div>

        <div class="module">

            <div class="module-icon">📊</div>

            <h3>Results</h3>

            <p>
                View assessment performance and student results.
            </p>

            <a href="results.php">
                View Results →
            </a>

        </div>

        <div class="module">

            <div class="module-icon">🏆</div>

            <h3>Certificates</h3>

            <p>
                View and manage certificates issued to students.
            </p>

            <a href="certificates.php">
                Manage Certificates →
            </a>

        </div>

    </div>

</div>

</body>

</html>