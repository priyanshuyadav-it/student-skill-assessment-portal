# Student Skill Assessment & Certification Portal

A web-based platform for assessing student skills, tracking assessment performance, and managing digital certificates.

The system provides separate modules for students and administrators, including skill assessments, automatic result calculation, performance tracking, certificate generation, and certificate verification.

---

## 📌 Project Overview

The **Student Skill Assessment & Certification Portal** is designed to provide a centralized platform where students can:

- Create an account and log in
- View available technical skills
- Take skill-based assessments
- Receive automatic assessment results
- Track their performance
- View eligible certificates
- Generate and view digital certificates
- Verify certificates using a unique certificate number

Administrators can manage students, skills, assessments, questions, results, and certificates through an admin dashboard.

---

## 🚀 Features

### 👨‍🎓 Student Module

- Student Registration
- Student Login and Logout
- Student Dashboard
- View Available Skills
- View Available Assessments
- Take Online Assessments
- Automatic Score Calculation
- Pass/Fail Result Calculation
- View Assessment Results
- Performance Tracking
- Certificate Eligibility
- Digital Certificate Generation
- Certificate Viewing
- Certificate Verification

### 👨‍💼 Admin Module

- Admin Login
- Admin Dashboard
- Student Management
- Skill Management
- Assessment Management
- Question Management
- Result Management
- Certificate Management
- Activate/Deactivate Skills
- Activate/Deactivate Assessments
- Performance Analytics
- Pass vs Fail Visualization

---

## 📊 Admin Analytics

The admin dashboard includes a performance analytics section using **Chart.js**.

It provides:

- Total students
- Active skills
- Total assessments
- Total results
- Total certificates
- Pass vs Fail results
- Result summary

---

## 🏆 Certificate System

Students who successfully complete an assessment can receive a digital certificate.

Each certificate contains:

- Student name
- Skill name
- Assessment name
- Score
- Percentage
- Result status
- Issue date
- Certificate number
- Verification code

Certificates can be viewed and verified through the portal.

---

## 🛠️ Technologies Used

### Frontend

- HTML5
- CSS3
- JavaScript
- Bootstrap
- Chart.js

### Backend

- PHP

### Database

- MySQL

### Development Environment

- XAMPP
- Apache
- MySQL
- phpMyAdmin
- Visual Studio Code

### Version Control

- Git
- GitHub

---

## 📁 Project Structure

```text
student-skill-assessment-portal/
│
├── admin/
│   ├── assessments.php
│   ├── certificates.php
│   ├── dashboard.php
│   ├── login.php
│   ├── questions.php
│   ├── results.php
│   ├── skills.php
│   └── students.php
│
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
│
├── auth/
│   ├── login.php
│   ├── logout.php
│   └── register.php
│
├── certificates/
│   ├── generate.php
│   ├── verify.php
│   └── view.php
│
├── config/
│   └── database.php
│
├── database/
│   └── database.sql
│
├── includes/
│   └── auth.php
│
├── student/
│   ├── assessment.php
│   ├── certificates.php
│   ├── dashboard.php
│   ├── result.php
│   ├── results.php
│   ├── skills.php
│   ├── submit_assessment.php
│   └── take_assessment.php
│
├── .gitignore
├── index.php
└── README.md