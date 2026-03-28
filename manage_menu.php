<?php
session_start();
include "db.php";

// Protect page
if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

// Delete
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM menu WHERE menu_id=$id");
    header("Location: manage_menu.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM menu ORDER BY menu_id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Menu - Admin Panel</title>
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
        .table-container { background:white; padding:20px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.05); }
        .btn-add { display:inline-block; background:#7a1f2b; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; font-weight:500; margin-bottom:20px; transition:0.3s; }
        .btn-add:hover { background:#631723; }
        table { width:100%; border-collapse:collapse; background:white; border-radius:8px; overflow:hidden; }
        th, td { padding:15px; text-align:left; border-bottom:1px solid #ddd; }
        th { background:#7a1f2b; color:white; font-weight:500; text-align:center; }
        td { text-align:center; }
        img { width:60px; border-radius:8px; object-fit:cover; }
        .btn { padding:6px 12px; text-decoration:none; border-radius:5px; color:white; font-size:14px; transition:0.3s; }
        .edit { background:#258752; } .edit:hover{ background:#1b663e; }
        .delete { background:#c92a2a; } .delete:hover{ background:#a02222; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Al-Khaleej Arabian Restaurant</h2>
    <a href="admin_dash.php">Dashboard</a>
    <a href="manage_menu.php" style="background:#9e2f3d;">Manage Menu</a>
    <a href="manage_staff.php">Manage Staff</a>
    <a href="admin_orders.php">Orders</a>
    <a href="reports.php">Reports</a>
</div>

<div class="main">
    <div class="topbar">
        <h1>Manage Menu</h1>
        <form action="logout.php" method="POST">
            <button class="logout">Logout</button>
        </form>
    </div>

    <div class="table-container">
        <a href="add_menu.php" class="btn-add">+ Add New Item</a>
        
        <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Availability</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?= $row['menu_id']; ?></td>
            <td><?= $row['item_name']; ?></td>
            <td><?= $row['category']; ?></td>
            <td>₹ <?= $row['price']; ?></td>
            <td><?= ucfirst(str_replace('_', ' ', $row['availability'])); ?></td>
            <td><?= ucfirst($row['status']); ?></td>
            <td>
                <a href="edit_menu.php?id=<?= $row['menu_id']; ?>" class="btn edit">Edit</a>
                <a href="manage_menu.php?delete=<?= $row['menu_id']; ?>" class="btn delete" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
            </td>
        </tr>
        <?php } ?>
        </table>
    </div>
</div>

</body>
</html>