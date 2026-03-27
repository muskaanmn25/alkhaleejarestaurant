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
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
    body { background:#f4efec; display:flex; justify-content:center; align-items:center; min-height:100vh; flex-direction:column;}
    
    .track-container { background:white; padding:40px; border-radius:15px; box-shadow:0 10px 30px rgba(0,0,0,0.08); width:100%; max-width:450px; text-align:center; }
    .track-container h2 { font-family:'Playfair Display', serif; font-size:32px; color:#111; margin-bottom:25px; }
    
    .form-group { margin-bottom:20px; text-align:left; }
    .form-group label { display:block; font-weight:500; margin-bottom:8px; color:#333; font-size:14px; }
    .form-group input { width:100%; padding:14px; border:1px solid #ddd; border-radius:10px; background:#f9f9f9; outline:none; font-family:inherit; transition:0.3s;}
    .form-group input:focus { border-color:#7a1f2b; background:#fff;}
    
    .btn { width:100%; padding:14px; background:#7a1f2b; color:white; border:none; border-radius:10px; font-size:16px; font-weight:500; cursor:pointer; box-shadow:0 5px 15px rgba(122,31,43,0.3); transition:0.3s; margin-top:10px;}
    .btn:hover { background:#5c1520; transform:translateY(-2px); box-shadow:0 8px 20px rgba(122,31,43,0.4); }
    
    .error { color:#e74c3c; background:#fce8e6; padding:12px; border-radius:8px; margin-bottom:20px; font-weight:500; font-size:14px;}
    
    .result-card { background:#fff; border:1px solid #7a1f2b; border-radius:12px; padding:25px; margin-top:20px; text-align:left; box-shadow:0 5px 15px rgba(122,31,43,0.05);}
    .result-card h3 { font-family:'Playfair Display', serif; color:#7a1f2b; margin-top:0; border-bottom:1px solid #eee; padding-bottom:15px; margin-bottom:15px;}
    .result-item { margin-bottom:15px; font-size:15px; display:flex; justify-content:space-between; align-items:center;}
    .result-item strong { color:#777; font-weight:500;}
    .result-val { font-weight:600; color:#111; text-align:right;}
    
    .status-badge { display:inline-block; padding:6px 15px; border-radius:30px; color:white; font-size:14px; font-weight:500; }
    .status-pending { background:#f39c12; }
    .status-confirmed { background:#2ecc71; }
    .status-cancelled { background:#e74c3c; }

    .nav-links { margin-top:30px; }
    .nav-links a { color:#777; text-decoration:none; font-weight:500; transition:0.3s;}
    .nav-links a:hover { color:#7a1f2b; text-decoration:underline; }
</style>
</head>
<body>

<div class="track-container">
    <h2>Track Booking</h2>
    
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
            <div class="result-item"><strong>Name</strong> <span class="result-val"><?php echo htmlspecialchars($reservation_data['full_name']); ?></span></div>
            <div class="result-item"><strong>Date</strong> <span class="result-val"><?php echo date('M d, Y', strtotime($reservation_data['reservation_date'])); ?></span></div>
            <div class="result-item"><strong>Time</strong> <span class="result-val"><?php echo date('h:i A', strtotime($reservation_data['reservation_time'])); ?></span></div>
            <div class="result-item"><strong>Guests</strong> <span class="result-val"><?php echo htmlspecialchars($reservation_data['no_of_people']); ?> guests</span></div>
            <div class="result-item" style="margin-top:20px;">
                <strong>Status</strong> 
                <span class="status-badge status-<?php echo strtolower($reservation_data['status']); ?>">
                    <?php echo ucfirst($reservation_data['status']); ?>
                </span>
            </div>
            
            <?php if($reservation_data['status'] == 'confirmed'): ?>
                <div class="result-item" style="border-top:1px dashed #ddd; padding-top:15px; margin-top:15px;">
                    <strong>Table Number</strong> 
                    <?php if(!empty($reservation_data['table_number'])): ?>
                        <span style='font-size:24px; font-weight:700; color:#7a1f2b;'><?php echo htmlspecialchars($reservation_data['table_number']); ?></span>
                    <?php else: ?>
                        <span style="color:#f39c12; font-weight:500; font-size:13px;">Waiting to be assigned</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 25px;">
            <a href="track_reservation.php" class="btn" style="text-decoration:none; display:block;">Check Another</a>
        </div>
    <?php endif; ?>

    <div class="nav-links">
        <a href="mainpage.html">&larr; Return to Home</a>
    </div>
</div>

</body>
</html>
