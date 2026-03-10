<?php
include "db.php"; // Database connection

$reservations = mysqli_query($conn, "
    SELECT r.reservation_id, c.customer_name, r.reservation_date, r.reservation_time, r.guests, r.status 
    FROM reservations r 
    JOIN customers c ON r.customer_id = c.customer_id 
    WHERE r.status='pending'
");

while($row = mysqli_fetch_assoc($reservations)){
    echo $row['customer_name']." - ".$row['reservation_date']." ".$row['reservation_time']." - Guests: ".$row['guests'];
    echo " | <a href='confirm_reservation.php?id=".$row['reservation_id']."&action=confirm'>Confirm</a>";
    echo " | <a href='confirm_reservation.php?id=".$row['reservation_id']."&action=reject'>Reject</a><br>";
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage Reservations - Staff</title>
<style>
body{
    font-family: Arial;
    margin:0;
    display:flex;
    background:#f4efec;
}

.sidebar{
    width:250px;
    background:#7a1f2b;
    height:100vh;
    padding:30px 20px;
    color:white;
}

.sidebar h2{
    text-align:center;
    margin-bottom:40px;
}

.sidebar a{
    display:block;
    color:white;
    text-decoration:none;
    padding:10px;
    margin-bottom:10px;
    border-radius:5px;
}

.sidebar a:hover{
    background:#9e2f3d;
}

.main{
    flex:1;
    padding:30px;
}

table{
    width:100%;
    border-collapse:collapse;
    background:white;
}

th, td{
    padding:12px;
    border-bottom:1px solid #ddd;
    text-align:center;
}

th{
    background:#7a1f2b;
    color:white;
}

select{
    padding:5px;
}

button{
    padding:6px 10px;
    background:#7a1f2b;
    color:white;
    border:none;
    border-radius:4px;
    cursor:pointer;
}

button:hover{
    background:#9e2f3d;
}

.delete{
    background:red;
}

.status-pending{ color:#ff9800; font-weight:bold; }
.status-confirmed{ color:#28a745; font-weight:bold; }
.status-cancelled{ color:#dc3545; font-weight:bold; }
</style>
</head>

<body>

<div class="sidebar">
    <h2>Staff Panel</h2>
    <a href="staff_dashboard.php">Dashboard</a>
    <a href="staff_orders.php">Manage Orders</a>
    <a href="staff_reservations.php">Manage Reservations</a>
</div>

<div class="main">

<h1>Manage Reservations</h1>

<table>
<tr>
    <th>ID</th>
    <th>Customer ID</th>
    <th>Date</th>
    <th>Time</th>
    <th>No. of People</th>
    <th>Status</th>
    <th>Table No.</th>
    <th>Update</th>
    <th>Delete</th>
</tr>

<?php while($row = mysqli_fetch_assoc($reservations)){ ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['customer_id']; ?></td>
    <td><?php echo $row['reservation_date']; ?></td>
    <td><?php echo $row['reservation_time']; ?></td>
    <td><?php echo $row['no_of_people']; ?></td>

    <td class="status-<?php echo $row['status']; ?>">
        <?php echo ucfirst($row['status']); ?>
    </td>

    <td><?php echo $row['table_number']; ?></td>

    <td>
        <form method="POST">
            <input type="hidden" name="reservation_id" value="<?php echo $row['id']; ?>">
            <select name="status">
                <option value="pending" <?php if($row['status']=='pending') echo 'selected'; ?>>Pending</option>
                <option value="confirmed" <?php if($row['status']=='confirmed') echo 'selected'; ?>>Confirmed</option>
                <option value="cancelled" <?php if($row['status']=='cancelled') echo 'selected'; ?>>Cancelled</option>
            </select>
            <button name="update_status">Update</button>
        </form>
    </td>

    <td>
        <a href="staff_reservations.php?delete=<?php echo $row['id']; ?>">
            <button class="delete">Delete</button>
        </a>
    </td>
</tr>
<?php } ?>

</table>

</div>
</body>
</html>