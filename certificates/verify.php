<?php

require_once "../config/database.php";

$certificate = null;
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $verification_value = trim($_POST["verification_value"] ?? "");

    if ($verification_value === "") {

        $error = "Please enter a certificate number or verification code.";

    } else {

        $sql = "
            SELECT
                c.certificate_id,
                c.certificate_number,
                c.certificate_title,
                c.issue_date,
                c.verification_code,
                u.full_name,
                a.title AS assessment_title,
                s.skill_name,
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
                ON c.result_id = r.result_id
                AND r.assessment_id = a.assessment_id
            INNER JOIN skills s
                ON a.skill_id = s.skill_id
            WHERE c.certificate_number = :certificate_number
               OR c.verification_code = :verification_code
            LIMIT 1
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":certificate_number" => $verification_value,
            ":verification_code" => $verification_value
        ]);

        $certificate = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$certificate) {
            $error = "Certificate not found. Please check the certificate number or verification code.";
        }
    }
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

    <title>Verify Certificate | Student Skill Portal</title>

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
            max-width: 900px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            margin-bottom: 10px;
            font-size: 34px;
        }

        .header p {
            color: #6b7280;
            font-size: 17px;
        }

        .card {
            background: white;
            padding: 35px;
            border-radius: 14px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.07);
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            font-size: 16px;
        }

        input:focus {
            outline: none;
            border-color: #2563eb;
        }

        button {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 7px;
            background: #2563eb;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 14px;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        .success {
            text-align: center;
            color: #166534;
            margin-bottom: 25px;
        }

        .success-icon {
            width: 65px;
            height: 65px;
            margin: 0 auto 15px;
            border-radius: 50%;
            background: #dcfce7;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }

        .details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .detail-box {
            background: #f8fafc;
            padding: 18px;
            border-radius: 8px;
        }

        .detail-box span {
            display: block;
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 6px;
        }

        .detail-box strong {
            font-size: 16px;
        }

        .verified {
            display: inline-block;
            background: #dcfce7;
            color: #166534;
            padding: 8px 18px;
            border-radius: 20px;
            font-weight: bold;
            margin-bottom: 25px;
        }

        @media (max-width: 700px) {

            .navbar {
                padding: 15px 20px;
            }

            .container {
                margin-top: 30px;
            }

            .card {
                padding: 25px;
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

    <a href="../index.php">
        Home
    </a>

</nav>

<div class="container">

    <div class="header">

        <h1>Certificate Verification</h1>

        <p>
            Verify the authenticity of a certificate issued by the
            Student Skill Assessment & Certification Portal.
        </p>

    </div>

    <?php if (!$certificate): ?>

        <div class="card">

            <?php if ($error): ?>

                <div class="error">
                    <?php echo htmlspecialchars($error); ?>
                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="form-group">

                    <label for="verification_value">
                        Certificate Number or Verification Code
                    </label>

                    <input
                        type="text"
                        id="verification_value"
                        name="verification_value"
                        placeholder="Enter certificate number or verification code"
                        required
                    >

                </div>

                <button type="submit">
                    Verify Certificate
                </button>

            </form>

        </div>

    <?php else: ?>

        <div class="card">

            <div class="success">

                <div class="success-icon">
                    ✓
                </div>

                <h2>Certificate Verified</h2>

                <span class="verified">
                    ✓ Authentic Certificate
                </span>

            </div>

            <div class="details">

                <div class="detail-box">

                    <span>Certificate Holder</span>

                    <strong>
                        <?php echo htmlspecialchars($certificate["full_name"]); ?>
                    </strong>

                </div>

                <div class="detail-box">

                    <span>Certificate Title</span>

                    <strong>
                        <?php echo htmlspecialchars($certificate["certificate_title"]); ?>
                    </strong>

                </div>

                <div class="detail-box">

                    <span>Skill</span>

                    <strong>
                        <?php echo htmlspecialchars($certificate["skill_name"]); ?>
                    </strong>

                </div>

                <div class="detail-box">

                    <span>Assessment</span>

                    <strong>
                        <?php echo htmlspecialchars($certificate["assessment_title"]); ?>
                    </strong>

                </div>

                <div class="detail-box">

                    <span>Score</span>

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $certificate["obtained_marks"]
                            . " / "
                            . $certificate["total_marks"]
                        );
                        ?>
                    </strong>

                </div>

                <div class="detail-box">

                    <span>Percentage</span>

                    <strong>
                        <?php echo htmlspecialchars($certificate["percentage"]); ?>%
                    </strong>

                </div>

                <div class="detail-box">

                    <span>Result Status</span>

                    <strong>
                        <?php echo htmlspecialchars($certificate["result_status"]); ?>
                    </strong>

                </div>

                <div class="detail-box">

                    <span>Issue Date</span>

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            date(
                                "d F Y",
                                strtotime($certificate["issue_date"])
                            )
                        );
                        ?>
                    </strong>

                </div>

                <div class="detail-box">

                    <span>Certificate Number</span>

                    <strong>
                        <?php echo htmlspecialchars($certificate["certificate_number"]); ?>
                    </strong>

                </div>

                <div class="detail-box">

                    <span>Verification Code</span>

                    <strong>
                        <?php echo htmlspecialchars($certificate["verification_code"]); ?>
                    </strong>

                </div>

            </div>

        </div>

    <?php endif; ?>

</div>

</body>

</html>