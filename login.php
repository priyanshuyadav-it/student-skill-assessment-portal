<?php
require 'config/db.php';session_start();$error='';
if(isset($_SESSION['user_id'])){header('Location: dashboard.php');exit;}
if($_SERVER['REQUEST_METHOD']==='POST'){
$email=trim($_POST['email']);$password=$_POST['password'];
$stmt=$conn->prepare('SELECT id,name,password,role FROM users WHERE email=?');$stmt->bind_param('s',$email);$stmt->execute();$u=$stmt->get_result()->fetch_assoc();
$valid=$u && (password_verify($password,$u['password']) || hash_equals($u['password'],md5($password)));
if($valid){$_SESSION['user_id']=$u['id'];$_SESSION['name']=$u['name'];$_SESSION['role']=$u['role'];header('Location: dashboard.php');exit;}
$error='Invalid email or password.';
}
$pageTitle='Login';include 'includes/header.php';?>
<div class="row justify-content-center"><div class="col-md-5"><div class="card p-4"><h2>Welcome Back</h2><p class="text-muted">Sign in to continue.</p>
<?php if(isset($_GET['registered'])):?><div class="alert alert-success">Registration successful. Please login.</div><?php endif;?>
<?php if($error):?><div class="alert alert-danger"><?=$error?></div><?php endif;?>
<form method="post"><input class="form-control mb-3" type="email" name="email" placeholder="Email" required><input class="form-control mb-3" type="password" name="password" placeholder="Password" required><button class="btn btn-primary w-100">Login</button></form>
<div class="small text-muted mt-3">Demo student: student@skillportal.test / student123</div></div></div></div>
<?php include 'includes/footer.php'; ?>