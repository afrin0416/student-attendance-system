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
$sql_course = "SELECT * FROM courses WHERE course_id = '$course_id' AND teacher_id = '$teacher_id'";
$course = $conn->query($sql_course);

if($course->num_rows == 0){
    die("Access denied!");
}

$course_data = $course->fetch_assoc();

// Get total classes
$sql_total_classes = "SELECT COUNT(*) as total FROM class WHERE course_id = '$course_id'";
$total_classes = $conn->query($sql_total_classes)->fetch_assoc()['total'];

// Get attendance summary for each student
$sql_report = "SELECT s.student_id, s.name, s.roll_no,
               COUNT(DISTINCT cl.class_id) as total_classes,
               SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) as present,
               SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) as absent,
               SUM(CASE WHEN a.status = 'Late' THEN 1 ELSE 0 END) as late,
               ROUND((SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / COUNT(DISTINCT cl.class_id)) * 100, 2) as percentage
               FROM students s
               INNER JOIN enrollments e ON s.student_id = e.student_id
               LEFT JOIN class cl ON e.course_id = cl.course_id
               LEFT JOIN attendance a ON s.student_id = a.student_id AND cl.class_id = a.class_id
               WHERE e.course_id = '$course_id'
               GROUP BY s.student_id, s.name, s.roll_no
               ORDER BY s.roll_no";
$report = $conn->query($sql_report);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Attendance Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen py-8 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-semibold text-slate-900">📊 Semester Attendance Report</h2>
            </div>
            <div class="p-4 bg-slate-50 rounded-lg border border-slate-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-100 text-blue-800 mb-2">
                            <?php echo htmlspecialchars($course_data['course_code']); ?>
                        </span>
                        <p class="font-semibold text-slate-900"><?php echo htmlspecialchars($course_data['course_name']); ?></p>
                    </div>
                    <div class="text-slate-600">
                        <p><span class="font-medium">Semester:</span> <?php echo htmlspecialchars($course_data['semester']); ?></p>
                    </div>
                    <div class="text-slate-600">
                        <p><span class="font-medium">Teacher:</span> <?php echo htmlspecialchars($_SESSION['teacher_name']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6 hover:shadow-md transition duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Total Classes</p>
                        <p class="text-3xl font-bold text-slate-900"><?php echo $total_classes; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6 hover:shadow-md transition duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 mb-1">Total Students</p>
                        <p class="text-3xl font-bold text-slate-900"><?php echo $report->num_rows; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Table -->
        <?php if($report->num_rows > 0): ?>
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Roll No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Student Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Total Classes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Present</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Absent</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Late</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Attendance %</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        <?php 
                        $count = 1;
                        while($row = $report->fetch_assoc()): 
                            $percentage = $row['percentage'] ?: 0;
                        ?>
                        <tr class="hover:bg-slate-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <?php echo $count++; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                <?php echo htmlspecialchars($row['roll_no']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                <?php echo htmlspecialchars($row['name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <?php echo $row['total_classes'] ?: 0; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-medium">
                                <?php echo $row['present'] ?: 0; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium">
                                <?php echo $row['absent'] ?: 0; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600 font-medium">
                                <?php echo $row['late'] ?: 0; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if($percentage >= 75): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <?php echo number_format($percentage, 2); ?>%
                                    </span>
                                <?php elseif($percentage >= 50): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <?php echo number_format($percentage, 2); ?>%
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <?php echo number_format($percentage, 2); ?>%
                                    </span>
                                <?php endif; ?>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-slate-900">No data available</h3>
                <p class="mt-1 text-sm text-slate-500">No attendance data available yet.</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="mt-6 flex space-x-3 no-print">
            <a 
                href="dashboard.php" 
                class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </a>
            <button 
                onclick="window.print()" 
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Report
            </button>
        </div>
    </div>
</body>
</html>