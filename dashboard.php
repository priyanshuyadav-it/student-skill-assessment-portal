<?php
require 'config/db.php';require 'includes/auth.php';requireLogin();
$id=$_SESSION['user_id'];$name=$_SESSION['name'];
$stats=$conn->query("SELECT COUNT(*) attempts, COALESCE(AVG(percentage),0) avg_score, COALESCE(MAX(percentage),0) best_score, SUM(status='Passed') passed FROM assessments WHERE student_id=$id")->fetch_assoc();
$cats=$conn->query('SELECT * FROM categories ORDER BY name');
$pageTitle='Dashboard';include 'includes/header.php';?>
<div class="d-flex justify-content-between align-items-center mb-4"><div><h2>Welcome, <?=htmlspecialchars($name)?> 👋</h2><p class="text-muted">Measure your technical skills and build your certification profile.</p></div></div>
<div class="row g-3 mb-4">
<?php foreach([['Attempts',$stats['attempts']],['Average',round($stats['avg_score'],1).'%'],['Best Score',round($stats['best_score'],1).'%'],['Passed',$stats['passed']]] as $s):?>
<div class="col-6 col-lg-3"><div class="card p-3"><div class="text-muted"><?=$s[0]?></div><div class="stat"><?=$s[1]?></div></div></div><?php endforeach;?>
</div>
<h4 class="mb-3">Available Skill Assessments</h4><div class="row g-4">
<?php while($c=$cats->fetch_assoc()):?><div class="col-md-6 col-lg-3"><div class="card p-4 h-100"><h5><?=htmlspecialchars($c['name'])?></h5><p class="small text-muted"><?=htmlspecialchars($c['description'])?></p><a href="assessment.php?category=<?=$c['id']?>" class="btn btn-primary mt-auto">Start Assessment</a></div></div><?php endwhile;?>
</div>
<?php include 'includes/footer.php'; ?>