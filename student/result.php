<?php

require_once "../config/database.php";
require_once "../includes/auth.php";

requireStudent();

$result_id = filter_input(
    INPUT_GET,
    "result_id",
    FILTER_VALIDATE_INT
);

if (!$result_id) {
    header("Location: dashboard.php");
    exit;
}

/*
 * Fetch result details.
 * The result must belong to the logged-in student.
 */
$result_query = "
    SELECT
        r.result_id,
        r.attempt_id,
        r.total_marks,
        r.obtained_marks,
        r.percentage,
        r.result_status,
        r.completed_at,
        a.title AS assessment_title,
        s.skill_name
    FROM results r
    INNER JOIN assessments a
        ON r.assessment_id = a.assessment_id
    INNER JOIN skills s
        ON a.skill_id = s.skill_id
    WHERE r.result_id = ?
      AND r.user_id = ?
    LIMIT 1
";

$result_stmt = $pdo->prepare($result_query);

$result_stmt->execute([
    $result_id,
    $_SESSION["user_id"]
]);

$result = $result_stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    header("Location: dashboard.php");
    exit;
}

$is_pass = $result["result_status"] === "Pass";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Assessment Result</title>

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

        .container {
            max-width: 800px;
            margin: 45px auto;
            padding: 0 20px;
        }

        .result-card {
            background: white;
            padding: 40px;
            border-radius: 14px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.07);
            text-align: center;
        }

        .result-icon {
            width: 75px;
            height: 75px;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
        }

        .pass {
            background: #dcfce7;
        }

        .fail {
            background: #fee2e2;
        }

        .result-card h1 {
            margin-bottom: 10px;
        }

        .result-message {
            color: #6b7280;
            margin-bottom: 30px;
        }

        .score-box {
            background: #f8fafc;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
        }

        .percentage {
            font-size: 48px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 8px;
        }

        .score-text {
            color: #6b7280;
        }

        .details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 30px;
            text-align: left;
        }

        .detail {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
        }

        .detail-label {
            display: block;
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .detail-value {
            font-weight: bold;
        }

        .status {
            display: inline-block;
            padding: 9px 22px;
            border-radius: 25px;
            font-weight: bold;
            margin-bottom: 25px;
        }

        .status-pass {
            background: #dcfce7;
            color: #166534;
        }

        .status-fail {
            background: #fee2e2;
            color: #991b1b;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .button {
            display: inline-block;
            padding: 11px 20px;
            border-radius: 7px;
            text-decoration: none;
            font-weight: bold;
        }

        .primary {
            background: #2563eb;
            color: white;
        }

        .primary:hover {
            background: #1d4ed8;
        }

        .secondary {
            background: #e5e7eb;
            color: #374151;
        }

        .secondary:hover {
            background: #d1d5db;
        }

        @media (max-width: 600px) {

            .navbar {
                padding: 15px 20px;
            }

            .container {
                margin-top: 25px;
            }

            .result-card {
                padding: 25px 20px;
            }

            .details {
                grid-template-columns: 1fr;
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

    <div class="result-card">

        <div class="result-icon <?php echo $is_pass ? 'pass' : 'fail'; ?>">

            <?php echo $is_pass ? "✓" : "✕"; ?>

        </div>

        <h1>
            Assessment Completed
        </h1>

        <p class="result-message">

            <?php echo htmlspecialchars($result["assessment_title"]); ?>

            <br>

            Skill:
            <strong>
                <?php echo htmlspecialchars($result["skill_name"]); ?>
            </strong>

        </p>

        <div class="score-box">

            <div class="percentage">

                <?php echo htmlspecialchars($result["percentage"]); ?>%

            </div>

            <div class="score-text">

                <?php echo htmlspecialchars($result["obtained_marks"]); ?>

                /

                <?php echo htmlspecialchars($result["total_marks"]); ?>

                marks obtained

            </div>

        </div>

        <div>

            <span class="status <?php echo $is_pass ? 'status-pass' : 'status-fail'; ?>">

                <?php echo htmlspecialchars($result["result_status"]); ?>

            </span>

        </div>

        <div class="details">

            <div class="detail">

                <span class="detail-label">
                    Assessment
                </span>

                <span class="detail-value">
                    <?php echo htmlspecialchars($result["assessment_title"]); ?>
                </span>

            </div>

            <div class="detail">

                <span class="detail-label">
                    Skill
                </span>

                <span class="detail-value">
                    <?php echo htmlspecialchars($result["skill_name"]); ?>
                </span>

            </div>

            <div class="detail">

                <span class="detail-label">
                    Marks Obtained
                </span>

                <span class="detail-value">
                    <?php echo htmlspecialchars($result["obtained_marks"]); ?>
                </span>

            </div>

            <div class="detail">

                <span class="detail-label">
                    Completed On
                </span>

                <span class="detail-value">
                    <?php
                    echo htmlspecialchars(
                        date(
                            "d M Y, h:i A",
                            strtotime($result["completed_at"])
                        )
                    );
                    ?>
                </span>

            </div>

        </div>

        <div class="actions">

            <a
                href="skills.php"
                class="button primary"
            >
                Back to Skills
            </a>

            <a
                href="dashboard.php"
                class="button secondary"
            >
                Dashboard
            </a>

        </div>

    </div>

</div>

</body>

</html>
