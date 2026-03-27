<?php
session_start();
include "db.php";

// Protect page
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
$query = "SELECT r.*, c.full_name FROM reservation r LEFT JOIN customers c ON r.customer_id = c.customer_id ORDER BY r.reservation_id DESC";
$result = mysqli_query($conn, $query);
if(!$result){ die("Query Failed: " . mysqli_error($conn)); }
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage Reservations - Staff</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
<style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
    body { display:flex; background:#f4efec; min-height: 100vh; }
    /* ===== Sidebar ===== */
    .sidebar { width:250px; background:#7a1f2b; color:white; padding:30px 20px; }
    .sidebar h2 { font-family:'Playfair Display', serif; margin-bottom:40px; text-align:center; }
    .sidebar a { display:block; color:white; text-decoration:none; padding:12px; margin-bottom:10px; border-radius:6px; transition:0.3s; }
    .sidebar a:hover { background:#9e2f3d; }
    /* ===== Main Content ===== */
    .main { flex:1; padding:30px 40px; }
    .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; }
    .topbar h1 { font-family:'Playfair Display', serif; }
    .logout { background:#7a1f2b; color:white; border:none; padding:8px 15px; border-radius:5px; cursor:pointer; }
    /* ===== Table ===== */
    .table-container { background:white; padding:20px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.05); overflow-x: auto; }
    table { width:100%; border-collapse:collapse; border-radius:8px; overflow:hidden; }
    th, td { padding:15px; text-align:center; border-bottom:1px solid #ddd; font-size: 14px; }
    th { background:#7a1f2b; color:white; font-weight:500; }
    .status-pending { color: orange; font-weight: bold; }
    .status-confirmed { color: green; font-weight: bold; }
    .status-cancelled { color: red; font-weight: bold; }
    select, input[type="text"], input[type="number"] { padding:8px; border-radius:5px; border:1px solid #ccc; font-family:inherit; }
    button { padding:8px 12px; border:none; border-radius:5px; cursor:pointer; font-family:inherit; transition:0.3s; color:white; }
    .btn-update { background:#7a1f2b; } .btn-update:hover{ background:#631723; }
    .btn-delete { background:#c92a2a; text-decoration:none; padding:8px 12px; border-radius:5px; color:white; display:inline-block; } .btn-delete:hover{ background:#a02222; }
</style>
</head>
<body>

<div class="sidebar">
    <h2>Al-Khaleej Staff Panel</h2>
    <a href="staff_dashboard.php">Dashboard</a>
    <a href="staff_orders.php">Manage Orders</a>
    <a href="staff_reservations.php" style="background:#9e2f3d;">Manage Reservations</a>
</div>

<div class="main">
    <div class="topbar">
        <h1>Manage Reservations</h1>
        <form action="logout.php" method="POST">
            <button class="logout">Logout</button>
        </form>
    </div>

    <div class="table-container">
        <table>
        <tr>
            <th>ID</th>
            <th>Customer Name</th>
            <th>Date</th>
            <th>Time</th>
            <th>People</th>
            <th>Status</th>
            <th>Table No.</th>
            <th>Update</th>
            <th>Delete</th>
        </tr>

        <?php while($row = mysqli_fetch_assoc($result)){ ?>
        <tr>
            <td><?php echo $row['reservation_id']; ?></td>
            <td><?php echo !empty($row['full_name']) ? $row['full_name'] : "Guest (" . $row['customer_id'] . ")"; ?></td>
            <td><?php echo date('M d, Y', strtotime($row['reservation_date'])); ?></td>
            <td><?php echo date('H:i', strtotime($row['reservation_time'])); ?></td>
            <td><?php echo $row['no_of_people']; ?></td>

            <td class="status-<?php echo strtolower($row['status']); ?>">
                <?php echo ucfirst($row['status']); ?>
            </td>

            <td>
            <?php 
                if($row['status'] === 'confirmed'){
                    if(empty($row['table_number'])){
            ?>
                <form method="POST" action="assign_table.php" style="display:flex; justify-content:center; gap:5px;">
                    <input type="hidden" name="reservation_id" value="<?php echo $row['reservation_id']; ?>">
                    <input type="number" name="table_number" placeholder="No." min="1" required style="width:60px;">
                    <button type="submit" class="btn-update">Assign</button>
                </form>
            <?php
                    } else { echo "<b>" . $row['table_number'] . "</b>"; }
                } else {
                    echo "<span style='color:#999;'>N/A</span>";
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
                    <input type="text" name="table_number" placeholder="Table #" value="<?php echo htmlspecialchars((string)$row['table_number']); ?>" style="padding:5px; width:100%;">
                    <button name="update_status" class="btn-update" style="width:100%;">Update</button>
                </form>
            </td>

            <td>
                <a href="staff_reservations.php?delete=<?php echo $row['reservation_id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this reservation?');">Delete</a>
            </td>
        </tr>
        <?php } ?>
        </table>
    </div>

</div>
</body>
</html>