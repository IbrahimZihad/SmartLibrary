<?php
include 'connectdb.php';

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM booklist WHERE book_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
}

$search = $_GET['search'] ?? '';
$sql = "SELECT bl.book_id, bl.book_name, bl.total_copies, bl.available_copies, bi.cover_img, bi.pdf_file 
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

    <header class="bg-white shadow-md py-6 mb-6">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-indigo-700">Smart Library</h1>
            <nav>
                <ul class="flex space-x-4">
                    <li><a href="#book-list" class="text-indigo-600 hover:underline">Book List</a></li>
                    <li><a href="#cart" class="text-green-600 hover:underline">Your Cart</a></li>
                    <li><a href="#wishlist" class="text-yellow-600 hover:underline">Your Wishlist</a></li>
                    <li><a href="panalty.php" class="text-red-600 hover:underline">Penalty</a></li>
                    <li><a href="notification.php" class="text-white-600 hover:underline">Notification</a></li>
                </ul>
            </nav>
        </div>
    </header>

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
                                <img src="<?= $row['cover_img'] ?>" alt="Cover" class="h-20 w-auto rounded shadow-md hover:scale-110 transition-transform duration-300">
                            </td>
                            <td class="py-4 px-6">
                                <?php if (!empty($row['pdf_file'])): ?>
                                    <a href="<?= $row['pdf_file'] ?>" target="_blank" class="text-blue-600 hover:underline">Download</a>
                                <?php else: ?>
                                    <span class="text-gray-400 italic">Not Available</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-6 space-y-2 flex flex-col items-stretch">
                                <button onclick="addToCart(<?= $row['book_id'] ?>, '<?= addslashes($row['book_name']) ?>')"
                                    class="w-full flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-full shadow transition-all duration-200 font-semibold text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A1 1 0 007.5 17h9a1 1 0 00.85-1.53L17 13M7 13V6h13" />
                                    </svg>
                                    Add to Cart
                                </button>
                                <button onclick="addToWishlist('<?= addslashes($row['book_name']) ?>')"
                                    class="w-full flex items-center gap-2 bg-yellow-400 hover:bg-yellow-500 text-white px-4 py-2 rounded-full shadow transition-all duration-200 font-semibold text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                    Add to Wishlist
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <section id="cart" class="mt-10">
            <h2 class="text-2xl font-semibold mb-4">Your Borrow Cart (Max 3)</h2>
            <ul id="cart-items" class="list-disc ml-6 text-lg"></ul>
        </section>

        <section id="wishlist" class="mt-10">
            <h2 class="text-2xl font-semibold mb-4">Your Wishlist</h2>
            <ul id="wishlist-items" class="list-disc ml-6 text-lg"></ul>
        </section>
    </div>

    <script>
        let cart = [];
        let wishlist = [];

        function addToCart(id, title) {
            if (cart.length >= 3) {
                alert("You can only borrow a maximum of 3 books.");
                return;
            }
            cart.push({
                id,
                title
            });
            updateCart();
        }

        function removeFromCart(id) {
            cart = cart.filter(item => item.id !== id);
            updateCart();
        }

        function updateCart() {
            const cartItems = document.getElementById('cart-items');
            cartItems.innerHTML = '';
            cart.forEach(item => {
                const li = document.createElement('li');
                li.innerHTML = `${item.title} <button onclick="removeFromCart(${item.id})" class="text-red-500 ml-2">Remove</button>`;
                cartItems.appendChild(li);
            });
        }

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
    </script>

    <style>
        @keyframes drop-in {
            0% {
                opacity: 0;
                transform: translateY(-30px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slide-up {
            0% {
                opacity: 0;
                transform: translateY(40px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-drop-in {
            animation: drop-in 0.7s ease-out;
        }

        .animate-slide-up {
            animation: slide-up 0.8s ease-out;
        }
    </style>
</body>

</html>