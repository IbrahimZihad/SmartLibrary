<?php
session_start();
include 'connectdb.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Join students and studentimage table to get all info
$sql = "SELECT s.*, si.front_img FROM students s LEFT JOIN studentimage si ON s.student_id = si.student_id WHERE s.student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Set image path (front image)
$photo = $user['front_img'] ?? '';
if ($photo) {
    // যদি path-এ StudentImage না থাকে, prepend করুন
    if (strpos($photo, 'StudentImage/') !== 0) {
        $photo = 'StudentImage/' . $student_id . '/' . $photo;
    }
} else {
    $photo = 'default-avatar.png';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card mx-auto shadow" style="max-width: 400px;">
            <div class="card-body text-center">
                <img src="<?= htmlspecialchars($photo) ?>"
                     alt="Profile"
                     class="rounded-circle mb-3"
                     style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #6366f1;"
                     onerror="this.onerror=null;this.src='default-avatar.png';">
                <h3 class="mb-1"><?= htmlspecialchars($user['name']) ?></h3>
                <p class="text-muted mb-2"><?= htmlspecialchars($user['email']) ?></p>
                <p class="mb-0"><strong>ID:</strong> <?= htmlspecialchars($user['student_id']) ?></p>
                <a href="StudentDashboad.php" class="btn btn-primary mt-4">Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>