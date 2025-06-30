<?php
session_start();
include('../dbconnection.php');

// Only proceed if student is logged in
if (!isset($_SESSION['student_id'])) {
    echo "Unauthorized access.";
    exit;
}

$student_id = $_SESSION['student_id'];
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
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-100 to-purple-100 font-sans py-10 px-6">

    <div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow-lg">
        <h1 class="text-3xl font-bold text-indigo-700 mb-6 text-center">📢 Your Notifications</h1>

        <?php if (count($notifications) > 0): ?>
            <ul class="space-y-4">
                <?php foreach ($notifications as $note): ?>
                    <li class="bg-indigo-50 border border-indigo-200 rounded-md p-4 shadow flex items-center space-x-4">
                        <svg class="w-6 h-6 text-indigo-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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

</body>
</html>
