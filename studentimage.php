<?php
session_start();
include 'connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id']) && isset($_FILES['front_img'])) {
    // Receiving student data from previous form
    $student_id = $_POST['student_id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $department = $_POST['department'];

    // Image data
    $front_img = $_FILES['front_img'];
    $left_img = $_FILES['left_img'];
    $right_img = $_FILES['right_img'];

    $upload_dir = "StudentImage/$student_id/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Save images
    $front_path = $upload_dir . "front_" . basename($front_img['name']);
    $left_path  = $upload_dir . "left_"  . basename($left_img['name']);
    $right_path = $upload_dir . "right_" . basename($right_img['name']);

    move_uploaded_file($front_img['tmp_name'], $front_path);
    move_uploaded_file($left_img['tmp_name'], $left_path);
    move_uploaded_file($right_img['tmp_name'], $right_path);

    // Insert into students
    $stmt1 = $conn->prepare("INSERT INTO students (student_id, name, phone, email, department) VALUES (?, ?, ?, ?, ?)");
    $stmt1->bind_param("sssss", $student_id, $name, $phone, $email, $department);
    $stmt1->execute();

    // Insert into studentimage
    $stmt2 = $conn->prepare("INSERT INTO studentimage (student_id, front_img, left_img, right_img) VALUES (?, ?, ?, ?)");
    $stmt2->bind_param("ssss", $student_id, $front_path, $left_path, $right_path);
    $stmt2->execute();

    echo "<script>alert('Registration Successful!'); window.location.href='login.php';</script>";
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Display image upload form after first step
    $_SESSION['student_data'] = $_POST;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Images</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
    <div class="container">
        <h2 class="mb-4">Upload Student Images</h2>

        <form method="POST" enctype="multipart/form-data">
            <!-- Hidden inputs to retain student info -->
            <?php foreach ($_SESSION['student_data'] as $key => $value): ?>
                <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
            <?php endforeach; ?>

            <div class="mb-3">
                <label class="form-label">Front Image</label>
                <input type="file" name="front_img" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Left Image</label>
                <input type="file" name="left_img" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Right Image</label>
                <input type="file" name="right_img" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success">Register</button>
        </form>
    </div>
</body>
</html>
