<?php
session_start();
include '../connectdb.php';

if (!isset($_SESSION['student_id']) || !isset($_SESSION['student_name'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'];
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Query with book join
$query = "SELECT bh.*, bl.book_name 
          FROM borrowhistory bh 
          JOIN booklist bl ON bh.book_id = bl.book_id 
          WHERE bh.student_id = ?";
$params = [$student_id];
$types = "s";

// Smart filter
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
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeInUp { animation: fadeInUp 0.6s ease-out; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-100 to-purple-100 font-sans">
    <!-- Navbar -->
    <header class="bg-white shadow-md py-4 px-6 flex justify-between items-center animate-fade-in-up">
        <!-- Left: Logo -->
        <div class="text-indigo-700 font-bold text-xl tracking-wide">
            📚 Smart Library
        </div>

        <!-- Center: Navigation Links -->
        <nav class="flex gap-8 text-lg font-medium text-gray-700">
            <a href="StudentDashboard.php" class="hover:text-indigo-600 transition">🏠 Your Dashboard</a>
            <a href="bookList.php?student_id=<?= urlencode($student_id) ?>" class="hover:text-indigo-600 transition">📚 Books</a>
            <a href="#" class="hover:text-indigo-600 transition">🕘 History</a>
            <a href="notification.php?student_id=<?= urlencode($student_id) ?>" class="hover:text-indigo-600 transition">🔔 Notifications</a>
        </nav>

        <!-- Right: Profile Dropdown -->
        <div class="relative">
            <button id="profileBtn" class="flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-full shadow hover:bg-indigo-700 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A6.002 6.002 0 0112 15h0a6.002 6.002 0 016.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </button>
            <div id="profileDropdown" class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg text-gray-800 hidden z-50">
                <div class="px-4 py-3 border-b">
                    <p class="font-semibold"><?= htmlspecialchars($student_name) ?></p>
                    <p class="text-sm text-gray-500">ID: <?= htmlspecialchars($student_id) ?></p>
                </div>
                <a href="profile.php" class="block px-4 py-2 hover:bg-indigo-50">👤 View Profile</a>
                <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-red-50">🚪 Logout</a>
            </div>
        </div>
    </header>

<!-- Main -->
<div class="max-w-6xl mx-auto px-6">
    <form method="GET" class="flex justify-center mb-6 mt-6 animate-fadeInUp">
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
        <p class="text-center text-red-500 text-lg mt-6 animate-fadeInUp">⚠️ No borrowing history found.</p>
    <?php endif; ?>
</div>

<script>
    // Profile dropdown toggle
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('profileBtn');
        const dropdown = document.getElementById('profileDropdown');
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        });
        document.addEventListener('click', () => dropdown.classList.add('hidden'));
    });
</script>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
