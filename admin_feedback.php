<?php
session_start();
include "db.php";

if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    mysqli_query($conn,"DELETE FROM feedback WHERE feedback_id='$id'");
    header("Location: admin_feedback.php");
    exit();
}

$feedbacks = mysqli_query($conn, "SELECT * FROM feedback ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Customer Feedback - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
        body { display:flex; background:#f4efec; min-height: 100vh; }
        .sidebar { width:250px; background:#7a1f2b; color:white; padding:30px 20px; }
        .sidebar h2 { font-family:'Playfair Display', serif; margin-bottom:40px; text-align:center; }
        .sidebar a { display:block; color:white; text-decoration:none; padding:12px; margin-bottom:10px; border-radius:6px; transition:0.3s; }
        .sidebar a:hover { background:#9e2f3d; }
        .main { flex:1; padding:30px 40px; }
        .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; }
        .topbar h1 { font-family:'Playfair Display', serif; }
        .logout { background:#7a1f2b; color:white; border:none; padding:8px 15px; border-radius:5px; cursor:pointer; }
        
        .table-container { background:white; padding:20px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.05); overflow-x: auto; }
        table { width:100%; border-collapse:collapse; border-radius:8px; overflow:hidden; }
        th, td { padding:15px; text-align:left; border-bottom:1px solid #ddd; font-size: 14px; }
        th { background:#7a1f2b; color:white; font-weight:500; }
        .btn-delete { background:#c92a2a; text-decoration:none; padding:8px 12px; border-radius:5px; color:white; display:inline-block; } 
        .btn-delete:hover{ background:#a02222; }
        .stars { color: gold; font-size:18px;}
    </style>
</head>
<body>
<div class="sidebar">
    <h2>Al-Khaleej</h2>
    <a href="admin_dash.php">Dashboard</a>
    <a href="manage_menu.php">Manage Menu</a>
    <a href="manage_staff.php">Manage Staff</a>
    <a href="admin_orders.php">Orders</a>
    <a href="reports.php">Reports</a>
    <a href="admin_feedback.php" style="background:#9e2f3d;">Customer Feedback</a>
</div>
<div class="main">
    <div class="topbar">
        <h1>Customer Feedback</h1>
        <form action="logout.php" method="POST">
            <button class="logout">Logout</button>
        </form>
    </div>
    <div class="table-container">
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Rating</th>
                <th>Comments</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
            <?php while($row = mysqli_fetch_assoc($feedbacks)){ ?>
            <tr>
                <td><?php echo $row['feedback_id']; ?></td>
                <td><?php echo htmlspecialchars($row['customer_name']); ?><br><span style="color:#777; font-size:12px;"><?php echo htmlspecialchars($row['email']); ?></span></td>
                <td class="stars"><?php echo str_repeat('★', $row['rating']) . str_repeat('☆', 5 - $row['rating']); ?></td>
                <td style="max-width:300px;"><?php echo htmlspecialchars($row['comments']); ?></td>
                <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                <td><a href="?delete=<?php echo $row['feedback_id']; ?>" class="btn-delete" onclick="return confirm('Delete?');">Delete</a></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>
</body>
</html>
