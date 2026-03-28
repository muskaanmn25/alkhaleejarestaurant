<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$customer_email = $_SESSION['customer_email'];
$customer_username = explode("@", $customer_email)[0];

/* ===== GET CART ===== */
$cart_query = mysqli_query($conn, "SELECT * FROM cart WHERE customer_id='$customer_id' ORDER BY cart_id DESC LIMIT 1");
if (mysqli_num_rows($cart_query) > 0) {
    $cart = mysqli_fetch_assoc($cart_query);
    $cart_id = $cart['cart_id'];
} else {
    mysqli_query($conn, "INSERT INTO cart(customer_id) VALUES('$customer_id')");
    $cart_id = mysqli_insert_id($conn);
}

/* ===== REMOVE/UPDATE QTY ===== */
if (isset($_GET['remove'])) {
    $id = intval($_GET['remove']);
    mysqli_query($conn, "DELETE FROM cart_items WHERE items_id='$id' AND cart_id='$cart_id'");
    header("Location: cart.php");
    exit();
}
if (isset($_GET['inc'])) {
    $id = intval($_GET['inc']);
    mysqli_query($conn, "UPDATE cart_items SET quantity=quantity+1 WHERE items_id='$id' AND cart_id='$cart_id'");
    header("Location: cart.php");
    exit();
}
if (isset($_GET['dec'])) {
    $id = intval($_GET['dec']);
    $q = mysqli_query($conn, "SELECT quantity FROM cart_items WHERE items_id='$id'");
    $d = mysqli_fetch_assoc($q);
    if ($d['quantity'] > 1) {
        mysqli_query($conn, "UPDATE cart_items SET quantity=quantity-1 WHERE items_id='$id'");
    }
    header("Location: cart.php");
    exit();
}

/* ===== PLACE ORDER ===== */
if (isset($_POST['place_order'])) {
    $cart_items_res = mysqli_query($conn, "
        SELECT ci.*, m.price 
        FROM cart_items ci
        JOIN menu m ON ci.menu_id = m.menu_id
        WHERE ci.cart_id='$cart_id'
    ");

    if (mysqli_num_rows($cart_items_res) == 0) {
        $msg = "Your cart is empty!";
    } else {
        $total = 0;
        $cart_items = [];
        while ($row = mysqli_fetch_assoc($cart_items_res)) {
            $row['total_price'] = $row['quantity'] * $row['price'];
            $total += $row['total_price'];
            $cart_items[] = $row;
        }

        $order_date = date('Y-m-d H:i:s');
        $order_type = isset($_POST['order_type']) ? mysqli_real_escape_string($conn, $_POST['order_type']) : 'dine_in';
        $status = 'pending';

        $order_query = mysqli_query($conn, "INSERT INTO orders (customer_id, order_date, order_type, total_amount, status) VALUES ('$customer_id', '$order_date', '$order_type', '$total', '$status')");

        if ($order_query) {
            $order_id = mysqli_insert_id($conn);
            foreach ($cart_items as $item) {
                mysqli_query($conn, "INSERT INTO order_items (order_id, menu_id, quantity, price) VALUES ('$order_id', '{$item['menu_id']}', '{$item['quantity']}', '{$item['price']}')");
            }
            mysqli_query($conn, "DELETE ci, c FROM cart c LEFT JOIN cart_items ci ON ci.cart_id=c.cart_id WHERE c.cart_id='$cart_id'");
            header("Location: payment.php?order_id=$order_id");
            exit();
        } else {
            $msg = "Failed to place order: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Your Cart - Al-Khaleej</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f4efec;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: #7a1f2b;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar h1 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: 500;
            transition: 0.3s;
        }

        .navbar a:hover {
            color: #ffcccc;
        }

        .cart-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 0 20px;
            flex: 1;
            width: 100%;
        }

        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .cart-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: #111;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: white;
            color: #555;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 500;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
            transition: 0.3s;
        }

        .btn-back:hover {
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.1);
            color: #7a1f2b;
        }

        .cart-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            color: #777;
            font-weight: 500;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        td {
            font-weight: 500;
            color: #333;
        }

        .item-name {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            color: #111;
        }

        .qty-control {
            display: inline-flex;
            align-items: center;
            background: #f9f9f9;
            border-radius: 8px;
            border: 1px solid #eee;
            overflow: hidden;
        }

        .qty-btn {
            text-decoration: none;
            color: #555;
            width: 30px;
            height: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            background: white;
            transition: 0.2s;
        }

        .qty-btn:hover {
            background: #7a1f2b;
            color: white;
        }

        .qty-val {
            padding: 0 15px;
            font-weight: 600;
            font-size: 15px;
        }

        .btn-remove {
            color: #e74c3c;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: 0.2s;
        }

        .btn-remove:hover {
            color: #c0392b;
            text-decoration: underline;
        }

        .cart-summary {
            margin-top: 30px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            width: 300px;
            padding: 15px 0;
            border-top: 2px solid #eee;
        }

        .total-label {
            font-size: 18px;
            color: #555;
        }

        .total-amount {
            font-size: 24px;
            font-weight: 600;
            color: #7a1f2b;
        }

        .btn-checkout {
            margin-top: 20px;
            width: 300px;
            padding: 16px;
            background: #7a1f2b;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 500;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(122, 31, 43, 0.3);
            transition: 0.3s;
        }

        .btn-checkout:hover {
            background: #5c1520;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(122, 31, 43, 0.4);
        }

        .empty-state {
            text-align: center;
            padding: 50px 0;
        }

        .empty-state h3 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: #555;
            margin-bottom: 15px;
        }

        .empty-state a {
            display: inline-block;
            margin-top: 15px;
            padding: 12px 25px;
            background: #7a1f2b;
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 500;
        }
    </style>
