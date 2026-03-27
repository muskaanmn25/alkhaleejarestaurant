<?php
include "db.php";

$reservation_found = false;
$error = '';
$reservation_data = null;

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $reservation_id = mysqli_real_escape_string($conn, $_POST['reservation_id']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    $query = "SELECT r.*, c.full_name, c.email 
              FROM reservation r
              JOIN customers c ON r.customer_id = c.customer_id
              WHERE r.reservation_id = '$reservation_id' AND c.phone = '$phone'";
    
    $result = mysqli_query($conn, $query);
    if($result && mysqli_num_rows($result) > 0){
        $reservation_found = true;
        $reservation_data = mysqli_fetch_assoc($result);
    } else {
        $error = "No matching reservation found for the provided ID and Phone number.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Track Reservation - Al-Khaleej</title>
<style>
body { font-family: 'Poppins', Arial, sans-serif; background: #f8f5f2; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; flex-direction: column; }
.track-container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 500px; text-align:center; }
.track-container h2 { color: #7a2c2c; font-size: 26px; margin-bottom: 20px; }
.form-group { margin-bottom: 20px; text-align: left; }
.form-group label { display: block; font-weight: bold; margin-bottom: 5px; color:#333; }
.form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
.btn { width: 100%; padding: 12px; background: #d4af37; border: none; color: white; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; }
.btn:hover { background: #b5952f; }
.error { color: #d9534f; margin-bottom: 20px; font-weight:bold; }

.result-card { background: #fffcf5; padding: 20px; border: 1px solid #e6d3a5; border-radius: 8px; margin-top:20px; text-align: left;}
.result-card h3 { color: #7a2c2c; margin-top: 0; border-bottom: 2px solid #e6d3a5; padding-bottom:10px;}
.result-item { margin-bottom: 15px; font-size: 15px; }
.result-item strong { display:inline-block; width: 120px; color:#555;}
.status-badge { display:inline-block; padding: 5px 10px; border-radius: 15px; color:white; font-size: 14px; font-weight: bold; }
.status-pending { background: #ffc107; color: #333; }
.status-confirmed { background: #28a745; }
.status-cancelled { background: #dc3545; }

.nav-links { margin-top: 20px; }
.nav-links a { color: #7a2c2c; text-decoration: none; font-weight:bold; }
.nav-links a:hover { text-decoration: underline; }
</style>
</head>
<body>

<div class="track-container">
    <h2>Track Your Reservation</h2>
    
    <?php if(!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if(!$reservation_found): ?>
    <form method="POST">
        <div class="form-group">
            <label>Reservation ID</label>
            <input type="number" name="reservation_id" required placeholder="Ex. 1042">
        </div>
        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone" required placeholder="Enter the phone used for booking">
        </div>
        <button type="submit" class="btn">Check Status</button>
    </form>
    <?php else: ?>
        <div class="result-card">
            <h3>Reservation Details</h3>
            <div class="result-item"><strong>Name:</strong> <?php echo htmlspecialchars($reservation_data['full_name']); ?></div>
            <div class="result-item"><strong>Date:</strong> <?php echo htmlspecialchars($reservation_data['reservation_date']); ?></div>
            <div class="result-item"><strong>Time:</strong> <?php echo htmlspecialchars($reservation_data['reservation_time']); ?></div>
            <div class="result-item"><strong>Guests:</strong> <?php echo htmlspecialchars($reservation_data['no_of_people']); ?> guests</div>
            <div class="result-item">
                <strong>Status:</strong> 
                <span class="status-badge status-<?php echo strtolower($reservation_data['status']); ?>">
                    <?php echo ucfirst($reservation_data['status']); ?>
                </span>
            </div>
            <?php if($reservation_data['status'] == 'confirmed'): ?>
                <div class="result-item">
                    <strong>Table Number:</strong> 
                    <?php 
                        echo !empty($reservation_data['table_number']) 
                            ? "<span style='font-size: 1.2em; font-weight:bold; color:#7a2c2c;'>" . htmlspecialchars($reservation_data['table_number']) . "</span>" 
                            : "Waiting to be assigned by staff"; 
                    ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 20px;">
            <a href="track_reservation.php" class="btn" style="text-decoration:none; display:inline-block; padding: 10px 20px; width:auto;">Check Another</a>
        </div>
    <?php endif; ?>

    <div class="nav-links">
        <a href="mainpage.html">&larr; Return to Home</a>
    </div>
</div>

</body>
</html>
