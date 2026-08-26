<?php
$pageTitle='Home'; include 'includes/header.php';
?>
<div class="hero p-5 mb-4">
<div class="row align-items-center"><div class="col-lg-8">
<span class="badge bg-light text-primary mb-3">Academic Skill Evaluation Platform</span>
<h1 class="display-5 fw-bold">Assess Skills. Track Progress. Earn Certificates.</h1>
<p class="lead">A professional web portal where students can attempt skill assessments, view analytics and generate certificates.</p>
<a href="register.php" class="btn btn-light btn-lg me-2">Get Started</a>
<a href="login.php" class="btn btn-outline-light btn-lg">Student Login</a>
</div><div class="col-lg-4 text-center display-1">🎓</div></div>
</div>
<div class="row g-4">
<div class="col-md-4"><div class="card p-4 h-100"><h4>📝 Assessments</h4><p class="text-muted">Attempt MCQ-based tests across technical skill categories.</p></div></div>
<div class="col-md-4"><div class="card p-4 h-100"><h4>📊 Analytics</h4><p class="text-muted">Visualize scores and skill-wise performance using Chart.js.</p></div></div>
<div class="col-md-4"><div class="card p-4 h-100"><h4>🏆 Certification</h4><p class="text-muted">Generate a printable achievement certificate after passing.</p></div></div>
</div>
<?php include 'includes/footer.php'; ?>