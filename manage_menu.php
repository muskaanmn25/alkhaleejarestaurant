<?php
session_start();
include "db.php";

// Only admin allowed
if($_SESSION['role'] != "admin"){
    header("Location: login.php");
    exit();
}

// Delete
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM menu WHERE menu_id=$id");
    header("Location: manage_menu.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM menu ORDER BY menu_id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Menu</title>
    <style>
        table {
			width:100%; border-collapse: collapse; }
        th, td { 
		padding:10px; border:1px solid #ccc; text-align:center; }
        img { 
		width:60px; }
        .btn { 
		padding:6px 10px; text-decoration:none; border-radius:5px; }
        .edit {
			background:green; color:white; }
        .delete 
		{ background:red; color:white; }
        .add 
		{ background:black; color:white; padding:8px 15px; }
    </style>
</head>
<body>

<h2>Manage Menu</h2>

<a href="add_menu.php" class="btn add">+ Add New Item</a>
<br><br>

<table>
<tr>
    <th>ID</th>
    <th>Image</th>
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
    <td>
        <?php if($row['image']) { ?>
            <img src="uploads/<?= $row['image']; ?>">
        <?php } ?>
    </td>
    <td><?= $row['item_name']; ?></td>
    <td><?= $row['category']; ?></td>
    <td><?= $row['price']; ?></td>
    <td><?= $row['availability']; ?></td>
    <td><?= $row['status']; ?></td>
    <td>
        <a href="edit_menu.php?id=<?= $row['menu_id']; ?>" class="btn edit">Edit</a>
        <a href="manage_menu.php?delete=<?= $row['menu_id']; ?>" 
           class="btn delete"
           onclick="return confirm('Are you sure?');">Delete</a>
    </td>
</tr>
<?php } ?>

</table>

</body>
</html>