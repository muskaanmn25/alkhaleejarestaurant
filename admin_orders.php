<?php
session_start();
include("db.php");

// Get today's orders
$query = "SELECT orders.*, users.full_name AS customer_name
          FROM orders
          JOIN users ON orders.customer_id = users.id
          WHERE DATE(order_date) = CURDATE()
          ORDER BY order_date DESC";
		  
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Today's Orders</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Today's Orders</h2>

<table border="1" width="100%" cellpadding="10">
    <tr>
        <th>Order ID</th>
        <th>Customer Name</th>
        <th>Order Type</th>
        <th>Total Amount</th>
        <th>Status</th>
        <th>Order Time</th>
        <th>Action</th>
    </tr>

    <?php while($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['customer_name']; ?></td>
        <td><?php echo $row['order_type']; ?></td>
        <td><?php echo $row['total_amount']; ?></td>
        <td><?php echo $row['status']; ?></td>
        <td><?php echo $row['order_date']; ?></td>
        <td>
            <a href="update_order_status.php?id=<?php echo $row['id']; ?>">Update</a>
        </td>
    </tr>
    <?php } ?>

</table>

</body>
</html>