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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="row min-vh-100">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 bg-dark text-white p-3">
            <h4 class="text-center mb-4">Admin Panel</h4>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="adminDashboard.php" class="nav-link text-white">Dashboard</a></li>
                <li class="nav-item"><a href="studentList.php" class="nav-link text-white fw-bold">Student List</a></li>
                <li class="nav-item"><a href="adminBookList.php" class="nav-link text-white">Book List</a></li>
                <li class="nav-item"><a href="penaltyList.php" class="nav-link text-white">Penalty List</a></li>
                <li class="nav-item"><a href="borrowedList.php" class="nav-link text-white">Borrowed Books</a></li>
            </ul>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 col-lg-10 p-4">
            <!-- Search bar -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Student List</h2>
                <form class="d-flex" action="studentList.php" method="GET">
                    <input type="text" name="search" class="form-control me-2" placeholder="Search by Name or ID" value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>

            <!-- Student table -->
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Photo</th>
                            <th scope="col">Student ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td>
                                    <?php if ($row['front_img']) : ?>
                                        <img src="<?= htmlspecialchars($row['front_img']) ?>" alt="Photo" class="rounded-circle border" style="width: 60px; height: 60px; object-fit: cover;">
                                    <?php else : ?>
                                        <span class="text-muted">No Photo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $row['student_id'] ?></td>
                                <td>
                                    <a href="studentDetails.php?id=<?= $row['student_id'] ?>" class="text-decoration-none"><?= $row['name'] ?></a>
                                </td>
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
                        <?php if ($result->num_rows === 0): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No students found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

</body>
</html>
