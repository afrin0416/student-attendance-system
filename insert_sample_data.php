<?php
include 'database.php';

// Insert Departments
$conn->query("INSERT IGNORE INTO departments (department_id, department_name) VALUES 
    (1, 'Computer Science and Engineering'),
    (2, 'Electrical and Electronic Engineering'),
    (3, 'Business Administration')");

// Insert Teachers (password: 123456)
$conn->query("INSERT IGNORE INTO teachers (teacher_id, username, password, teacher_name, email, department_id) VALUES 
    (1, 'ahmed_hasan', '123456', 'Dr. Ahmed Hasan', 'ahmed@buet.edu.bd', 1),
    (2, 'fatema_khatun', '123456', 'Prof. Fatema Khatun', 'fatema@du.ac.bd', 1),
    (3, 'rahim_karim', '123456', 'Dr. Rahim Karim', 'rahim@ju.edu.bd', 2)");

// Insert Courses
$conn->query("INSERT IGNORE INTO courses (course_id, course_code, course_name, department_id, teacher_id, semester) VALUES 
    (1, 'CSE101', 'Introduction to Programming', 1, 1, 'Spring 2024'),
    (2, 'CSE201', 'Data Structures', 1, 1, 'Spring 2024'),
    (3, 'CSE301', 'Database Systems', 1, 2, 'Spring 2024'),
    (4, 'EEE101', 'Circuit Analysis', 2, 3, 'Spring 2024')");

// Insert Students
$conn->query("INSERT IGNORE INTO students (student_id, name, roll_no, email, department_id, semester) VALUES 
    (1, 'Tanvir Rahman', 'CSE001', 'tanvir@student.com', 1, 'Spring 2024'),
    (2, 'Nusrat Jahan', 'CSE002', 'nusrat@student.com', 1, 'Spring 2024'),
    (3, 'Sajib Alam', 'CSE003', 'sajib@student.com', 1, 'Spring 2024'),
    (4, 'Rumana Akter', 'CSE004', 'rumana@student.com', 1, 'Spring 2024'),
    (5, 'Mahmudul Hasan', 'CSE005', 'mahmudul@student.com', 1, 'Spring 2024'),
    (6, 'Shakib Khan', 'CSE006', 'shakib@student.com', 1, 'Spring 2024'),
    (7, 'Parveen Sultana', 'CSE007', 'parveen@student.com', 1, 'Spring 2024'),
    (8, 'Farhan Hossain', 'CSE008', 'farhan@student.com', 1, 'Spring 2024')");

// Enroll students in courses
$conn->query("INSERT IGNORE INTO enrollments (student_id, course_id, enrollment_date) VALUES 
    (1, 1, '2024-01-01'), (1, 2, '2024-01-01'),
    (2, 1, '2024-01-01'), (2, 2, '2024-01-01'),
    (3, 1, '2024-01-01'), (3, 3, '2024-01-01'),
    (4, 1, '2024-01-01'), (4, 2, '2024-01-01'),
    (5, 2, '2024-01-01'), (5, 3, '2024-01-01'),
    (6, 1, '2024-01-01'), (6, 2, '2024-01-01'),
    (7, 2, '2024-01-01'), (7, 3, '2024-01-01'),
    (8, 1, '2024-01-01'), (8, 3, '2024-01-01')");

echo "Sample Bangladeshi data inserted successfully!<br>";
echo "<a href='index.php'>Go to Login Page</a>";
$conn->close();
?>
