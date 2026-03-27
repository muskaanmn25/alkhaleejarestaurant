<?php
session_start();
include 'db.php';

$error = "";

if(isset($_POST['login'])){

    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);

    $query = "SELECT * FROM staff WHERE LOWER(email)=LOWER('$email')";
    $result = mysqli_query($conn,$query);

    if(mysqli_num_rows($result) == 1){

        $row = mysqli_fetch_assoc($result);

        if($password == $row['password']){

            if($row['status'] == 'active') {
                $_SESSION['staff_id'] = $row['staff_id'];
                $_SESSION['name'] = $row['name'];

                header("Location: staff_dashboard.php");
                exit();
            } else {
                $error = "Account is inactive. Please contact admin.";
            }

        } else {
            $error = "Incorrect password";
        }

    } else {
        $error = "Email not found";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Staff Login - Al-Khaleej</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
<style>
    *{ margin:0; padding:0; box-sizing:border-box; }
    body{ height:100vh; display:flex; justify-content:center; align-items:center; background:#f5f6f8; font-family: 'Poppins', sans-serif; }
    .login-container{ width: 420px; background:#ffffff; padding:40px; border-radius:16px; box-shadow:0 15px 35px rgba(0,0,0,0.08); }
    .login-container h1{ text-align:center; margin-bottom:30px; font-family:'Playfair Display', serif; font-size:36px; font-weight:600; color:#111; line-height:1.2; }
    .input-group{ margin-bottom:20px; }
    .input-group label{ display:block; margin-bottom:8px; font-size:14px; font-weight:500; color:#333; }
    .input-group input{ width:100%; padding:14px; border-radius:10px; border:1px solid #e0e0e0; background:#f2f4f7; font-size:14px; outline:none; transition:0.3s; font-family:inherit;}
    .input-group input:focus{ border-color:#8B0000; background:#fff; }
    .login-btn{ width:100%; padding:14px; border:none; border-radius:10px; font-size:16px; font-weight:500; cursor:pointer; color:#fff; background:linear-gradient(to right, #5b0000, #8B0000); transition:0.3s; font-family:inherit;}
    .login-btn:hover{ opacity:0.9; transform:translateY(-2px); box-shadow:0 5px 15px rgba(139,0,0,0.3); }
    .error{ text-align:center; color:#d93025; background:#fce8e6; padding:10px; border-radius:8px; margin-bottom:20px; font-size:14px; }
    @media(max-width:480px){ .login-container{ width:90%; padding:30px; } }
</style>
</head>

<body>

<div class="login-container">
    <h1>Al-Khaleej<br><span style="font-size:24px; color:#555;">Staff Portal</span></h1>

    <?php if(!empty($error)){ ?>
        <div class="error"><?php echo $error; ?></div>
    <?php } ?>

    <form method="POST">
        <div class="input-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="Enter your email" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter your password" required>
        </div>

        <button type="submit" name="login" class="login-btn">Sign In</button>
    </form>
</div>

</body>
</html>