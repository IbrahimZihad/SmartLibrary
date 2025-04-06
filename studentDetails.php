<?php
// Database connection
include 'connectdb.php';

$student_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle student deletion
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_student'])) {
    $conn->query("DELETE FROM borrow_records WHERE student_id = $student_id");
    $conn->query("DELETE FROM penalty WHERE student_id = $student_id");
    $conn->query("DELETE FROM studentimage WHERE student_id = $student_id");
    $conn->query("DELETE FROM students WHERE student_id = $student_id");

    echo "<script>alert('Student record deleted successfully.'); window.location.href = 'studentList.php';</script>";
    exit();
}

// Handle penalty payment
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['pay_penalty'])) {
    // Update penalty table
    $conn->query("UPDATE penalty SET status = 'paid' WHERE student_id = $student_id");

    // Update borrow records
    $conn->query("UPDATE borrow_records SET penalty_amount = 0 WHERE student_id = $student_id");

    echo "<script>alert('Penalty paid successfully.'); window.location.href = 'studentDetails.php?id=$student_id';</script>";
    exit();
}

// Fetch student info
$student_sql = "SELECT name FROM students WHERE student_id = $student_id";
$student_result = $conn->query($student_sql);
$student = $student_result->fetch_assoc();

if (!$student) {
    echo "<script>alert('Student not found.'); window.location.href = 'studentList.php';</script>";
    exit();
}

// Fetch borrowed book data
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
    <title><?= htmlspecialchars($student['name']) ?> - Borrowed Books</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="admin.css">
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

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?= htmlspecialchars($student['name']) ?> - Borrowed Books</h2>
        <div class="d-flex gap-2">
            <?php if ($total_penalty > 0): ?>
                <form method="POST" onsubmit="return confirmPay(<?= $total_penalty ?>);">
                    <button type="submit" name="pay_penalty" class="btn btn-warning">Pay Penalty</button>
                </form>
            <?php endif; ?>
            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this student and all their records?');">
                <button type="submit" name="delete_student" class="btn btn-danger">Delete Student</button>
            </form>
        </div>
    </div>

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
                    <td><?= htmlspecialchars($row['book_title']) ?></td>
                    <td><?= $row['borrow_date'] ?></td>
                    <td><?= $row['due_date'] ?></td>
                    <td><?= $row['return_date'] ? $row['return_date'] : 'Not Returned' ?></td>
                    <td><?= $row['penalty_amount'] > 0 ? $row['penalty_amount'] : 'No Penalty' ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h4>Total Penalty: <strong><?= $total_penalty ? $total_penalty : '0' ?> Taka</strong></h4>
</div>

<script>
    function confirmPay(amount) {
        return confirm("Are you sure you want to pay the total penalty of " + amount + " Taka?");
    }
</script>

</body>
</html>
