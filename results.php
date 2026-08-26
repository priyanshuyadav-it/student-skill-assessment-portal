<?php
require 'config/db.php';require 'includes/auth.php';requireLogin();
$uid=$_SESSION['user_id'];$rows=$conn->query("SELECT a.*,c.name category FROM assessments a JOIN categories c ON c.id=a.category_id WHERE a.student_id=$uid ORDER BY a.attempted_at DESC");
$chart=$conn->query("SELECT c.name,ROUND(AVG(a.percentage),1) avg_score FROM assessments a JOIN categories c ON c.id=a.category_id WHERE a.student_id=$uid GROUP BY c.id ORDER BY c.name");
$labels=[];$values=[];while($x=$chart->fetch_assoc()){$labels[]=$x['name'];$values[]=(float)$x['avg_score'];}
$pageTitle='Results';include 'includes/header.php';?>
<div class="row g-4"><div class="col-lg-7"><div class="card p-4"><h4>Assessment History</h4><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Skill</th><th>Score</th><th>Status</th><th>Date</th></tr></thead><tbody>
<?php while($r=$rows->fetch_assoc()):?><tr><td><?=htmlspecialchars($r['category'])?></td><td><?=number_format($r['percentage'],1)?>%</td><td><span class="badge <?=$r['status']==='Passed'?'bg-success':'bg-danger'?>"><?=$r['status']?></span></td><td><?=date('d M Y',strtotime($r['attempted_at']))?></td></tr><?php endwhile;?>
</tbody></table></div></div></div><div class="col-lg-5"><div class="card p-4"><h4>Skill Performance</h4><canvas id="skillChart"></canvas></div></div></div>
<script>new Chart(document.getElementById('skillChart'),{type:'bar',data:{labels:<?=json_encode($labels)?>,datasets:[{label:'Average %',data:<?=json_encode($values)?>}]},options:{scales:{y:{beginAtZero:true,max:100}}}});</script>
<?php include 'includes/footer.php'; ?>