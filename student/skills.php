<?php

require_once "../config/database.php";
require_once "../includes/auth.php";

requireStudent();

$query = "
    SELECT
        skill_id,
        skill_name,
        description,
        category,
        difficulty_level
    FROM skills
    WHERE is_active = 1
    ORDER BY skill_name ASC
";

$stmt = $pdo->prepare($query);
$stmt->execute();

$skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Available Skills</title>

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

        .navbar a {
            color: white;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.15);
            padding: 9px 16px;
            border-radius: 6px;
        }

        .navbar a:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .page-header {
            margin-bottom: 25px;
        }

        .page-header h1 {
            margin-bottom: 8px;
        }

        .page-header p {
            color: #6b7280;
        }

        .skills-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .skill-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease;
        }

        .skill-card:hover {
            transform: translateY(-4px);
        }

        .skill-card h3 {
            margin-top: 0;
            margin-bottom: 12px;
        }

        .skill-card p {
            color: #6b7280;
            line-height: 1.5;
            min-height: 48px;
        }

        .skill-info {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 15px;
        }

        .badge {
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 13px;
            background: #eff6ff;
            color: #1d4ed8;
        }

        .difficulty {
            background: #f3f4f6;
            color: #374151;
        }

        .assessment-link {
            display: inline-block;
            margin-top: 18px;
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
        }

        .assessment-link:hover {
            text-decoration: underline;
        }

        .no-skills {
            background: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            color: #6b7280;
        }

        @media (max-width: 900px) {

            .skills-grid {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 600px) {

            .skills-grid {
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

    <a href="dashboard.php">
        Dashboard
    </a>

</nav>

<div class="container">

    <div class="page-header">

        <h1>Available Skills</h1>

        <p>
            Explore the skills available for assessment and certification.
        </p>

    </div>

    <?php if (count($skills) > 0): ?>

        <div class="skills-grid">

            <?php foreach ($skills as $skill): ?>

                <div class="skill-card">

                    <h3>
                        <?php echo htmlspecialchars($skill["skill_name"]); ?>
                    </h3>

                    <p>
                        <?php
                        echo htmlspecialchars(
                            $skill["description"] ?? "No description available."
                        );
                        ?>
                    </p>

                    <div class="skill-info">

                        <?php if (!empty($skill["category"])): ?>

                            <span class="badge">
                                <?php echo htmlspecialchars($skill["category"]); ?>
                            </span>

                        <?php endif; ?>

                        <span class="badge difficulty">
                            <?php echo htmlspecialchars($skill["difficulty_level"]); ?>
                        </span>

                    </div>

                    <a href="assessment.php?skill_id=<?php echo $skill["skill_id"]; ?>"
                       class="assessment-link">
                        View Assessment →
                    </a>

                </div>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="no-skills">

            <h3>No skills available</h3>

            <p>
                There are currently no active skills available for assessment.
            </p>

        </div>

    <?php endif; ?>

</div>

</body>

</html>