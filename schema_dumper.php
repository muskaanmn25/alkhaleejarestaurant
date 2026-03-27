<?php
include "db.php";
$res = mysqli_query($conn, 'DESCRIBE feedback');
while($r = mysqli_fetch_assoc($res)){
    echo $r['Field'] . " - " . $r['Type'] . "\n";
}
?>
