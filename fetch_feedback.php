<?php
include "db.php";
header('Content-Type: application/json');

$query = "SELECT customer_name, rating, comments, created_at FROM feedback WHERE status='approved' ORDER BY created_at DESC LIMIT 3";
$result = mysqli_query($conn, $query);

$feedbacks = [];
if($result){
    while($row = mysqli_fetch_assoc($result)){
        $feedbacks[] = $row;
    }
}
echo json_encode($feedbacks);
?>
