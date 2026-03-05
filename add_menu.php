<?php
session_start();
include "db.php";

if(isset($_POST['add'])){

    $item_name = $_POST['item_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $availability = $_POST['availability'];
    $status = $_POST['status'];

    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    if($image != ""){
        move_uploaded_file($tmp, "uploads/".$image);
    }

    $query = "INSERT INTO menu 
        (item_name, category, price, description, image, availability, status)
        VALUES
        ('$item_name', '$category', '$price', '$description', '$image', '$availability', '$status')";

    mysqli_query($conn, $query);

    header("Location: manage_menu.php");
    exit();
}
?>

<h2>Add Menu Item</h2>

<form method="POST" enctype="multipart/form-data">

    Item Name:
    <input type="text" name="item_name" required><br><br>

    Category:
    <input type="text" name="category" required><br><br>

    Price:
    <input type="number" step="0.01" name="price" required><br><br>

    Description:
    <textarea name="description"></textarea><br><br>

    Image:
    <input type="file" name="image"><br><br>

    Availability:
    <select name="availability">
        <option value="available">Available</option>
        <option value="not_available">Not Available</option>
    </select><br><br>

    Status:
    <select name="status">
        <option value="available">Available</option>
        <option value="inactive">Inactive</option>
    </select><br><br>

    <button type="submit" name="add">Add Item</button>

</form>