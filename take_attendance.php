<?php
session_start();
include 'database.php';

if(!isset($_SESSION['teacher_id'])){
    header("Location: index.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];
$class_id = isset($_GET['class_id']) ? $_GET['class_id'] : '';

// Fetch class details
$sql_class = "SELECT cl.*, c.course_name, c.course_code 
              FROM class cl
              INNER JOIN courses c ON cl.course_id = c.course_id
              WHERE cl.class_id = '$class_id' AND cl.teacher_id = '$teacher_id'";
$class_result = $conn->query($sql_class);

if($class_result->num_rows == 0){
    die("Invalid class or access denied!");
}

$class_data = $class_result->fetch_assoc();
$course_id = $class_data['course_id'];

// Check if attendance already taken
$sql_check = "SELECT COUNT(*) as count FROM attendance WHERE class_id = '$class_id'";
$attendance_check = $conn->query($sql_check)->fetch_assoc();

if($attendance_check['count'] > 0){
    header("Location: view_attendance.php?class_id=$class_id");
    exit();
}

// Get enrolled students
$sql_students = "SELECT s.* FROM students s
                 INNER JOIN enrollments e ON s.student_id = e.student_id
                 WHERE e.course_id = '$course_id'
                 ORDER BY s.roll_no";
$students = $conn->query($sql_students);

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['attendance'])){
    $date = $class_data['class_date'];
    $success_count = 0;
    
    foreach($_POST['attendance'] as $student_id => $status){
        $remarks = isset($_POST['remarks'][$student_id]) ? mysqli_real_escape_string($conn, $_POST['remarks'][$student_id]) : '';
        
        $sql_insert = "INSERT INTO attendance (class_id, student_id, status, date, remarks) 
                       VALUES ('$class_id', '$student_id', '$status', '$date', '$remarks')";
        
        if($conn->query($sql_insert)){
            $success_count++;
        }
    }
    
    $_SESSION['success_message'] = "Attendance recorded for $success_count students!";
    header("Location: view_attendance.php?class_id=$class_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Attendance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    <script>
        function markAll(status) {
            const selects = document.querySelectorAll('.status-select');
            selects.forEach(select => {
                select.value = status;
            });
        }
    </script>
</head>
<body class="bg-slate-50 min-h-screen py-8 px-4">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6 mb-6">
            <h2 class="text-2xl font-semibold text-slate-900 mb-4">Take Attendance</h2>
            <div class="p-4 bg-slate-50 rounded-lg border border-slate-200">
                <div class="flex items-center mb-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                        <?php echo htmlspecialchars($class_data['course_code']); ?>
                    </span>
                    <h3 class="ml-3 text-base font-semibold text-slate-900"><?php echo htmlspecialchars($class_data['course_name']); ?></h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3 text-sm text-slate-600">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="font-medium">Date:</span>
                        <span class="ml-2"><?php echo date('F d, Y', strtotime($class_data['class_date'])); ?></span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-medium">Time:</span>
                        <span class="ml-2"><?php echo date('h:i A', strtotime($class_data['class_time'])); ?></span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                        <span class="font-medium">Topic:</span>
                        <span class="ml-2"><?php echo htmlspecialchars($class_data['topic'] ?: 'N/A'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-4 mb-6">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-slate-700">Quick Actions:</span>
                <div class="flex space-x-2">
                    <button 
                        type="button" 
                        onclick="markAll('Present')" 
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition duration-150"
                    >
                        Mark All Present
                    </button>
                    <button 
                        type="button" 
                        onclick="markAll('Absent')" 
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition duration-150"
                    >
                        Mark All Absent
                    </button>
                </div>
            </div>
        </div>

        <!-- Attendance Form -->
        <form method="post" action="">
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Roll No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Student Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Remarks</th>
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
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <select 
                                        name="attendance[<?php echo $student['student_id']; ?>]" 
                                        class="status-select px-3 py-1.5 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150" 
                                        required
                                    >
                                        <option value="Present" selected>Present</option>
                                        <option value="Absent">Absent</option>
                                        <option value="Late">Late</option>
                                    </select>
                                </td>
                                <td class="px-6 py-4">
                                    <input 
                                        type="text" 
                                        name="remarks[<?php echo $student['student_id']; ?>]" 
                                        placeholder="Optional"
                                        class="w-full px-2 py-1 text-sm border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                                    >
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-6 flex items-center space-x-3">
                <button 
                    type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Submit Attendance
                </button>
                <a 
                    href="dashboard.php" 
                    class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150"
                >
                    Cancel
                </a>
            </div>
        </form>
    </div>
</body>
</html>