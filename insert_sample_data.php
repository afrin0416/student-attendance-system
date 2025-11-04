<?php
include 'database.php';

// Insert Departments
$conn->query("INSERT IGNORE INTO departments (department_id, department_name) VALUES 
    (1, 'Computer Science and Engineering'),
    (2, 'Electrical and Electronic Engineering'),
    (3, 'Business Administration'),
    (4, 'Civil Engineering')");

// Insert Teachers (password: 123456)
$conn->query("INSERT IGNORE INTO teachers (teacher_id, username, password, teacher_name, email, department_id) VALUES 
    (1, 'nazma', '123456', 'Nazma Akter', 'nazma@buet.edu.bd', 1),
    (2, 'fatema_khatun', '123456', 'Prof. Fatema Khatun', 'fatema@du.ac.bd', 1),
    (3, 'anika', '123456', 'Anika Karim', 'anika@ju.edu.bd', 2)");

// Insert Courses
$conn->query("INSERT IGNORE INTO courses (course_id, course_code, course_name, department_id, teacher_id, semester) VALUES 
    (1, 'CSE101', 'Introduction to Programming', 1, 1, 'Spring 2025'),
    (2, 'CSE201', 'Data Structures', 1, 1, 'Spring 2025'),
    (3, 'CSE301', 'Database Systems', 1, 2, 'Spring 2025'),
    (4, 'EEE101', 'Circuit Analysis', 2, 3, 'Spring 2025')");

// Insert Students
$conn->query("INSERT IGNORE INTO students (student_id, name, roll_no, email, department_id, semester) VALUES 
    (1, 'Afroza Afrin', 'CSE001', 'afroza@student.com', 1, 'Spring 2025'),
    (2, 'Dola', 'CSE002', 'dola@student.com', 1, 'Spring 2025'),
    (3, 'Fouzia', 'CSE003', 'fouzia@student.com', 1, 'Spring 2025'),
    (4, 'jungkook', 'CSE004', 'jungkook@student.com', 1, 'Spring 2025'),
    (5, 'Mahmudul Hasan', 'CSE005', 'mahmudul@student.com', 1, 'Spring 2025'),
    (6, 'Shakib Khan', 'CSE006', 'shakib@student.com', 1, 'Spring 2025'),
    (7, 'Parveen Sultana', 'CSE007', 'parveen@student.com', 1, 'Spring 2025'),
    (8, 'Farhan Hossain', 'CSE008', 'farhan@student.com', 1, 'Spring 2025')");

// Enroll students in courses
$conn->query("INSERT IGNORE INTO enrollments (student_id, course_id, enrollment_date) VALUES 
    (1, 1, '2025-01-01'), (1, 2, '2025-01-01'),
    (2, 1, '2025-01-01'), (2, 2, '2025-01-01'),
    (3, 1, '2025-01-01'), (3, 3, '2025-01-01'),
    (4, 1, '2025-01-01'), (4, 2, '2025-01-01'),
    (5, 2, '2025-01-01'), (5, 3, '2025-01-01'),
    (6, 1, '2025-01-01'), (6, 2, '2025-01-01'),
    (7, 2, '2025-01-01'), (7, 3, '2025-01-01'),
    (8, 1, '2025-01-01'), (8, 3, '2025-01-01')");

echo "data inserted successfully!<br>";
echo "<a href='index.php'>Go to Login Page</a>";
$conn->close();
?>
<!-- INSERT IGNORE INTO teachers (teacher_id, username, password, teacher_name, email, department_id) VALUES
(4, 'kawsar', '123456', 'Md. Kawsar', 'kawsar@buet.edu.bd', 1),
(5, 'shanchayan', '123456', 'Shanchayan Bhattachariya', 'shanchayan@du.ac.bd', 2),
(6, 'abdur_rahman', '123456', 'Abdur Rahman', 'abdur@cuet.edu.bd', 3),
(7, 'tasnim', '123456', 'Tasnim Sadia', 'tasnim@ruet.edu.bd', 1),
(8, 'alema', '123456', 'Alema Khatun', 'alema@ju.edu.bd', 2);
....assigned courses to these teachers as well. -
-- Insert Courses for new teachers
INSERT IGNORE INTO courses (course_id, course_code, course_name, department_id, teacher_id, semester) VALUES
(5, 'CSE401', 'Computer Networks', 1, 4, 'Spring 2025'),
(6, 'EEE201', 'Digital Logic Design', 2, 5, 'Spring 2025'),
(7, 'BBA101', 'Principles of Management', 3, 6, 'Spring 2025'),
(8, 'CSE402', 'Artificial Intelligence', 1, 7, 'Spring 2025'),
(9, 'EEE301', 'Power Electronics', 2, 8, 'Spring 2025');
....added more students..
INSERT IGNORE INTO students (student_id, name, roll_no, email, department_id, semester) VALUES
(9, 'Rupa', 'CSE009', 'rupa@student.com', 1, 'Spring 2025'),
(10, 'Kotha', 'CSE010', 'kotha@student.com', 1, 'Spring 2025'),
(11, 'Shehab', 'EEE009', 'shehab@student.com', 2, 'Spring 2025'),
(12, 'Nidhi', 'BBA009', 'nidhi@student.com', 3, 'Spring 2025'),
(13, 'Nadia', 'CSE011', 'nadia@student.com', 1, 'Spring 2025'),
(14, 'Shajib', 'EEE010', 'shajib@student.com', 2, 'Spring 2025'),
(15, 'Nayeem', 'BBA010', 'nayeem@student.com', 3, 'Spring 2025'),
(16, 'Jumana', 'CSE012', 'jumana@student.com', 1, 'Spring 2025'),
(17, 'Habib', 'EEE011', 'habib@student.com', 2, 'Spring 2025');
....enrolled these students in various courses..
-- Enroll new students in different courses
INSERT IGNORE INTO enrollments (student_id, course_id, enrollment_date) VALUES
-- Rupa
(9, 1, '2025-01-01'), (9, 5, '2025-01-01'),
-- Kotha
(10, 2, '2025-01-01'), (10, 8, '2025-01-01'),
-- Shehab
(11, 4, '2025-01-01'), (11, 6, '2025-01-01'),
-- Nidhi
(12, 3, '2025-01-01'), (12, 7, '2025-01-01'),
-- Nadia
(13, 1, '2025-01-01'), (13, 8, '2025-01-01'),
-- Shajib
(14, 4, '2025-01-01'), (14, 9, '2025-01-01'),
-- Nayeem
(15, 3, '2025-01-01'), (15, 7, '2025-01-01'),
-- Jumana
(16, 2, '2025-01-01'), (16, 5, '2025-01-01'),
-- Habib
(17, 4, '2025-01-01'), (17, 9, '2025-01-01');

 -->