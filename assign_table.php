<?php
include "db.php";

$reservation_id = $_POST['reservation_id'] ?? '';
$table_number   = $_POST['table_number'] ?? '';

if($reservation_id && $table_number){
    // Only update if reservation is confirmed
    $check = mysqli_query($conn, "SELECT status FROM reservation WHERE reservation_id='$reservation_id'");
    $row = mysqli_fetch_assoc($check);

    if($row['status'] === 'confirmed'){
        $update = "UPDATE reservation 
                   SET table_number='$table_number' 
                   WHERE reservation_id='$reservation_id'";
        mysqli_query($conn, $update);
        header("Location: staff_reservations.php");
        exit();
    } else {
        die("Cannot assign table. Reservation is not confirmed.");
    }
} else {
    die("Missing reservation ID or table number.");
}
?>