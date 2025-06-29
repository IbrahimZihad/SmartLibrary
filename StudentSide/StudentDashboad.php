<?php
session_start();
if (!isset($_SESSION['student_name'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student Portal</title>
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
    <header class="relative bg-gradient-to-r from-indigo-600 to-blue-500 py-8 shadow-lg animate-fade-in-up">
        <div class="container mx-auto px-4 flex flex-col items-center">
            <div class="absolute right-8 top-8 flex items-center gap-4">
                <!-- Profile Dropdown Button -->
                <div class="relative">
                    <button id="profileBtn" type="button" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-800 px-4 py-2 rounded-full shadow transition-all duration-200 font-semibold text-white focus:outline-none">
                        <span><?= htmlspecialchars($_SESSION['student_name']) ?></span>
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="profileDropdown" class="absolute right-0 mt-2 w-40 bg-white text-gray-800 rounded shadow-lg opacity-0 pointer-events-none transition-opacity duration-200 z-50">
                        <a href="profile.php" class="block px-4 py-2 hover:bg-indigo-100">View Profile</a>
                        <a href="logout.php" class="block px-4 py-2 hover:bg-indigo-100">Logout</a>
                    </div>
                </div>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">
                <span class="text-green-300 drop-shadow-lg">Welcome</span>
                🎓Your Student
                <span class="text-yellow-300 drop-shadow-lg">DashBoard</span>
            </h1>
<nav class="flex gap-10 text-lg font-medium">
    <a href="notification.php" class="hover:text-yellow-300 transition-all duration-300">🔔 Notifications</a>
    <a href="Student_borrow_history.php?student_id=<?= urlencode($_SESSION['student_id']) ?>" class="hover:text-yellow-300 transition-all duration-300">📜 Borrowing History</a>
    <a href="bookList.php" class="hover:text-yellow-300 transition-all duration-300">📚 Book List</a>
</nav>
        </div>
    </header>
    <main class="container mx-auto px-4 py-10">
        <section class="bg-white rounded-xl shadow-2xl p-6 max-w-3xl mx-auto animate-fade-in-up">
            <h2 class="text-2xl font-semibold text-indigo-700 mb-4">🛒 Your Borrow Cart</h2>
            <ul id="cart-items" class="space-y-3 text-lg text-gray-800"></ul>
            <p id="empty-cart" class="text-gray-400 italic text-center mt-4 hidden">No items in cart yet.</p>
        </section>
    </main>
    <script>
        // Profile dropdown for mobile (click)
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('profileBtn');
            const dropdown = document.getElementById('profileDropdown');
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('opacity-0');
                dropdown.classList.toggle('pointer-events-none');
            });
            document.addEventListener('click', function() {
                dropdown.classList.add('opacity-0');
                dropdown.classList.add('pointer-events-none');
            });
        });

        // Borrow cart logic with remove button
        const cartItems = document.getElementById("cart-items");
        const emptyMsg = document.getElementById("empty-cart");
        let cart = JSON.parse(localStorage.getItem("cart")) || [];

        function updateCart() {
            cartItems.innerHTML = "";
            if (cart.length === 0) {
                emptyMsg.classList.remove("hidden");
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
        // Remove item from cart
        window.removeFromCart = function(idx) {
            cart.splice(idx, 1);
            localStorage.setItem("cart", JSON.stringify(cart));
            updateCart();
        }
        updateCart();
    </script>
</body>

</html>