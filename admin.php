<?php
include 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Delete user
if (isset($_GET['delete_user'])) {
    $id = $_GET['delete_user'];
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    header("Location: admin.php");
}

// Update report status
if (isset($_GET['resolve_report'])) {
    $id = $_GET['resolve_report'];
    mysqli_query($conn, "UPDATE reports SET status='Resolved' WHERE id=$id");
    header("Location: admin.php");
}

// Get all users
$users = mysqli_query($conn, "SELECT * FROM users");

// Get all reports
$reports = mysqli_query($conn, "SELECT * FROM reports ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Police Officer - Royal Oman Police</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; min-height: 100vh; background-color: #f0f4f8; display: flex; flex-direction: column; }
  header { background-color: #1a3a6b; padding: 12px 20px; display: flex; align-items: center; justify-content: space-between; }
  header .header-text { color: white; font-size: 14px; font-weight: 600; }
  header div { display: flex; gap: 10px; }
  header a { color: white; font-size: 13px; text-decoration: none; background: #2a4a8a; padding: 6px 15px; border-radius: 20px; }
  main { flex: 1; padding: 20px; max-width: 1100px; margin: 0 auto; width: 100%; display: flex; flex-direction: column; gap: 25px; }
  h2 { color: #1a3a6b; font-size: 18px; border-bottom: 2px solid #1a3a6b; padding-bottom: 8px; margin-bottom: 15px; }
  .section { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
  table { width: 100%; border-collapse: collapse; font-size: 13px; }
  th { background-color: #1a3a6b; color: white; padding: 10px; text-align: left; }
  td { padding: 10px; border-bottom: 1px solid #eee; color: #333; }
  tr:hover { background-color: #f5f5f5; }
  .btn-delete { background: #dc3545; color: white; border: none; padding: 5px 12px; border-radius: 15px; font-size: 11px; cursor: pointer; text-decoration: none; }
  .btn-resolve { background: #28a745; color: white; border: none; padding: 5px 12px; border-radius: 15px; font-size: 11px; cursor: pointer; text-decoration: none; }
  .badge-process { background: #fff3cd; color: #856404; padding: 4px 10px; border-radius: 20px; font-size: 11px; }
  .badge-resolved { background: #d1e7dd; color: #0a3622; padding: 4px 10px; border-radius: 20px; font-size: 11px; }
  .stats { display: flex; gap: 15px; flex-wrap: wrap; }
  .stat-card { background: #1a3a6b; color: white; padding: 20px 30px; border-radius: 10px; text-align: center; flex: 1; }
  .stat-card .number { font-size: 30px; font-weight: 700; }
  .stat-card .label { font-size: 13px; opacity: 0.8; }
  footer { background-color: #1a3a6b; padding: 15px; display: flex; justify-content: center; }
  footer span { color: white; font-size: 12px; }
</style>
</head>
<body>
<header>
  <div class="header-text">🛡️ ROYAL OMAN POLICE | Police Officer</div>
  <div>
    <a href="police.php">👮 Officer Dashboard</a>
    <a href="logout.php">Logout</a>
  </div>
</header>

<main>
  <h2>👮 Police Officer Panel</h2>
  <p style="color:#1a3a6b;">Welcome, <?php echo $_SESSION['username']; ?>!</p>

  <!-- Statistics -->
  <div class="stats">
    <div class="stat-card">
      <div class="number"><?php echo mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users")); ?></div>
      <div class="label">Total Users</div>
    </div>
    <div class="stat-card">
      <div class="number"><?php echo mysqli_num_rows(mysqli_query($conn, "SELECT * FROM reports")); ?></div>
      <div class="label">Total Reports</div>
    </div>
    <div class="stat-card">
      <div class="number"><?php echo mysqli_num_rows(mysqli_query($conn, "SELECT * FROM reports WHERE status='Resolved'")); ?></div>
      <div class="label">Resolved Reports</div>
    </div>
    <div class="stat-card">
      <div class="number"><?php echo mysqli_num_rows(mysqli_query($conn, "SELECT * FROM reports WHERE status='Under Process'")); ?></div>
      <div class="label">Under Process</div>
    </div>
  </div>

  <!-- Users Table -->
  <div class="section">
    <h2>👥 Manage Users</h2>
    <table>
 <thead>
  <tr>
   <th>ID</th>
    <th>Username</th>
  <th>Role</th>
 <th>Created At</th>
   <th>Action</th>
 </tr>
 </thead>
 <tbody>
        <?php while($user = mysqli_fetch_assoc($users)) { ?>
        <tr>
          <td><?php echo $user['id']; ?></td>
          <td><?php echo $user['username']; ?></td>
          <td><?php echo $user['role']; ?></td>
          <td><?php echo $user['created_at']; ?></td>
          <td>
            <a href="admin.php?delete_user=<?php echo $user['id']; ?>" 
               class="btn-delete" 
               onclick="return confirm('Are you sure you want to delete this user?')">
              Delete
 </a>
 </td>
 </tr>
 <?php } ?>
</tbody>
 </table>
  </div>

  <!-- Reports Table -->
  <div class="section">
<h2>📋 Manage Reports</h2>
<table>
<thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Phone</th>
          <th>Incident Type</th>
          <th>Details</th>
          <th>Location</th>
          <th>Status</th>
          <th>Date</th>
          <th>Action</th>
 </tr>
 </thead>
<tbody>
 <?php while($report = mysqli_fetch_assoc($reports)) { ?>
 <tr>
 <td><?php echo $report['id']; ?></td>
<td><?php echo $report['reporter_name']; ?></td>
 <td><?php echo $report['phone']; ?></td>
  <td><?php echo $report['incident_type']; ?></td>
 <td><?php echo $report['details']; ?></td>
<td><?php echo $report['location']; ?></td>
 <td>
<?php if($report['status'] == 'Resolved') { ?>
 <span class="badge-resolved">Resolved</span>
<?php } else { ?>
 <span class="badge-process">Under Process</span>
<?php } ?>
 </td>
<td><?php echo $report['created_at']; ?></td>
 <td>
<?php if($report['status'] != 'Resolved') { ?>
 <a href="admin.php?resolve_report=<?php echo $report['id']; ?>" 
               class="btn-resolve">
              Resolve
 </a>
  <?php } ?>
</td>
</tr>
 <?php } ?>
</tbody>
</table>
</div>
</main>

<footer><span>🛡️ ROYAL OMAN POLICE | شرطة عُمان السلطانية</span></footer>
</body>
</html>