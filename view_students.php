<?php
session_start();
include 'database.php';

if(!isset($_SESSION['teacher_id'])){
    header("Location: index.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];
$course_id = isset($_GET['course_id']) ? $_GET['course_id'] : '';

// Verify access
$sql_course = "SELECT c.*, d.department_name 
               FROM courses c 
               LEFT JOIN departments d ON c.department_id = d.department_id
               WHERE c.course_id = '$course_id' AND c.teacher_id = '$teacher_id'";
$course = $conn->query($sql_course);

if($course->num_rows == 0){
    die("Access denied!");
}

$course_data = $course->fetch_assoc();

// Get enrolled students
$sql_students = "SELECT s.*, d.department_name 
                 FROM students s
                 INNER JOIN enrollments e ON s.student_id = e.student_id
                 LEFT JOIN departments d ON s.department_id = d.department_id
                 WHERE e.course_id = '$course_id'
                 ORDER BY s.roll_no";
$students = $conn->query($sql_students);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen py-8 px-4">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6 mb-6">
            <h2 class="text-2xl font-semibold text-slate-900 mb-4">Enrolled Students</h2>
            <div class="p-4 bg-slate-50 rounded-lg border border-slate-200">
                <div class="flex items-center mb-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                        <?php echo htmlspecialchars($course_data['course_code']); ?>
                    </span>
                    <h3 class="ml-3 text-base font-semibold text-slate-900"><?php echo htmlspecialchars($course_data['course_name']); ?></h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3 text-sm text-slate-600">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="font-medium">Department:</span>
                        <span class="ml-2"><?php echo htmlspecialchars($course_data['department_name']); ?></span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="font-medium">Semester:</span>
                        <span class="ml-2"><?php echo htmlspecialchars($course_data['semester']); ?></span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span class="font-medium">Total Students:</span>
                        <span class="ml-2"><?php echo $students->num_rows; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Table -->
        <?php if($students->num_rows > 0): ?>
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Roll No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Semester</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        <?php 
                        $count = 1;
                        while($student = $students->fetch_assoc()): 
                        ?>
                        <tr class="hover:bg-slate-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <?php echo $count++; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                <?php echo htmlspecialchars($student['roll_no']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                <?php echo htmlspecialchars($student['name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <?php echo htmlspecialchars($student['email']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <?php echo htmlspecialchars($student['department_name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <?php echo htmlspecialchars($student['semester']); ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm">
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-slate-900">No students</h3>
                <p class="mt-1 text-sm text-slate-500">No students enrolled in this course.</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="mt-6">
            <a 
                href="dashboard.php" 
                class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </a>
        </div>
    </div>
</body>
</html>