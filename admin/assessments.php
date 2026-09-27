<?php

require_once "../includes/auth.php";
require_once "../config/database.php";

requireAdmin();

$message = "";
$error = "";

/* -----------------------------
   Add Assessment
------------------------------ */

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_assessment"])) {

    $skill_id = (int) $_POST["skill_id"];
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $duration_minutes = (int) $_POST["duration_minutes"];
    $passing_percentage = (float) $_POST["passing_percentage"];

    if (
        $skill_id <= 0 ||
        empty($title) ||
        $duration_minutes <= 0 ||
        $passing_percentage < 0 ||
        $passing_percentage > 100
    ) {
        $error = "Please enter valid assessment details.";
    } else {

        $sql = "
            INSERT INTO assessments
            (
                skill_id,
                title,
                description,
                duration_minutes,
                passing_percentage,
                total_questions,
                is_active
            )
            VALUES
            (
                :skill_id,
                :title,
                :description,
                :duration_minutes,
                :passing_percentage,
                0,
                1
            )
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":skill_id" => $skill_id,
            ":title" => $title,
            ":description" => $description,
            ":duration_minutes" => $duration_minutes,
            ":passing_percentage" => $passing_percentage
        ]);

        $message = "Assessment added successfully.";
    }
}

/* -----------------------------
   Toggle Assessment Status
------------------------------ */

