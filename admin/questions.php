<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../auth/login.php");
    exit;
}

$assessment_id = filter_input(INPUT_GET, "assessment_id", FILTER_VALIDATE_INT);

if (!$assessment_id) {
    die("Invalid assessment ID.");
}

/* Fetch assessment details */
$stmt = $pdo->prepare("
    SELECT 
        a.assessment_id,
        a.title,
        a.description,
        a.duration_minutes,
        a.passing_percentage,
        a.total_questions,
        a.is_active,
        s.skill_name
    FROM assessments a
    INNER JOIN skills s ON a.skill_id = s.skill_id
    WHERE a.assessment_id = ?
");

$stmt->execute([$assessment_id]);
$assessment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$assessment) {
    die("Assessment not found.");
}

$message = "";
$message_type = "";

/* Add question */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_question"])) {

    $question_text = trim($_POST["question_text"] ?? "");
    $option_a = trim($_POST["option_a"] ?? "");
    $option_b = trim($_POST["option_b"] ?? "");
    $option_c = trim($_POST["option_c"] ?? "");
    $option_d = trim($_POST["option_d"] ?? "");
    $correct_option = $_POST["correct_option"] ?? "";
    $marks = (float)($_POST["marks"] ?? 1);

    if (
        $question_text === "" ||
        $option_a === "" ||
        $option_b === "" ||
        $option_c === "" ||
        $option_d === "" ||
        !in_array($correct_option, ["A", "B", "C", "D"], true)
    ) {
        $message = "Please fill in all question fields.";
        $message_type = "error";
    } else {

        $stmt = $pdo->prepare("
            INSERT INTO questions
            (
                assessment_id,
                question_text,
                option_a,
                option_b,
                option_c,
                option_d,
                correct_option,
                marks
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $assessment_id,
            $question_text,
            $option_a,
            $option_b,
            $option_c,
            $option_d,
            $correct_option,
            $marks
        ]);

        /* Update total question count */
        $stmt = $pdo->prepare("
            UPDATE assessments
            SET total_questions = (
                SELECT COUNT(*)
                FROM questions
                WHERE assessment_id = ?
            )
            WHERE assessment_id = ?
        ");

        $stmt->execute([$assessment_id, $assessment_id]);

        header(
            "Location: questions.php?assessment_id=" .
            $assessment_id .
            "&success=added"
        );
        exit;
    }
}

