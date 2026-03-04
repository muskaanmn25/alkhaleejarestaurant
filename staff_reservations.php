<?php
session_start();

if(!isset($_SESSION['staff_id'])){
    header("Location: staff_login.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","alkhaleej_db");
if(!$conn){
    die("Connection Failed: " . mysqli_connect_error());
}

/* ===== UPDATE RESERVATION STATUS ONLY ===== */
if(isset($_POST['update_status'])){
    $id = $_POST['reservation_id'];
    $status = $_POST['status'];

    mysqli_query($conn,"UPDATE reservation SET status='$status' WHERE id='$id'");
    header("Location: staff_reservations.php");
    exit();
}

/* ===== DELETE RESERVATION ===== */
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn,"DELETE FROM reservation WHERE id='$id'");
    header("Location: staff_reservations.php");
    exit();
}

/* ===== FETCH RESERVATIONS ===== */
$reservations = mysqli_query($conn,"SELECT * FROM reservation ORDER BY id DESC");
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

/* Sidebar */
.sidebar{
    width:250px;
    background:#7a1f2b;  /* dark maroon */
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
    background:#9e2f3d;  /* lighter maroon */
}

/* Main */
.main{
    flex:1;
    padding:30px;
}

h1{
    margin-bottom:20px;
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
    background:#7a1f2b; /* header dark maroon */
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

    <!-- Status column -->
    <td class="status-<?php echo $row['status']; ?>">
        <?php echo ucfirst($row['status']); ?>
    </td>

    <!-- Table number column -->
    <td><?php echo $row['table_number']; ?></td>

    <!-- Update column -->
    <td>
        <form method="POST">
            <input type="hidden" name="reservation_id" value="<?php echo $row['id']; ?>">
            <select name="status">
                <option value="pending" <?php if($row['status']=='pending') echo 'selected'; ?>>Pending</option>
                <option value="confirmed" <?php if($row['status']=='confirmed') echo 'selected'; ?>>Confirm</option>
                <option value="cancelled" <?php if($row['status']=='cancelled') echo 'selected'; ?>>Cancel</option>
            </select>
            <button name="update_status">Update</button>
        </form>
    </td>

    <!-- Delete column -->
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