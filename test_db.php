<?php
include 'database.php';

if($conn){
    echo "Database connected successfully!";
} else {
    echo "Connection failed!";
}
?>
