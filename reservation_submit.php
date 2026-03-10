<?php
session_start();
include "db.php"; // your database connection

$customer_id = $_SESSION['customer_id']; // logged-in customer
$name = $_POST['name']; // customer name
$date = $_POST['date']; // reservation date
$time = $_POST['time']; // reservation time

// 1. Insert reservation
$sql = "INSERT INTO reservations (customer_id, name, date, time) VALUES ($customer_id, '$name', '$date', '$time')";
if(mysqli_query($conn, $sql)){
    
    $reservation_id = mysqli_insert_id($conn); // get new reservation ID
    
    // 2. Insert notification for this customer
    $notification_msg = "Your reservation #$reservation_id on $date at $time has been confirmed!";
    mysqli_query($conn, "INSERT INTO customer_notifications (customer_id, message) VALUES ($customer_id, '$notification_msg')");
    
    echo "Reservation confirmed and notification added!";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>