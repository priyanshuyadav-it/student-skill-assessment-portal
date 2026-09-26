<?php

require_once "../includes/auth.php";

requireStudent();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Dashboard</title>

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
            background: #2563eb;
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
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .welcome {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
            margin-bottom: 25px;
        }

        .welcome h1 {
            margin-top: 0;
        }

        .welcome p {
            color: #6b7280;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
        }

        .card h3 {
            margin-top: 0;
        }

        .card p {
            color: #6b7280;
            line-height: 1.5;
        }

        .card a {
            display: inline-block;
            margin-top: 10px;
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
        }

        @media (max-width: 768px) {

            .cards {
                grid-template-columns: 1fr;
            }

            .navbar {
                padding: 15px 20px;
            }

            .container {
                margin-top: 25px;
            }

        }

    </style>

</head>

<body>

<nav class="navbar">

    <h2>Student Skill Portal</h2>

    <a href="../auth/logout.php" class="logout">
        Logout
    </a>

</nav>

<div class="container">

    <div class="welcome">

        <h1>
            Welcome,
            <?php echo htmlspecialchars($_SESSION["full_name"]); ?>! 👋
        </h1>

        <p>
            You are successfully logged in to the Student Skill Assessment & Certification Portal.
        </p>

    </div>

    <div class="cards">

        <div class="card">

            <h3>📚 Skills</h3>

            <p>
                Explore available skills and assessments.
            </p>

            <a href="skills.php">
                View Skills →
            </a>

        </div>

        <div class="card">

            <h3>📝 Assessments</h3>

            <p>
                Take assessments and evaluate your technical skills.
            </p>

            <a href="assessment.php">
                View Assessments →
            </a>

        </div>

        <div class="card">

            <h3>🏆 Certificates</h3>

            <p>
                View and verify your earned certificates.
            </p>

            <a href="certificates.php">
                My Certificates →
            </a>

        </div>

    </div>

</div>

</body>

</html>