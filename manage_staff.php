<?php
session_start();
include "db.php";

// Protect page
if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

/* -------- ADD STAFF -------- */
if(isset($_POST['add_staff'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $salary = floatval($_POST['salary']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); 

    if(empty($password)){
        echo "<script>alert('Please enter a password.'); window.history.back();</script>";
        exit();
    }

    $sql = "INSERT INTO staff (name, email, phone, salary, role, password) 
            VALUES ('$name', '$email', '$phone', '$salary', '$role', '$password')";

    if(mysqli_query($conn, $sql)){
        echo "<script>alert('Staff added successfully!'); window.location='manage_staff.php';</script>";
    } else {
        echo "<script>alert('Error adding staff: " . mysqli_error($conn) . "');</script>";
    }
}

/* -------- STATUS + DELETE -------- */
if(isset($_GET['inactive'])){
    $id = intval($_GET['inactive']);
    mysqli_query($conn,"UPDATE staff SET status='inactive' WHERE staff_id='$id'");
    header("Location: manage_staff.php");
    exit();
}

if(isset($_GET['active'])){
    $id = intval($_GET['active']);
    mysqli_query($conn,"UPDATE staff SET status='active' WHERE staff_id='$id'");
    header("Location: manage_staff.php");
    exit();
}

if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    mysqli_query($conn,"DELETE FROM staff WHERE staff_id='$id'");
    header("Location: manage_staff.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin - Staff Management</title>
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
    .btn-add { display:inline-block; background:#7a1f2b; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; font-weight:500; margin-bottom:20px; transition:0.3s; cursor:pointer; border:none; font-size:15px; }
    .btn-add:hover { background:#631723; }
    table { width:100%; border-collapse:collapse; background:white; border-radius:8px; overflow:hidden; }
    th, td { padding:15px; text-align:left; border-bottom:1px solid #ddd; }
    th { background:#7a1f2b; color:white; font-weight:500; text-align:center; }
    td { text-align:center; }
    .btn { padding:6px 12px; text-decoration:none; border-radius:5px; color:white; font-size:14px; transition:0.3s; display:inline-block; margin:2px; }
    .action { background:#1f4e5f; } .action:hover{ background:#143845; }
    .delete { background:#c92a2a; } .delete:hover{ background:#a02222; }
    .status-active { color: green; font-weight: bold; }
    .status-inactive { color: red; font-weight: bold; }

    /* Modal */
    .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:10; }
    .modal-content { background:white; width:400px; margin:8% auto; padding:30px; border-radius:10px; position:relative; }
    .close { position:absolute; right:20px; top:15px; cursor:pointer; color:#777; font-size:24px; }
    .close:hover { color:red; }
    .form-group { margin-bottom:15px; }
    .form-group label { display:block; margin-bottom:5px; font-size:14px; font-weight:500; }
    .form-group input, .form-group select { width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-family:inherit; }
    .form-group input:focus, .form-group select:focus { border-color:#7a1f2b; outline:none; }
    .btn-submit { width:100%; padding:12px; background:#7a1f2b; color:white; border:none; border-radius:5px; font-size:16px; cursor:pointer; margin-top:10px; }
    .btn-submit:hover { background:#631723; }
    h3.modal-title { font-family:'Playfair Display', serif; margin-bottom:20px; font-size:24px; }
</style>
</head>
<body>

<div class="sidebar">
    <h2>Al-Khaleej Arabian Restaurant</h2>
    <a href="admin_dash.php">Dashboard</a>
    <a href="manage_menu.php">Manage Menu</a>
    <a href="manage_staff.php" style="background:#9e2f3d;">Manage Staff</a>
    <a href="admin_orders.php">Orders</a>
    <a href="reports.php">Reports</a>
</div>

<div class="main">
    <div class="topbar">
        <h1>Manage Staff</h1>
        <form action="logout.php" method="POST">
            <button class="logout">Logout</button>
        </form>
    </div>

    <div class="table-container">
        <button class="btn-add" onclick="openModal()">+ Add New Staff</button>

        <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Salary</th>
            <th>Role</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php
        $result = mysqli_query($conn,"SELECT * FROM staff ORDER BY staff_id DESC");
        while($row = mysqli_fetch_assoc($result)){
        ?>
        <tr>
            <td><?php echo $row['staff_id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['phone']; ?></td>
            <td>₹ <?php echo $row['salary']; ?></td>
            <td><?php echo ucfirst($row['role']); ?></td>
            <td class="status-<?php echo strtolower($row['status']); ?>"><?php echo ucfirst($row['status']); ?></td>
            <td>
                <?php if($row['status']=='active'){ ?>
                    <a href="?inactive=<?php echo $row['staff_id']; ?>" class="btn action">Set Inactive</a>
                <?php } else { ?>
                    <a href="?active=<?php echo $row['staff_id']; ?>" class="btn action">Set Active</a>
                <?php } ?>
                <a href="?delete=<?php echo $row['staff_id']; ?>" class="btn delete" onclick="return confirm('Delete this staff permanently?')">Delete</a>
            </td>
        </tr>
        <?php } ?>
        </table>
    </div>

</div>

<!-- POPUP MODAL -->
<div class="modal" id="staffModal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">×</span>
        <h3 class="modal-title">Add New Staff</h3>

        <form method="POST">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Salary (₹)</label>
                <input type="number" name="salary" step="0.01" required>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role">
                    <option value="waiter">Waiter</option>
                    <option value="chef">Chef</option>
                    <option value="cashier">Cashier</option>
                    <option value="manager">Manager</option>
                </select>
            </div>
            <button type="submit" name="add_staff" class="btn-submit">Add Staff</button>
        </form>
    </div>
</div>

<script>
function openModal(){ document.getElementById("staffModal").style.display="block"; }
function closeModal(){ document.getElementById("staffModal").style.display="none"; }
</script>

</body>
</html>