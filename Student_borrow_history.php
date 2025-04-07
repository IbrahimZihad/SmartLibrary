<?php
include 'connectdb.php'; // Include your database connection file

$student_id = isset($_GET['student_id']) ? $_GET['student_id'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$query = "SELECT * FROM borrowhistory WHERE student_id = ?";
$params = [$student_id];
$types = "s";

// Smart filters
if (!empty($search)) {
    if (is_numeric($search)) {
        $query .= " AND book_id = ?";
        $types .= "i";
        $params[] = $search;
    } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $search)) {
        $query .= " AND borrow_date = ?";
        $types .= "s";
        $params[] = $search;
    } elseif (strtolower($search) === "sort:title") {
        $query .= " ORDER BY book_title ASC";
    } else {
        $query .= " AND book_title LIKE ?";
        $types .= "s";
        $params[] = "%$search%";
    }
}

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Borrowing History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 30px;
        }

        h2 {
            text-align: center;
        }

        form {
            text-align: center;
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 8px;
            width: 250px;
        }

        button {
            padding: 8px 16px;
            background: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 10px #ccc;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #eee;
            text-align: center;
        }

        th {
            background: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }

        .no-data {
            text-align: center;
            color: red;
        }
    </style>
</head>

<body>

    <h2>Borrowing History for Student ID: <?php echo htmlspecialchars($student_id); ?></h2>

    <form method="GET">
        <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
        <input type="text" name="search" placeholder="Search by Book Title/ ID/ Date/ type sort:title" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Book Title</th>
                <th>Book ID</th>
                <th>Borrow Date</th>
                <th>Due Date</th>
                <th>Return Date</th>
                <th>Borrowed Copies</th>
                <th>Penalty</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                    <td><?php echo htmlspecialchars($row['book_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['borrow_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['due_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['return_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['borrowed_copies']); ?></td>
                    <td><?php echo htmlspecialchars($row['penalty']); ?> ৳</td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p class="no-data">No borrowing history found for this student ID or search term.</p>
    <?php endif; ?>

</body>

</html>

<?php
$stmt->close();
$conn->close();
?>