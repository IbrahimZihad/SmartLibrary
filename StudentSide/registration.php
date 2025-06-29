<?php
// Start session to store submitted data
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="height: 100vh; display: flex; align-items: center; justify-content: center; background-image: url('../Images/background.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">

    <!-- Registration Modal-Style Card -->
    <div class="modal-dialog ">
        <div class="modal-content shadow-lg p-4 rounded">
            <div class="modal-header">
                <h5 class="modal-title">Student Registration</h5>
            </div>
            <div class="modal-body">
                <form method="POST" action="studentimage.php">
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Student ID</label>
                        <input type="text" name="student_id" id="student_id" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" name="phone" id="phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="department" class="form-label">Department</label>
                        <input type="text" name="department" id="department" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Next</button>
                </form>
            </div>
            <div class="modal-footer justify-content-center">
                <p class="mb-0">Already registered? <a href="login.php">Sign In</a></p>
            </div>
        </div>
    </div>

</body>
</html>
