<?php
session_start();
include 'connectdb.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id']);
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ? AND email = ?");
    $stmt->bind_param("ss", $student_id, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $_SESSION['student_id'] = $student_id;
        $_SESSION['student_name'] = $result->fetch_assoc()['name'];
        header("Location: studentDashboard.php");
        exit();
    } else {
        $error = "Invalid Student ID or Email!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="height: 100vh; display: flex; align-items: center; justify-content: center; background-image: url('Images/background.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">

    <!-- Login Modal -->
    <div class="modal-dialog">
        <div class="modal-content shadow-lg p-4 rounded">
            <div class="modal-header">
                <h5 class="modal-title">Student Login</h5>
            </div>
            <div class="modal-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Student ID</label>
                        <input type="text" name="student_id" id="student_id" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
            <div class="modal-footer justify-content-center">
                <p class="mb-0">New student? <a href="registration.php">Register here</a></p>
            </div>
        </div>
    </div>

</body>
</html>
