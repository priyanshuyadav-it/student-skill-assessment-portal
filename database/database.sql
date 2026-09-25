-- ============================================================
-- Student Skill Assessment & Certification Portal
-- Database Schema
-- ============================================================

CREATE DATABASE IF NOT EXISTS student_skill_portal;

USE student_skill_portal;


-- ============================================================
-- 1. USERS TABLE
-- Stores students and administrators
-- ============================================================

CREATE TABLE users (
    user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    full_name VARCHAR(100) NOT NULL,

    email VARCHAR(150) NOT NULL UNIQUE,

    password_hash VARCHAR(255) NOT NULL,

    role ENUM('student', 'admin') NOT NULL DEFAULT 'student',

    profile_image VARCHAR(255) DEFAULT NULL,

    is_active TINYINT(1) NOT NULL DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);


-- ============================================================
-- 2. SKILLS TABLE
-- Stores skills available for assessment
-- ============================================================

CREATE TABLE skills (
    skill_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    skill_name VARCHAR(100) NOT NULL UNIQUE,

    description TEXT DEFAULT NULL,

    category VARCHAR(100) DEFAULT NULL,

    difficulty_level ENUM('Beginner', 'Intermediate', 'Advanced')
        DEFAULT 'Beginner',

    is_active TINYINT(1) NOT NULL DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- ============================================================
-- 3. ASSESSMENTS TABLE
-- Stores assessment information
-- ============================================================

CREATE TABLE assessments (
    assessment_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    skill_id INT UNSIGNED NOT NULL,

    title VARCHAR(150) NOT NULL,

    description TEXT DEFAULT NULL,

    duration_minutes INT UNSIGNED NOT NULL DEFAULT 30,

    passing_percentage DECIMAL(5,2) NOT NULL DEFAULT 40.00,

    total_questions INT UNSIGNED NOT NULL DEFAULT 0,

    is_active TINYINT(1) NOT NULL DEFAULT 1,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_assessment_skill
        FOREIGN KEY (skill_id)
        REFERENCES skills(skill_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


-- ============================================================
-- 4. QUESTIONS TABLE
-- Stores assessment questions
-- ============================================================

CREATE TABLE questions (
    question_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    assessment_id INT UNSIGNED NOT NULL,

    question_text TEXT NOT NULL,

    option_a VARCHAR(500) NOT NULL,

    option_b VARCHAR(500) NOT NULL,

    option_c VARCHAR(500) NOT NULL,

    option_d VARCHAR(500) NOT NULL,

    correct_option ENUM('A', 'B', 'C', 'D') NOT NULL,

    marks DECIMAL(5,2) NOT NULL DEFAULT 1.00,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_question_assessment
        FOREIGN KEY (assessment_id)
        REFERENCES assessments(assessment_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


-- ============================================================
-- 5. ASSESSMENT ATTEMPTS TABLE
-- Stores every attempt made by a student
-- ============================================================

CREATE TABLE assessment_attempts (
    attempt_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id INT UNSIGNED NOT NULL,

    assessment_id INT UNSIGNED NOT NULL,

    started_at DATETIME NOT NULL,

    completed_at DATETIME DEFAULT NULL,

    status ENUM('in_progress', 'completed', 'abandoned')
        NOT NULL DEFAULT 'in_progress',

    score DECIMAL(6,2) DEFAULT NULL,

    percentage DECIMAL(5,2) DEFAULT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_attempt_user
        FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_attempt_assessment
        FOREIGN KEY (assessment_id)
        REFERENCES assessments(assessment_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


-- ============================================================
-- 6. ANSWERS TABLE
-- Stores answers submitted during an assessment
-- ============================================================

CREATE TABLE answers (
    answer_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    attempt_id INT UNSIGNED NOT NULL,

    question_id INT UNSIGNED NOT NULL,

    selected_option ENUM('A', 'B', 'C', 'D') DEFAULT NULL,

    is_correct TINYINT(1) DEFAULT NULL,

    marks_obtained DECIMAL(5,2) NOT NULL DEFAULT 0.00,

    answered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_answer_attempt
        FOREIGN KEY (attempt_id)
        REFERENCES assessment_attempts(attempt_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_answer_question
        FOREIGN KEY (question_id)
        REFERENCES questions(question_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    UNIQUE KEY unique_attempt_question
        (attempt_id, question_id)
);


-- ============================================================
-- 7. RESULTS TABLE
-- Stores final assessment results
-- ============================================================

CREATE TABLE results (
    result_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    attempt_id INT UNSIGNED NOT NULL UNIQUE,

    user_id INT UNSIGNED NOT NULL,

    assessment_id INT UNSIGNED NOT NULL,

    total_marks DECIMAL(6,2) NOT NULL,

    obtained_marks DECIMAL(6,2) NOT NULL,

    percentage DECIMAL(5,2) NOT NULL,

    result_status ENUM('Pass', 'Fail') NOT NULL,

    completed_at DATETIME NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_result_attempt
        FOREIGN KEY (attempt_id)
        REFERENCES assessment_attempts(attempt_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_result_user
        FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_result_assessment
        FOREIGN KEY (assessment_id)
        REFERENCES assessments(assessment_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


-- ============================================================
-- 8. CERTIFICATES TABLE
-- Stores certificates generated for eligible students
-- ============================================================

CREATE TABLE certificates (
    certificate_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    certificate_number VARCHAR(50) NOT NULL UNIQUE,

    user_id INT UNSIGNED NOT NULL,

    result_id INT UNSIGNED NOT NULL UNIQUE,

    certificate_title VARCHAR(150) NOT NULL,

    issue_date DATE NOT NULL,

    certificate_file VARCHAR(255) DEFAULT NULL,

    verification_code VARCHAR(100) NOT NULL UNIQUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_certificate_user
        FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_certificate_result
        FOREIGN KEY (result_id)
        REFERENCES results(result_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


-- ============================================================
-- 9. INDEXES
-- Improves database query performance
-- ============================================================

CREATE INDEX idx_users_role
    ON users(role);

CREATE INDEX idx_skills_category
    ON skills(category);

CREATE INDEX idx_assessments_skill
    ON assessments(skill_id);

CREATE INDEX idx_questions_assessment
    ON questions(assessment_id);

CREATE INDEX idx_attempts_user
    ON assessment_attempts(user_id);

CREATE INDEX idx_attempts_assessment
    ON assessment_attempts(assessment_id);

CREATE INDEX idx_results_user
    ON results(user_id);

CREATE INDEX idx_certificates_user
    ON certificates(user_id);


-- ============================================================
-- 10. SAMPLE SKILLS
-- ============================================================

INSERT INTO skills
    (skill_name, description, category, difficulty_level)
VALUES
    (
        'Python',
        'Programming and problem-solving using Python.',
        'Programming',
        'Beginner'
    ),
    (
        'Java',
        'Object-oriented programming using Java.',
        'Programming',
        'Intermediate'
    ),
    (
        'SQL',
        'Database queries, joins, and data manipulation.',
        'Database',
        'Beginner'
    ),
    (
        'HTML & CSS',
        'Web page structure and styling.',
        'Web Development',
        'Beginner'
    ),
    (
        'JavaScript',
        'Client-side web programming and DOM manipulation.',
        'Web Development',
        'Intermediate'
    );