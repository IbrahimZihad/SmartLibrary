<?php
include '../connectdb.php'; // Include your database connection file

$student_id = isset($_GET['student_id']) ? $_GET['student_id'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fixed: Join with booklist to get book_name
$query = "SELECT bh.*, bl.book_name 
          FROM borrowhistory bh 
          JOIN booklist bl ON bh.book_id = bl.book_id 
          WHERE bh.student_id = ?";
$params = [$student_id];
$types = "s";

// Smart filters
if (!empty($search)) {
    if (is_numeric($search)) {
        $query .= " AND bh.book_id = ?";
        $types .= "i";
        $params[] = $search;
    } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $search)) {
        $query .= " AND bh.borrow_date = ?";
        $types .= "s";
        $params[] = $search;
    } elseif (strtolower($search) === "sort:title") {
        $query .= " ORDER BY bl.book_name ASC";
    } else {
        $query .= " AND bl.book_name LIKE ?";
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

        <!-- Navbar (same style as bookList.php) -->
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b-4 border-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 shadow-lg py-4 mb-10 w-full transition-all duration-300">
            <div class="flex justify-between items-center w-full px-8">
                <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-700 via-purple-700 to-pink-600 drop-shadow-lg tracking-wide">
                    Smart Library
                </h1>
                <nav>
                    <ul class="flex space-x-8 text-lg font-semibold">
                        <li>
                            <a href="bookList.php" class="text-indigo-600 hover:text-indigo-800 hover:scale-110 transition-all duration-200">📚 Book List</a>
                        </li>
                        <li>
                            <a href="Student_borrow_history.php?student_id=<?= urlencode($student_id) ?>" class="text-green-600 hover:text-green-800 hover:scale-110 transition-all duration-200">📜 Your Borrow History</a>
                        </li>
                        <li>
                            <a href="#cart" class="text-yellow-600 hover:text-yellow-700 hover:scale-110 transition-all duration-200">🛒 Your Cart</a>
                        </li>
                        <li>
                            <a href="#wishlist" class="text-yellow-600 hover:text-yellow-700 hover:scale-110 transition-all duration-200">💖 Your Wishlist</a>
                        </li>
                        <li>
                            <a href="notification.php" class="text-indigo-600 hover:text-indigo-800 hover:scale-110 transition-all duration-200">🔔 Notification</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </header>

        <form method="GET" class="flex justify-center mb-6 animate-fadeInUp">
            <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id) ?>">
            <input type="text" name="search" placeholder="🔍 Search Book Title/ID/Date or type 'sort:title'" value="<?= htmlspecialchars($search) ?>"
                class="px-4 py-2 border border-indigo-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-indigo-400 w-96">
            <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-r-md hover:bg-indigo-700 transition-all">Search</button>
        </form>

        <?php if ($result->num_rows > 0): ?>
            <div class="overflow-x-auto animate-fadeInUp">
                <table class="min-w-full bg-white/90 rounded-2xl shadow-2xl border border-indigo-100 backdrop-blur">
                    <thead class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 text-white">
                        <tr>
                            <th class="py-4 px-4 font-bold tracking-wide">📚 Book Title</th>
                            <th class="py-4 px-4 font-bold tracking-wide">🔢 Book ID</th>
                            <th class="py-4 px-4 font-bold tracking-wide">📅 Borrow Date</th>
                            <th class="py-4 px-4 font-bold tracking-wide">📆 Due Date</th>
                            <th class="py-4 px-4 font-bold tracking-wide">✅ Return Date</th>
                            <th class="py-4 px-4 font-bold tracking-wide">📦 Borrowed</th>
                            <th class="py-4 px-4 font-bold tracking-wide">💰 Penalty</th>
                        </tr>
                    </thead>
                    <tbody class="text-center text-gray-700">
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="border-t hover:bg-indigo-50 hover:scale-[1.02] hover:shadow-lg transition-all duration-200">
                                <td class="py-3 px-4 font-semibold text-indigo-700"><?= htmlspecialchars($row['book_name']) ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($row['book_id']) ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($row['borrow_date']) ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($row['due_date']) ?></td>
                                <td class="py-3 px-4"><?= $row['return_date'] ? htmlspecialchars($row['return_date']) : '<span class="italic text-red-500">Not Returned</span>' ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($row['borrowed_copies']) ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($row['penalty']) ?> ৳</td>
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
