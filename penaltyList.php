<?php
include 'connectdb.php';

// Auto calculate penalties
include 'calculatePenalties.php';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    $sid = $conn->real_escape_string($_POST['student_id']);
    $conn->query("UPDATE penalty SET status='paid' WHERE student_id='$sid'");
    header("Location: penaltyList.php");
    exit;
}

// Handle search
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Fetch filtered unpaid penalties
$query = "
SELECT 
    s.student_id,
    s.name,
    s.email,
    s.phone,
    b.book_name,
    bh.due_date,
    bh.return_date,
    DATEDIFF(bh.return_date, bh.due_date) * 10 AS penalty_amount,
    p.status
FROM borrowhistory bh
JOIN students s ON bh.student_id = s.student_id
JOIN booklist b ON bh.book_id = b.book_id
JOIN penalty p ON bh.student_id = p.student_id
WHERE bh.return_date > bh.due_date 
  AND p.status = ''
  AND (s.student_id LIKE '%$search%' OR s.name LIKE '%$search%')
ORDER BY bh.due_date DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Penalty List</title>
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
                <li class="nav-item"><a href="penaltyList.php" class="nav-link text-white fw-bold">Penalty List</a></li>
                <li class="nav-item"><a href="borrowedList.php" class="nav-link text-white">Borrowed Books</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <!-- Left: Title -->
                <div>
                    <h4 class="mb-3">Penalty Status (Unpaid Only)</h4>
                </div>

                <!-- Right: Search Form -->
                <form method="GET" class="d-flex" style="max-width: 300px;">
                    <input type="text" name="search" class="form-control me-2" placeholder="Search by Name or ID" value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>

            <!-- Penalty Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Book Title</th>
                            <th>Due Date</th>
                            <th>Penalty (TK)</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['student_id'] ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['phone']) ?></td>
                                    <td><?= htmlspecialchars($row['book_name']) ?></td>
                                    <td><?= $row['due_date'] ?></td>
                                    <td><?= $row['penalty_amount'] ?> TK</td>
                                    <td><span class="badge bg-danger">Not Paid</span></td>
                                    <td>
                                        <form method="POST" action="" onsubmit="return confirm('Mark this penalty as paid?');">
                                            <input type="hidden" name="student_id" value="<?= $row['student_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-success">Mark as Paid</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-muted text-center">No unpaid penalties found.</td>
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
