<?php
include 'connectdb.php';

// Handle search input
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Query borrow records with search filter
$sql = "
SELECT 
    s.student_id,
    s.name AS student_name,
    b.book_id,
    b.book_name,
    bh.borrow_date,
    bh.due_date,
    bh.borrowed_copies
FROM borrowhistory bh
JOIN students s ON bh.student_id = s.student_id
JOIN booklist b ON bh.book_id = b.book_id
WHERE 
    s.student_id LIKE '%$search%' OR
    s.name LIKE '%$search%' OR
    b.book_id LIKE '%$search%' OR
    b.book_name LIKE '%$search%' OR
    bh.borrow_date LIKE '%$search%'
ORDER BY bh.borrow_date DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Borrowed Books</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
                <li class="nav-item"><a href="studentList.php" class="nav-link text-white">Student List</a></li>
                <li class="nav-item"><a href="adminBookList.php" class="nav-link text-white">Book List</a></li>
                <li class="nav-item"><a href="penaltyList.php" class="nav-link text-white">Penalty List</a></li>
                <li class="nav-item"><a href="borrowedList.php" class="nav-link text-white fw-bold">Borrowed Books</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <!-- Left Title -->
                <div>
                    <h4 class="mb-3">Borrowed Book Records</h4>
                </div>

                <!-- Right Search Form -->
                <form method="GET" class="d-flex" style="max-width: 300px;">
                    <input type="text" name="search" class="form-control me-2" placeholder="Search by Name, ID, or Date" value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>

            <!-- Borrowed List Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Book ID</th>
                            <th>Book Title</th>
                            <th>Borrow Date</th>
                            <th>Due Date</th>
                            <th>Borrowed Copies</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['student_id'] ?></td>
                                    <td><?= htmlspecialchars($row['student_name']) ?></td>
                                    <td><?= $row['book_id'] ?></td>
                                    <td><?= htmlspecialchars($row['book_name']) ?></td>
                                    <td><?= $row['borrow_date'] ?></td>
                                    <td><?= $row['due_date'] ?></td>
                                    <td><?= $row['borrowed_copies'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-muted text-center">No borrowed book records found.</td>
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
