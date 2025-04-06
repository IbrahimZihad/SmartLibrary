<?php
// bookList.php
include 'connectdb.php'; // assumes you have a db_connection.php for DB connection

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM booklist WHERE book_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    // Related images will be deleted due to ON DELETE CASCADE
}

// Handle search
$search = $_GET['search'] ?? '';
$sql = "SELECT bl.book_id, bl.book_name, bl.total_copies, bl.available_copies, bi.cover_img FROM booklist bl
        LEFT JOIN bookimage bi ON bl.book_id = bi.book_id
        WHERE bl.book_name LIKE ? OR bl.book_id LIKE ?";

$stmt = $conn->prepare($sql);
$likeSearch = "%$search%";
$stmt->bind_param("ss", $likeSearch, $likeSearch);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book List</title>
    <style>
        table, th, td { border: 1px solid black; border-collapse: collapse; padding: 8px; }
        img { max-width: 80px; }
    </style>
</head>
<body>
<h1>Library Book List</h1>

<form method="GET" action="">
    <input type="text" name="search" placeholder="Search by name or ID" value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
</form>
<br>
<button onclick="location.href='addBooks.php'">Add Book</button>
<br><br>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Total Copies</th>
        <th>Available Copies</th>
        <th>Cover Image</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['book_id'] ?></td>
        <td><?= htmlspecialchars($row['book_name']) ?></td>
        <td><?= $row['total_copies'] ?></td>
        <td><?= $row['available_copies'] ?></td>
        <td><img src="<?= $row['cover_img'] ?>" alt="Cover"></td>
        <td><a href="bookList.php?delete_id=<?= $row['book_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a></td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
