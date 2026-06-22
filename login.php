<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'officer') {
            header("Location: police.php");
        } else {
            header("Location: dashboard.php");
        }
        exit();
    } else {
        $error = "❌ Wrong username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Royal Oman Police</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; min-height: 100vh; background: linear-gradient(135deg, #b8cfe8 0%, #a8c4e0 30%, #c5d8ec 60%, #d0e2f0 100%); display: flex; flex-direction: column; }
  header { background-color: #1a3a6b; padding: 10px 20px; display: flex; align-items: center; }
  header .header-text { color: white; font-size: 14px; font-weight: 600; }
  main { flex: 1; display: flex; justify-content: center; align-items: center; padding: 20px; flex-direction: column; gap: 15px; }
  .login-card { width: 100%; max-width: 360px; border-radius: 8px; overflow: hidden; box-shadow: 0 8px 32px rgba(26,58,107,0.25); }
  .login-card-header { background-color: #1a3a6b; color: white; text-align: center; padding: 20px; font-size: 16px; }
  .login-card-body { background-color: white; padding: 35px 30px; display: flex; flex-direction: column; align-items: center; gap: 15px; }
  .form-group { width: 100%; display: flex; flex-direction: column; gap: 5px; }
  .form-group label { font-size: 13px; color: #333; }
  .form-group input { border: 1px solid #ccc; border-radius: 4px; padding: 10px 12px; font-size: 13px; outline: none; width: 100%; }
  .confirm-btn { background-color: #1a3a6b; color: white; border: none; border-radius: 50px; padding: 12px 50px; font-size: 15px; cursor: pointer; }
  .error-msg { color: red; font-size: 13px; }
  .register-link { font-size: 13px; color: #1a3a6b; }
  .register-link a { color: #1a3a6b; font-weight: 600; }
  footer { background-color: #1a3a6b; padding: 12px; display: flex; justify-content: center; }
  footer span { color: white; font-size: 12px; }
</style>
</head>
<body>
<header><div class="header-text">🛡️ ROYAL OMAN POLICE | شرطة عُمان السلطانية</div></header>
<main>
<div class="login-card">
<div class="login-card-header">Login</div>
<div class="login-card-body">
 <form method="POST" style="width:100%; display:flex; flex-direction:column; align-items:center; gap:15px;">
 <div class="form-group">
<label>Username</label>
<input type="text" name="username" placeholder="Enter your username" required>
</div>
<div class="form-group">
 <label>Password</label>
<input type="password" name="password" placeholder="Enter your password" required>
</div>
 <?php if(isset($error)) echo "<span class='error-msg'>$error</span>"; ?>
<button type="submit" class="confirm-btn">Login</button>
</form>
<div class="register-link">Don't have an account? <a href="register.php">Register here</a></div>
</div>
</div>
</main>
<footer><span>🛡️ ROYAL OMAN POLICE | شرطة عُمان السلطانية</span></footer>
</body>
</html>