<?php
session_start();
include "db.php";

// Optional: check if admin is logged in
if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

// Fetch all staff records
$sql = "SELECT id, name, email, phone, salary, role FROM staff";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff List</title>
    <style>
        table {
            width: 80%;
            border-collapse: collapse;
            margin: 30px auto;
            font-family: Arial, sans-serif;
        }
        th, td {
            border: 1px solid #333;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        h2 {
            text-align: center;
            font-family: Arial, sans-serif;
        }
        a.button {
            text-decoration: none;
            padding: 5px 10px;
            background-color: #007BFF;
            color: white;
            border-radius: 3px;
        }
        a.button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<h2>Staff List</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Salary</th>
        <th>Role</th>
        <th>Action</th>
    </tr>
    <?php
    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
            echo "<tr>";
            echo "<td>". $row['id'] ."</td>";
            echo "<td>". $row['name'] ."</td>";
            echo "<td>". $row['email'] ."</td>";
            echo "<td>". $row['phone'] ."</td>";
            echo "<td>". $row['salary'] ."</td>";
            echo "<td>". $row['role'] ."</td>";
            echo "<td>
                    <a class='button' href='edit_staff.php?id=".$row['id']."'>Edit</a> 
                    <a class='button' href='delete_staff.php?id=".$row['id']."' onclick='return confirm(\"Are you sure?\");'>Delete</a>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No staff found.</td></tr>";
    }
    ?>
</table>

</body>
</html>