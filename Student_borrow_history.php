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
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>

<body class="bg-gradient-to-tr from-blue-100 via-purple-100 to-pink-100 min-h-screen py-10 font-sans">

    <div class="max-w-6xl mx-auto px-6">
        <h2 class="text-4xl font-bold text-center text-indigo-700 mb-8 animate-fadeInUp">
            📖 Borrowing History for Student ID: <?php echo htmlspecialchars($student_id); ?>
        </h2>

        <form method="GET" class="flex justify-center mb-6 animate-fadeInUp">
            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
            <input type="text" name="search" placeholder="🔍 Search Book Title/ID/Date or type 'sort:title'" value="<?php echo htmlspecialchars($search); ?>"
                class="px-4 py-2 border border-indigo-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-indigo-400 w-96">
            <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-r-md hover:bg-indigo-700 transition-all">Search</button>
        </form>

        <?php if ($result->num_rows > 0): ?>
            <div class="overflow-x-auto animate-fadeInUp">
                <table class="min-w-full bg-white rounded-xl shadow-lg">
                    <thead class="bg-indigo-600 text-white">
                        <tr>
                            <th class="py-3 px-4">📚 Book Title</th>
                            <th class="py-3 px-4">🔢 Book ID</th>
                            <th class="py-3 px-4">📅 Borrow Date</th>
                            <th class="py-3 px-4">📆 Due Date</th>
                            <th class="py-3 px-4">✅ Return Date</th>
                            <th class="py-3 px-4">📦 Borrowed</th>
                            <th class="py-3 px-4">💰 Penalty</th>
                        </tr>
                    </thead>
                    <tbody class="text-center text-gray-700">
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="border-t hover:bg-indigo-50 transition">
                                <td class="py-3 px-4 font-semibold text-indigo-700"><?php echo htmlspecialchars($row['book_title']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($row['book_id']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($row['borrow_date']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($row['due_date']); ?></td>
                                <td class="py-3 px-4"><?php echo $row['return_date'] ? htmlspecialchars($row['return_date']) : '<span class="italic text-red-500">Not Returned</span>'; ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($row['borrowed_copies']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($row['penalty']); ?> ৳</td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center text-red-500 text-lg mt-6 animate-fadeInUp">⚠️ No borrowing history found for this student ID or search term.</p>
        <?php endif; ?>
    </div>

</body>

</html>

<?php
$stmt->close();
$conn->close();
?>