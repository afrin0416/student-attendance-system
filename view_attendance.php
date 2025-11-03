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

// Fetch attendance records
$sql_attendance = "SELECT a.*, s.name, s.roll_no, s.email
                   FROM attendance a
                   INNER JOIN students s ON a.student_id = s.student_id
                   WHERE a.class_id = '$class_id'
                   ORDER BY s.roll_no";
$attendance_records = $conn->query($sql_attendance);

// Calculate statistics
$total = $attendance_records->num_rows;
$sql_stats = "SELECT 
              SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present,
              SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as absent,
              SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) as late
              FROM attendance WHERE class_id = '$class_id'";
$stats = $conn->query($sql_stats)->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance</title>
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
        <!-- Success Message -->
        <?php if(isset($_SESSION['success_message'])): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-md">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm text-green-800">
                    <?php 
                    echo $_SESSION['success_message']; 
                    unset($_SESSION['success_message']);
                    ?>
                </p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6 mb-6">
            <h2 class="text-2xl font-semibold text-slate-900 mb-4">Attendance Record</h2>
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

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6 hover:shadow-md transition duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Total Students</p>
                        <p class="text-3xl font-bold text-slate-900"><?php echo $total; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6 hover:shadow-md transition duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Present</p>
                        <p class="text-3xl font-bold text-green-600"><?php echo $stats['present']; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6 hover:shadow-md transition duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Absent</p>
                        <p class="text-3xl font-bold text-red-600"><?php echo $stats['absent']; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6 hover:shadow-md transition duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Late</p>
                        <p class="text-3xl font-bold text-yellow-600"><?php echo $stats['late']; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Table -->
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
                        $attendance_records->data_seek(0);
                        while($record = $attendance_records->fetch_assoc()): 
                        ?>
                        <tr class="hover:bg-slate-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <?php echo $count++; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                <?php echo htmlspecialchars($record['roll_no']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                <?php echo htmlspecialchars($record['name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <?php echo htmlspecialchars($record['email']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if($record['status'] == 'Present'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Present
                                    </span>
                                <?php elseif($record['status'] == 'Absent'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Absent
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Late
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                <?php echo htmlspecialchars($record['remarks'] ?: '-'); ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

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