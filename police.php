<?php
include 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Update report status
if (isset($_GET['update_status'])) {
    $id = $_GET['update_status'];
    $new_status = $_GET['status'];
    mysqli_query($conn, "UPDATE reports SET status='$new_status' WHERE id=$id");
    header("Location: police.php");
    exit();
}

$sql = "SELECT * FROM reports ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Police Officer - Royal Oman Police</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; min-height: 100vh; background-color: #f0f4f8; display: flex; flex-direction: column; }
  header { background-color: #1a3a6b; padding: 12px 20px; display: flex; align-items: center; justify-content: space-between; }
  header .header-text { color: white; font-size: 14px; font-weight: 600; }
  header div { display: flex; gap: 10px; }
  header a { color: white; font-size: 13px; text-decoration: none; background: #2a4a8a; padding: 6px 15px; border-radius: 20px; }
  main { flex: 1; padding: 20px; max-width: 1100px; margin: 0 auto; width: 100%; display: flex; flex-direction: column; gap: 20px; }
  h2 { color: #1a3a6b; font-size: 18px; border-bottom: 2px solid #1a3a6b; padding-bottom: 8px; }
  .map-section { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
  .reports-section { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
  table { width: 100%; border-collapse: collapse; font-size: 13px; }
  th { background-color: #1a3a6b; color: white; padding: 10px; text-align: left; }
  td { padding: 10px; border-bottom: 1px solid #eee; color: #333; }
  tr:hover { background-color: #f5f5f5; }
  .badge-process { background: #fff3cd; color: #856404; padding: 4px 10px; border-radius: 20px; font-size: 11px; }
  .badge-onway { background: #cfe2ff; color: #084298; padding: 4px 10px; border-radius: 20px; font-size: 11px; }
  .badge-arrived { background: #d1e7dd; color: #0a3622; padding: 4px 10px; border-radius: 20px; font-size: 11px; }
  .btn-status { border: none; padding: 4px 10px; border-radius: 15px; font-size: 11px; cursor: pointer; text-decoration: none; display: inline-block; margin: 2px; }
  .btn-onway { background: #0d6efd; color: white; }
  .btn-arrived { background: #28a745; color: white; }
  .btn-process { background: #ffc107; color: #333; }
  footer { background-color: #1a3a6b; padding: 15px; display: flex; justify-content: center; }
  footer span { color: white; font-size: 12px; }
</style>
</head>
<body>
<header>
  <div class="header-text">🛡️ ROYAL OMAN POLICE | شرطة عُمان السلطانية</div>
  <div>
    <a href="admin.php">⚙️ Admin Panel</a>
    <a href="logout.php">Logout</a>
  </div>
</header>

<main>
  <h2>👮 Police Officer Dashboard</h2>
  <p style="color:#1a3a6b;">Welcome, <?php echo $_SESSION['username']; ?>!</p>

  <div class="map-section">
    <h2 style="margin-bottom:15px;">🗺️ Incident Location Map</h2>
    <div id="map" style="width:100%; height:350px; border-radius:8px;"></div>
  </div>

  <div class="reports-section">
    <h2 style="margin-bottom:15px;">📋 Emergency Reports</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Phone</th>
          <th>Civil No.</th>
          <th>Incident Type</th>
          <th>Details</th>
          <th>Location</th>
          <th>Status</th>
          <th>Update Status</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
          <td><?php echo $row['id']; ?></td>
          <td><?php echo $row['reporter_name']; ?></td>
          <td><?php echo $row['phone']; ?></td>
          <td><?php echo $row['civil_number']; ?></td>
          <td><?php echo $row['incident_type']; ?></td>
          <td><?php echo $row['details']; ?></td>
          <td><?php echo $row['location']; ?></td>
          <td>
            <?php if($row['status'] == 'Under Process') { ?>
              <span class="badge-process">Under Process</span>
            <?php } elseif($row['status'] == 'On the way') { ?>
              <span class="badge-onway">On the way</span>
            <?php } else { ?>
              <span class="badge-arrived">Arrived</span>
            <?php } ?>
          </td>
          <td>
            <a href="police.php?update_status=<?php echo $row['id']; ?>&status=Under+Process" class="btn-status btn-process">Under Process</a>
            <a href="police.php?update_status=<?php echo $row['id']; ?>&status=On+the+way" class="btn-status btn-onway">On the way</a>
            <a href="police.php?update_status=<?php echo $row['id']; ?>&status=Arrived" class="btn-status btn-arrived">Arrived</a>
          </td>
          <td><?php echo $row['created_at']; ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</main>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;
        var map = L.map('map').setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        L.marker([lat, lng]).addTo(map).bindPopup('📍 Incident Location').openPopup();
        L.marker([lat + 0.005, lng + 0.005]).addTo(map).bindPopup('🚓 Patrol Location');
    });
}
</script>

<footer><span>🛡️ ROYAL OMAN POLICE | شرطة عُمان السلطانية</span></footer>
</body>
</html>