<?php
include "db.php";

$id = $_GET['id'];

if(isset($_POST['update'])) {
    $status = $_POST['status'];

    $update = "UPDATE orders SET status='$status' WHERE id='$id'";
    mysqli_query($conn, $update);

    header("Location: admin_orders.php");
}
?>

<form method="POST">
    <h3>Update Order Status</h3>

    <select name="status">
        <option value="pending">Pending</option>
        <option value="preparing">Preparing</option>
        <option value="completed">Completed</option>
        <option value="cancelled">Cancelled</option>
    </select>

    <button type="submit" name="update">Update</button>
</form>