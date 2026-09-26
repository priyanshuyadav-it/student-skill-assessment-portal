<?php

require_once "../config/database.php";
require_once "../includes/auth.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {

        $message = "Email and password are required.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } else {

        $query = "
            SELECT user_id, full_name, email, password_hash, role, is_active
            FROM users
            WHERE email = ?
            LIMIT 1
        ";

        $stmt = $pdo->prepare($query);
        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {

            $message = "Invalid email or password.";
            $message_type = "error";

        } elseif (!$user["is_active"]) {

            $message = "Your account is currently inactive.";
            $message_type = "error";

        } elseif (!password_verify($password, $user["password_hash"])) {

            $message = "Invalid email or password.";
            $message_type = "error";

        } else {

            // Regenerate session ID for security
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["full_name"] = $user["full_name"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["role"] = $user["role"];

            // Redirect according to user role
            if ($user["role"] === "admin") {
                header("Location: ../admin/dashboard.php");
                exit;
            }

            header("Location: ../student/dashboard.php");
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Login</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .login-container {
            width: 420px;
            background: #ffffff;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #111827;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 6px;
            font-weight: bold;
            color: #111827;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        input:focus {
            outline: none;
            border-color: #2563eb;
        }

        button {
            width: 100%;
            margin-top: 22px;
            padding: 12px;
            border: none;
            border-radius: 6px;
            background: #2563eb;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            text-align: center;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
        }

        .register-link a {
            color: #2563eb;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

    </style>

</head>

<body>

<div class="login-container">

    <h2>Student Login</h2>

    <p class="subtitle">
        Student Skill Assessment & Certification Portal
    </p>

    <?php if (!empty($message)): ?>

        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>

    <form method="POST" action="">

        <label for="email">Email Address</label>

        <input
            type="email"
            id="email"
            name="email"
            placeholder="Enter your email"
            required
        >

        <label for="password">Password</label>

        <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter your password"
            required
        >

        <button type="submit">
            Login
        </button>

    </form>

    <div class="register-link">

        Don't have an account?

        <a href="register.php">
            Create account
        </a>

    </div>

</div>

</body>

</html>