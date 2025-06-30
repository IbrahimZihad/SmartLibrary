<?php
session_start();
if (!isset($_SESSION['student_name']) || !isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}
$student_name = $_SESSION['student_name'];
$student_id = $_SESSION['student_id'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out both;
        }
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
            <a href="#" class="hover:text-indigo-600 transition">🏠 Your Dashboard</a>
            <a href="bookList.php?student_id=<?= urlencode($student_id) ?>" class="hover:text-indigo-600 transition">📚 Books</a>
            <a href="Student_borrow_history.php?student_id=<?= urlencode($student_id) ?>" class="hover:text-indigo-600 transition">🕘 History</a>
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

    <!-- Main Body -->
    <main class="container mx-auto px-4 py-10">
        <section class="bg-white rounded-xl shadow-2xl p-6 max-w-3xl mx-auto animate-fade-in-up">
            <h2 class="text-2xl font-semibold text-indigo-700 mb-4">🛒 Your Borrow Cart</h2>
            <ul id="cart-items" class="space-y-3 text-lg text-gray-800"></ul>
            <div id="empty-cart" class="text-gray-500 italic text-center mt-4 hidden"></div>
        </section>
    </main>

    <!-- JS -->
    <script>
        // Profile dropdown toggle
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('profileBtn');
            const dropdown = document.getElementById('profileDropdown');

            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', function () {
                dropdown.classList.add('hidden');
            });
        });

        // Borrow Cart
        const cartItems = document.getElementById("cart-items");
        const emptyMsg = document.getElementById("empty-cart");
        let cart = JSON.parse(localStorage.getItem("cart")) || [];

        function updateCart() {
            cartItems.innerHTML = "";
            if (cart.length === 0) {
                emptyMsg.classList.remove("hidden");
                fetchBorrowedBooks();
            } else {
                emptyMsg.classList.add("hidden");
                cart.forEach((item, idx) => {
                    const li = document.createElement("li");
                    li.className = "bg-indigo-100 rounded px-4 py-2 shadow-md flex justify-between items-center hover:bg-indigo-200 transition";
                    li.innerHTML = `<span>${item.title}</span>
                        <button onclick="removeFromCart(${idx})" class="ml-2 text-red-500 hover:text-red-700 font-bold px-2 py-1 rounded">&times;</button>`;
                    cartItems.appendChild(li);
                });
            }
        }

        window.removeFromCart = function (idx) {
            cart.splice(idx, 1);
            localStorage.setItem("cart", JSON.stringify(cart));
            updateCart();
        };

        // Run AI API
        fetch('../ai_book_action_api.php')
            .then(res => res.json())
            .then(data => console.log("AI API:", data))
            .catch(err => console.error("AI API error", err));

        // Borrowed books
        function fetchBorrowedBooks() {
            fetch("get_borrowed_books.php")
                .then(res => res.json())
                .then(data => {
                    if (Array.isArray(data) && data.length > 0) {
                        emptyMsg.innerHTML = '<h3 class="font-semibold text-lg text-gray-700 mb-2">📚 Borrowed Books</h3>';
                        data.forEach(book => {
                            const div = document.createElement('div');
                            div.className = "flex items-center gap-4 mb-4 p-3 bg-gray-100 rounded shadow";
                            div.innerHTML = `
                                <img src="${book.cover_img || 'default-cover.jpg'}" class="w-16 h-20 object-cover border rounded" alt="Cover">
                                <div class="text-left">
                                    <p><strong>ID:</strong> ${book.book_id}</p>
                                    <p><strong>Name:</strong> ${book.book_name}</p>
                                    <p><strong>Due:</strong> ${book.due_date}</p>
                                </div>`;
                            emptyMsg.appendChild(div);
                        });
                    } else {
                        emptyMsg.textContent = "No items in cart or borrowed.";
                    }
                })
                .catch(err => {
                    emptyMsg.textContent = "Error loading borrowed books.";
                    console.error(err);
                });
        }

        updateCart();
    </script>
</body>

</html>
