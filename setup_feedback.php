<?php
include "db.php";
$sql = "CREATE TABLE IF NOT EXISTS feedback (
    feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comments TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if(mysqli_query($conn, $sql)){
    echo "Feedback table created successfully!";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}
?>
