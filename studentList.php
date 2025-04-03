<?php
// Database connection
include 'connectdb.php';
// Fetch all students
$sql = "SELECT student_id, name, email, contact_number FROM students ORDER BY name ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body class="p-4">

    <h2 class="mb-4">Student List</h2>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?= $row['student_id'] ?></td>
                    <td><a href="studentDetails.php?id=<?= $row['student_id'] ?>" class="text-decoration-none"><?= $row['name'] ?></a></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['contact_number'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>
