<?php

require_once "../includes/auth.php";
require_once "../config/database.php";

requireAdmin();

$sql = "
    SELECT
        c.certificate_id,
        c.certificate_number,
        c.certificate_title,
        c.issue_date,
        c.verification_code,
        c.certificate_file,
        u.full_name,
        u.email,
        s.skill_name
    FROM certificates c
    INNER JOIN users u
        ON c.user_id = u.user_id
    INNER JOIN results r
        ON c.result_id = r.result_id
    INNER JOIN assessments a
        ON r.assessment_id = a.assessment_id
    INNER JOIN skills s
        ON a.skill_id = s.skill_id
    ORDER BY c.issue_date DESC, c.certificate_id DESC
";

$stmt = $pdo->query($sql);
$certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Certificates | Student Skill Portal</title>

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
            white-space: nowrap;
        }

        td {
            padding: 16px;
            border-top: 1px solid #eef2f7;
            font-size: 14px;
            vertical-align: middle;
        }

        tr:hover {
            background: #f8fbff;
        }

        .student-name {
            font-weight: bold;
            color: #172033;
            margin-bottom: 4px;
        }

        .email {
            color: #64748b;
            font-size: 13px;
        }

        .certificate-number {
            font-weight: bold;
            color: #2563eb;
        }

        .skill {
            font-weight: bold;
            color: #334155;
        }

        .verification-code {
            display: inline-block;
            background: #f1f5f9;
            color: #334155;
            padding: 7px 10px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 12px;
        }

        .view-btn {
            display: inline-block;
            background: #2563eb;
            color: white;
            text-decoration: none;
            padding: 9px 14px;
            border-radius: 7px;
            font-weight: bold;
            font-size: 13px;
        }

        .view-btn:hover {
            background: #1d4ed8;
        }

        .empty {
            text-align: center;
            padding: 60px 20px;
            color: #64748b;
        }

        .empty-icon {
            font-size: 45px;
            margin-bottom: 10px;
        }

        @media (max-width: 900px) {

            .table-card {
                overflow-x: auto;
            }

            table {
                min-width: 1100px;
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

        <span>
            👤 <?= htmlspecialchars($_SESSION['full_name'] ?? 'Administrator') ?>
        </span>

        <a href="../auth/logout.php" class="logout">
            Logout
        </a>

    </div>

</nav>


<div class="container">

    <div class="header-card">

        <h1>🏆 Certificate Management</h1>

        <p>
            View and manage certificates issued to students
            after successfully completing their assessments.
        </p>

        <a href="dashboard.php" class="back-link">
            ← Back to Dashboard
        </a>

    </div>


    <div class="table-card">

        <div class="table-title">
            Certificates (<?= count($certificates) ?>)
        </div>


        <?php if (count($certificates) > 0): ?>

            <table>

                <thead>

                    <tr>

                        <th>Student</th>

                        <th>Certificate Number</th>

                        <th>Certificate Title</th>

                        <th>Skill</th>

                        <th>Issue Date</th>

                        <th>Verification Code</th>

                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                    <?php foreach ($certificates as $certificate): ?>

                        <tr>

                            <td>

                                <div class="student-name">
                                    <?= htmlspecialchars($certificate['full_name']) ?>
                                </div>

                                <div class="email">
                                    <?= htmlspecialchars($certificate['email']) ?>
                                </div>

                            </td>


                            <td>

                                <span class="certificate-number">
                                    <?= htmlspecialchars($certificate['certificate_number']) ?>
                                </span>

                            </td>


                            <td>
                                <?= htmlspecialchars($certificate['certificate_title']) ?>
                            </td>


                            <td>

                                <span class="skill">
                                    <?= htmlspecialchars($certificate['skill_name']) ?>
                                </span>

                            </td>


                            <td>

                                <?= date(
                                    'd M Y',
                                    strtotime($certificate['issue_date'])
                                ) ?>

                            </td>


                            <td>

                                <span class="verification-code">
                                    <?= htmlspecialchars($certificate['verification_code']) ?>
                                </span>

                            </td>


                            <td>

                                <?php if (!empty($certificate['certificate_file'])): ?>

                                    <a
                                        href="../certificates/<?= htmlspecialchars($certificate['certificate_file']) ?>"
                                        class="view-btn"
                                        target="_blank"
                                    >
                                        View Certificate
                                    </a>

                                <?php else: ?>

                                    <span style="color:#64748b;">
                                        Not Generated
                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div class="empty">

                <div class="empty-icon">
                    🏆
                </div>

                <h3>No Certificates Issued</h3>

                <p>
                    Certificates will appear here when students
                    successfully complete eligible assessments.
                </p>

            </div>

        <?php endif; ?>

    </div>

</div>

</body>

</html>