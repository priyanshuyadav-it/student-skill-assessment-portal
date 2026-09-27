<?php

require_once "../config/database.php";
require_once "../includes/auth.php";

requireStudent();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit;
}

$attempt_id = filter_input(
    INPUT_POST,
    "attempt_id",
    FILTER_VALIDATE_INT
);

$submitted_answers = $_POST["answers"] ?? [];

if (!$attempt_id || !is_array($submitted_answers)) {
    die("Invalid assessment submission.");
}

try {

    $pdo->beginTransaction();

    /*
     * Get the current attempt.
     * It must belong to the logged-in student.
     */
    $attempt_query = "
        SELECT
            aa.attempt_id,
            aa.assessment_id,
            aa.status,
            a.passing_percentage
        FROM assessment_attempts aa
        INNER JOIN assessments a
            ON aa.assessment_id = a.assessment_id
        WHERE aa.attempt_id = ?
          AND aa.user_id = ?
        LIMIT 1
    ";

    $attempt_stmt = $pdo->prepare($attempt_query);

    $attempt_stmt->execute([
        $attempt_id,
        $_SESSION["user_id"]
    ]);

    $attempt = $attempt_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$attempt) {
        throw new Exception("Assessment attempt not found.");
    }

    if ($attempt["status"] !== "in_progress") {
        throw new Exception("This assessment has already been submitted.");
    }

    $assessment_id = (int) $attempt["assessment_id"];

    /*
     * Fetch all questions for this assessment.
     */
    $question_query = "
        SELECT
            question_id,
            correct_option,
            marks
        FROM questions
        WHERE assessment_id = ?
        ORDER BY question_id ASC
    ";

    $question_stmt = $pdo->prepare($question_query);
    $question_stmt->execute([$assessment_id]);

    $questions = $question_stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($questions) === 0) {
        throw new Exception("No questions found for this assessment.");
    }

    /*
     * Calculate total marks and obtained marks.
     */
    $total_marks = 0;
    $obtained_marks = 0;

    /*
     * Insert answers.
     */
    $answer_query = "
        INSERT INTO answers
        (
            attempt_id,
            question_id,
            selected_option,
            is_correct,
            marks_obtained
        )
        VALUES (?, ?, ?, ?, ?)
    ";

    $answer_stmt = $pdo->prepare($answer_query);

    foreach ($questions as $question) {

        $question_id = (int) $question["question_id"];

        $marks = (float) $question["marks"];

        $total_marks += $marks;

        $selected_option = $submitted_answers[$question_id] ?? null;

        /*
         * Accept only valid options.
         */
        if (!in_array($selected_option, ["A", "B", "C", "D"], true)) {
            $selected_option = null;
        }

        $is_correct = (
            $selected_option !== null &&
            $selected_option === $question["correct_option"]
        ) ? 1 : 0;

        $marks_obtained = $is_correct ? $marks : 0;

        $obtained_marks += $marks_obtained;

        $answer_stmt->execute([
            $attempt_id,
            $question_id,
            $selected_option,
            $is_correct,
            $marks_obtained
        ]);
    }

    /*
     * Calculate percentage.
     */
    $percentage = 0;

    if ($total_marks > 0) {
        $percentage = ($obtained_marks / $total_marks) * 100;
    }

    $percentage = round($percentage, 2);

    /*
     * Determine Pass / Fail.
     */
    $result_status = (
        $percentage >= (float) $attempt["passing_percentage"]
    ) ? "Pass" : "Fail";

    $completed_at = date("Y-m-d H:i:s");

    /*
     * Update assessment attempt.
     */
    $update_attempt_query = "
        UPDATE assessment_attempts
        SET
            completed_at = ?,
            status = 'completed',
            score = ?,
            percentage = ?
        WHERE attempt_id = ?
    ";

    $update_attempt_stmt = $pdo->prepare($update_attempt_query);

    $update_attempt_stmt->execute([
        $completed_at,
        $obtained_marks,
        $percentage,
        $attempt_id
    ]);

    /*
     * Save result.
     */
    $result_query = "
        INSERT INTO results
        (
            attempt_id,
            user_id,
            assessment_id,
            total_marks,
            obtained_marks,
            percentage,
            result_status,
            completed_at
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ";

    $result_stmt = $pdo->prepare($result_query);

    $result_stmt->execute([
        $attempt_id,
        $_SESSION["user_id"],
        $assessment_id,
        $total_marks,
        $obtained_marks,
        $percentage,
        $result_status,
        $completed_at
    ]);

    $result_id = $pdo->lastInsertId();

    /*
     * Commit all database changes.
     */
    $pdo->commit();

    /*
     * Redirect to result page.
     */
    header(
        "Location: result.php?result_id=" .
        urlencode($result_id)
    );

    exit;

} catch (Exception $e) {

    /*
     * Roll back all changes if something fails.
     */
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    die(
        "Unable to submit assessment: " .
        htmlspecialchars($e->getMessage())
    );
}

?>