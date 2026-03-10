<?php
session_start();
include "db.php";

$message = "";

if(isset($_POST['login'])){

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    // Check if customer already exists
    $check = "SELECT * FROM customers WHERE phone='$phone'";
    $result = mysqli_query($conn, $check);

    if(mysqli_num_rows($result) > 0){

        // Existing customer → Login
        $row = mysqli_fetch_assoc($result);
        $_SESSION['customer_id'] = $row['id'];
        $_SESSION['customer_email'] = $row['email'];
        $_SESSION['customer_phone'] = $row['phone'];

        header("Location: customer_dashboard.php");
        exit();

    } else {

        // New customer → Register automatically
        $insert = "INSERT INTO customers (email, phone) 
                   VALUES ('$email', '$phone')";
        mysqli_query($conn, $insert);

        $_SESSION['customer_id'] = mysqli_insert_id($conn);
        $_SESSION['customer_email'] = $email;
        $_SESSION['customer_phone'] = $phone;

        header("Location: customer_dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Login</title>
	
 <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background:#f5f6f8;
            font-family: 'Poppins', sans-serif;
        }

        .login-container{
            width: 420px;
            background:#ffffff;
            padding:40px;
            border-radius:16px;
            box-shadow:0 15px 35px rgba(0,0,0,0.08);
        }

        .login-container h1{
            text-align:center;
            margin-bottom:30px;
            font-family:'Playfair Display', serif;
            font-size:36px;
            font-weight:600;
            color:#111;
        }

        .input-group{
            margin-bottom:20px;
        }

        .input-group label{
            display:block;
            margin-bottom:8px;
            font-size:14px;
            font-weight:500;
            color:#333;
        }

        .input-group input{
            width:100%;
            padding:14px;
            border-radius:10px;
            border:1px solid #e0e0e0;
            background:#f2f4f7;
            font-size:14px;
            outline:none;
            transition:0.3s;
        }

        .input-group input:focus{
            border-color:#8B0000;
            background:#fff;
        }

        .login-btn{
            width:100%;
            padding:14px;
            border:none;
            border-radius:10px;
            font-size:16px;
            font-weight:500;
            cursor:pointer;
            color:#fff;
            background:linear-gradient(to right, #5b0000, #8B0000);
            transition:0.3s;
        }

        .login-btn:hover{
            opacity:0.9;
        }

        .message{
            text-align:center;
            color:red;
            margin-bottom:15px;
            font-size:14px;
        }

        @media(max-width:480px){
            .login-container{
                width:90%;
                padding:30px;
            }
        }
    </style>
</head>

<body>

<div class="login-container">

    <h1>Customer Login</h1>

    <?php if(!empty($message)){ ?>
        <div class="message"><?php echo $message; ?></div>
    <?php } ?>

    <form method="POST">

        <div class="input-group">
            <label>Email</label>
<input type="email" name="email" placeholder="Enter your Gmail" required>
        </div>

        <div class="input-group">
            <label>Phone Number</label>
            <input type="tel" name="phone" maxlength="10" pattern="[0-9]{10}" required>
        </div>

        <button type="submit" name="login" class="login-btn">
            Continue
        </button>

    </form>

</div>

</body>
</html>