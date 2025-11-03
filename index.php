<?php
session_start();
include 'database.php';

if(isset($_SESSION['teacher_id'])){
    header("Location: dashboard.php");
    exit();
}

if(isset($_POST['login'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM teachers WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if($result->num_rows > 0){
        $teacher = $result->fetch_assoc();
        $_SESSION['teacher_id'] = $teacher['teacher_id'];
        $_SESSION['teacher_name'] = $teacher['teacher_name'];
        $_SESSION['username'] = $teacher['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Login - Attendance System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Login Card -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <!-- Header -->
            <div class="px-8 pt-8 pb-6 text-center border-b border-slate-100">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-semibold text-slate-900">Attendance System</h1>
                <p class="text-sm text-slate-500 mt-2">Class Attendance Management</p>
            </div>
            
            <!-- Form -->
            <div class="p-8">
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
                
                <form method="post" action="" class="space-y-5">
                    <!-- Username Field -->
                    <div class="space-y-2">
                        <label for="username" class="block text-sm font-medium text-slate-700">
                            Username
                        </label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            required
                            class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                            placeholder="Enter your username"
                        >
                    </div>
                    
                    <!-- Password Field -->
                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-medium text-slate-700">
                            Password
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                            placeholder="Enter your password"
                        >
                    </div>
                    
                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        name="login" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-md transition duration-150 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Sign In
                    </button>
                </form>
            </div>
            
            <!-- Demo Credentials -->
            <div class="px-8 pb-8">
                <div class="bg-slate-50 border border-slate-200 rounded-md p-4">
                    <h4 class="text-xs font-semibold text-slate-700 uppercase tracking-wide mb-2">Demo Credentials</h4>
                    <div class="text-sm text-slate-600 space-y-1">
                        <p><span class="font-medium text-slate-700">Username:</span> john_doe</p>
                        <p><span class="font-medium text-slate-700">Password:</span> 123456</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>