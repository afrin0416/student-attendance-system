<?php
session_start();
include 'database.php';

if(!isset($_SESSION['teacher_id'])){
    header("Location: index.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];
$course_id = isset($_GET['course_id']) ? $_GET['course_id'] : '';

// Verify teacher owns this course
$sql_verify = "SELECT * FROM courses WHERE course_id = '$course_id' AND teacher_id = '$teacher_id'";
$course = $conn->query($sql_verify);

if($course->num_rows == 0){
    die("Access denied!");
}

$course_data = $course->fetch_assoc();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $class_date = mysqli_real_escape_string($conn, $_POST['class_date']);
    $class_time = mysqli_real_escape_string($conn, $_POST['class_time']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $topic = mysqli_real_escape_string($conn, $_POST['topic']);
    
    $sql_insert = "INSERT INTO class (course_id, teacher_id, class_date, class_time, duration, topic) 
                   VALUES ('$course_id', '$teacher_id', '$class_date', '$class_time', '$duration', '$topic')";
    
    if($conn->query($sql_insert)){
        $class_id = $conn->insert_id;
        header("Location: take_attendance.php?class_id=$class_id");
        exit();
    } else {
        $error = "Error creating class: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Class</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen py-8 px-4">
    <div class="max-w-2xl mx-auto">
        <!-- Header Card -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6 mb-6">
            <h2 class="text-2xl font-semibold text-slate-900 mb-2">Create New Class</h2>
            <div class="mt-4 p-4 bg-slate-50 rounded-lg border border-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-100 text-blue-800 mb-2">
                            <?php echo htmlspecialchars($course_data['course_code']); ?>
                        </span>
                        <p class="text-sm font-medium text-slate-900"><?php echo htmlspecialchars($course_data['course_name']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6">
            <?php if(isset($error)): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-md">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-red-800"><?php echo $error; ?></p>
                </div>
            </div>
            <?php endif; ?>

            <form method="post" action="" class="space-y-6">
                <!-- Class Date -->
                <div>
                    <label for="class_date" class="block text-sm font-medium text-slate-700 mb-2">
                        Class Date <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="class_date" 
                        name="class_date" 
                        value="<?php echo date('Y-m-d'); ?>" 
                        required
                        class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                    >
                </div>

                <!-- Class Time -->
                <div>
                    <label for="class_time" class="block text-sm font-medium text-slate-700 mb-2">
                        Class Time <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="time" 
                        id="class_time" 
                        name="class_time" 
                        required
                        class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                    >
                </div>

                <!-- Duration -->
                <div>
                    <label for="duration" class="block text-sm font-medium text-slate-700 mb-2">
                        Duration (minutes) <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="duration" 
                        name="duration" 
                        value="60" 
                        min="15" 
                        max="300" 
                        required
                        class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                    >
                </div>

                <!-- Topic -->
                <div>
                    <label for="topic" class="block text-sm font-medium text-slate-700 mb-2">
                        Topic/Description
                    </label>
                    <textarea 
                        id="topic" 
                        name="topic" 
                        rows="3"
                        placeholder="Enter class topic or description"
                        class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                    ></textarea>
                </div>

                <!-- Buttons -->
                <div class="flex items-center space-x-3 pt-4">
                    <button 
                        type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Class & Take Attendance
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
    </div>
</body>
</html>