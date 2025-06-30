<?php
session_start();
include '../connectdb.php';

if (!isset($_SESSION['student_id']) || !isset($_SESSION['student_name'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'];

// Delete book logic
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM booklist WHERE book_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
}

// Search
$search = $_GET['search'] ?? '';
$sql = "SELECT bl.book_id, bl.book_name, bl.total_copies, bl.available_copies, bi.cover_img, bi.pdf_path AS pdf_file 
        FROM booklist bl
        LEFT JOIN bookimage bi ON bl.book_id = bi.book_id
        WHERE bl.book_name LIKE ? OR bl.book_id LIKE ?";
$stmt = $conn->prepare($sql);
$likeSearch = "%$search%";
$stmt->bind_param("ss", $likeSearch, $likeSearch);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Book List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
</head>

<body class="bg-gradient-to-br from-purple-100 via-indigo-100 to-blue-100 text-gray-900 font-[Inter] min-h-screen">
<!-- New Dashboard Style Header -->
<header class="bg-white shadow-md py-4 px-6 flex justify-between items-center animate-fade-in-up">
    <div class="text-indigo-700 font-bold text-xl tracking-wide">
        📚 Smart Library
    </div>
    <nav class="flex gap-8 text-lg font-medium text-gray-700">
        <a href="StudentDashboard.php" class="hover:text-indigo-600 transition">🏠 Dashboard</a>
        <a href="#" class="hover:text-indigo-600 font-semibold transition">📚 Books</a>
        <a href="Student_borrow_history.php?student_id=<?= urlencode($student_id) ?>" class="hover:text-indigo-600 transition">🕘 History</a>
        <a href="notification.php?student_id=<?= urlencode($student_id) ?>" class="hover:text-indigo-600 transition">🔔 Notifications</a>
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

<!-- Main -->
<div class="container mx-auto px-6 py-6">
    <form method="GET" action="" class="flex justify-center mb-8">
        <input type="text" name="search" placeholder="🔍 Search by name or ID"
               value="<?= htmlspecialchars($search) ?>"
               class="w-1/3 px-5 py-3 rounded-l-full border-2 border-indigo-300 shadow-inner focus:outline-none focus:ring-2 focus:ring-indigo-400 transition-all duration-300" />
        <button type="submit"
                class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-r-full shadow-md hover:bg-indigo-700 hover:shadow-lg hover:scale-105 transition-all duration-300">
            Search
        </button>
    </form>

    <div class="overflow-x-auto" id="book-list">
        <table id="bookTable" class="min-w-full bg-white shadow-xl rounded-2xl overflow-hidden animate-slide-up border border-indigo-200">
            <thead class="bg-indigo-600 text-white text-lg">
                <tr>
                    <th class="py-4 px-6 text-left">ID</th>
                    <th class="py-4 px-6 text-left">Name</th>
                    <th class="py-4 px-6 text-left">Total Copies</th>
                    <th class="py-4 px-6 text-left">Available</th>
                    <th class="py-4 px-6 text-left">Cover</th>
                    <th class="py-4 px-6 text-left">PDF</th>
                    <th class="py-4 px-6 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-base">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="hover:bg-indigo-50 transition duration-300 ease-in-out hover:scale-[1.01]">
                        <td class="py-4 px-6"><?= $row['book_id'] ?></td>
                        <td class="py-4 px-6 font-medium text-indigo-700"><?= htmlspecialchars($row['book_name']) ?></td>
                        <td class="py-4 px-6"><?= $row['total_copies'] ?></td>
                        <td class="py-4 px-6"><?= $row['available_copies'] ?></td>
                        <td class="py-4 px-6">
                            <?php if (!empty($row['cover_img'])): ?>
                                <img src="../<?= htmlspecialchars($row['cover_img']) ?>" alt="Cover" class="h-20 w-auto rounded shadow-md hover:scale-110 transition-transform duration-300">
                            <?php else: ?>
                                <span class="text-gray-400 italic">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-4 px-6">
                            <?php if (!empty($row['pdf_file'])): ?>
                                <a href="../<?= htmlspecialchars($row['pdf_file']) ?>" target="_blank" class="text-blue-600 hover:underline">Download</a>
                            <?php else: ?>
                                <span class="text-gray-400 italic">Not Available</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-4 px-6 space-y-2 flex flex-col items-stretch">
                            <button onclick="addToWishlist('<?= addslashes($row['book_name']) ?>')"
                                class="w-full flex items-center gap-2 bg-yellow-400 hover:bg-yellow-500 text-white px-4 py-2 rounded-full shadow transition-all duration-200 font-semibold text-sm">
                                ❤️ Add to Wishlist
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <section id="cart" class="mt-10">
        <h2 class="text-2xl font-semibold mb-4">Your Borrow Cart (Max 3)</h2>
        <ul id="cart-items" class="list-disc ml-6 text-lg space-y-1"></ul>
    </section>

    <section id="wishlist" class="mt-10">
        <h2 class="text-2xl font-semibold mb-4">Your Wishlist</h2>
        <ul id="wishlist-items" class="list-disc ml-6 text-lg space-y-1"></ul>
    </section>
</div>

<script>
    const cart = [];
    let wishlist = [];

    function addToWishlist(title) {
        if (!wishlist.includes(title)) {
            wishlist.push(title);
            updateWishlist();
        }
    }

    function removeFromWishlist(title) {
        wishlist = wishlist.filter(item => item !== title);
        updateWishlist();
    }

    function updateWishlist() {
        const wishlistItems = document.getElementById('wishlist-items');
        wishlistItems.innerHTML = '';
        wishlist.forEach(item => {
            const li = document.createElement('li');
            li.innerHTML = `${item} <button onclick="removeFromWishlist('${item}')" class="text-red-500 ml-2">Remove</button>`;
            wishlistItems.appendChild(li);
        });
    }

    window.addEventListener('DOMContentLoaded', () => {
        const dropdown = document.getElementById('profileDropdown');
        document.getElementById('profileBtn').addEventListener('click', function (e) {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        });
        document.addEventListener('click', () => dropdown.classList.add('hidden'));

        fetch('../StudentSide/get_borrowed_books.php')
            .then(res => res.json())
            .then(data => {
                const cartSection = document.getElementById('cart-items');
                if (Array.isArray(data) && data.length > 0) {
                    const heading = document.createElement('h3');
                    heading.textContent = 'Borrowed Books:';
                    heading.className = 'font-semibold mb-2 text-indigo-700';
                    cartSection.appendChild(heading);
                    data.forEach(book => {
                        const li = document.createElement('li');
                        li.innerHTML = `📘 <strong>${book.book_name}</strong> <span class="text-sm text-gray-500">(Due: ${book.due_date})</span>`;
                        cartSection.appendChild(li);
                    });
                }
            }).catch(console.error);
    });
</script>

</body>
</html>
