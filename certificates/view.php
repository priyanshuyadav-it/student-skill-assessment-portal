<?php

require_once "../config/database.php";

$certificateNumber = $_GET['certificate'] ?? '';

if (empty($certificateNumber)) {
    die("Invalid certificate request.");
}

$sql = "
    SELECT
        c.certificate_number,
        c.certificate_title,
        c.issue_date,
        c.verification_code,
        u.full_name,
        s.skill_name,
        a.title AS assessment_title,
        r.obtained_marks,
        r.total_marks,
        r.percentage,
        r.result_status
    FROM certificates c
    INNER JOIN users u
        ON c.user_id = u.user_id
    INNER JOIN results r
        ON c.result_id = r.result_id
    INNER JOIN assessments a
        ON r.assessment_id = a.assessment_id
    INNER JOIN skills s
        ON a.skill_id = s.skill_id
    WHERE c.certificate_number = ?
    LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$certificateNumber]);

$certificate = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$certificate) {
    die("Certificate not found.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars($certificate['certificate_title']) ?>
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 40px;
            background: #eef3f8;
            font-family: Arial, Helvetica, sans-serif;
            color: #172033;
        }

        .certificate-wrapper {
            max-width: 1100px;
            margin: 0 auto;
        }

        .certificate {
            background: #ffffff;
            border: 12px solid #2563eb;
            padding: 12px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        }

        .certificate-inner {
            border: 3px solid #d4af37;
            min-height: 650px;
            padding: 55px;
            text-align: center;
            position: relative;
        }

        .top-title {
            font-size: 18px;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 18px;
        }

        h1 {
            margin: 0;
            font-family: Georgia, serif;
            font-size: 48px;
            color: #1e3a8a;
        }

        .subtitle {
            margin-top: 12px;
            font-size: 20px;
            color: #475569;
        }

        .presented {
            margin-top: 45px;
            font-size: 17px;
            color: #64748b;
        }

        .student-name {
            margin: 15px 0;
            font-family: Georgia, serif;
            font-size: 42px;
            font-weight: bold;
            color: #111827;
        }

        .line {
            width: 55%;
            height: 2px;
            margin: 0 auto 25px;
            background: #d4af37;
        }

        .achievement {
            font-size: 18px;
            color: #475569;
            line-height: 1.7;
        }

        .skill {
            font-size: 30px;
            font-weight: bold;
            color: #2563eb;
            margin: 10px 0;
        }

        .details {
            display: flex;
            justify-content: center;
            gap: 70px;
            margin-top: 35px;
        }

        .detail-box {
            text-align: center;
        }

        .detail-label {
            font-size: 13px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .detail-value {
            margin-top: 7px;
            font-size: 17px;
            font-weight: bold;
        }

        .certificate-number {
            margin-top: 35px;
            font-size: 14px;
            color: #64748b;
        }

        .verification {
            margin-top: 8px;
            font-size: 13px;
            color: #64748b;
        }

        .actions {
            text-align: center;
            margin-top: 25px;
        }

        .btn {
            display: inline-block;
            padding: 12px 22px;
            margin: 5px;
            border-radius: 7px;
            text-decoration: none;
            font-size: 15px;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }

        .btn-print {
            background: #2563eb;
            color: white;
        }

        .btn-back {
            background: #64748b;
            color: white;
        }

        @media print {

            body {
                padding: 0;
                background: white;
            }

            .certificate-wrapper {
                max-width: none;
            }

            .certificate {
                box-shadow: none;
                width: 100%;
            }

            .actions {
                display: none;
            }

        }

    </style>
</head>

<body>

<div class="certificate-wrapper">

    <div class="certificate">

        <div class="certificate-inner">

            <div class="top-title">
                Student Skill Assessment & Certification Portal
            </div>

            <h1>Certificate of Achievement</h1>

            <div class="subtitle">
                This certificate is proudly presented to
            </div>

            <div class="presented">
                This is to certify that
            </div>

            <div class="student-name">
                <?= htmlspecialchars($certificate['full_name']) ?>
            </div>

            <div class="line"></div>

            <div class="achievement">
                has successfully completed the
                <strong>
                    <?= htmlspecialchars($certificate['assessment_title']) ?>
                </strong>
                assessment and demonstrated proficiency in
            </div>

            <div class="skill">
                <?= htmlspecialchars($certificate['skill_name']) ?>
            </div>

            <div class="achievement">
                with a score of
                <strong>
                    <?= htmlspecialchars($certificate['obtained_marks']) ?>
                    /
                    <?= htmlspecialchars($certificate['total_marks']) ?>
                </strong>
                and an overall percentage of
                <strong>
                    <?= htmlspecialchars($certificate['percentage']) ?>%
                </strong>.
            </div>

            <div class="details">

                <div class="detail-box">
                    <div class="detail-label">
                        Result
                    </div>

                    <div class="detail-value">
                        <?= htmlspecialchars($certificate['result_status']) ?>
                    </div>
                </div>

                <div class="detail-box">
                    <div class="detail-label">
                        Issue Date
                    </div>

                    <div class="detail-value">
                        <?= date(
                            'd F Y',
                            strtotime($certificate['issue_date'])
                        ) ?>
                    </div>
                </div>

            </div>

            <div class="certificate-number">
                Certificate No:
                <strong>
                    <?= htmlspecialchars($certificate['certificate_number']) ?>
                </strong>
            </div>

            <div class="verification">
                Verification Code:
                <?= htmlspecialchars($certificate['verification_code']) ?>
            </div>

        </div>

    </div>

    <div class="actions">

        <button
            class="btn btn-print"
            onclick="window.print()">
            🖨 Print / Save as PDF
        </button>

        <a
            class="btn btn-back"
            href="../student/certificates.php">
            ← Back to Certificates
        </a>

    </div>

</div>

</body>
</html>
