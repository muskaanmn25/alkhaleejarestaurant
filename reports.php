<?php
session_start();
include "db.php";

// ===== DAILY REPORT =====
$daily_query = "
    SELECT COUNT(*) AS total_orders,
           SUM(total_amount) AS total_revenue
    FROM orders
    WHERE DATE(order_date) = CURDATE()
";
$daily_result = mysqli_query($conn, $daily_query);
$daily = mysqli_fetch_assoc($daily_result);

// ===== MONTHLY REPORT =====
$monthly_query = "
    SELECT COUNT(*) AS total_orders,
           SUM(total_amount) AS total_revenue
    FROM orders
    WHERE MONTH(order_date) = MONTH(CURDATE())
    AND YEAR(order_date) = YEAR(CURDATE())
";
$monthly_result = mysqli_query($conn, $monthly_query);
$monthly = mysqli_fetch_assoc($monthly_result);

// ===== ORDER STATUS REPORT =====
$status_query = "
    SELECT status, COUNT(*) AS total
    FROM orders
    GROUP BY status
";
$status_result = mysqli_query($conn, $status_query);

// ===== TOP 5 SELLING ITEMS =====
$top_items_query = "
    SELECT m.item_name, SUM(oi.quantity) AS total_sold
    FROM order_items oi
    JOIN menu m ON oi.menu_id = m.menu_id
    GROUP BY m.item_name
    ORDER BY total_sold DESC
    LIMIT 5
";
$top_items_result = mysqli_query($conn, $top_items_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Reports</title>
    <style>
        body { font-family: Arial; background:#f4f4f4; padding:20px; }
        h2 { margin-top:40px; }
        .card-container { display:flex; gap:20px; margin-bottom:30px; }
        .card {
            background:white;
            padding:20px;
            border-radius:10px;
            width:250px;
            box-shadow:0 3px 8px rgba(0,0,0,0.1);
        }
        table {
            width:100%;
            border-collapse:collapse;
            background:white;
            margin-top:15px;
        }
        th, td {
            padding:12px;
            border:1px solid #ddd;
            text-align:center;
        }
        th { background:#8B1E3F; color:white; }
    </style>
</head>
<body>

<h1>📊 Admin Reports</h1>

<!-- SUMMARY CARDS -->
<div class="card-container">
    <div class="card">
        <h3>Today's Orders</h3>
        <p><?php echo $daily['total_orders'] ?? 0; ?></p>
    </div>

    <div class="card">
        <h3>Today's Revenue</h3>
        <p>₹ <?php echo $daily['total_revenue'] ?? 0; ?></p>
    </div>

    <div class="card">
        <h3>Monthly Orders</h3>
        <p><?php echo $monthly['total_orders'] ?? 0; ?></p>
    </div>

    <div class="card">
        <h3>Monthly Revenue</h3>
        <p>₹ <?php echo $monthly['total_revenue'] ?? 0; ?></p>
    </div>
</div>

<!-- ORDER STATUS REPORT -->
<h2>Order Status Report</h2>
<table>
    <tr>
        <th>Status</th>
        <th>Total Orders</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($status_result)) { ?>
    <tr>
        <td><?php echo ucfirst($row['status']); ?></td>
        <td><?php echo $row['total']; ?></td>
    </tr>
    <?php } ?>
</table>

<!-- TOP SELLING ITEMS -->
<h2>Top 5 Selling Items</h2>
<table>
    <tr>
        <th>Item Name</th>
        <th>Quantity Sold</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($top_items_result)) { ?>
    <tr>
        <td><?php echo $row['item_name']; ?></td>
        <td><?php echo $row['total_sold']; ?></td>
    </tr>
    <?php } ?>
</table>

</body>
</html>