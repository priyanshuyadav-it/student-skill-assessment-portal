<?php

require_once "../config/database.php";
require_once "../includes/auth.php";

requireStudent();

$skill_id = filter_input(INPUT_GET, "skill_id", FILTER_VALIDATE_INT);

if (!$skill_id) {
    header("Location: skills.php");
    exit;
}

/*
 * Fetch selected skill
 */
$skill_query = "
    SELECT
        skill_id,
        skill_name,
        description,
        category,
        difficulty_level
    FROM skills
    WHERE skill_id = ?
      AND is_active = 1
    LIMIT 1
";

$skill_stmt = $pdo->prepare($skill_query);
$skill_stmt->execute([$skill_id]);

$skill = $skill_stmt->fetch(PDO::FETCH_ASSOC);

if (!$skill) {
    header("Location: skills.php");
    exit;
}

/*
 * Fetch assessments for selected skill
 */
$assessment_query = "
    SELECT
        assessment_id,
        title,
        description,
        duration_minutes,
        passing_percentage,
        total_questions
    FROM assessments
    WHERE skill_id = ?
      AND is_active = 1
    ORDER BY created_at DESC
";

$assessment_stmt = $pdo->prepare($assessment_query);
$assessment_stmt->execute([$skill_id]);

$assessments = $assessment_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo htmlspecialchars($skill["skill_name"]); ?> Assessment</title>

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
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .skill-header {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
            margin-bottom: 25px;
        }

        .skill-header h1 {
            margin-top: 0;
            margin-bottom: 10px;
        }

        .skill-header p {
            color: #6b7280;
            line-height: 1.6;
        }

        .badges {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 15px;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 13px;
        }

        .assessment-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
            margin-bottom: 20px;
        }

        .assessment-card h2 {
            margin-top: 0;
        }

        .assessment-description {
            color: #6b7280;
            line-height: 1.6;
        }

        .assessment-details {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin: 20px 0;
        }

        .detail {
            background: #f3f4f6;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 14px;
        }

        .start-button {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 11px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
        }

        .start-button:hover {
            background: #1d4ed8;
        }

        .no-assessment {
            background: white;
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
        }

        .no-assessment h2 {
            margin-top: 0;
        }

        .no-assessment p {
            color: #6b7280;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
        }

        @media (max-width: 600px) {

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

    <div class="skill-header">

        <h1>
            <?php echo htmlspecialchars($skill["skill_name"]); ?>
        </h1>

        <p>
            <?php
            echo htmlspecialchars(
                $skill["description"] ?? "No description available."
            );
            ?>
        </p>

        <div class="badges">

            <?php if (!empty($skill["category"])): ?>

                <span class="badge">
                    <?php echo htmlspecialchars($skill["category"]); ?>
                </span>

            <?php endif; ?>

            <span class="badge">
                <?php echo htmlspecialchars($skill["difficulty_level"]); ?>
            </span>

        </div>

    </div>

    <?php if (count($assessments) > 0): ?>

        <?php foreach ($assessments as $assessment): ?>

            <div class="assessment-card">

                <h2>
                    <?php echo htmlspecialchars($assessment["title"]); ?>
                </h2>

                <p class="assessment-description">

                    <?php
                    echo htmlspecialchars(
                        $assessment["description"]
                        ?? "Test your knowledge and skills through this assessment."
                    );
                    ?>

                </p>

                <div class="assessment-details">

                    <div class="detail">
                        ⏱️
                        <?php echo (int) $assessment["duration_minutes"]; ?>
                        minutes
                    </div>

                    <div class="detail">
                        📝
                        <?php echo (int) $assessment["total_questions"]; ?>
                        questions
                    </div>

                    <div class="detail">
                        🎯
                        <?php echo htmlspecialchars($assessment["passing_percentage"]); ?>%
                        to pass
                    </div>

                </div>

                <a
                    href="take_assessment.php?assessment_id=<?php echo (int) $assessment["assessment_id"]; ?>"
                    class="start-button"
                >
                    Start Assessment →
                </a>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="no-assessment">

            <h2>No Assessment Available</h2>

            <p>
                There is currently no assessment available for
                <strong>
                    <?php echo htmlspecialchars($skill["skill_name"]); ?>
                </strong>.
            </p>

            <p>
                Please check back later when an assessment has been added.
            </p>

            <a href="skills.php" class="back-link">
                ← Back to Skills
            </a>

        </div>

    <?php endif; ?>

</div>

</body>

</html>