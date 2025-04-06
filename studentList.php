<?php
// Database connection
include 'connectdb.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch students with their front image
$sql = "SELECT s.student_id, s.name, s.email, s.phone, i.front_img 
        FROM students s
        LEFT JOIN studentimage i ON s.student_id = i.student_id
        WHERE s.student_id LIKE '%$search%' OR s.name LIKE '%$search%'
        ORDER BY s.student_id ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="admin.css">
    <style>
        .student-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
        }
        .highlight {
            background-color: #d1ecf1 !important;
        }
    </style>
</head>
<body class="p-4">

<div class="sidebar">
    <h4 class="text-center">Admin Panel</h4>
    <a href="adminDashboard.php">Dashboard</a>
    <a href="studentList.php">Student List</a>
    <a href="bookList.php">Book List</a>
    <a href="penaltyList.php">Penalty List</a>
    <a href="borrowedList.php">Borrowed Books</a>
</div>

<nav class="navbar bg-light mb-3">
    <form class="d-flex" action="studentList.php" method="GET">
        <input type="text" name="search" class="form-control me-2" placeholder="Search by Name or ID" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
</nav>

<h2 class="mb-4">Student List</h2>

<table class="table table-bordered align-middle">
    <thead class="table-dark">
        <tr>
            <th>Photo</th>
            <th>Student ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr id="student-<?= $row['student_id'] ?>" class="student-row" data-id="<?= $row['student_id'] ?>" data-name="<?= strtolower($row['name']) ?>">
                <td>
                    <?php if ($row['front_img']) : ?>
                        <img src="<?= htmlspecialchars($row['front_img']) ?>" alt="Student Photo" class="student-img">
                    <?php else : ?>
                        <span class="text-muted">No Photo</span>
                    <?php endif; ?>
                </td>
                <td><?= $row['student_id'] ?></td>
                <td><a href="studentDetails.php?id=<?= $row['student_id'] ?>" class="text-decoration-none"><?= $row['name'] ?></a></td>
                <td><?= $row['email'] ?></td>
                <td><?= $row['phone'] ?></td>
                <td>
                    <form method="POST" action="deleteStudent.php" onsubmit="return confirm('Are you sure you want to delete this student?');">
                        <input type="hidden" name="student_id" value="<?= $row['student_id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