if (isset($_GET["toggle"])) {

    $assessment_id = (int) $_GET["toggle"];

    $stmt = $pdo->prepare("
        UPDATE assessments
        SET is_active = IF(is_active = 1, 0, 1)
        WHERE assessment_id = :assessment_id
    ");

    $stmt->execute([
        ":assessment_id" => $assessment_id
    ]);

    header("Location: assessments.php?status=updated");
    exit;
}

/* -----------------------------
   Success Message
------------------------------ */

if (isset($_GET["status"]) && $_GET["status"] === "updated") {
    $message = "Assessment status updated successfully.";
}

/* -----------------------------
   Get Active Skills
------------------------------ */

$skillStmt = $pdo->query("
    SELECT skill_id, skill_name
    FROM skills
    WHERE is_active = 1
    ORDER BY skill_name ASC
");

$skills = $skillStmt->fetchAll(PDO::FETCH_ASSOC);

/* -----------------------------
   Get Assessments
------------------------------ */

$assessmentStmt = $pdo->query("
    SELECT
        a.assessment_id,
        a.title,
        a.description,
        a.duration_minutes,
        a.passing_percentage,
        a.total_questions,
        a.is_active,
        a.created_at,
        s.skill_name
    FROM assessments a
    INNER JOIN skills s
        ON a.skill_id = s.skill_id
    ORDER BY a.created_at DESC
");

$assessments = $assessmentStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Assessments | Student Skill Portal</title>

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

        .nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .admin-name {
            font-weight: bold;
        }

        .logout {
            color: white;
            text-decoration: none;
            background: rgba(255,255,255,0.15);
            padding: 9px 16px;
            border-radius: 6px;
        }

        .logout:hover {
            background: rgba(255,255,255,0.25);
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .header-card,
        .form-card,
        .table-card {
            background: white;
            border-radius: 14px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.06);
        }

        .header-card {
            padding: 30px;
            margin-bottom: 25px;
        }

        .header-card h1 {
            margin: 0 0 8px;
            font-size: 32px;
        }

        .header-card p {
            color: #64748b;
            margin-bottom: 20px;
        }

        .back {
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
        }

        .message {
            background: #dcfce7;
            color: #166534;
            padding: 15px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 15px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .form-card {
            padding: 30px;
            margin-bottom: 25px;
        }

        .form-card h2 {
            margin-top: 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 15px;
            font-family: Arial, sans-serif;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #2563eb;
        }

        .btn {
            border: none;
            background: #2563eb;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn:hover {
            background: #1d4ed8;
        }

        .table-card {
            overflow: hidden;
        }

        .table-header {
            padding: 22px 25px;
            border-bottom: 1px solid #e5e7eb;
        }

        .table-header h2 {
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 16px 18px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            background: #f8fafc;
            font-size: 14px;
        }

        td {
            vertical-align: middle;
        }

        .assessment-title {
            font-weight: bold;
        }

        .skill {
            color: #2563eb;
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .active {
            background: #dcfce7;
            color: #166534;
        }

        .inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .action {
            display: inline-block;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: bold;
        }

        .toggle {
            background: #fee2e2;
            color: #991b1b;
        }

        .activate {
            background: #dcfce7;
            color: #166534;
        }

        .questions {
            background: #dbeafe;
            color: #1d4ed8;
            margin-right: 5px;
        }

        .empty {
            text-align: center;
            padding: 40px;
            color: #64748b;
        }

        @media (max-width: 900px) {

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
            }

            .table-card {
                overflow-x: auto;
            }

            table {
                min-width: 900px;
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

    <div class="nav-right">

        <span class="admin-name">
            👤 <?php echo htmlspecialchars($_SESSION["full_name"]); ?>
        </span>

        <a href="../auth/logout.php" class="logout">
            Logout
        </a>

    </div>

</nav>

<div class="container">

    <div class="header-card">

        <h1>📝 Manage Assessments</h1>

        <p>
            Create and manage skill-based assessments for students.
        </p>

        <a href="dashboard.php" class="back">
            ← Back to Dashboard
        </a>

    </div>

    <?php if ($message): ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>

    <?php if ($error): ?>

        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>

    <div class="form-card">

        <h2>➕ Create New Assessment</h2>

        <form method="POST">

            <div class="form-grid">

                <div class="form-group">

                    <label for="skill_id">
                        Skill
                    </label>

                    <select name="skill_id" id="skill_id" required>

                        <option value="">
                            Select Skill
                        </option>

                        <?php foreach ($skills as $skill): ?>

                            <option value="<?php echo $skill["skill_id"]; ?>">

                                <?php echo htmlspecialchars($skill["skill_name"]); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="form-group">

                    <label for="title">
                        Assessment Title
                    </label>

                    <input
                        type="text"
                        name="title"
                        id="title"
                        placeholder="e.g. Python Beginner Assessment"
                        required
                    >

                </div>

                <div class="form-group full">

                    <label for="description">
                        Description
                    </label>

                    <textarea
                        name="description"
                        id="description"
                        placeholder="Describe the assessment..."
                    ></textarea>

                </div>

                <div class="form-group">

                    <label for="duration_minutes">
                        Duration (Minutes)
                    </label>

                    <input
                        type="number"
                        name="duration_minutes"
                        id="duration_minutes"
                        value="30"
                        min="1"
                        required
                    >

                </div>

                <div class="form-group">

                    <label for="passing_percentage">
                        Passing Percentage
                    </label>

                    <input
                        type="number"
                        name="passing_percentage"
                        id="passing_percentage"
                        value="40"
                        min="0"
                        max="100"
                        step="0.01"
                        required
                    >

                </div>

            </div>

            <button
                type="submit"
                name="add_assessment"
                class="btn"
            >
                Create Assessment
            </button>

        </form>

    </div>

    <div class="table-card">

        <div class="table-header">

            <h2>
                📋 Available Assessments
                (<?php echo count($assessments); ?>)
            </h2>

        </div>

        <?php if (count($assessments) > 0): ?>

            <table>

                <thead>

                    <tr>

                        <th>Assessment</th>
                        <th>Skill</th>
                        <th>Duration</th>
                        <th>Passing</th>
                        <th>Questions</th>
                        <th>Status</th>
                        <th>Created On</th>
                        <th>Action</th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($assessments as $assessment): ?>

                        <tr>

                            <td>

                                <div class="assessment-title">

                                    <?php
                                    echo htmlspecialchars(
                                        $assessment["title"]
                                    );
                                    ?>

                                </div>

                            </td>

                            <td>

                                <span class="skill">

                                    <?php
                                    echo htmlspecialchars(
                                        $assessment["skill_name"]
                                    );
                                    ?>

                                </span>

                            </td>

                            <td>
                                <?php
                                echo (int) $assessment["duration_minutes"];
                                ?>
                                min
                            </td>

                            <td>
                                <?php
                                echo number_format(
                                    $assessment["passing_percentage"],
                                    2
                                );
                                ?>%
                            </td>

                            <td>
                                <?php
                                echo (int) $assessment["total_questions"];
                                ?>
                            </td>

                            <td>

                                <?php if ($assessment["is_active"]): ?>

                                    <span class="badge active">
                                        Active
                                    </span>

                                <?php else: ?>

                                    <span class="badge inactive">
                                        Inactive
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <?php
                                echo date(
                                    "d M Y",
                                    strtotime($assessment["created_at"])
                                );
                                ?>

                            </td>

                            <td>

                                <a
                                    href="questions.php?assessment_id=<?php echo $assessment["assessment_id"]; ?>"
                                    class="action questions"
                                >
                                    Questions
                                </a>

                                <?php if ($assessment["is_active"]): ?>

                                    <a
                                        href="assessments.php?toggle=<?php echo $assessment["assessment_id"]; ?>"
                                        class="action toggle"
                                    >
                                        Deactivate
                                    </a>

                                <?php else: ?>

                                    <a
                                        href="assessments.php?toggle=<?php echo $assessment["assessment_id"]; ?>"
                                        class="action activate"
                                    >
                                        Activate
                                    </a>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div class="empty">

                No assessments have been created yet.

            </div>

        <?php endif; ?>

    </div>

</div>

</body>

</html>