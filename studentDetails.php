<?php
// Database connection
include 'connectdb.php';

// Get student ID from URL
$student_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch student details
$student_sql = "SELECT name FROM students WHERE student_id = $student_id";
$student_result = $conn->query($student_sql);
$student = $student_result->fetch_assoc();

// Fetch borrowed books & penalties
$sql = "SELECT br.record_id, b.title AS book_title, br.borrow_date, br.due_date, br.return_date, br.penalty_amount 
        FROM borrow_records br
        JOIN books b ON br.book_id = b.book_id
        WHERE br.student_id = $student_id
        ORDER BY br.borrow_date DESC";

$result = $conn->query($sql);

// Calculate total penalty
$total_penalty_sql = "SELECT SUM(penalty_amount) AS total_penalty FROM borrow_records WHERE student_id = $student_id";
$total_penalty_result = $conn->query($total_penalty_sql);
$total_penalty = $total_penalty_result->fetch_assoc()['total_penalty'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Borrowed Books</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

    <h2 class="mb-4"><?= $student['name'] ?> - Borrowed Books</h2>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Record ID</th>
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
                    <td><?= $row['book_title'] ?></td>
                    <td><?= $row['borrow_date'] ?></td>
                    <td><?= $row['due_date'] ?></td>
                    <td><?= $row['return_date'] ? $row['return_date'] : 'Not Returned' ?></td>
                    <td><?= $row['penalty_amount'] > 0 ? $row['penalty_amount'] : 'No Penalty' ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h4>Total Penalty: <strong><?= $total_penalty ? $total_penalty : '0' ?> Taka</strong></h4>

</body>
</html>
