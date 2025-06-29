<?php
include 'connectdb.php';

$student_id = isset($_GET['id']) ? $conn->real_escape_string($_GET['id']) : '';

// Redirect if no student ID
if (!$student_id) {
    echo "<script>alert('Invalid student ID'); window.location.href = 'studentList.php';</script>";
    exit;
}

// Handle student deletion
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_student'])) {
    $conn->query("DELETE FROM borrowhistory WHERE student_id = '$student_id'");
    $conn->query("DELETE FROM penalty WHERE student_id = '$student_id'");
    $conn->query("DELETE FROM studentimage WHERE student_id = '$student_id'");
    $conn->query("DELETE FROM students WHERE student_id = '$student_id'");

    echo "<script>alert('Student record deleted successfully.'); window.location.href = 'studentList.php';</script>";
    exit();
}

// Handle penalty payment
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['pay_penalty'])) {
    $conn->query("UPDATE penalty SET status = 'paid', total_penalty = 0 WHERE student_id = '$student_id'");
    $conn->query("UPDATE borrowhistory SET penalty = 0 WHERE student_id = '$student_id'");

    echo "<script>alert('Penalty paid successfully.'); window.location.href = 'studentDetails.php?id=$student_id';</script>";
    exit();
}

// Fetch student
$student_sql = "SELECT name FROM students WHERE student_id = '$student_id'";
$student_result = $conn->query($student_sql);
$student = $student_result->fetch_assoc();

if (!$student) {
    echo "<script>alert('Student not found.'); window.location.href = 'studentList.php';</script>";
    exit();
}

// Borrow history
$sql = "SELECT bh.book_id, b.book_name, bh.borrow_date, bh.due_date, bh.return_date, bh.penalty, bh.borrowed_copies
        FROM borrowhistory bh
        JOIN booklist b ON bh.book_id = b.book_id
        WHERE bh.student_id = '$student_id'
        ORDER BY bh.borrow_date DESC";
$result = $conn->query($sql);

// Total penalty
$penalty_result = $conn->query("SELECT total_penalty FROM penalty WHERE student_id = '$student_id'");
$penalty_data = $penalty_result->fetch_assoc();
$total_penalty = $penalty_data['total_penalty'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($student['name']) ?> - Borrowed Books</title>
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
                <li class="nav-item"><a href="bookList.php" class="nav-link text-white">Book List</a></li>
                <li class="nav-item"><a href="penaltyList.php" class="nav-link text-white">Penalty List</a></li>
                <li class="nav-item"><a href="borrowedList.php" class="nav-link text-white">Borrowed Books</a></li>
            </ul>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4><?= htmlspecialchars($student['name']) ?> (ID: <?= $student_id ?>) - Borrow History</h4>

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

            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Book ID</th>
                            <th>Book Title</th>
                            <th>Borrow Date</th>
                            <th>Due Date</th>
                            <th>Return Date</th>
                            <th>Borrowed Copies</th>
                            <th>Penalty (Taka)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['book_id'] ?></td>
                                    <td><?= htmlspecialchars($row['book_name']) ?></td>
                                    <td><?= $row['borrow_date'] ?></td>
                                    <td><?= $row['due_date'] ?></td>
                                    <td><?= $row['return_date'] ?: '<span class="text-danger">Not Returned</span>' ?></td>
                                    <td><?= $row['borrowed_copies'] ?></td>
                                    <td><?= $row['penalty'] > 0 ? $row['penalty'] . ' TK' : '<span class="text-success">No Penalty</span>' ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-muted text-center">No borrow history found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <h5 class="mt-3">Total Penalty: <strong><?= $total_penalty ?> Taka</strong></h5>
        </main>
    </div>
</div>

<script>
    function confirmPay(amount) {
        return confirm("Are you sure you want to pay the total penalty of " + amount + " Taka?");
    }
</script>

</body>
</html>
