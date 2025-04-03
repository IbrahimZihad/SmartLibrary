<?php
// Database connection
include 'connectdb.php';
// Fetch all students
$sql = "SELECT student_id, name, email, contact_number FROM students ORDER BY name ASC";
$result = $conn->query($sql);

$search = isset($_GET['search']) ? $_GET['search'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
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

    <!-- Navbar with Search Box -->
    <!-- Navbar with Search Box -->
    <nav class="navbar bg-light mb-3">
        <form class="d-flex" action="studentList.php" method="GET">
            <input type="text" name="search" id="searchInput" class="form-control me-2" placeholder="Search by Name or ID" value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </nav>

    <h2 class="mb-4">Student List</h2>

    <table class="table table-bordered" id="studentTable">
        <thead class="table-dark">
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <!-- Add a data search attribute for easy matching -->
                <tr id="student-<?= $row['student_id'] ?>" class="student-row" data-id="<?= $row['student_id'] ?>" data-name="<?= strtolower($row['name']) ?>">
                    <td><?= $row['student_id'] ?></td>
                    <td><a href="studentDetails.php?id=<?= $row['student_id'] ?>" class="text-decoration-none"><?= $row['name'] ?></a></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['contact_number'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Highlight the row based on search query
            const searchQuery = "<?= strtolower($search) ?>";
            if (searchQuery) {
                let rows = document.querySelectorAll(".student-row");
                let found = false;

                rows.forEach(row => {
                    const studentId = row.dataset.id;
                    const studentName = row.dataset.name;

                    // Match name or ID with the search query
                    if (studentId.includes(searchQuery) || studentName.includes(searchQuery)) {
                        row.scrollIntoView({ behavior: "smooth", block: "center" }); // Scroll to the row
                        row.classList.add("highlight"); // Highlight the row
                        found = true;
                    } else {
                        row.classList.remove("highlight"); // Remove highlight if no match
                    }
                });

                if (!found) {
                    alert("No student found with the given Name or ID.");
                }
            }
        });
    </script>
</body>
</html>