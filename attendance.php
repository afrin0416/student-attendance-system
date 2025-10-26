<?php
session_start();
include 'database.php';

if(!isset($_SESSION['teacher_id'])){
    header("Location: index.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status'])){
    $teacher_id = $_SESSION['teacher_id'];
    $date = date('Y-m-d');
    
    foreach($_POST['status'] as $student_id => $status){
        $sql = "INSERT INTO attendance (student_id, date, status) 
                VALUES ('$student_id', '$date', '$status')";
        $conn->query($sql);
    }
    header("Location: dashboard.php");
}
?>
