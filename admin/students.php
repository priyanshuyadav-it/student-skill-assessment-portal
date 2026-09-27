<?php

require_once "../includes/auth.php";
require_once "../config/database.php";

requireAdmin();

/*
|--------------------------------------------------------------------------
| Handle Activate / Deactivate
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["toggle_status"])) {

    $user_id = filter_input(INPUT_POST, "user_id", FILTER_VALIDATE_INT);

    if ($user_id) {

        $stmt = $pdo->prepare("
            UPDATE users
            SET is_active = IF(is_active = 1, 0, 1)
            WHERE user_id = :user_id
              AND role = 'student'
        ");

        $stmt->execute([
            ":user_id" => $user_id
        ]);
    }

    header("Location: students.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Fetch Students
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT
        user_id,
        full_name,
        email,
        is_active,
        created_at
    FROM users
    WHERE role = 'student'
    ORDER BY created_at DESC
");

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Students | Student Skill Portal</title>

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

        /* Navbar */

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

        .nav-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .admin-name {
            font-size: 15px;
        }

        .logout {
            color: white;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.15);
            padding: 9px 16px;
            border-radius: 6px;
        }

        .logout:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        /* Container */

        .container {
            max-width: 1150px;
            margin: 40px auto;
            padding: 0 20px;
        }

        /* Page Header */

        .page-header {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
            margin-bottom: 25px;
        }

        .page-header h1 {
            margin: 0 0 8px;
            font-size: 30px;
        }

        .page-header p {
            margin: 0;
            color: #6b7280;
        }

        /* Back button */

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #2563eb;
            font-weight: bold;
        }

        /* Table Card */

        .table-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .table-header {
            padding: 20px 25px;
            border-bottom: 1px solid #e5e7eb;
        }

        .table-header h2 {
            margin: 0;
            font-size: 20px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f8fafc;
            color: #374151;
            font-size: 14px;
            text-align: left;
            padding: 15px 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        td {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        tr:hover {
            background: #fafcff;
        }

        /* Student */

        .student-name {
            font-weight: bold;
            color: #111827;
        }

        .student-id {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 4px;
        }

        .email {
            color: #4b5563;
        }

        /* Status */

        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .active {
            background: #dcfce7;
            color: #166534;
        }

        .inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Button */

        .action-form {
            margin: 0;
        }

        .action-btn {
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: bold;
        }

        .deactivate {
            background: #fee2e2;
            color: #991b1b;
        }

        .deactivate:hover {
            background: #fecaca;
        }

        .activate {
            background: #dcfce7;
            color: #166534;
        }

        .activate:hover {
            background: #bbf7d0;
        }

        /* Empty */

        .empty {
            text-align: center;
            padding: 50px 20px;
            color: #6b7280;
        }

        .empty-icon {
            font-size: 45px;
            margin-bottom: 10px;
        }

        /* Responsive */

        @media (max-width: 768px) {

            .navbar {
                padding: 15px 20px;
            }

            .admin-name {
                display: none;
            }

            .container {
                margin-top: 25px;
            }

            .page-header {
                padding: 25px;
            }

            .page-header h1 {
                font-size: 25px;
            }

            th,
            td {
                white-space: nowrap;
            }

        }

    </style>

</head>

<body>

<nav class="navbar">

    <h2>Student Skill Portal</h2>

    <div class="nav-right">

        <span class="admin-name">
            👤 <?php echo htmlspecialchars($_SESSION["full_name"]); ?>
        </span>

        <a href="../auth/logout.php" class="logout">
            Logout
        </a>

    </div>

</nav>

<div class="container">

    <div class="page-header">

        <h1>👨‍🎓 Manage Students</h1>

        <p>
            View registered students and manage their portal accounts.
        </p>

        <a href="dashboard.php" class="back-btn">
            ← Back to Dashboard
        </a>

    </div>

    <div class="table-card">

        <div class="table-header">

            <h2>
                Registered Students
                (<?php echo count($students); ?>)
            </h2>

        </div>

        <?php if (count($students) > 0): ?>

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>Student</th>

                            <th>Email</th>

                            <th>Status</th>

                            <th>Registered On</th>

                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($students as $student): ?>

                            <tr>

                                <td>

                                    <div class="student-name">
                                        <?php echo htmlspecialchars($student["full_name"]); ?>
                                    </div>

                                    <div class="student-id">
                                        ID: #<?php echo (int)$student["user_id"]; ?>
                                    </div>

                                </td>

                                <td class="email">

                                    <?php echo htmlspecialchars($student["email"]); ?>

                                </td>

                                <td>

                                    <?php if ((int)$student["is_active"] === 1): ?>

                                        <span class="status active">
                                            Active
                                        </span>

                                    <?php else: ?>

                                        <span class="status inactive">
                                            Inactive
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <?php
                                    echo date(
                                        "d M Y",
                                        strtotime($student["created_at"])
                                    );
                                    ?>

                                </td>

                                <td>

                                    <form
                                        method="POST"
                                        class="action-form"
                                    >

                                        <input
                                            type="hidden"
                                            name="user_id"
                                            value="<?php echo (int)$student["user_id"]; ?>"
                                        >

                                        <button
                                            type="submit"
                                            name="toggle_status"
                                            class="action-btn <?php echo ((int)$student["is_active"] === 1) ? 'deactivate' : 'activate'; ?>"
                                        >

                                            <?php
                                            echo ((int)$student["is_active"] === 1)
                                                ? "Deactivate"
                                                : "Activate";
                                            ?>

                                        </button>

                                    </form>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="empty">

                <div class="empty-icon">
                    👨‍🎓
                </div>

                <h3>No Students Found</h3>

                <p>
                    No student accounts have been registered yet.
                </p>

            </div>

        <?php endif; ?>

    </div>

</div>

</body>

</html>