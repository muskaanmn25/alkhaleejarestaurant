<?php
include "db.php";

$name   = $_POST['customer_name'] ?? '';
$phone  = $_POST['phone'] ?? '';
$email  = $_POST['email'] ?? '';
$date   = $_POST['reservation_date'] ?? '';
$time   = $_POST['reservation_time'] ?? '';
$people = $_POST['guests'] ?? '';

if(empty($name) || empty($phone) || empty($date) || empty($time) || empty($people)){
    die("<h3>All fields are required!</h3>");
}

$getCustomer = "SELECT customer_id FROM customers WHERE phone = '$phone'";
$result = mysqli_query($conn, $getCustomer);

if(mysqli_num_rows($result) > 0){
    $row = mysqli_fetch_assoc($result);
    $customer_id = $row['customer_id'];
    mysqli_query($conn, "UPDATE customers SET full_name='$name', email='$email' WHERE customer_id='$customer_id'");
} else {
    mysqli_query($conn, "INSERT INTO customers (full_name, phone, email) VALUES ('$name', '$phone', '$email')");
    $customer_id = mysqli_insert_id($conn);
}

$sql = "INSERT INTO reservation (customer_id, reservation_date, reservation_time, no_of_people) VALUES ('$customer_id', '$date', '$time', '$people')";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reservation Status - Al-Khaleej</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
    body { background:#f4efec; display:flex; justify-content:center; align-items:center; height:100vh; }
    .message-box { background:white; padding:40px; border-radius:15px; box-shadow:0 10px 30px rgba(0,0,0,0.08); text-align:center; max-width:450px; width:90%; }
    .message-box h3 { font-family:'Playfair Display', serif; font-size:32px; margin-bottom:20px; color:#111; }
    .message-box p { color:#555; margin-bottom:25px; font-size:15px; line-height:1.6; }
    .id-box { background:#fcf7f7; padding:20px; border-radius:10px; margin-bottom:25px; border:2px dashed #7a1f2b; }
    .id-box p { margin:0 0 5px 0; font-size:14px; font-weight:500; color:#777; }
    .id-box h2 { font-size:36px; color:#7a1f2b; font-weight:600; margin:0;}
    
    .btn-container { display:flex; gap:15px; justify-content:center; }
    .btn { padding:12px 25px; border:none; border-radius:8px; cursor:pointer; text-decoration:none; font-weight:500; transition:0.3s; flex:1; text-align:center;}
    .btn-track { background:#7a1f2b; color:white; }
    .btn-track:hover { background:#5c1520; }
    .btn-home { background:#eee; color:#333; }
    .btn-home:hover { background:#ddd; }
    
    .error-text { color:#e74c3c !important; }
</style>
</head>
<body>
<div class="message-box">
    <?php if(mysqli_query($conn, $sql)): 
        $res_id = mysqli_insert_id($conn);
    ?>
        <h3>Reservation Successful!</h3>
        <p>Thank you, <?php echo htmlspecialchars($name); ?>. Your request for <?php echo htmlspecialchars($people); ?> guests on <?php echo date('M d', strtotime($date)); ?> at <?php echo date('h:i A', strtotime($time)); ?> has been received.</p>
        
        <div class="id-box">
            <p>Your Reservation ID</p>
            <h2>#<?php echo $res_id; ?></h2>
        </div>
        
        <p style="font-size:13px;">Save this ID to track your table assignment later.</p>
        
        <div class="btn-container">
            <a href="track_reservation.php" class="btn btn-track">Track Status</a>
            <a href="mainpage.html" class="btn btn-home">Home</a>
        </div>
    <?php else: ?>
        <h3 class="error-text">Reservation Failed</h3>
        <p>Something went wrong. Please try again later.</p>
        <p style="font-size:12px; color:#999;"><?php echo mysqli_error($conn); ?></p>
        <a href="mainpage.html" class="btn btn-home" style="width:100%; display:block;">Return Home</a>
    <?php endif; ?>
</div>
</body>
</html>