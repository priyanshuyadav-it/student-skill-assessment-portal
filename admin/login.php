<?php

session_start();

require_once "../config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {

        $error = "Please enter your email and password.";

    } else {

        $sql = "
            SELECT user_id, full_name, email, password_hash, role, is_active
            FROM users
            WHERE email = :email
            LIMIT 1
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":email" => $email
        ]);

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (
            $admin &&
            $admin["role"] === "admin" &&
            (int) $admin["is_active"] === 1 &&
            password_verify($password, $admin["password_hash"])
        ) {

            session_regenerate_id(true);

            $_SESSION["user_id"] = $admin["user_id"];
            $_SESSION["full_name"] = $admin["full_name"];
            $_SESSION["email"] = $admin["email"];
            $_SESSION["role"] = $admin["role"];

            header("Location: dashboard.php");
            exit;

        } else {

            $error = "Invalid admin credentials.";

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

    <title>Admin Login | Student Skill Portal</title>

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

        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
        }

        .login-card {
            width: 100%;
            max-width: 430px;
            background: white;
            padding: 40px;
            border-radius: 14px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        .icon {
            width: 65px;
            height: 65px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: #dbeafe;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
        }

        h1 {
            text-align: center;
            margin: 0 0 8px;
            font-size: 28px;
        }

        .subtitle {
            text-align: center;
            color: #6b7280;
            margin-bottom: 30px;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px 14px;
            border-radius: 7px;
            margin-bottom: 20px;
            font-size: 14px;
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
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
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

        .back {
            text-align: center;
            margin-top: 25px;
        }

        .back a {
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
        }

        .back a:hover {
            text-decoration: underline;
        }

        @media (max-width: 500px) {

            .login-card {
                padding: 30px 22px;
            }

        }

    </style>

</head>

<body>

<div class="page">

    <div class="login-card">

        <div class="icon">
            🔐
        </div>

        <h1>Admin Login</h1>

        <p class="subtitle">
            Student Skill Assessment & Certification Portal
        </p>

        <?php if ($error): ?>

            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter admin email"
                    autocomplete="email"
                    required
                >

            </div>

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter admin password"
                    autocomplete="current-password"
                    required
                >

            </div>

            <button type="submit">
                Login as Admin
            </button>

        </form>

        <div class="back">

            <a href="../index.php">
                ← Back to Home
            </a>

        </div>

    </div>

</div>

</body>

</html>