/* Delete question */
if (isset($_GET["delete"])) {

    $question_id = filter_input(
        INPUT_GET,
        "delete",
        FILTER_VALIDATE_INT
    );

    if ($question_id) {

        $stmt = $pdo->prepare("
            DELETE FROM questions
            WHERE question_id = ?
            AND assessment_id = ?
        ");

        $stmt->execute([
            $question_id,
            $assessment_id
        ]);

        $stmt = $pdo->prepare("
            UPDATE assessments
            SET total_questions = (
                SELECT COUNT(*)
                FROM questions
                WHERE assessment_id = ?
            )
            WHERE assessment_id = ?
        ");

        $stmt->execute([
            $assessment_id,
            $assessment_id
        ]);

        header(
            "Location: questions.php?assessment_id=" .
            $assessment_id .
            "&success=deleted"
        );
        exit;
    }
}

/* Success messages */
if (isset($_GET["success"])) {

    if ($_GET["success"] === "added") {
        $message = "Question added successfully.";
        $message_type = "success";
    }

    if ($_GET["success"] === "deleted") {
        $message = "Question deleted successfully.";
        $message_type = "success";
    }
}

/* Fetch questions */
$stmt = $pdo->prepare("
    SELECT *
    FROM questions
    WHERE assessment_id = ?
    ORDER BY question_id ASC
");

$stmt->execute([$assessment_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Manage Questions | Student Skill Portal
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f7fb;
            color: #172033;
        }

        .navbar {
            background: #2864e6;
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            font-size: 24px;
            font-weight: 700;
        }

        .admin-area {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .logout {
            background: rgba(255,255,255,0.15);
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 8px;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        }

        h1 {
            margin-top: 0;
            font-size: 32px;
        }

        h2 {
            margin-top: 0;
        }

        .subtitle {
            color: #64748b;
            font-size: 17px;
        }

        .assessment-info {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-top: 25px;
        }

        .info-box {
            background: #f7f9fc;
            padding: 18px;
            border-radius: 10px;
        }

        .info-label {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 6px;
        }

        .info-value {
            font-size: 18px;
            font-weight: 700;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            font-weight: 700;
            margin-bottom: 8px;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 13px 15px;
            border: 1px solid #d5dce7;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #2864e6;
        }

        .btn {
            border: none;
            background: #2864e6;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn:hover {
            background: #1f54c7;
        }

        .btn-danger {
            background: #ffe1e1;
            color: #b42318;
            text-decoration: none;
            padding: 8px 13px;
            border-radius: 7px;
            font-weight: 700;
        }

        .btn-danger:hover {
            background: #ffd0d0;
        }

        .back-link {
            color: #2864e6;
            text-decoration: none;
            font-weight: 700;
        }

        .alert {
            padding: 15px 18px;
            border-radius: 9px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .success {
            background: #dff8e7;
            color: #087a38;
        }

        .error {
            background: #ffe1e1;
            color: #b42318;
        }

        .question-card {
            border: 1px solid #e1e6ef;
            border-radius: 12px;
            padding: 22px;
            margin-bottom: 18px;
        }

        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }

        .question-number {
            color: #2864e6;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .question-text {
            font-size: 17px;
            font-weight: 700;
            line-height: 1.5;
        }

        .options {
            margin-top: 18px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .option {
            background: #f7f9fc;
            padding: 12px;
            border-radius: 8px;
        }

        .correct {
            background: #dff8e7;
            color: #087a38;
            font-weight: 700;
        }

        .empty {
            text-align: center;
            padding: 40px;
            color: #64748b;
        }

        @media (max-width: 800px) {

            .assessment-info,
            .form-grid,
            .options {
                grid-template-columns: 1fr;
            }

            .navbar {
                padding: 18px 20px;
            }

            .admin-area {
                gap: 8px;
            }

            .container {
                margin-top: 25px;
            }

        }

    </style>

</head>

<body>

<nav class="navbar">

    <div class="brand">
        Student Skill Portal
    </div>

    <div class="admin-area">
        <span>👤 Portal Administrator</span>
        <a class="logout" href="../auth/logout.php">
            Logout
        </a>
    </div>

</nav>

<div class="container">

    <div class="card">

        <h1>📝 Manage Questions</h1>

        <p class="subtitle">
            Add and manage questions for this assessment.
        </p>

        <br>

        <a class="back-link"
           href="assessments.php">
            ← Back to Assessments
        </a>

        <div class="assessment-info">

            <div class="info-box">
                <div class="info-label">
                    Assessment
                </div>

                <div class="info-value">
                    <?= htmlspecialchars($assessment["title"]) ?>
                </div>
            </div>

            <div class="info-box">
                <div class="info-label">
                    Skill
                </div>

                <div class="info-value">
                    <?= htmlspecialchars($assessment["skill_name"]) ?>
                </div>
            </div>

            <div class="info-box">
                <div class="info-label">
                    Duration
                </div>

                <div class="info-value">
                    <?= (int)$assessment["duration_minutes"] ?> min
                </div>
            </div>

            <div class="info-box">
                <div class="info-label">
                    Passing
                </div>

                <div class="info-value">
                    <?= number_format(
                        (float)$assessment["passing_percentage"],
                        0
                    ) ?>%
                </div>
            </div>

        </div>

    </div>


    <?php if ($message !== ""): ?>

        <div class="alert <?= $message_type ?>">
            <?= htmlspecialchars($message) ?>
        </div>

    <?php endif; ?>


    <div class="card">

        <h2>➕ Add New Question</h2>

        <form method="POST">

            <div class="form-grid">

                <div class="full-width">

                    <label>
                        Question
                    </label>

                    <textarea
                        name="question_text"
                        placeholder="Enter the question..."
                        required
                    ></textarea>

                </div>


                <div>

                    <label>
                        Option A
                    </label>

                    <input
                        type="text"
                        name="option_a"
                        placeholder="Enter option A"
                        required
                    >

                </div>


                <div>

                    <label>
                        Option B
                    </label>

                    <input
                        type="text"
                        name="option_b"
                        placeholder="Enter option B"
                        required
                    >

                </div>


                <div>

                    <label>
                        Option C
                    </label>

                    <input
                        type="text"
                        name="option_c"
                        placeholder="Enter option C"
                        required
                    >

                </div>


                <div>

                    <label>
                        Option D
                    </label>

                    <input
                        type="text"
                        name="option_d"
                        placeholder="Enter option D"
                        required
                    >

                </div>


                <div>

                    <label>
                        Correct Option
                    </label>

                    <select name="correct_option" required>

                        <option value="">
                            Select correct option
                        </option>

                        <option value="A">
                            A
                        </option>

                        <option value="B">
                            B
                        </option>

                        <option value="C">
                            C
                        </option>

                        <option value="D">
                            D
                        </option>

                    </select>

                </div>


                <div>

                    <label>
                        Marks
                    </label>

                    <input
                        type="number"
                        name="marks"
                        value="1"
                        min="0.5"
                        step="0.5"
                        required
                    >

                </div>

            </div>

            <br>

            <button
                type="submit"
                name="add_question"
                class="btn"
            >
                Add Question
            </button>

        </form>

    </div>


    <div class="card">

        <h2>
            Questions
            (<?= count($questions) ?>)
        </h2>


        <?php if (count($questions) === 0): ?>

            <div class="empty">
                No questions have been added yet.
            </div>

        <?php else: ?>

            <?php foreach ($questions as $index => $question): ?>

                <div class="question-card">

                    <div class="question-header">

                        <div>

                            <div class="question-number">
                                Question <?= $index + 1 ?>
                            </div>

                            <div class="question-text">
                                <?= htmlspecialchars(
                                    $question["question_text"]
                                ) ?>
                            </div>

                        </div>

                        <a
                            class="btn-danger"
                            href="questions.php?assessment_id=<?= $assessment_id ?>&delete=<?= $question["question_id"] ?>"
                            onclick="return confirm('Are you sure you want to delete this question?');"
                        >
                            Delete
                        </a>

                    </div>


                    <div class="options">

                        <div class="option <?= $question["correct_option"] === "A" ? "correct" : "" ?>">
                            <strong>A.</strong>
                            <?= htmlspecialchars($question["option_a"]) ?>
                        </div>

                        <div class="option <?= $question["correct_option"] === "B" ? "correct" : "" ?>">
                            <strong>B.</strong>
                            <?= htmlspecialchars($question["option_b"]) ?>
                        </div>

                        <div class="option <?= $question["correct_option"] === "C" ? "correct" : "" ?>">
                            <strong>C.</strong>
                            <?= htmlspecialchars($question["option_c"]) ?>
                        </div>

                        <div class="option <?= $question["correct_option"] === "D" ? "correct" : "" ?>">
                            <strong>D.</strong>
                            <?= htmlspecialchars($question["option_d"]) ?>
                        </div>

                    </div>

                    <br>

                    <strong>
                        Marks:
                    </strong>

                    <?= htmlspecialchars($question["marks"]) ?>

                    &nbsp; | &nbsp;

                    <strong>
                        Correct Answer:
                    </strong>

                    <?= htmlspecialchars($question["correct_option"]) ?>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</div>

</body>
</html>