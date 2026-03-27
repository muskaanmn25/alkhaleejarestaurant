<?php
include "db.php";

$reservation_id = $_POST['reservation_id'] ?? '';
$table_number   = $_POST['table_number'] ?? '';

if($reservation_id && $table_number){
    $check = mysqli_query($conn, "SELECT status FROM reservation WHERE reservation_id='$reservation_id'");
    $row = mysqli_fetch_assoc($check);

    if($row['status'] === 'confirmed'){
        $update = "UPDATE reservation SET table_number='$table_number' WHERE reservation_id='$reservation_id'";
        mysqli_query($conn, $update);
        header("Location: staff_reservations.php");
        exit();
    } else {
        $error = "Cannot assign table. Reservation is not confirmed.";
    }
} else {
    $error = "Missing reservation ID or table number.";
}

if(isset($error)):
?>
<!DOCTYPE html>
<html>
<head>
    <title>Error - Assign Table</title>
    <style>
        body{font-family:'Segoe UI',Arial,sans-serif; background:#f4efec; display:flex; justify-content:center; align-items:center; height:100vh;}
        .card{background:white; padding:40px; border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,0.1); text-align:center; max-width:400px; width:100%;}
        h3{color:#d93025; margin-bottom:15px;}
        p{color:#555; margin-bottom:25px;}
        a{display:inline-block; padding:10px 20px; background:#7a1f2b; color:white; text-decoration:none; border-radius:5px;}
    </style>
</head>
<body>
    <div class="card">
        <h3>Action Failed</h3>
        <p><?php echo $error; ?></p>
        <a href="staff_reservations.php">Go Back</a>
    </div>
</body>
</html>
<?php endif; ?>