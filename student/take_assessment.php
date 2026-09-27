<?php

require_once "../config/database.php";
require_once "../includes/auth.php";

requireStudent();

$assessment_id = filter_input(
    INPUT_GET,
    "assessment_id",
    FILTER_VALIDATE_INT
);

if (!$assessment_id) {
    header("Location: skills.php");
    exit;
}

/*
 * Fetch assessment details
 */
$assessment_query = "
    SELECT
        a.assessment_id,
        a.title,
        a.description,
        a.duration_minutes,
        a.passing_percentage,
        a.total_questions,
        s.skill_name
    FROM assessments a
    INNER JOIN skills s
        ON a.skill_id = s.skill_id
    WHERE a.assessment_id = ?
      AND a.is_active = 1
    LIMIT 1
";

$assessment_stmt = $pdo->prepare($assessment_query);
$assessment_stmt->execute([$assessment_id]);

$assessment = $assessment_stmt->fetch(PDO::FETCH_ASSOC);

if (!$assessment) {
    header("Location: skills.php");
    exit;
}

/*
 * Fetch questions
 */
$question_query = "
    SELECT
        question_id,
        question_text,
        option_a,
        option_b,
        option_c,
        option_d,
        marks
    FROM questions
    WHERE assessment_id = ?
    ORDER BY question_id ASC
";

$question_stmt = $pdo->prepare($question_query);
$question_stmt->execute([$assessment_id]);

$questions = $question_stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($questions) === 0) {
    header(
        "Location: assessment.php?skill_id=" .
        urlencode($assessment_id)
    );
    exit;
}

/*
 * Create assessment attempt
 */
$attempt_query = "
    INSERT INTO assessment_attempts
    (
        user_id,
        assessment_id,
        started_at,
        status
    )
    VALUES (?, ?, NOW(), 'in_progress')
";

$attempt_stmt = $pdo->prepare($attempt_query);

$attempt_stmt->execute([
    $_SESSION["user_id"],
    $assessment_id
]);

$attempt_id = $pdo->lastInsertId();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($assessment["title"]); ?>
    </title>

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
            max-width: 900px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .assessment-header {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
            margin-bottom: 25px;
        }

        .assessment-header h1 {
            margin-top: 0;
        }

        .assessment-header p {
            color: #6b7280;
            line-height: 1.6;
        }

        .details {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }

        .detail {
            background: #eff6ff;
            color: #1d4ed8;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 13px;
        }

        .question-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
            margin-bottom: 20px;
        }

        .question-number {
            color: #2563eb;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .question-text {
            font-size: 17px;
            font-weight: bold;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .option {
            display: block;
            padding: 12px 15px;
            margin-bottom: 10px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.2s;
        }

        .option:hover {
            background: #f9fafb;
            border-color: #2563eb;
        }

        .option input {
            margin-right: 10px;
        }

        .submit-section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
            text-align: center;
        }

        .submit-button {
            background: #2563eb;
            color: white;
            border: none;
            padding: 13px 30px;
            border-radius: 7px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .submit-button:hover {
            background: #1d4ed8;
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

    <div class="assessment-header">

        <h1>
            <?php echo htmlspecialchars($assessment["title"]); ?>
        </h1>

        <p>
            <?php
            echo htmlspecialchars(
                $assessment["description"]
                ?? "Complete this assessment to evaluate your skills."
            );
            ?>
        </p>

        <div class="details">

            <span class="detail">
                💻 <?php echo htmlspecialchars($assessment["skill_name"]); ?>
            </span>

            <span class="detail">
                ⏱️ <?php echo (int) $assessment["duration_minutes"]; ?> minutes
            </span>

            <span class="detail">
                📝 <?php echo count($questions); ?> questions
            </span>

            <span class="detail">
                🎯 Pass: <?php echo htmlspecialchars($assessment["passing_percentage"]); ?>%
            </span>

        </div>

    </div>

    <form
        method="POST"
        action="submit_assessment.php"
    >

        <input
            type="hidden"
            name="attempt_id"
            value="<?php echo htmlspecialchars($attempt_id); ?>"
        >

        <?php foreach ($questions as $index => $question): ?>

            <div class="question-card">

                <div class="question-number">
                    Question <?php echo $index + 1; ?>
                </div>

                <div class="question-text">

                    <?php
                    echo htmlspecialchars(
                        $question["question_text"]
                    );
                    ?>

                </div>

                <label class="option">

                    <input
                        type="radio"
                        name="answers[<?php echo $question["question_id"]; ?>]"
                        value="A"
                        required
                    >

                    A.
                    <?php
                    echo htmlspecialchars(
                        $question["option_a"]
                    );
                    ?>

                </label>

                <label class="option">

                    <input
                        type="radio"
                        name="answers[<?php echo $question["question_id"]; ?>]"
                        value="B"
                    >

                    B.
                    <?php
                    echo htmlspecialchars(
                        $question["option_b"]
                    );
                    ?>

                </label>

                <label class="option">

                    <input
                        type="radio"
                        name="answers[<?php echo $question["question_id"]; ?>]"
                        value="C"
                    >

                    C.
                    <?php
                    echo htmlspecialchars(
                        $question["option_c"]
                    );
                    ?>

                </label>

                <label class="option">

                    <input
                        type="radio"
                        name="answers[<?php echo $question["question_id"]; ?>]"
                        value="D"
                    >

                    D.
                    <?php
                    echo htmlspecialchars(
                        $question["option_d"]
                    );
                    ?>

                </label>

            </div>

        <?php endforeach; ?>

        <div class="submit-section">

            <button
                type="submit"
                class="submit-button"
            >
                Submit Assessment
            </button>

        </div>

    </form>

</div>

</body>

</html>