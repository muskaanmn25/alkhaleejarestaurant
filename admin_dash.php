<?php
session_start();

// Protect page
if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard - Al-Khaleej Arabian Restaurant</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    display:flex;
    background:#f4efec;
}

/* ===== Sidebar ===== */
.sidebar{
    width:250px;
    height:100vh;
    background:#7a1f2b;
    color:white;
    padding:30px 20px;
}

.sidebar h2{
    font-family:'Playfair Display', serif;
    margin-bottom:40px;
    text-align:center;
}

.sidebar a{
    display:block;
    color:white;
    text-decoration:none;
    padding:12px;
    margin-bottom:10px;
    border-radius:6px;
    transition:0.3s;
}

.sidebar a:hover{
    background:#9e2f3d;
}

/* ===== Main Content ===== */
.main{
    flex:1;
    padding:30px 40px;
}

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}

.topbar h1{
    font-family:'Playfair Display', serif;
}

.logout{
    background:#7a1f2b;
    color:white;
    border:none;
    padding:8px 15px;
    border-radius:5px;
    cursor:pointer;
}

/* ===== Dashboard Cards ===== */
.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
}

.card{
    background:white;
    padding:25px;
    border-radius:10px;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
    transition:0.3s;
}

.card:hover{
    transform:translateY(-5px);
}

.card h3{
    margin-bottom:10px;
    color:#7a1f2b;
}

.card p{
    font-size:22px;
    font-weight:bold;
}

/* ===== Footer ===== */
.footer{
    margin-top:40px;
    text-align:center;
    font-size:14px;
    color:#777;
}
</style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Al-Khaleej Arabian Restaurant</h2>
    <a href="admin_dash.php">Dashboard</a>
    <a href="manage_menu.php">Manage Menu</a>
    <a href="manage_staff.php">Manage Staff</a>
    <a href="admin_orders.php">Orders</a>
    <a href="reports.php">Reports</a>
</div>

<!-- Main Content -->
<div class="main">

    <div class="topbar">
        <h1>Welcome, <?php echo $_SESSION['full_name']; ?> </h1>
        <form action="logout.php" method="POST">
            <button class="logout">Logout</button>
        </form>
    </div>

    <div class="cards">
        <div class="card">
            <h3>Total Users</h3>
            <p>120</p>
        </div>

        <div class="card">
            <h3>Total Orders</h3>
            <p>85</p>
        </div>

        <div class="card">
            <h3>Revenue</h3>
            <p>4,250</p>
        </div>

        <div class="card">
            <h3>Pending Reports</h3>
            <p>5</p>
        </div>
    </div>

    <div class="footer">
         Al-Khaleej Arabian Restaurant | Admin Panel
    </div>

</div>

</body>
</html>