<?php
$conn = mysqli_connect('localhost','root','','alkhaleej_db');
if(mysqli_query($conn, "ALTER TABLE orders DROP FOREIGN KEY orders_ibfk_1")) {
    echo "Dropped FK on orders.\n";
} else {
    echo "Could not drop FK: " . mysqli_error($conn) . "\n";
}
?>
