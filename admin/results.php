<?php

require_once "../includes/auth.php";
require_once "../config/database.php";

requireAdmin();

$sql = "
    SELECT
        r.result_id,
        u.full_name,
        u.email,
        a.title AS assessment_title,
        s.skill_name,
        r.total_marks,
        r.obtained_marks,
        r.percentage,
        r.result_status,
        r.completed_at
    FROM results r
    INNER JOIN users u
        ON r.user_id = u.user_id
    INNER JOIN assessments a
        ON r.assessment_id = a.assessment_id
    INNER JOIN skills s
        ON a.skill_id = s.skill_id
    ORDER BY r.completed_at DESC
";

$stmt = $pdo->query($sql);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Results | Student Skill Portal</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #172033;
        }

        .navbar {
            background: #2563eb;
            color: white;
            padding: 20px 44px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            font-size: 24px;
            font-weight: bold;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .logout {
            background: rgba(255,255,255,0.15);
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
        }

        .container {
            max-width: 1250px;
            margin: 42px auto;
            padding: 0 24px;
        }

        .header-card {
            background: white;
            padding: 32px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.06);
            margin-bottom: 28px;
        }

        .header-card h1 {
            margin: 0 0 10px;
            font-size: 32px;
        }

        .header-card p {
            margin: 0 0 20px;
            color: #64748b;
            font-size: 17px;
        }

        .back-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
        }

        .table-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        }

        .table-title {
            padding: 24px 28px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 22px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f8fafc;
            text-align: left;
            padding: 16px;
            font-size: 14px;
            color: #475569;
        }

        td {
            padding: 16px;
            border-top: 1px solid #eef2f7;
            font-size: 14px;
        }

        tr:hover {
            background: #f8fbff;
        }

        .student-name {
            font-weight: bold;
            color: #172033;
        }

        .email {
            color: #64748b;
            margin-top: 4px;
        }

        .skill {
            color: #2563eb;
            font-weight: bold;
        }

        .status {
            display: inline-block;
            padding: 7px 13px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 13px;
        }

        .pass {
            background: #dcfce7;
            color: #15803d;
        }

        .fail {
            background: #fee2e2;
            color: #b91c1c;
        }

        .percentage {
            font-weight: bold;
            font-size: 16px;
        }

        .empty {
            text-align: center;
            padding: 60px 20px;
            color: #64748b;
        }

        @media (max-width: 900px) {
            .table-card {
                overflow-x: auto;
            }

            table {
                min-width: 1000px;
            }
        }
    </style>
</head>

<body>

<nav class="navbar">

    <div class="brand">
        Student Skill Portal
    </div>

    <div class="admin-info">
        <span>👤 <?= htmlspecialchars($_SESSION['full_name'] ?? 'Administrator') ?></span>

        <a href="../auth/logout.php" class="logout">
            Logout
        </a>
    </div>

</nav>

<div class="container">

    <div class="header-card">

        <h1>📊 Assessment Results</h1>

        <p>
            View student assessment performance, scores, percentages,
            and pass/fail status.
        </p>

        <a href="dashboard.php" class="back-link">
            ← Back to Dashboard
        </a>

    </div>

    <div class="table-card">

        <div class="table-title">
            Results (<?= count($results) ?>)
        </div>

        <?php if (count($results) > 0): ?>

            <table>

                <thead>

                    <tr>
                        <th>Student</th>
                        <th>Assessment</th>
                        <th>Skill</th>
                        <th>Score</th>
                        <th>Percentage</th>
                        <th>Status</th>
                        <th>Completed On</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($results as $result): ?>

                        <tr>

                            <td>
                                <div class="student-name">
                                    <?= htmlspecialchars($result['full_name']) ?>
                                </div>

                                <div class="email">
                                    <?= htmlspecialchars($result['email']) ?>
                                </div>
                            </td>

                            <td>
                                <?= htmlspecialchars($result['assessment_title']) ?>
                            </td>

                            <td class="skill">
                                <?= htmlspecialchars($result['skill_name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($result['obtained_marks']) ?>
                                /
                                <?= htmlspecialchars($result['total_marks']) ?>
                            </td>

                            <td class="percentage">
                                <?= number_format((float)$result['percentage'], 2) ?>%
                            </td>

                            <td>

                                <?php if ($result['result_status'] === 'Pass'): ?>

                                    <span class="status pass">
                                        ✓ Pass
                                    </span>

                                <?php else: ?>

                                    <span class="status fail">
                                        ✕ Fail
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>
                                <?= date(
                                    'd M Y, h:i A',
                                    strtotime($result['completed_at'])
                                ) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div class="empty">

                <h3>No Results Available</h3>

                <p>
                    Student assessment results will appear here
                    after an assessment is completed.
                </p>

            </div>

        <?php endif; ?>

    </div>

</div>

</body>
</html>