</head>

<body>

    <div class="navbar">
        <h1>Al-Khaleej</h1>
        <div class="nav-links">
            <a href="customer_dashboard.php">Menu</a>
            <a href="submit_feedback.php">Feedback</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="cart-container">
        <div class="cart-header">
            <h2>Your Cart</h2>
            <a href="customer_dashboard.php" class="btn-back">← Continue Shopping</a>
        </div>

        <?php if (isset($msg))
            echo "<div style='background:#fce8e6; color:#d93025; padding:15px; border-radius:8px; margin-bottom:20px; font-weight:500; text-align:center;'>$msg</div>"; ?>

        <div class="cart-card">
            <?php
            $total = 0;
            $q = mysqli_query($conn, "SELECT ci.*,m.item_name,m.price FROM cart_items ci JOIN menu m ON ci.menu_id=m.menu_id WHERE ci.cart_id='$cart_id'");

            if (mysqli_num_rows($q) == 0) {
                ?>
                <div class="empty-state">
                    <h3>Your cart is empty</h3>
                    <p style="color:#777;">Looks like you haven't added anything to your cart yet.</p>
                    <a href="customer_dashboard.php">Browse Menu</a>
                </div>
                <?php
            } else {
                ?>
                <table>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th style="text-align:center;">Quantity</th>
                        <th style="text-align:right;">Subtotal</th>
                        <th style="text-align:center;">Action</th>
                    </tr>

                    <?php
                    while ($item = mysqli_fetch_assoc($q)) {
                        $t = $item['price'] * $item['quantity'];
                        $total += $t;
                        ?>
                        <tr>
                            <td class="item-name"><?php echo $item['item_name']; ?></td>
                            <td>₹<?php echo number_format($item['price'], 2); ?></td>
                            <td style="text-align:center;">
                                <div class="qty-control">
                                    <a class="qty-btn" href="?dec=<?php echo $item['items_id']; ?>">-</a>
                                    <span class="qty-val"><?php echo $item['quantity']; ?></span>
                                    <a class="qty-btn" href="?inc=<?php echo $item['items_id']; ?>">+</a>
                                </div>
                            </td>
                            <td style="text-align:right;">₹<?php echo number_format($t, 2); ?></td>
                            <td style="text-align:center;"><a class="btn-remove"
                                    href="?remove=<?php echo $item['items_id']; ?>">Remove</a></td>
                        </tr>
                    <?php } ?>
                </table>

                <div class="cart-summary">
                    <div class="total-row">
                        <span class="total-label">Total Amount</span>
                        <span class="total-amount">₹<?php echo number_format($total, 2); ?></span>
                    </div>

                    <form method="POST">
                        <div style="margin-bottom: 20px; text-align: left; width: 300px;">
                            <label style="font-weight: 500; font-size: 16px; display: block; margin-bottom: 8px;">Order
                                Type</label>
                            <select name="order_type"
                                style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; font-family: inherit; font-size: 16px;"
                                required>
                                <option value="dine_in">Dine In (Table Service)</option>
                                <option value="parcel">Parcel (Takeaway)</option>
                            </select>
                        </div>
                        <button name="place_order" class="btn-checkout">Proceed to Checkout</button>
                    </form>
                </div>
            <?php } ?>
        </div>
    </div>

</body>

</html>