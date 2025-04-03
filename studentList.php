<?php
// Database connection
include 'connectdb.php';
// Fetch all students
$sql = "SELECT student_id, name, email, contact_number FROM students ORDER BY name ASC";
$result = $conn->query($sql);
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
    <nav class="navbar bg-light mb-3">
        <form class="d-flex" onsubmit="return searchStudent(event)">
            <input type="text" id="searchInput" class="form-control me-2" placeholder="Search by Name or ID">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </nav>

    <h2 class="mb-4">Student List</h2>

    <table class="table table-bordered">
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
                <tr>
                    <td><?= $row['student_id'] ?></td>
                    <td><a href="studentDetails.php?id=<?= $row['student_id'] ?>" class="text-decoration-none"><?= $row['name'] ?></a></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['contact_number'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <script>
        function searchStudent(event) {
            event.preventDefault(); // Prevent page reload
            let input = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll("#studentTable tbody tr");
            let found = false;

            rows.forEach(row => {
                let studentId = row.cells[0].innerText.toLowerCase();
                let studentName = row.cells[1].innerText.toLowerCase();

                // Remove previous highlights
                row.classList.remove("highlight");

                // Check if the input matches student ID or Name
                if (studentId.includes(input) || studentName.includes(input)) {
                    row.scrollIntoView({ behavior: "smooth", block: "center" });
                    row.classList.add("highlight");
                    found = true;
                }
            });

            if (!found) {
                alert("No student found with the given Name or ID.");
            }
        }
    </script>

</body>
</html>
