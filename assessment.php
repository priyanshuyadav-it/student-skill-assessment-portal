<?php
require 'config/db.php';require 'includes/auth.php';requireLogin();
$catId=(int)($_GET['category']??0);
$cat=$conn->query("SELECT * FROM categories WHERE id=$catId")->fetch_assoc();
if(!$cat){header('Location: dashboard.php');exit;}
$q=$conn->query("SELECT * FROM questions WHERE category_id=$catId ORDER BY id LIMIT 10");
$questions=[];while($row=$q->fetch_assoc())$questions[]=$row;
if(!$questions){die('No questions available for this category.');}
if($_SERVER['REQUEST_METHOD']==='POST'){
$score=0;$total=0;
foreach($questions as $row){$total+=$row['marks'];$selected=$_POST['q_'.$row['id']]??'';if($selected===$row['correct_option'])$score+=$row['marks'];}
$pct=round(($score/$total)*100,2);$status=$pct>=50?'Passed':'Needs Improvement';
$stmt=$conn->prepare('INSERT INTO assessments(student_id,category_id,score,total_marks,percentage,status) VALUES(?,?,?,?,?,?)');
$stmt->bind_param('iiiids',$_SESSION['user_id'],$catId,$score,$total,$pct,$status);$stmt->execute();$assessmentId=$conn->insert_id;
foreach($questions as $row){$selected=$_POST['q_'.$row['id']]??null;$correct=($selected===$row['correct_option'])?1:0;$a=$conn->prepare('INSERT INTO assessment_answers(assessment_id,question_id,selected_option,is_correct) VALUES(?,?,?,?)');$a->bind_param('iisi',$assessmentId,$row['id'],$selected,$correct);$a->execute();}
header('Location: result.php?id='.$assessmentId);exit;
}
$pageTitle='Assessment';include 'includes/header.php';?>
<div class="card p-4"><div class="d-flex justify-content-between"><div><h3><?=htmlspecialchars($cat['name'])?> Assessment</h3><p class="text-muted"><?=count($questions)?> questions · Pass mark 50%</p></div><div class="badge bg-dark fs-6 h-100" id="timer">10:00</div></div>
<form method="post" id="assessmentForm" onsubmit="return confirmSubmit()">
<?php foreach($questions as $i=>$row):?><div class="mb-4"><h5><?=$i+1?>. <?=htmlspecialchars($row['question'])?></h5>
<?php foreach(['A'=>'option_a','B'=>'option_b','C'=>'option_c','D'=>'option_d'] as $letter=>$field):?><label class="option-card d-block"><input type="radio" name="q_<?=$row['id']?>" value="<?=$letter?>" required> <strong><?=$letter?>.</strong> <?=htmlspecialchars($row[$field])?></label><?php endforeach;?>
</div><?php endforeach;?><button class="btn btn-success btn-lg">Submit Assessment</button></form></div>
<script>startTimer(600,'timer','assessmentForm');</script>
<?php include 'includes/footer.php'; ?>