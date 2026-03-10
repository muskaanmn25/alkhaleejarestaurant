<?php
session_start();
include "db.php";

/* -------- ADD STAFF -------- */
if(isset($_POST['add_staff'])){

    // Get form values
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $salary = $_POST['salary'];
    $role = $_POST['role'];
    $password = $_POST['password']; // admin provides plain text password

    // Validation
    if(empty($password)){
        echo "<script>alert('Please enter a password.'); window.history.back();</script>";
        exit();
    }

    // Insert into database (plain text password)
    $sql = "INSERT INTO staff (name, email, phone, salary, role, password) 
            VALUES ('$name', '$email', '$phone', '$salary', '$role', '$password')";

    if(mysqli_query($conn, $sql)){
        echo "<script>alert('Staff added successfully!'); window.location='staff_list.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

/* -------- STATUS + DELETE -------- */

if(isset($_GET['inactive'])){
    mysqli_query($conn,"UPDATE staff SET status='inactive' WHERE staff_id='$_GET[inactive]'");
}

if(isset($_GET['active'])){
    mysqli_query($conn,"UPDATE staff SET status='active' WHERE staff_id='$_GET[active]'");
}

if(isset($_GET['delete'])){
    mysqli_query($conn,"DELETE FROM staff WHERE staff_id='$_GET[delete]'");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin - Staff Management</title>

<style>
body{
    font-family: Arial;
    background:#f4f4f4;
    padding:30px;
}

button{
    padding:8px 15px;
    background:#800000;
    color:white;
    border:none;
    cursor:pointer;
}

table{
    width:100%;
    background:white;
    border-collapse: collapse;
}

th,td{
    padding:10px;
    border:1px solid #ddd;
    text-align:center;
}

th{
    background:#800000;
    color:white;
}

/* Modal */
.modal{
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.6);
}

.modal-content{
    background:white;
    width:400px;
    margin:8% auto;
    padding:20px;
    border-radius:5px;
}

.close{
    float:right;
    cursor:pointer;
    color:red;
    font-size:18px;
}
</style>
</head>

<body>

<h2>Staff Management</h2>

<button onclick="openModal()">+ Add New Staff</button>

<br><br>

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
$result = mysqli_query($conn,"SELECT * FROM staff");
while($row = mysqli_fetch_assoc($result)){
?>
<tr>
    <td><?php echo $row['staff_id']; ?></td>
    <td><?php echo $row['name']; ?></td>
    <td><?php echo $row['email']; ?></td>
    <td><?php echo $row['phone']; ?></td>
    <td><?php echo $row['salary']; ?></td>
    <td><?php echo $row['role']; ?></td>
    <td><?php echo $row['status']; ?></td>
    <td>

    <?php if($row['status']=='active'){ ?>
        <a href="?inactive=<?php echo $row['staff_id']; ?>">Inactive</a> |
    <?php } else { ?>
        <a href="?active=<?php echo $row['staff_id']; ?>">Active</a> |
    <?php } ?>

    <a href="?delete=<?php echo $row['staff_id']; ?>" 
       onclick="return confirm('Delete this staff?')">
       Delete
    </a>

    </td>
</tr>
<?php } ?>
</table>


<!-- POPUP MODAL -->
<div class="modal" id="staffModal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">×</span>
        <h3>Add New Staff</h3>

        <form method="POST">
            Name:<br>
            <input type="text" name="name" required><br><br>

            Email:<br>
            <input type="email" name="email" required><br><br>

            Phone:<br>
            <input type="text" name="phone" required><br><br>

            Password:<br>
            <input type="password" name="password" required><br><br>

            Salary:<br>
            <input type="number" name="salary" required><br><br>

            Role:<br>
            <select name="role">
                <option value="waiter">Waiter</option>
                <option value="chef">Chef</option>
                <option value="cashier">Cashier</option>
                <option value="manager">Manager</option>
            </select><br><br>

            <button type="submit" name="add_staff">Add Staff</button>
        </form>
    </div>
</div>

<script>
function openModal(){
    document.getElementById("staffModal").style.display="block";
}

function closeModal(){
    document.getElementById("staffModal").style.display="none";
}
</script>

</body>
</html>