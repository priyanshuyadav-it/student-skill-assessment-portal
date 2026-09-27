<?php

require_once "../config/database.php";
require_once "../includes/auth.php";

requireStudent();

/*
 * Fetch certificates and certificate-eligible results
 * belonging only to the logged-in student.
 */

$query = "
    SELECT
        r.result_id,
        r.obtained_marks,
        r.total_marks,
        r.percentage,
        r.result_status,
        r.completed_at,
        a.title AS assessment_title,
        s.skill_name,
        c.certificate_id,
        c.certificate_number,
        c.certificate_title,
        c.issue_date,
        c.verification_code,
        c.certificate_file
    FROM results r

    INNER JOIN assessments a
        ON r.assessment_id = a.assessment_id

    INNER JOIN skills s
        ON a.skill_id = s.skill_id

    LEFT JOIN certificates c
        ON r.result_id = c.result_id

    WHERE r.user_id = ?
      AND r.result_status = 'Pass'

    ORDER BY r.completed_at DESC
";

$stmt = $pdo->prepare($query);

$stmt->execute([
    $_SESSION["user_id"]
]);

$certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Certificates</title>

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

        .certificate-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .certificate-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
        }

        .certificate-icon {
            font-size: 38px;
            margin-bottom: 10px;
        }

        .certificate-card h2 {
            margin: 5px 0 10px;
            font-size: 21px;
        }

        .skill {
            color: #2563eb;
            font-weight: bold;
            margin-bottom: 18px;
        }

        .details {
            background: #f8fafc;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 7px 0;
            font-size: 14px;
        }

        .detail-label {
            color: #6b7280;
        }

        .detail-value {
            font-weight: bold;
            text-align: right;
        }

        .eligible {
            display: inline-block;
            background: #dcfce7;
            color: #166534;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .certificate-number {
            color: #374151;
            font-size: 13px;
            margin-top: 12px;
        }

        .button {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 16px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 7px;
            font-weight: bold;
        }

        .button:hover {
            background: #1d4ed8;
        }

        .secondary-button {
            background: #eff6ff;
            color: #2563eb;
            margin-left: 8px;
        }

        .secondary-button:hover {
            background: #dbeafe;
        }

        .empty-state {
            background: white;
            padding: 55px 25px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
        }

        .empty-state .icon {
            font-size: 50px;
        }

        .empty-state h2 {
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #6b7280;
            margin-bottom: 25px;
        }

        @media (max-width: 768px) {

            .certificate-grid {
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

        <h1>🏆 My Certificates</h1>

        <p>
            View your earned certificates and certificate eligibility.
        </p>

    </div>


    <?php if (count($certificates) > 0): ?>

        <div class="certificate-grid">

            <?php foreach ($certificates as $certificate): ?>

                <div class="certificate-card">

                    <div class="certificate-icon">
                        🏆
                    </div>


                    <h2>

                        <?php

                        echo htmlspecialchars(
                            $certificate["assessment_title"]
                        );

                        ?>

                    </h2>


                    <div class="skill">

                        Skill:

                        <?php

                        echo htmlspecialchars(
                            $certificate["skill_name"]
                        );

                        ?>

                    </div>


                    <span class="eligible">
                        ✓ Certificate Eligible
                    </span>


                    <div class="details">

                        <div class="detail-row">

                            <span class="detail-label">
                                Score
                            </span>

                            <span class="detail-value">

                                <?php

                                echo htmlspecialchars(
                                    $certificate["obtained_marks"]
                                );

                                ?>

                                /

                                <?php

                                echo htmlspecialchars(
                                    $certificate["total_marks"]
                                );

                                ?>

                            </span>

                        </div>


                        <div class="detail-row">

                            <span class="detail-label">
                                Percentage
                            </span>

                            <span class="detail-value">

                                <?php

                                echo htmlspecialchars(
                                    $certificate["percentage"]
                                );

                                ?>%

                            </span>

                        </div>


                        <div class="detail-row">

                            <span class="detail-label">
                                Status
                            </span>

                            <span class="detail-value">
                                Pass
                            </span>

                        </div>

                    </div>


                    <?php if ($certificate["certificate_id"]): ?>

                        <div class="certificate-number">

                            Certificate No:

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $certificate["certificate_number"]
                                );

                                ?>

                            </strong>

                        </div>


                        <!-- Verify Certificate -->

                        <a
                            href="../certificates/verify.php?code=<?php echo urlencode($certificate["verification_code"]); ?>"
                            class="button"
                        >
                            Verify Certificate
                        </a>


                        <!-- View Certificate -->

                        <a
                            href="../certificates/view.php?certificate=<?php echo urlencode($certificate["certificate_number"]); ?>"
                            class="button secondary-button"
                        >
                            View Certificate
                        </a>


                    <?php else: ?>

                        <div class="certificate-number">

                            Your certificate has not been generated yet.

                        </div>


                        <!-- Generate Certificate -->

                        <a
                            href="../certificates/generate.php?result_id=<?php echo (int) $certificate["result_id"]; ?>"
                            class="button"
                        >
                            Generate Certificate
                        </a>

                    <?php endif; ?>


                    <!-- View Result -->

                    <a
                        href="result.php?result_id=<?php echo (int) $certificate["result_id"]; ?>"
                        class="button secondary-button"
                    >
                        View Result
                    </a>


                </div>

            <?php endforeach; ?>

        </div>


    <?php else: ?>


        <div class="empty-state">

            <div class="icon">
                🏆
            </div>


            <h2>
                No Certificates Available
            </h2>


            <p>
                Complete and pass an assessment to become eligible
                for a certificate.
            </p>


            <a
                href="skills.php"
                class="button"
            >
                Explore Skills
            </a>

        </div>


    <?php endif; ?>


</div>

</body>

</html>