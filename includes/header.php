<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = $pageTitle ?? 'SkillCert Portal';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($pageTitle) ?> | SkillCert</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
<div class="container">
<a class="navbar-brand fw-bold" href="index.php">SkillCert</a>
<button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
<div class="collapse navbar-collapse" id="nav">
<ul class="navbar-nav ms-auto align-items-lg-center">
<?php if(isset($_SESSION['user_id'])): ?>
<li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
<li class="nav-item"><a class="nav-link" href="results.php">Results</a></li>
<?php if(($_SESSION['role'] ?? '') === 'admin'): ?><li class="nav-item"><a class="nav-link" href="admin.php">Admin</a></li><?php endif; ?>
<li class="nav-item"><a class="btn btn-light btn-sm ms-lg-2" href="logout.php">Logout</a></li>
<?php else: ?>
<li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
<li class="nav-item"><a class="btn btn-light btn-sm ms-lg-2" href="register.php">Register</a></li>
<?php endif; ?>
</ul>
</div></div></nav>
<main class="container py-4">
