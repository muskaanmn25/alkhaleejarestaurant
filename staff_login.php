<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "alkhaleej_db");

if(isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM staff WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0){
        $_SESSION['staff'] = $email;
        header("Location: staff_panel.php");
    } else {
        $error = "Invalid Email or Password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Staff Login</title>

<style>
body {
    margin: 0;
    font-family: Arial;
    background-color: #f3f1ef;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.box {
    background: white;
    padding: 30px;
    width: 320px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

h2 {
    text-align: center;
}

input {
    width: 100%;
    padding: 10px;
    margin: 8px 0 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

button {
    width: 100%;
    padding: 10px;
    background: #7c1f2a;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background: #5c1520;
}

.error {
    color: red;
    text-align: center;
}
</style>
</head>

<body>

<div class="box">
    <h2>AL khaleej<br>Staff Login</h2>

    <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Enter Email" required>
        <input type="password" name="password" id="password" placeholder="Enter Password" required>
        <button type="submit" name="login">Sign In</button>
    </form>
</div>

<script>
/* Simple show password toggle */
</script>

</body>
</html>