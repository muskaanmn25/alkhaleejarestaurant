<?php
session_start();
include "db.php";

$msg = "";

if(isset($_POST['login'])){

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM users 
              WHERE email='$email' 
              AND password='$password'";

    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) == 1){

        $row = mysqli_fetch_assoc($result);

        // ✅ Set session
        $_SESSION['admin_id'] = $row['admin_id'];
        $_SESSION['full_name'] = $row['full_name'];

        // ✅ Redirect
        header("Location: admin_dash.php");
        exit();

    } else {
        $msg = "Invalid Email or Password!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    height:100vh;
    background:#f4f1ee;
    display:flex;
    justify-content:center;
    align-items:center;
}

/* Main Container */
.container{
    text-align:center;
}

/* Heading */
.container h2{
    font-family:'Playfair Display', serif;
    font-size:42px;
    margin-bottom:30px;
}

/* Card */
.card{
    width:420px;
    background:white;
    padding:40px;
    border-radius:16px;
    box-shadow:0 15px 35px rgba(0,0,0,0.08);
    text-align:left;
}

/* Labels */
label{
    font-weight:500;
    display:block;
    margin-bottom:8px;
}

/* Inputs */
input{
    width:100%;
    padding:14px;
    border-radius:10px;
    border:1px solid #ddd;
    margin-bottom:20px;
    font-size:14px;
    outline:none;
    transition:0.3s;
}

input:focus{
    border-color:#7b1e2b;
    box-shadow:0 0 0 2px rgba(123,30,43,0.1);
}

/* Button */
button{
    width:100%;
    padding:15px;
    border:none;
    border-radius:10px;
    background:#7b1e2b;
    color:white;
    font-size:16px;
    font-weight:500;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    background:#631723;
}

/* Error message */
.error{
    color:red;
    text-align:center;
    margin-bottom:15px;
}
</style>

</head>
<body>

<div class="container">

    <h2>Admin Login</h2>

    <div class="card">

        <?php if($msg != "") echo "<div class='error'>$msg</div>"; ?>

        <form method="POST">
            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit" name="login">Login</button>
        </form>

    </div>

</div>

</body>
</html>