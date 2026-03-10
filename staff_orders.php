<?php
session_start();

if(!isset($_SESSION['staff_id'])){
    header("Location: staff_login.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","alkhaleej_db");
if(!$conn){ die("Connection Failed: " . mysqli_connect_error()); }

/* ===== UPDATE ORDER STATUS ===== */
if(isset($_POST['update_status'])){
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    mysqli_query($conn,"UPDATE orders SET status='$status' WHERE id='$order_id'");
    header("Location: staff_orders.php");
    exit();
}

/* ===== DELETE ORDER ===== */
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn,"DELETE FROM orders WHERE id='$id'");
    header("Location: staff_orders.php");
    exit();
}

/* ===== FETCH ORDERS ===== */
$orders = mysqli_query($conn,"SELECT * FROM orders ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Orders - Staff</title>
<style>
body{ font-family: Arial; margin:0; display:flex; background:#f4efec; }
.sidebar{ width:250px; background:#7a1f2b; height:100vh; padding:30px 20px; color:white; }
.sidebar h2{text-align:center; margin-bottom:40px;}
.sidebar a{display:block; color:white; text-decoration:none; padding:10px; margin-bottom:10px; border-radius:5px;}
.sidebar a:hover{background:#2f6d80;}
.main{flex:1; padding:30px;}
h1{margin-bottom:20px;}
table{width:100%; border-collapse:collapse; background:white;}
th, td{padding:12px; border-bottom:1px solid #ddd; text-align:center;}
th{background:#7a1f2b; color:white;}
select{padding:5px;}
button{padding:6px 10px; background:#1f4e5f; color:white; border:none; border-radius:4px; cursor:pointer;}
.delete{background:red;}
.view{background:#28a745;}
.status-pending{ color:orange; font-weight:bold; }
.status-preparing{ color:blue; font-weight:bold; }
.status-completed{ color:green; font-weight:bold; }
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

<h1>Manage Orders</h1>

<table>
<tr>
    <th>ID</th>
    <th>Customer ID</th>
    <th>Order Date</th>
    <th>Order Type</th>
    <th>Total Amount</th>
    <th>Status</th>
    <th>Update</th>
    <th>View</th>
    <th>Delete</th>
</tr>

<?php while($row = mysqli_fetch_assoc($orders)){ ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['customer_id']; ?></td>
    <td><?php echo $row['order_date']; ?></td>
    <td><?php echo $row['order_type']; ?></td>
    <td>₹ <?php echo $row['total_amount']; ?></td>

    <td class="status-<?php echo $row['status']; ?>">
        <?php echo ucfirst($row['status']); ?>
    </td>

    <td>
        <form method="POST">
            <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
            <select name="status">
                <option value="pending" <?php if($row['status']=='pending') echo "selected"; ?>>Pending</option>
                <option value="preparing" <?php if($row['status']=='preparing') echo "selected"; ?>>Preparing</option>
                <option value="completed" <?php if($row['status']=='completed') echo "selected"; ?>>Completed</option>
            </select>
            <button name="update_status">Update</button>
        </form>
    </td>

    <td>
        <a href="view_order.php?order_id=<?php echo $row['id']; ?>">
            <button class="view">View</button>
        </a>
    </td>

    <td>
        <a href="staff_orders.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?');">
            <button class="delete">Delete</button>
        </a>
    </td>
</tr>
<?php } ?>
</table>

</div>
</body>
</html>