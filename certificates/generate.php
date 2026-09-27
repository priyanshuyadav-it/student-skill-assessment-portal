<?php

require_once "../config/database.php";
require_once "../includes/auth.php";

requireStudent();

$userId = (int) $_SESSION["user_id"];

$resultId = filter_input(
    INPUT_GET,
    "result_id",
    FILTER_VALIDATE_INT
);

if (!$resultId) {
    die("Invalid result ID.");
}

/*
 * Get the passed result belonging to the logged-in student.
 */
$query = "
    SELECT
        r.result_id,
        r.user_id,
        r.percentage,
        r.obtained_marks,
        r.total_marks,
        r.result_status,
        r.completed_at,
        u.full_name,
        a.title AS assessment_title,
        s.skill_name
    FROM results r

    INNER JOIN users u
        ON r.user_id = u.user_id

    INNER JOIN assessments a
        ON r.assessment_id = a.assessment_id

    INNER JOIN skills s
        ON a.skill_id = s.skill_id

    WHERE r.result_id = ?
      AND r.user_id = ?
      AND r.result_status = 'Pass'

    LIMIT 1
";

$stmt = $pdo->prepare($query);

$stmt->execute([
    $resultId,
    $userId
]);

$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    die("Certificate is not available for this result.");
}

/*
 * Check whether a certificate already exists.
 */
$certificateQuery = "
    SELECT
        certificate_id,
        certificate_number,
        certificate_title,
        issue_date,
        verification_code
    FROM certificates
    WHERE result_id = ?
    LIMIT 1
";

$certificateStmt = $pdo->prepare($certificateQuery);

$certificateStmt->execute([
    $resultId
]);

$certificate = $certificateStmt->fetch(PDO::FETCH_ASSOC);

/*
 * Create certificate if it does not already exist.
 */
