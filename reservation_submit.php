<?php
include "db.php";

// GET FORM DATA
$name   = $_POST['customer_name'] ?? '';
$phone  = $_POST['phone'] ?? '';
$email  = $_POST['email'] ?? '';
$date   = $_POST['reservation_date'] ?? '';
$time   = $_POST['reservation_time'] ?? '';
$people = $_POST['guests'] ?? '';

// VALIDATION
if(empty($name) || empty($phone) || empty($date) || empty($time) || empty($people)){
    die("<h3>All fields are required!</h3>");
}

// 🔹 STEP 1: Get or Create customer_id
$getCustomer = "SELECT customer_id FROM customers WHERE phone = '$phone'";
$result = mysqli_query($conn, $getCustomer);

if(mysqli_num_rows($result) > 0){
    // Customer exists, update their name and email just in case
    $row = mysqli_fetch_assoc($result);
    $customer_id = $row['customer_id'];
    mysqli_query($conn, "UPDATE customers SET full_name='$name', email='$email' WHERE customer_id='$customer_id'");
} else {
    // New customer! Create them first so we have an ID for the reservation
    mysqli_query($conn, "INSERT INTO customers (full_name, phone, email) VALUES ('$name', '$phone', '$email')");
    $customer_id = mysqli_insert_id($conn);
}

// 🔹 STEP 2: Insert reservation
$sql = "INSERT INTO reservation
(customer_id, reservation_date, reservation_time, no_of_people) 
VALUES 
('$customer_id', '$date', '$time', '$people')";

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reservation Status</title>
<style>
body { font-family: 'Poppins', Arial, sans-serif; background: #f8f5f2; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
.message-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); text-align: center; max-width: 400px; }
.message-box h3 { color: #7a2c2c; font-size: 24px; margin-bottom: 20px; }
.message-box p { color: #555; margin-bottom: 30px; }
.btn { padding: 12px 25px; background: #d4af37; border: none; color: white; border-radius: 5px; cursor: pointer; text-decoration: none; font-weight: 500; }
.btn:hover { background: #b5952f; }
.error-text { color: #d9534f; }
</style>
</head>
<body>
<div class="message-box">
    <?php if(mysqli_query($conn, $sql)): 
        $res_id = mysqli_insert_id($conn);
    ?>
        <h3>Reservation Successful!</h3>
        <p>Thank you, <?php echo htmlspecialchars($name); ?>. Your reservation for <?php echo htmlspecialchars($people); ?> guests on <?php echo htmlspecialchars($date); ?> at <?php echo htmlspecialchars($time); ?> has been received.</p>
        <div style="background:#f4efec; padding:15px; border-radius:5px; margin-bottom:20px; border: 1px dashed #7a2c2c;">
            <p style="margin:0; color:#333; font-size:14px;">Please save your Reservation ID:</p>
            <h2 style="margin:5px 0; color:#7a2c2c; font-size:28px;">#<?php echo $res_id; ?></h2>
        </div>
        <p style="font-size:13px;">You can check your table number later using the Track Booking link on the home page.</p>
        <br>
        <a href="track_reservation.php" class="btn" style="background:#5cb85c; margin-right:5px; padding: 10px 15px;">Track Now</a>
        <a href="mainpage.html" class="btn" style="padding: 10px 15px;">Home</a>
    <?php else: ?>
        <h3 class="error-text">Reservation Failed</h3>
        <p>Error: <?php echo mysqli_error($conn); ?></p>
        <a href="mainpage.html" class="btn">Try Again</a>
    <?php endif; ?>
</div>
</body>
</html>