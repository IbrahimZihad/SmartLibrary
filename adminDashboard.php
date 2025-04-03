<?php
// Database connection
include 'connectdb.php';

// Get today's date as default
$selected_date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");

// Fetch borrow records for the selected date
$sql = "SELECT br.record_id, s.name AS student_name, b.title AS book_title, br.borrow_date, br.due_date, br.return_date, br.penalty_amount 
        FROM borrow_records br
        JOIN students s ON br.student_id = s.student_id
        JOIN books b ON br.book_id = b.book_id
        WHERE DATE(br.borrow_date) = '$selected_date'
        ORDER BY br.borrow_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { display: flex; }
        .sidebar { width: 250px; background: #343a40; color: white; min-height: 100vh; padding-top: 20px; }
        .sidebar a { display: block; color: white; padding: 10px; text-decoration: none; }
        .sidebar a:hover { background: #495057; }
        .content { flex-grow: 1; padding: 20px; }
        .navbar { background: #007bff; padding: 10px; color: white; }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center">Admin Panel</h4>
    <a href="adminDashboard.php">Dashboard</a>
    <a href="studentList.php">Student List</a>
    <a href="bookList.php">Book List</a>
    <a href="penaltyList.php">Penalty List</a>
    <a href="borrowedList.php">Borrowed Books</a>
</div>

<!-- Main Content -->
<div class="content">
    
    <!-- Navbar with Date Picker -->
    <nav class="navbar">
        <form method="GET" class="d-flex">
            <input type="date" name="date" value="<?= $selected_date ?>" class="form-control me-2">
            <button type="submit" class="btn btn-light">Search</button>
        </form>
    </nav>

    <!-- Borrowed Books List -->
    <div class="mt-4">
        <h4>Borrowed Books on <?= $selected_date ?></h4>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Record ID</th>
                    <th>Student Name</th>
                    <th>Book Title</th>
                    <th>Borrow Date</th>
                    <th>Due Date</th>
                    <th>Return Date</th>
                    <th>Penalty (Taka)</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?= $row['record_id'] ?></td>
                        <td><?= $row['student_name'] ?></td>
                        <td><?= $row['book_title'] ?></td>
                        <td><?= $row['borrow_date'] ?></td>
                        <td><?= $row['due_date'] ?></td>
                        <td><?= $row['return_date'] ? $row['return_date'] : 'Not Returned' ?></td>
                        <td><?= $row['penalty_amount'] > 0 ? $row['penalty_amount'] : 'No Penalty' ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