if (!$certificate) {

    $certificateNumber =
        "SSC-" .
        date("Y") .
        "-" .
        strtoupper(
            substr(
                bin2hex(random_bytes(5)),
                0,
                8
            )
        );

    $verificationCode =
        strtoupper(
            bin2hex(random_bytes(16))
        );

    $certificateTitle =
        $result["skill_name"] .
        " Skill Certification";

    $insertQuery = "
        INSERT INTO certificates
        (
            certificate_number,
            user_id,
            result_id,
            certificate_title,
            issue_date,
            verification_code
        )
        VALUES (?, ?, ?, ?, CURDATE(), ?)
    ";

    $insertStmt = $pdo->prepare($insertQuery);

    $insertStmt->execute([
        $certificateNumber,
        $userId,
        $resultId,
        $certificateTitle,
        $verificationCode
    ]);

    /*
     * Fetch the newly created certificate.
     */
    $certificateStmt->execute([
        $resultId
    ]);

    $certificate = $certificateStmt->fetch(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Certificate</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px;
            font-family: Georgia, "Times New Roman", serif;
            background: #f1f5f9;
            color: #111827;
        }

        .actions {
            max-width: 1000px;
            margin: 0 auto 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .button {
            display: inline-block;
            padding: 11px 18px;
            border-radius: 7px;
            text-decoration: none;
            font-family: Arial, sans-serif;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }

        .dashboard {
            background: #2563eb;
            color: white;
        }

        .print {
            background: #16a34a;
            color: white;
        }

        .certificate {
            max-width: 1000px;
            margin: auto;
            background: white;
            border: 12px solid #2563eb;
            padding: 12px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.12);
        }

        .certificate-inner {
            border: 3px solid #93c5fd;
            padding: 55px 60px;
            text-align: center;
            min-height: 650px;
            position: relative;
        }

        .top-label {
            font-family: Arial, sans-serif;
            color: #2563eb;
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        .certificate h1 {
            margin: 20px 0 5px;
            font-size: 46px;
            color: #1e3a8a;
        }

        .subtitle {
            font-family: Arial, sans-serif;
            color: #64748b;
            font-size: 17px;
        }

        .presented {
            margin-top: 45px;
            font-family: Arial, sans-serif;
            color: #64748b;
            font-size: 16px;
        }

        .student-name {
            margin: 15px 0;
            font-size: 38px;
            font-weight: bold;
            color: #111827;
        }

        .certificate-text {
            max-width: 720px;
            margin: 20px auto;
            font-family: Arial, sans-serif;
            color: #475569;
            font-size: 17px;
            line-height: 1.7;
        }

        .skill-name {
            color: #2563eb;
            font-size: 28px;
            font-weight: bold;
            margin: 20px 0;
        }

        .score {
            font-family: Arial, sans-serif;
            color: #475569;
            font-size: 15px;
        }

        .details {
            display: flex;
            justify-content: space-between;
            gap: 30px;
            margin-top: 55px;
            font-family: Arial, sans-serif;
            font-size: 13px;
        }

        .detail {
            flex: 1;
        }

        .detail-line {
            border-top: 1px solid #94a3b8;
            margin-bottom: 8px;
        }

        .detail strong {
            display: block;
            color: #111827;
        }

        .certificate-number {
            margin-top: 30px;
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #64748b;
        }

        .verification {
            margin-top: 8px;
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #94a3b8;
            word-break: break-all;
        }

        @media print {

            body {
                padding: 0;
                background: white;
            }

            .actions {
                display: none;
            }

            .certificate {
                box-shadow: none;
                max-width: none;
                width: 100%;
                border-width: 10px;
            }

        }

        @media (max-width: 700px) {

            body {
                padding: 10px;
            }

            .certificate-inner {
                padding: 35px 20px;
            }

            .certificate h1 {
                font-size: 34px;
            }

            .student-name {
                font-size: 28px;
            }

            .details {
                flex-direction: column;
                gap: 25px;
            }

        }

    </style>

</head>

<body>

<div class="actions">

    <a
        href="../student/certificates.php"
        class="button dashboard"
    >
        ← My Certificates
    </a>

    <button
        onclick="window.print()"
        class="button print"
    >
        🖨 Print / Save PDF
    </button>

</div>

<div class="certificate">

    <div class="certificate-inner">

        <div class="top-label">
            Student Skill Assessment & Certification Portal
        </div>

        <h1>
            Certificate of Achievement
        </h1>

        <div class="subtitle">
            This certificate is proudly presented to
        </div>

        <div class="presented">
            Awarded to
        </div>

        <div class="student-name">

            <?php
            echo htmlspecialchars(
                $result["full_name"]
            );
            ?>

        </div>

        <div class="certificate-text">

            For successfully completing the

            <strong>
                <?php
                echo htmlspecialchars(
                    $result["assessment_title"]
                );
                ?>
            </strong>

            and demonstrating proficiency in

        </div>

        <div class="skill-name">

            <?php
            echo htmlspecialchars(
                $result["skill_name"]
            );
            ?>

        </div>

        <div class="score">

            Assessment Score:

            <strong>
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

                &nbsp;(
                <?php
                echo htmlspecialchars(
                    $result["percentage"]
                );
                ?>%
                )
            </strong>

        </div>

        <div class="details">

            <div class="detail">

                <div class="detail-line"></div>

                <strong>
                    Issue Date
                </strong>

                <?php
                echo htmlspecialchars(
                    date(
                        "d F Y",
                        strtotime(
                            $certificate["issue_date"]
                        )
                    )
                );
                ?>

            </div>

            <div class="detail">

                <div class="detail-line"></div>

                <strong>
                    Certificate Number
                </strong>

                <?php
                echo htmlspecialchars(
                    $certificate["certificate_number"]
                );
                ?>

            </div>

        </div>

        <div class="certificate-number">

            Certificate issued by
            <strong>
                Student Skill Assessment & Certification Portal
            </strong>

        </div>

        <div class="verification">

            Verification Code:
            <?php
            echo htmlspecialchars(
                $certificate["verification_code"]
            );
            ?>

        </div>

    </div>

</div>

</body>

</html>