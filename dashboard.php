<?php
include 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['reporter_name'];
    $phone = $_POST['phone'];
    $civil = $_POST['civil_number'];
    $type = $_POST['incident_type'];
    $details = $_POST['details'];
    $location = $_POST['location'];
    $status = "Under Process";

    $sql = "INSERT INTO reports (reporter_name, phone, civil_number, incident_type, details, location, status) VALUES ('$name', '$phone', '$civil', '$type', '$details', '$location', '$status')";
    
    if (mysqli_query($conn, $sql)) {
        $success = "✅ Your report has been submitted successfully!";
        $new_report_id = mysqli_insert_id($conn);
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Get latest report for this user
$username = $_SESSION['username'];
$latest = mysqli_query($conn, "SELECT * FROM reports WHERE reporter_name='$username' ORDER BY created_at DESC LIMIT 1");
$report = mysqli_fetch_assoc($latest);
$patrol_status = $report ? $report['status'] : 'Under Process';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - Royal Oman Police</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; min-height: 100vh; background-color: #f0f4f8; display: flex; flex-direction: column; }
  header { background-color: #1a3a6b; padding: 12px 20px; display: flex; align-items: center; justify-content: space-between; }
  header .header-text { color: white; font-size: 14px; font-weight: 600; }
  header a { color: white; font-size: 13px; text-decoration: none; background: #2a4a8a; padding: 6px 15px; border-radius: 20px; }
  main { flex: 1; padding: 20px; display: flex; flex-direction: column; gap: 20px; max-width: 900px; margin: 0 auto; width: 100%; }
  h2 { color: #1a3a6b; font-size: 18px; border-bottom: 2px solid #1a3a6b; padding-bottom: 8px; }
  .report-form { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); display: flex; flex-direction: column; gap: 15px; }
  .form-row { display: flex; gap: 15px; }
  .form-group { flex: 1; display: flex; flex-direction: column; gap: 5px; }
  .form-group label { font-size: 13px; color: #333; font-weight: 600; }
  .form-group input, .form-group select, .form-group textarea { border: 1px solid #ccc; border-radius: 6px; padding: 10px 12px; font-size: 13px; outline: none; width: 100%; }
  .form-group textarea { height: 100px; resize: vertical; }
  .submit-btn { background-color: #1a3a6b; color: white; border: none; border-radius: 25px; padding: 12px 40px; font-size: 14px; cursor: pointer; align-self: center; }
  .success-msg { color: green; font-size: 14px; font-weight: 600; text-align: center; }
  .error-msg { color: red; font-size: 13px; text-align: center; }
  .map-section { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
  .status-section { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; }
  .status-card { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 15px 20px; border-radius: 8px; background: #f5f8ff; border: 1px solid #c8d8f0; }
  .status-card .status-icon { font-size: 28px; }
  .status-card .status-label { font-size: 12px; color: #666; }
  .status-card .status-value { font-size: 14px; color: #1a3a6b; font-weight: 700; }
  .badge-process { background: #fff3cd; color: #856404; padding: 5px 15px; border-radius: 20px; font-size: 12px; }
  .badge-onway { background: #cfe2ff; color: #084298; padding: 5px 15px; border-radius: 20px; font-size: 12px; }
  .badge-arrived { background: #d1e7dd; color: #0a3622; padding: 5px 15px; border-radius: 20px; font-size: 12px; }
  footer { background-color: #1a3a6b; padding: 15px; display: flex; justify-content: center; }
  footer span { color: white; font-size: 12px; }
</style>
</head>
<body>
<header>
  <div class="header-text">🛡️ ROYAL OMAN POLICE | شرطة عُمان السلطانية</div>
  <a href="logout.php">Logout</a>
</header>

<main>
  <h2>🚨 Emergency Report</h2>
  <p style="color:#1a3a6b; margin-bottom:10px;">Welcome, <?php echo $_SESSION['username']; ?>!</p>
  
  <div class="report-form">
    <form method="POST">
      <div class="form-row">
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" name="reporter_name" placeholder="Enter your full name" required>
        </div>
        <div class="form-group">
          <label>Phone Number</label>
          <input type="tel" name="phone" placeholder="Enter your phone number" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Civil Number</label>
          <input type="text" name="civil_number" placeholder="Enter your civil ID" required>
        </div>
        <div class="form-group">
          <label>Incident Type</label>
          <select name="incident_type" required>
            <option value="">Select incident type</option>
            <option>Traffic Accident</option>
            <option>Theft</option>
            <option>Fire</option>
            <option>Medical Emergency</option>
            <option>Other</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>📝 Additional Details</label>
        <textarea name="details" placeholder="Please describe the incident in detail..."></textarea>
      </div>
      <div class="form-group">
        <label>📍 Your Location (Auto-detected)</label>
        <input type="text" id="location_field" name="location" readonly style="background:#f5f5f5;color:#666;">
      </div>
      <?php if(isset($success)) echo "<p class='success-msg'>$success</p>"; ?>
      <?php if(isset($error)) echo "<p class='error-msg'>$error</p>"; ?>
      <button type="submit" class="submit-btn">Submit Report</button>
    </form>
  </div>

  <div class="map-section">
    <h2 style="margin-bottom:15px;">🗺️ Live Tracking Map</h2>
    <div id="map" style="width:100%; height:400px; border-radius:8px;"></div>
  </div>

  <div class="status-section">
    <div class="status-card">
      <div class="status-icon">📋</div>
      <div class="status-label">Patrol Status</div>
      <?php if($patrol_status == 'Under Process') { ?>
        <span class="badge-process">Under Process</span>
      <?php } elseif($patrol_status == 'On the way') { ?>
        <span class="badge-onway">On the way</span>
      <?php } elseif($patrol_status == 'Arrived') { ?>
        <span class="badge-arrived">Arrived</span>
      <?php } else { ?>
        <span class="badge-process"><?php echo $patrol_status; ?></span>
      <?php } ?>
    </div>
    <div class="status-card"><div class="status-icon">⏱️</div><div class="status-label">Estimated Time</div><div class="status-value">5 minutes</div></div>
    <div class="status-card"><div class="status-icon">👮</div><div class="status-label">Officer</div><div class="status-value">Mohammed Al Wahibi</div></div>
  </div>
</main>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;

        document.getElementById('location_field').value = lat + ", " + lng;

        var map = L.map('map').setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        L.marker([lat, lng]).addTo(map)
            .bindPopup('📍 Your Location').openPopup();
    });
}
</script>

<footer><span>🛡️ ROYAL OMAN POLICE | شرطة عُمان السلطانية</span></footer>
</body>
</html>