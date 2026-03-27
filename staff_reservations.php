<?php
session_start();
include "db.php";

// 🔐 Protect page
if(!isset($_SESSION['staff_id'])){
    header("Location: staff_login.php");
    exit();
}

/* ===== UPDATE STATUS ===== */
if(isset($_POST['update_status'])){
    $id = intval($_POST['reservation_id']);
    $status = $_POST['status'];
    $table_number = mysqli_real_escape_string($conn, $_POST['table_number']);

    mysqli_query($conn,"UPDATE reservation SET status='$status', table_number='$table_number' WHERE reservation_id='$id'");
    header("Location: staff_reservations.php");
    exit();
}

/* ===== DELETE ===== */
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);

    mysqli_query($conn,"DELETE FROM reservation WHERE reservation_id='$id'");
    header("Location: staff_reservations.php");
    exit();
}

/* ===== FETCH DATA ===== */
$query = "
SELECT r.*, c.full_name 
FROM reservation r
LEFT JOIN customers c ON r.customer_id = c.customer_id
ORDER BY r.reservation_id DESC
";

$result = mysqli_query($conn, $query);

if(!$result){
    die("Query Failed: " . mysqli_error($conn));
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
    <h2>Al-Khaleej Staff Panel</h2>
    <a href="staff_dashboard.php">Dashboard</a>
    <a href="staff_orders.php">Manage Orders</a>
    <a href="staff_reservations.php">Manage Reservations</a>
</div>

<div class="main">

<h1>Manage Reservations</h1>

<table>
<tr>
    <th>ID</th>
    <th>Customer Name</th>
    <th>Date</th>
    <th>Time</th>
    <th>No. of People</th>
    <th>Status</th>
    <th>Table No.</th>
    <th>Update</th>
    <th>Delete</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)){ ?>
<tr>
    <td><?php echo $row['reservation_id']; ?></td>
    <td><?php echo !empty($row['full_name']) ? $row['full_name'] : "Guest (" . $row['customer_id'] . ")"; ?></td>
    <td><?php echo $row['reservation_date']; ?></td>
    <td><?php echo $row['reservation_time']; ?></td>
    <td><?php echo $row['no_of_people']; ?></td>

    <td class="status-<?php echo $row['status']; ?>">
        <?php echo ucfirst($row['status']); ?>
    </td>

    <td>
    <?php 
        // Only allow table number if status is confirmed
        if($row['status'] === 'confirmed'){
            // Show input box if table_number is empty
            if(empty($row['table_number'])){
                ?>
                <form method="POST" action="assign_table.php" style="display:flex; justify-content:center;">
                    <input type="hidden" name="reservation_id" value="<?php echo $row['reservation_id']; ?>">
                    <input type="number" name="table_number" placeholder="Table No" min="1" required style="width:60px;">
                    <button type="submit">Assign</button>
                </form>
                <?php
            } else {
                echo $row['table_number'];
            }
        } else {
            echo "<span style='color:#999;'>Not Assigned</span>";
        }
    ?>
</td>

    <td>
        <form method="POST" style="display:flex; flex-direction:column; gap:5px; align-items:center;">
            <input type="hidden" name="reservation_id" value="<?php echo $row['reservation_id']; ?>">
            <select name="status">
                <option value="pending" <?php if($row['status']=='pending') echo 'selected'; ?>>Pending</option>
                <option value="confirmed" <?php if($row['status']=='confirmed') echo 'selected'; ?>>Confirmed</option>
                <option value="cancelled" <?php if($row['status']=='cancelled') echo 'selected'; ?>>Cancelled</option>
            </select>
            <input type="text" name="table_number" placeholder="Table #" value="<?php echo htmlspecialchars((string)$row['table_number']); ?>" style="padding:5px; width:70px; text-align:center;">
            <button name="update_status">Update</button>
        </form>
    </td>

    <td>
        <a href="staff_reservations.php?delete=<?php echo $row['reservation_id']; ?>">
            <button class="delete">Delete</button>
        </a>
    </td>
</tr>
<?php } ?>

</table>

</div>
</body>
</html>