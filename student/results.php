<?php

require_once "../config/database.php";
require_once "../includes/auth.php";

requireStudent();

/*
 * Fetch all assessment results for the logged-in student.
 */
$query = "
    SELECT
        r.result_id,
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
    WHERE r.user_id = ?
    ORDER BY r.completed_at DESC
";

$stmt = $pdo->prepare($query);

$stmt->execute([
    $_SESSION["user_id"]
]);

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Results</title>

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

        .results-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            background: #f8fafc;
            font-size: 14px;
            color: #374151;
        }

        td {
            font-size: 14px;
        }

        tbody tr:hover {
            background: #f9fafb;
        }

        .assessment-title {
            font-weight: bold;
            color: #111827;
        }

        .skill-name {
            color: #6b7280;
            margin-top: 4px;
            font-size: 13px;
        }

        .percentage {
            font-weight: bold;
            color: #2563eb;
        }

        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .status-pass {
            background: #dcfce7;
            color: #166534;
        }

        .status-fail {
            background: #fee2e2;
            color: #991b1b;
        }

        .view-button {
            display: inline-block;
            padding: 7px 12px;
            background: #eff6ff;
            color: #2563eb;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 13px;
        }

        .view-button:hover {
            background: #dbeafe;
        }

        .empty-state {
            background: white;
            padding: 50px 25px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
        }

        .empty-state h2 {
            margin-top: 0;
        }

        .empty-state p {
            color: #6b7280;
            margin-bottom: 25px;
        }

        .primary-button {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 11px 18px;
            border-radius: 7px;
            text-decoration: none;
            font-weight: bold;
        }

        .primary-button:hover {
            background: #1d4ed8;
        }

        @media (max-width: 700px) {

            .navbar {
                padding: 15px 20px;
            }

            .container {
                margin-top: 25px;
            }

            th,
            td {
                padding: 12px;
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

        <h1>My Assessment Results</h1>

        <p>
            View your previous assessment attempts and performance.
        </p>

    </div>

    <?php if (count($results) > 0): ?>

        <div class="results-card">

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>Assessment</th>

                            <th>Score</th>

                            <th>Percentage</th>

                            <th>Status</th>

                            <th>Completed On</th>

                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($results as $result): ?>

                            <tr>

                                <td>

                                    <div class="assessment-title">

                                        <?php
                                        echo htmlspecialchars(
                                            $result["assessment_title"]
                                        );
                                        ?>

                                    </div>

                                    <div class="skill-name">

                                        <?php
                                        echo htmlspecialchars(
                                            $result["skill_name"]
                                        );
                                        ?>

                                    </div>

                                </td>

                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $result["obtained_marks"]
                                    );
                                    ?>

                                    /

                                    <?php
                                    echo htmlspecialchars(
                                        $result["total_marks"]
                                    );
                                    ?>

                                </td>

                                <td class="percentage">

                                    <?php
                                    echo htmlspecialchars(
                                        $result["percentage"]
                                    );
                                    ?>%

                                </td>

                                <td>

                                    <span
                                        class="status
                                        <?php
                                        echo $result["result_status"] === "Pass"
                                            ? "status-pass"
                                            : "status-fail";
                                        ?>"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $result["result_status"]
                                        );
                                        ?>

                                    </span>

                                </td>

                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        date(
                                            "d M Y, h:i A",
                                            strtotime(
                                                $result["completed_at"]
                                            )
                                        )
                                    );
                                    ?>

                                </td>

                                <td>

                                    <a
                                        href="result.php?result_id=<?php echo (int) $result["result_id"]; ?>"
                                        class="view-button"
                                    >
                                        View Result
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    <?php else: ?>

        <div class="empty-state">

            <h2>No Results Yet</h2>

            <p>
                You have not completed any assessments yet.
            </p>

            <a
                href="skills.php"
                class="primary-button"
            >
                Explore Skills
            </a>

        </div>

    <?php endif; ?>

</div>

</body>

</html>