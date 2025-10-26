<?php
include 'database.php'; // database connection

// Teachers table
$sql_teachers = "CREATE TABLE IF NOT EXISTS teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    department VARCHAR(100),
    course VARCHAR(100)
)";

$conn->query($sql_teachers);

// Students table
$sql_students = "CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    roll_no VARCHAR(50) NOT NULL,
    department VARCHAR(100),
    course VARCHAR(100)
)";

$conn->query($sql_students);

// Attendance table
$sql_attendance = "CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('Present','Absent') NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id)
)";

if($conn->query($sql_attendance) === TRUE){
    echo "Tables created successfully!";
} else {
    echo "Error creating tables: " . $conn->error;
}

$conn->close();
?>
