<?php
session_start();
include('../dbconnection.php');

if (!isset($_SESSION['student_id']) || !isset($_SESSION['student_name'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'];
$today = date("Y-m-d");
$days_before_due = 3;

$query = "
SELECT bl.book_name, bh.due_date
FROM borrowhistory bh
JOIN booklist bl ON bh.book_id = bl.book_id
WHERE bh.student_id = ?
AND DATEDIFF(bh.due_date, ?) BETWEEN 0 AND ?
AND bh.return_date IS NULL
";

$stmt = $con->prepare($query);
$stmt->bind_param("ssi", $student_id, $today, $days_before_due);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>📢 Notifications</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease-out both;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-100 to-purple-100 font-sans min-h-screen">

<!-- Navbar -->
<header class="bg-white shadow-md py-4 px-6 flex justify-between items-center animate-fade-in-up">
    <div class="text-indigo-700 font-bold text-xl tracking-wide">
        📚 Smart Library
    </div>
    <nav class="flex gap-8 text-lg font-medium text-gray-700">
        <a href="StudentDashboard.php" class="hover:text-indigo-600 transition">🏠 Dashboard</a>
        <a href="bookList.php?student_id=<?= urlencode($student_id) ?>" class="hover:text-indigo-600 font-semibold transition">📚 Books</a>
        <a href="Student_borrow_history.php?student_id=<?= urlencode($student_id) ?>" class="hover:text-indigo-600 transition">🕘 History</a>
        <a href="#" class="hover:text-indigo-600 transition">🔔 Notifications</a>
    </nav>
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
            <a href="profile.php" class="block px-4 py-2 hover:bg-indigo-50">👤 Profile</a>
            <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-red-50">🚪 Logout</a>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="pt-28 max-w-3xl mx-auto px-4 animate-fadeInUp">
    <div class="bg-white p-6 rounded-xl shadow-lg">
        <h1 class="text-3xl font-bold text-indigo-700 mb-6 text-center">📢 Your Notifications</h1>

        <?php if (count($notifications) > 0): ?>
            <ul class="space-y-4">
                <?php foreach ($notifications as $note): ?>
                    <li class="bg-indigo-50 border border-indigo-200 rounded-md p-4 shadow flex items-start space-x-4">
                        <svg class="w-6 h-6 text-indigo-600 shrink-0 mt-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 20h.01M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        <div class="text-gray-800">
                            📢 The due date of <span class="font-semibold text-indigo-700"><?= htmlspecialchars($note['book_name']) ?></span>
                            is <span class="font-semibold"><?= htmlspecialchars($note['due_date']) ?></span>.
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-center text-gray-500 italic">✅ No upcoming due dates within 3 days.</p>
        <?php endif; ?>
    </div>
</main>

<!-- Dropdown Script -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('profileBtn');
        const dropdown = document.getElementById('profileDropdown');
        btn.addEventListener('click', e => {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        });
        document.addEventListener('click', () => dropdown.classList.add('hidden'));
    });
</script>

</body>
</html>
