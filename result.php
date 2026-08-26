<?php
require 'config/db.php';require 'includes/auth.php';requireLogin();
$id=(int)($_GET['id']??0);$uid=$_SESSION['user_id'];
$stmt=$conn->prepare("SELECT a.*,c.name category,u.name student FROM assessments a JOIN categories c ON c.id=a.category_id JOIN users u ON u.id=a.student_id WHERE a.id=? AND a.student_id=?");$stmt->bind_param('ii',$id,$uid);$stmt->execute();$r=$stmt->get_result()->fetch_assoc();
if(!$r){header('Location: results.php');exit;}
$pageTitle='Assessment Result';include 'includes/header.php';?>
<div class="row justify-content-center"><div class="col-lg-8"><div class="card p-5 text-center">
<div class="display-4 mb-3"><?=$r['status']==='Passed'?'🏆':'📚'?></div><h2><?=htmlspecialchars($r['category'])?> Assessment</h2>
<p class="text-muted"><?=date('d M Y, h:i A',strtotime($r['attempted_at']))?></p><div class="display-3 fw-bold text-primary"><?=number_format($r['percentage'],1)?>%</div>
<p>Score: <strong><?=$r['score']?> / <?=$r['total_marks']?></strong></p><span class="badge <?=$r['status']==='Passed'?'badge-pass':'badge-fail'?> fs-6"><?=$r['status']?></span>
<div class="mt-4"><a class="btn btn-primary me-2" href="certificate.php?id=<?=$r['id']?>">View Certificate</a><a class="btn btn-outline-secondary" href="results.php">Back to Results</a></div>
</div></div></div>
<?php include 'includes/footer.php'; ?>