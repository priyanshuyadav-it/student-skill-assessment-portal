<?php

require_once "../config/database.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Validate required fields
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {

        $message = "All fields are required.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters.";
        $message_type = "error";

    } elseif ($password !== $confirm_password) {

        $message = "Passwords do not match.";
        $message_type = "error";

    } else {

        // Check whether email already exists
        $check_query = "SELECT user_id FROM users WHERE email = ?";
        $check_stmt = $pdo->prepare($check_query);
        $check_stmt->execute([$email]);

        if ($check_stmt->fetch()) {

            $message = "An account with this email already exists.";
            $message_type = "error";

        } else {

            // Securely hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insert student account
            $insert_query = "
                INSERT INTO users 
                (full_name, email, password_hash, role)
                VALUES (?, ?, ?, 'student')
            ";

            $insert_stmt = $pdo->prepare($insert_query);

            if ($insert_stmt->execute([
                $full_name,
                $email,
                $password_hash
            ])) {

                $message = "Registration successful! You can now login.";
                $message_type = "success";

            } else {

                $message = "Registration failed. Please try again.";
                $message_type = "error";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Registration</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .register-container {
            width: 420px;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
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
        }

        input {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
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

        .success {
            background: #dcfce7;
            color: #166534;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: #2563eb;
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="register-container">

    <h2>Create Student Account</h2>

    <p class="subtitle">
        Student Skill Assessment & Certification Portal
    </p>

    <?php if (!empty($message)): ?>

        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>

    <form method="POST" action="">

        <label for="full_name">Full Name</label>

        <input
            type="text"
            id="full_name"
            name="full_name"
            placeholder="Enter your full name"
            required
        >

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
            placeholder="Enter password"
            required
        >

        <label for="confirm_password">Confirm Password</label>

        <input
            type="password"
            id="confirm_password"
            name="confirm_password"
            placeholder="Confirm password"
            required
        >

        <button type="submit">
            Create Account
        </button>

    </form>

    <div class="login-link">
        Already have an account?
        <a href="login.php">Login here</a>
    </div>

</div>

</body>

</html>