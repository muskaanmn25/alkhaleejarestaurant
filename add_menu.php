<?php
session_start();
include "db.php";

// Protect page
if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

if(isset($_POST['add'])){
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = $_POST['price'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $availability = $_POST['availability'];
    $status = $_POST['status'];

    $check_query = "SELECT * FROM menu WHERE item_name = '$item_name'";
    $check_result = mysqli_query($conn, $check_query);

    if(mysqli_num_rows($check_result) > 0) {
        $error = "Menu item '$item_name' already exists!";
    } else {
        $query = "INSERT INTO menu 
            (item_name, category, price, description, availability, status)
            VALUES
            ('$item_name', '$category', '$price', '$description', '$availability', '$status')";

        mysqli_query($conn, $query);
        header("Location: manage_menu.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Menu Item - Admin Panel</title>
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
        
        /* ===== Form Container ===== */
        .form-container { background:white; padding:30px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.05); max-width:600px;}
        .form-group { margin-bottom:20px; }
        .form-group label { display:block; margin-bottom:8px; font-weight:500; font-size:14px; }
        .form-group input, .form-group textarea, .form-group select { width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; font-family:inherit; outline:none; transition:0.3s; background:#f9f9f9; }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color:#7a1f2b; background:#fff; }
        .btn-submit { background:#7a1f2b; color:white; padding:12px 20px; border:none; border-radius:8px; width:100%; font-size:16px; font-weight:500; cursor:pointer; transition:0.3s; }
        .btn-submit:hover { background:#631723; }
        .btn-back { display:inline-block; margin-bottom:20px; color:#555; text-decoration:none; font-size:14px; }
        .btn-back:hover { color:#7a1f2b; text-decoration:underline; }
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
        <h1>Add Menu Item</h1>
        <form action="logout.php" method="POST">
            <button class="logout">Logout</button>
        </form>
    </div>

    <a href="manage_menu.php" class="btn-back">← Back to Menu List</a>

    <div class="form-container">
        <?php if(isset($error)){ ?>
            <div style="background:#f8d7da; color:#721c24; padding:10px; border-radius:5px; margin-bottom:15px; border:1px solid #f5c6cb; text-align:center;">
                <?= $error ?>
            </div>
        <?php } ?>
        <form method="POST">

            <div class="form-group">
                <label>Item Name</label>
                <input type="text" name="item_name" required>
            </div>

            <div class="form-group">
                <label>Category</label>
                <input type="text" name="category" required>
            </div>

            <div class="form-group">
                <label>Price (₹)</label>
                <input type="number" step="0.01" name="price" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3"></textarea>
            </div>


            <div class="form-group">
                <label>Availability</label>
                <select name="availability">
                    <option value="available">Available</option>
                    <option value="not_available">Not Available</option>
                </select>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="available">Available</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <button type="submit" name="add" class="btn-submit">Add Item</button>

        </form>
    </div>
</div>
</body>
</html>