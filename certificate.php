<?php
require 'config/db.php';require 'includes/auth.php';requireLogin();
$id=(int)($_GET['id']??0);$uid=$_SESSION['user_id'];
$stmt=$conn->prepare("SELECT a.*,c.name category,u.name student FROM assessments a JOIN categories c ON c.id=a.category_id JOIN users u ON u.id=a.student_id WHERE a.id=? AND a.student_id=? AND a.status='Passed'");$stmt->bind_param('ii',$id,$uid);$stmt->execute();$r=$stmt->get_result()->fetch_assoc();
if(!$r){header('Location: results.php');exit;}
$pageTitle='Certificate';include 'includes/header.php';?>
<div class="no-print mb-3"><button onclick="window.print()" class="btn btn-primary">Print / Save as PDF</button></div>
<div class="certificate"><div class="small text-uppercase">Student Skill Assessment & Certification Portal</div><h1 class="mt-4">Certificate of Achievement</h1><p class="lead mt-5">This certificate is proudly presented to</p><h2 class="display-5 fw-bold"><?=htmlspecialchars($r['student'])?></h2><p class="lead mt-4">for successfully completing the <strong><?=htmlspecialchars($r['category'])?></strong> assessment</p><h3 class="mt-4">Score: <?=number_format($r['percentage'],1)?>%</h3><p class="text-muted mt-5">Issued on <?=date('d F Y',strtotime($r['attempted_at']))?></p><div class="row mt-5"><div class="col">_________________<br>Project Coordinator</div><div class="col">_________________<br>Authorized Signatory</div></div></div>
<?php include 'includes/footer.php'; ?>