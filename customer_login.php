<?php
session_start();
$conn = mysqli_connect("localhost","root","","alkhaleej_db");
/* LOGIN CHECK */
if(!isset($_SESSION['customer'])){
    header("Location: customer_login.php");
    exit();
}
/* ================= SEND OTP ================= */
if(isset($_POST['send_otp'])){

    $credential = $_POST['credential'];

    $query = mysqli_query($conn,
        "SELECT * FROM customers 
         WHERE email='$credential' 
         OR phone='$credential'"
    );

    if(mysqli_num_rows($query) > 0){

        $user = mysqli_fetch_assoc($query);

        $otp = rand(100000,999999); // Generate 6 digit OTP

        $_SESSION['otp'] = $otp;
        $_SESSION['cust_id'] = $user['id'];

        echo "<script>alert('Your OTP is: $otp');</script>"; 
        // For project demo (In real system send SMS/Email)

        $_SESSION['show_otp'] = true;

    }else{
        echo "<script>alert('User not found');</script>";
    }
}

/* ================= VERIFY OTP ================= */
if(isset($_POST['verify_otp'])){

    if($_POST['otp'] == $_SESSION['otp']){

        $_SESSION['customer'] = $_SESSION['cust_id'];

        header("Location: customer_dashboard.php");
        exit();

    }else{
        echo "<script>alert('Invalid OTP');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Customer Login</title>

<style>
body{
    font-family:Arial;
    background:linear-gradient(to right,#8b1e2d,#6e1422);
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.login-box{
    background:white;
    padding:40px;
    border-radius:10px;
    width:350px;
    text-align:center;
}

input{
    width:100%;
    padding:10px;
    margin:10px 0;
    border-radius:5px;
    border:1px solid #ccc;
}

button{
    width:100%;
    padding:10px;
    background:#8b1e2d;
    color:white;
    border:none;
    border-radius:5px;
    cursor:pointer;
}

button:hover{
    background:#6e1422;
}
</style>
</head>
<body>

<div class="login-box">

<h2>Customer Login</h2>

<?php if(!isset($_SESSION['show_otp'])){ ?>

<!-- STEP 1: ENTER EMAIL OR PHONE -->
<form method="POST">
    <input type="text" name="credential" 
           placeholder="Enter Email or Phone" required>
    <button type="submit" name="send_otp">Send OTP</button>
</form>

<?php } else { ?>

<!-- STEP 2: VERIFY OTP -->
<form method="POST">
    <input type="text" name="otp" 
           placeholder="Enter OTP" required>
    <button type="submit" name="verify_otp">Verify OTP</button>
</form>

<?php } ?>

</div>

</body>
</html>