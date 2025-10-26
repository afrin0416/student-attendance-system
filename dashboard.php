<?php
session_start();
include 'database.php';

if(!isset($_SESSION['teacher_id'])){
    header("Location: index.php");
    exit();
}

// Fetch teacher info
$teacher_id = $_SESSION['teacher_id'];
$sql = "SELECT * FROM teachers WHERE id='$teacher_id'";
$teacher = $conn->query($sql)->fetch_assoc();

// Fetch students for this teacher's course
$sql_students = "SELECT * FROM students WHERE course='{$teacher['course']}'";
$students = $conn->query($sql_students);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo $teacher['username']; ?></h2>
    <h3>Course: <?php echo $teacher['course']; ?></h3>

    <h3>Take Attendance</h3>
    <form method="post" action="attendance.php">
        <table border="1" cellpadding="5">
            <tr>
                <th>Student Name</th>
                <th>Roll No</th>
                <th>Status</th>
            </tr>
            <?php while($row = $students->fetch_assoc()){ ?>
            <tr>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['roll_no']; ?></td>
                <td>
                    <select name="status[<?php echo $row['id']; ?>]">
                        <option value="Present">Present</option>
                        <option value="Absent">Absent</option>
                    </select>
                </td>
            </tr>
            <?php } ?>
        </table><br>
        <input type="submit" value="Submit Attendance">
    </form>

    <br><a href="logout.php">Logout</a>
</body>
</html>
