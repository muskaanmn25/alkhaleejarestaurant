<?php
session_start();
include "db.php";

if(!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

$email = $_SESSION['customer_email'];
$name = explode('@', $email)[0];

$msg = '';
if(isset($_POST['submit_feedback'])){
    $c_name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
    $c_email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $rating = intval($_POST['rating'] ?? 5);
    $comments = mysqli_real_escape_string($conn, $_POST['comments'] ?? $_POST['message'] ?? '');

    $q = "INSERT INTO feedback (customer_name, email, rating, comments, status) VALUES ('$c_name', '$c_email', '$rating', '$comments', 'approved')";
    if(mysqli_query($conn, $q)){
        $msg = "Thank you! Your feedback has been submitted successfully.";
    } else {
        $msg = "Error submitting feedback. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Leave Feedback - Al-Khaleej</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
body { background:#f4efec; display:flex; justify-content:center; align-items:center; min-height:100vh; }
.card { background:white; padding:40px; border-radius:15px; box-shadow:0 10px 30px rgba(0,0,0,0.08); max-width:500px; width:90%; text-align:center; margin:40px 0;}
h2 { font-family:'Playfair Display', serif; font-size:32px; color:#7a1f2b; margin-bottom:20px; }
.form-group { text-align:left; margin-bottom:20px; }
.form-group label { display:block; font-weight:500; margin-bottom:8px; color:#333; font-size:14px; }
.form-group input, .form-group textarea, .form-group select { width:100%; padding:14px; border:1px solid #ddd; border-radius:10px; background:#f9f9f9; outline:none; font-family:inherit; transition:0.3s;}
.form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color:#7a1f2b; background:#fff;}
.btn { width:100%; padding:14px; background:#7a1f2b; color:white; border:none; border-radius:10px; font-size:16px; font-weight:500; cursor:pointer; box-shadow:0 5px 15px rgba(122,31,43,0.3); transition:0.3s; margin-top:10px;}
.btn:hover { background:#5c1520; transform:translateY(-2px); box-shadow:0 8px 20px rgba(122,31,43,0.4); }
.success { padding:15px; background:#d4edda; color:#155724; border-radius:8px; margin-bottom:20px; font-weight:500;}
</style>
</head>
<body>
<div class="card">
    <h2>Leave Feedback</h2>
    <?php if(!empty($msg)): ?>
        <div class="success"><?php echo $msg; ?></div>
        <a href="mainpage.html" class="btn" style="text-decoration:none; display:inline-block;">Return to Home</a>
    <?php else: ?>
        <form method="POST">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>
            <div class="form-group">
                <label>Email (Optional)</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <div class="form-group">
                <label>Rating</label>
                <select name="rating" required>
                    <option value="5">⭐⭐⭐⭐⭐ 5 Stars - Excellent</option>
                    <option value="4">⭐⭐⭐⭐ 4 Stars - Very Good</option>
                    <option value="3">⭐⭐⭐ 3 Stars - Average</option>
                    <option value="2">⭐⭐ 2 Stars - Poor</option>
                    <option value="1">⭐ 1 Star - Terrible</option>
                </select>
            </div>
            <div class="form-group">
                <label>Comments</label>
                <textarea name="comments" rows="4" required placeholder="Tell us about your experience..."></textarea>
            </div>
            <button name="submit_feedback" class="btn">Submit Review</button>
        </form>
        <div style="margin-top:20px;"><a href="mainpage.html" style="color:#777; text-decoration:none;">Cancel</a></div>
    <?php endif; ?>
</div>
</body>
</html>
