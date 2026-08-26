<?php
require 'config/db.php'; session_start();
if(isset($_SESSION['user_id'])){header('Location: dashboard.php');exit;}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
$name=trim($_POST['name']);$email=trim($_POST['email']);$password=$_POST['password'];
if(strlen($name)<3 || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($password)<6){$error='Enter valid details. Password must be at least 6 characters.';}
else{
$stmt=$conn->prepare('SELECT id FROM users WHERE email=?');$stmt->bind_param('s',$email);$stmt->execute();
if($stmt->get_result()->num_rows){$error='Email already registered.';}else{
$hash=password_hash($password,PASSWORD_DEFAULT);$stmt=$conn->prepare('INSERT INTO users(name,email,password) VALUES(?,?,?)');$stmt->bind_param('sss',$name,$email,$hash);$stmt->execute();
header('Location: login.php?registered=1');exit;
}}}
$pageTitle='Register';include 'includes/header.php'; ?>
<div class="row justify-content-center"><div class="col-md-6"><div class="card p-4"><h2>Create Student Account</h2><p class="text-muted">Register to begin your skill assessment journey.</p>
<?php if($error):?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php endif;?>
<form method="post"><div class="mb-3"><label class="form-label">Full Name</label><input class="form-control" name="name" required></div>
<div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" required></div>
<div class="mb-3"><label class="form-label">Password</label><input type="password" class="form-control" name="password" minlength="6" required></div>
<button class="btn btn-primary w-100">Create Account</button></form></div></div></div>
<?php include 'includes/footer.php'; ?>