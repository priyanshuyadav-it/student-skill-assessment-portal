<?php

require_once "../includes/auth.php";
require_once "../config/database.php";

requireAdmin();

$message = "";
$message_type = "";

/*
|--------------------------------------------------------------------------
| Add Skill
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_skill"])) {

    $skill_name = trim($_POST["skill_name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $difficulty_level = $_POST["difficulty_level"] ?? "Beginner";

    if ($skill_name === "") {

        $message = "Skill name is required.";
        $message_type = "error";

    } else {

        try {

            $stmt = $pdo->prepare("
                INSERT INTO skills
                (skill_name, description, category, difficulty_level)
                VALUES
                (:skill_name, :description, :category, :difficulty_level)
            ");

            $stmt->execute([
                ":skill_name" => $skill_name,
                ":description" => $description !== "" ? $description : null,
                ":category" => $category !== "" ? $category : null,
                ":difficulty_level" => $difficulty_level
            ]);

            $message = "Skill added successfully.";
            $message_type = "success";

        } catch (PDOException $e) {

            if ($e->getCode() === "23000") {
                $message = "This skill already exists.";
            } else {
                $message = "Unable to add skill.";
            }

            $message_type = "error";
        }
    }
}

/*
|--------------------------------------------------------------------------
| Toggle Skill Status
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["toggle_status"])) {

    $skill_id = filter_input(
        INPUT_POST,
        "skill_id",
        FILTER_VALIDATE_INT
    );

    if ($skill_id) {

        $stmt = $pdo->prepare("
            UPDATE skills
            SET is_active = IF(is_active = 1, 0, 1)
            WHERE skill_id = :skill_id
        ");

        $stmt->execute([
            ":skill_id" => $skill_id
        ]);

        $message = "Skill status updated successfully.";
        $message_type = "success";
    }
}

/*
|--------------------------------------------------------------------------
| Update Skill
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_skill"])) {

    $skill_id = filter_input(
        INPUT_POST,
        "skill_id",
        FILTER_VALIDATE_INT
    );

    $skill_name = trim($_POST["skill_name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $difficulty_level = $_POST["difficulty_level"] ?? "Beginner";

    if (!$skill_id || $skill_name === "") {

        $message = "Please enter a valid skill name.";
        $message_type = "error";

    } else {

        try {

            $stmt = $pdo->prepare("
                UPDATE skills
                SET
                    skill_name = :skill_name,
                    description = :description,
                    category = :category,
                    difficulty_level = :difficulty_level
                WHERE skill_id = :skill_id
            ");

            $stmt->execute([
                ":skill_name" => $skill_name,
                ":description" => $description !== "" ? $description : null,
                ":category" => $category !== "" ? $category : null,
                ":difficulty_level" => $difficulty_level,
                ":skill_id" => $skill_id
            ]);

            $message = "Skill updated successfully.";
            $message_type = "success";

        } catch (PDOException $e) {

            if ($e->getCode() === "23000") {
                $message = "Another skill with this name already exists.";
            } else {
                $message = "Unable to update skill.";
            }

            $message_type = "error";
        }
    }
}

/*
|--------------------------------------------------------------------------
| Fetch Skills
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT
        skill_id,
        skill_name,
        description,
        category,
        difficulty_level,
        is_active,
        created_at
    FROM skills
    ORDER BY created_at DESC
");

$skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Skills | Student Skill Portal</title>

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
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        /* Header */

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

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
        }

        /* Message */

        .message {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .message.success {
            background: #dcfce7;
            color: #166534;
        }

        .message.error {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Add Form */

        .form-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
            margin-bottom: 25px;
        }

        .form-card h2 {
            margin-top: 0;
            margin-bottom: 20px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        label {
            margin-bottom: 7px;
            font-weight: bold;
            color: #374151;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 11px 13px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            font-size: 15px;
            font-family: Arial, sans-serif;
        }

        textarea {
            min-height: 90px;
            resize: vertical;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #2563eb;
        }

        .add-btn {
            margin-top: 18px;
            background: #2563eb;
            color: white;
            border: none;
            padding: 11px 20px;
            border-radius: 7px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        .add-btn:hover {
            background: #1d4ed8;
        }

        /* Skills table */

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
            text-align: left;
            padding: 15px 18px;
            font-size: 14px;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }

        td {
            padding: 16px 18px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        tr:hover {
            background: #fafcff;
        }

        .skill-name {
            font-weight: bold;
            color: #111827;
        }

        .description {
            max-width: 300px;
            color: #6b7280;
            line-height: 1.4;
        }

        .category {
            color: #374151;
        }

        /* Difficulty */

        .difficulty {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }

        .beginner {
            background: #dbeafe;
            color: #1e40af;
        }

        .intermediate {
            background: #fef3c7;
            color: #92400e;
        }

        .advanced {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Status */

        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
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

        /* Buttons */

        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .action-btn {
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
        }

        .edit-btn {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .edit-btn:hover {
            background: #bfdbfe;
        }

        .deactivate-btn {
            background: #fee2e2;
            color: #991b1b;
        }

        .activate-btn {
            background: #dcfce7;
            color: #166534;
        }

        /* Empty */

        .empty {
            text-align: center;
            padding: 50px 20px;
            color: #6b7280;
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

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
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

        <h1>📚 Manage Skills</h1>

        <p>
            Add, update, and manage technical skills available on the portal.
        </p>

        <a href="dashboard.php" class="back-btn">
            ← Back to Dashboard
        </a>

    </div>

    <?php if ($message !== ""): ?>

        <div class="message <?php echo $message_type; ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <!-- Add Skill -->

    <div class="form-card">

        <h2>➕ Add New Skill</h2>

        <form method="POST">

            <div class="form-grid">

                <div class="form-group">

                    <label for="skill_name">
                        Skill Name
                    </label>

                    <input
                        type="text"
                        id="skill_name"
                        name="skill_name"
                        placeholder="e.g. C++"
                        required
                    >

                </div>

                <div class="form-group">

                    <label for="category">
                        Category
                    </label>

                    <input
                        type="text"
                        id="category"
                        name="category"
                        placeholder="e.g. Programming"
                    >

                </div>

                <div class="form-group">

                    <label for="difficulty_level">
                        Difficulty Level
                    </label>

                    <select
                        id="difficulty_level"
                        name="difficulty_level"
                    >

                        <option value="Beginner">
                            Beginner
                        </option>

                        <option value="Intermediate">
                            Intermediate
                        </option>

                        <option value="Advanced">
                            Advanced
                        </option>

                    </select>

                </div>

                <div class="form-group full">

                    <label for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        placeholder="Describe the skill..."
                    ></textarea>

                </div>

            </div>

            <button
                type="submit"
                name="add_skill"
                class="add-btn"
            >
                Add Skill
            </button>

        </form>

    </div>


    <!-- Skills List -->

    <div class="table-card">

        <div class="table-header">

            <h2>
                Available Skills
                (<?php echo count($skills); ?>)
            </h2>

        </div>

        <?php if (count($skills) > 0): ?>

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>Skill</th>
                            <th>Category</th>
                            <th>Difficulty</th>
                            <th>Status</th>
                            <th>Created On</th>
                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($skills as $skill): ?>

                            <tr>

                                <td>

                                    <div class="skill-name">

                                        <?php
                                        echo htmlspecialchars(
                                            $skill["skill_name"]
                                        );
                                        ?>

                                    </div>

                                </td>

                                <td class="category">

                                    <?php
                                    echo htmlspecialchars(
                                        $skill["category"] ?? "-"
                                    );
                                    ?>

                                </td>

                                <td>

                                    <?php

                                    $difficulty_class =
                                        strtolower(
                                            $skill["difficulty_level"]
                                        );

                                    ?>

                                    <span
                                        class="difficulty <?php echo $difficulty_class; ?>"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $skill["difficulty_level"]
                                        );
                                        ?>

                                    </span>

                                </td>

                                <td>

                                    <?php if ((int)$skill["is_active"] === 1): ?>

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
                                        strtotime($skill["created_at"])
                                    );
                                    ?>

                                </td>

                                <td>

                                    <div class="actions">

                                        <button
                                            type="button"
                                            class="action-btn edit-btn"
                                            onclick="editSkill(
                                                <?php echo (int)$skill["skill_id"]; ?>,
                                                '<?php echo htmlspecialchars($skill["skill_name"], ENT_QUOTES); ?>',
                                                '<?php echo htmlspecialchars($skill["category"] ?? "", ENT_QUOTES); ?>',
                                                '<?php echo htmlspecialchars($skill["difficulty_level"], ENT_QUOTES); ?>',
                                                '<?php echo htmlspecialchars($skill["description"] ?? "", ENT_QUOTES); ?>'
                                            )"
                                        >
                                            Edit
                                        </button>

                                        <form method="POST">

                                            <input
                                                type="hidden"
                                                name="skill_id"
                                                value="<?php echo (int)$skill["skill_id"]; ?>"
                                            >

                                            <button
                                                type="submit"
                                                name="toggle_status"
                                                class="action-btn <?php echo ((int)$skill["is_active"] === 1) ? 'deactivate-btn' : 'activate-btn'; ?>"
                                            >

                                                <?php
                                                echo ((int)$skill["is_active"] === 1)
                                                    ? "Deactivate"
                                                    : "Activate";
                                                ?>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="empty">

                <h3>No Skills Found</h3>

                <p>
                    Add your first skill using the form above.
                </p>

            </div>

        <?php endif; ?>

    </div>

</div>


<script>

function editSkill(
    skillId,
    skillName,
    category,
    difficulty,
    description
) {

    const skillNameInput =
        document.getElementById("skill_name");

    const categoryInput =
        document.getElementById("category");

    const difficultyInput =
        document.getElementById("difficulty_level");

    const descriptionInput =
        document.getElementById("description");

    skillNameInput.value = skillName;
    categoryInput.value = category;
    difficultyInput.value = difficulty;
    descriptionInput.value = description;

    let form = document.querySelector(".form-card form");

    let existingInput =
        form.querySelector('input[name="skill_id"]');

    if (!existingInput) {

        existingInput =
            document.createElement("input");

        existingInput.type = "hidden";
        existingInput.name = "skill_id";

        form.appendChild(existingInput);
    }

    existingInput.value = skillId;

    let button =
        form.querySelector('button[type="submit"]');

    button.name = "update_skill";
    button.textContent = "Update Skill";

    document.querySelector(".form-card")
        .scrollIntoView({
            behavior: "smooth"
        });

}

</script>

</body>

</html>