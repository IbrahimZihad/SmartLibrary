<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px);}
            to { opacity: 1; transform: translateY(0);}
        }
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out both; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-100 to-purple-100 font-sans">
    <header class="bg-indigo-700 text-white shadow-lg py-6 animate-fade-in-up">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold text-center mb-2">🎓 Welcome to Your Student Dashboard</h1>
            <nav>
                <ul class="flex justify-center space-x-8 text-lg font-medium">
                    <li>
                        <a href="notification.php" class="hover:underline hover:text-yellow-300 transition-all duration-300">🔔 Notifications</a>
                    </li>
                    <li>
                        <a href="Student_borrow_history.php" class="hover:underline hover:text-yellow-300 transition-all duration-300">📜 Borrowing History</a>
                    </li>
                    <li>
                        <a href="bookList.php" class="hover:underline hover:text-yellow-300 transition-all duration-300">📚 Book List</a>
                    </li>
                </ul>
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
        const cartItems = document.getElementById("cart-items");
        const emptyMsg = document.getElementById("empty-cart");
        const cart = JSON.parse(localStorage.getItem("cart")) || [];
        function updateCart() {
            cartItems.innerHTML = "";
            if (cart.length === 0) {
                emptyMsg.classList.remove("hidden");
            } else {
                emptyMsg.classList.add("hidden");
                cart.forEach(item => {
                    const li = document.createElement("li");
                    li.className = "bg-indigo-100 rounded px-4 py-2 shadow-md hover:bg-indigo-200 transition";
                    li.textContent = item.title;
                    cartItems.appendChild(li);
                });
            }
        }
        updateCart();
    </script>
</body>
</html>