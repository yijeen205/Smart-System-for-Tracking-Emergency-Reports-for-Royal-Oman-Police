<?php
include 'config.php';
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $role = $_POST['role'];
    $dob = $_POST['dob'];
    $mobile = $_POST['mobile'];
    $civil = $_POST['civil'];

    $sql = "INSERT INTO users (username, password, role, dob, mobile, civil) VALUES('$username', '$password', '$role', '$dob', '$mobile', '$civil')";

    if (mysqli_query($conn, $sql)) {
        header("Location: login.php");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registration - Royal Oman Police</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; min-height: 100vh; background: linear-gradient(135deg, #b8cfe8 0%, #a8c4e0 30%, #c5d8ec 60%, #d0e2f0 100%); display: flex; flex-direction: column; }
  header { background-color: #1a3a6b; padding: 10px 20px; display: flex; align-items: center; }
  header .header-text { color: white; font-size: 14px; font-weight: 600; }
  main { flex: 1; display: flex; justify-content: center; align-items: center; padding: 20px; }
  .register-card { width: 100%; max-width: 380px; border-radius: 8px; overflow: hidden; box-shadow: 0 8px 32px rgba(26,58,107,0.25); }
  .register-card-header { background-color: #1a3a6b; color: white; text-align: center; padding: 15px; font-size: 16px; }
  .register-card-body { background-color: white; padding: 25px 30px; display: flex; flex-direction: column; gap: 15px; }
  .form-group { display: flex; flex-direction: column; gap: 5px; }
  .form-group label { font-size: 13px; color: #333; font-weight: 500; }
  .form-group input, .form-group select { border: 1px solid #ccc; border-radius: 4px; padding: 10px 12px; font-size: 13px; outline: none; }
  .confirm-btn { background-color: #1a3a6b; color: white; border: none; border-radius: 50px; padding: 12px 50px; font-size: 15px; cursor: pointer; align-self: center; }
  .login-link { font-size: 13px; color: #1a3a6b; text-align: center; }
  .login-link a { color: #1a3a6b; font-weight: 600; }
  footer { background-color: #1a3a6b; padding: 12px; display: flex; justify-content: center; }
  footer span { color: white; font-size: 12px; }
</style>
</head>
<body>
<header><div class="header-text">🛡️ ROYAL OMAN POLICE | شرطة عُمان السلطانية</div></header>
<main>
<div class="register-card">
<div class="register-card-header">Registration</div>
 <div class="register-card-body">
 <form method="POST">
 <div class="form-group">
   <label>Username</label>
   <input type="text" name="username" placeholder="Enter your username" required>
 </div>
 <div class="form-group">
   <label>Date of Birth</label>
   <input type="date" name="dob" required>
 </div>
 <div class="form-group">
   <label>Mobile Number</label>
   <input type="tel" name="mobile" placeholder="Enter your mobile number" required>
 </div>
 <div class="form-group">
   <label>Civil Number</label>
   <input type="text" name="civil" placeholder="Enter your civil ID" required>
 </div>
 <div class="form-group">
   <label>Password</label>
   <input type="password" name="password" placeholder="Enter your password" required>
 </div>
 <div class="form-group">
   <label>Account Type</label>
   <select name="role" required>
     <option value="">Select account type</option>
     <option value="reporter">Reporter (Normal User)</option>
     <option value="officer">Police Officer</option>
   </select>
 </div>
 <br>
 <button type="submit" class="confirm-btn">Register</button>
 <br><br>
 <div class="login-link">Already have an account? <a href="login.php">Login here</a></div>
 </form>
 </div>
</div>
</main>
<footer><span>🛡️ ROYAL OMAN POLICE | شرطة عُمان السلطانية</span></footer>
</body>
</html>