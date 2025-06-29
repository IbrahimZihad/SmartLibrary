<?php
session_start();
include '../connectdb.php'; // Adjust path if necessary

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student and front image
$sql = "SELECT s.*, si.front_img FROM students s 
        LEFT JOIN studentimage si ON s.student_id = si.student_id 
        WHERE s.student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Construct image path like ../StudentImage/123/front.jpg
if (!empty($user['front_img'])) {
    $photo_path = "../StudentImage/" . $student_id . "/" . basename($user['front_img']);
    $photo = file_exists($photo_path) ? $photo_path : 'default-avatar.png';
} else {
    $photo = 'default-avatar.png';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-100 to-purple-100 font-sans">

    <div class="flex justify-center items-center min-h-screen px-4">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <!-- Profile Image -->
            <div class="flex justify-center pt-4">
                <img src="<?= htmlspecialchars($photo) ?>"
                     alt="Profile"
                     class="rounded-full border-4 border-indigo-500 shadow-md"
                     style="width: 120px; height: 120px; object-fit: cover;"
                     onerror="this.onerror=null;this.src='default-avatar.png';">
            </div>

            <!-- Student Info -->
            <div class="mt-6 space-y-3">
                <div class="flex justify-between border-b pb-2">
                    <span class="font-semibold">Name:</span>
                    <span><?= htmlspecialchars($user['name']) ?></span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="font-semibold">Phone:</span>
                    <span><?= htmlspecialchars($user['phone']) ?></span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="font-semibold">Email:</span>
                    <span><?= htmlspecialchars($user['email']) ?></span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="font-semibold">Department:</span>
                    <span><?= htmlspecialchars($user['department']) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="font-semibold">Student ID:</span>
                    <span><?= htmlspecialchars($user['student_id']) ?></span>
                </div>
            </div>

            <!-- Back Button -->
            <div class="text-center mt-6">
                <a href="StudentDashboad.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition-all duration-300">
                    ← Back to Dashboard
                </a>
            </div>
        </div>
    </div>

</body>
</html>
