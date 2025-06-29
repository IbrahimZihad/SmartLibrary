<?php
include 'connectdb.php';

$search_query = '';
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $search_query = "WHERE bl.book_id LIKE '%$search%' OR bl.book_name LIKE '%$search%'";
}

$sql = "SELECT bl.book_id, bl.book_name, bl.total_copies, bl.available_copies, bi.cover_img, bi.pdf_path
        FROM booklist bl
        LEFT JOIN bookimage bi ON bl.book_id = bi.book_id
        $search_query
        ORDER BY bl.book_id ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book List - Admin</title>
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
                <li class="nav-item"><a href="bookList.php" class="nav-link text-white fw-bold">Book List</a></li>
                <li class="nav-item"><a href="penaltyList.php" class="nav-link text-white">Penalty List</a></li>
                <li class="nav-item"><a href="borrowedList.php" class="nav-link text-white">Borrowed Books</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <!-- Add Book Button (left) -->
                <div>
                    <a href="addbooks.php" class="btn btn-success mb-2">Add Book</a>
                    <h4>Book List</h4>
                </div>

                <!-- Search Bar (right) -->
                <form method="GET" class="d-flex" style="max-width: 300px;">
                    <input type="text" name="search" class="form-control me-2" placeholder="Search by Book ID or Name" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>

            <!-- Book Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Book ID</th>
                            <th>Book Name</th>
                            <th>Total Copies</th>
                            <th>Available Copies</th>
                            <th>Cover Image</th>
                            <th>PDF Path</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()) : ?>
                                <tr>
                                    <td><?= $row['book_id'] ?></td>
                                    <td><?= htmlspecialchars($row['book_name']) ?></td>
                                    <td><?= $row['total_copies'] ?></td>
                                    <td><?= $row['available_copies'] ?></td>
                                    <td>
                                        <?php if ($row['cover_img']) : ?>
                                            <img src="<?= htmlspecialchars($row['cover_img']) ?>" alt="Cover" style="height: 60px;">
                                        <?php else : ?>
                                            <span class="text-muted">No Image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['pdf_path']) : ?>
                                            <a href="<?= htmlspecialchars($row['pdf_path']) ?>" target="_blank">View PDF</a>
                                        <?php else : ?>
                                            <span class="text-muted">No PDF</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="POST" action="deletebook.php" onsubmit="return confirm('Are you sure you want to delete this book?');">
                                            <input type="hidden" name="book_id" value="<?= $row['book_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">No books found.</td>
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
