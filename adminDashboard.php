<?php
// Database connection
include 'connectdb.php';

// Get today's date as default
$selected_date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");

// Fetch borrow records for the selected date
$sql = "SELECT 
            s.name AS student_name, 
            b.book_name AS book_title, 
            bh.student_id,
            bh.book_id,
            bh.borrow_date, 
            bh.due_date, 
            bh.return_date, 
            bh.penalty 
        FROM borrowhistory bh
        JOIN students s ON bh.student_id = s.student_id
        JOIN booklist b ON bh.book_id = b.book_id
        WHERE DATE(bh.borrow_date) = '$selected_date'
        ORDER BY bh.borrow_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
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
                <li class="nav-item"><a href="borrowedList.php" class="nav-link text-white">Borrowed Books</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 col-lg-10 p-4">
            <!-- Top Nav/Search -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Borrowed Books on <?= $selected_date ?></h4>
                <form method="GET" class="d-flex">
                    <input type="date" id="datePicker" name="date" value="<?= $selected_date ?>" class="form-control me-2" required>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>

            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    let today = new Date().toISOString().split('T')[0];
                    document.getElementById("datePicker").setAttribute("max", today);
                });
            </script>

            <!-- Borrowed List Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Book Title</th>
                            <th>Borrow Date</th>
                            <th>Due Date</th>
                            <th>Return Date</th>
                            <th>Penalty (Taka)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()) : ?>
                                <tr>
                                    <td><?= $row['student_id'] ?></td>
                                    <td><?= $row['student_name'] ?></td>
                                    <td><?= $row['book_title'] ?></td>
                                    <td><?= $row['borrow_date'] ?></td>
                                    <td><?= $row['due_date'] ?></td>
                                    <td><?= $row['return_date'] ?: '<span class="text-danger">Not Returned</span>' ?></td>
                                    <td><?= $row['penalty'] > 0 ? $row['penalty'] : '<span class="text-success">No Penalty</span>' ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">No records found for <?= $selected_date ?></td>
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
