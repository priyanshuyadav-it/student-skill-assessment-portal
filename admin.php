<?php
require 'config/db.php';require 'includes/auth.php';requireAdmin();
$totalStudents=$conn->query("SELECT COUNT(*) c FROM users WHERE role='student'")->fetch_assoc()['c'];
$totalAttempts=$conn->query("SELECT COUNT(*) c FROM assessments")->fetch_assoc()['c'];
$passRate=$conn->query("SELECT COALESCE(AVG(status='Passed')*100,0) p FROM assessments")->fetch_assoc()['p'];
$recent=$conn->query("SELECT a.*,u.name student,c.name category FROM assessments a JOIN users u ON u.id=a.student_id JOIN categories c ON c.id=a.category_id ORDER BY a.attempted_at DESC LIMIT 10");
$pageTitle='Admin Dashboard';include 'includes/header.php';?>
<h2 class="mb-4">Admin Dashboard</h2><div class="row g-3 mb-4">
<div class="col-md-4"><div class="card p-4"><div class="text-muted">Students</div><div class="stat"><?=$totalStudents?></div></div></div>
<div class="col-md-4"><div class="card p-4"><div class="text-muted">Total Attempts</div><div class="stat"><?=$totalAttempts?></div></div></div>
<div class="col-md-4"><div class="card p-4"><div class="text-muted">Pass Rate</div><div class="stat"><?=round($passRate,1)?>%</div></div></div></div>
<div class="card p-4"><h4>Recent Assessment Activity</h4><div class="table-responsive"><table class="table"><thead><tr><th>Student</th><th>Skill</th><th>Score</th><th>Status</th><th>Date</th></tr></thead><tbody>
<?php while($r=$recent->fetch_assoc()):?><tr><td><?=htmlspecialchars($r['student'])?></td><td><?=htmlspecialchars($r['category'])?></td><td><?=$r['percentage']?>%</td><td><?=$r['status']?></td><td><?=date('d M Y',strtotime($r['attempted_at']))?></td></tr><?php endwhile;?></tbody></table></div></div>
<?php include 'includes/footer.php'; ?>