<?php
include 'connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_name = $_POST['book_name'];
    $total_copies = intval($_POST['total_copies']);

    if ($total_copies < 0) {
        echo "<script>alert('Total copies cannot be less than 0'); window.history.back();</script>";
        exit;
    }

    $available_copies = $total_copies;

    $stmt = $conn->prepare("INSERT INTO booklist (book_name, total_copies, available_copies) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $book_name, $total_copies, $available_copies);
    $stmt->execute();
    $book_id = $stmt->insert_id;
    $stmt->close();

    $folder = "Book Images/book_$book_id/";
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    function uploadImage($fileInput, $folder, $name)
    {
        $target = $folder . $name . ".jpg";
        move_uploaded_file($_FILES[$fileInput]['tmp_name'], $target);
        return $target;
    }

    function uploadPDF($fileInput, $folder, $name)
    {
        $target = $folder . $name . ".pdf";
        move_uploaded_file($_FILES[$fileInput]['tmp_name'], $target);
        return $target;
    }

    $cover_img = uploadImage('cover_img', $folder, 'cover');
    $side_img = uploadImage('side_img', $folder, 'side');
    $back_img = uploadImage('back_img', $folder, 'back');
    $pdf_file = uploadPDF('pdf_file', $folder, 'book');

    $stmt = $conn->prepare("INSERT INTO bookimage (book_id, cover_img, side_img, back_img, pdf_file) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $book_id, $cover_img, $side_img, $back_img, $pdf_file);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Book added successfully!'); window.location='bookList.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Book</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-cover bg-center text-gray-900 font-sans min-h-screen py-12 px-6" style="background-image: url('Images/background.jpg'); background-repeat: no-repeat;">
    <div class="max-w-xl mx-auto bg-white p-8 rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold text-center text-indigo-700 mb-8">📘 Add New Book</h1>

        <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
            <div>
                <label class="block font-medium mb-1">Book Name:</label>
                <input type="text" name="book_name" required class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400">
            </div>

            <div>
                <label class="block font-medium mb-1">Total Copies:</label>
                <input type="number" name="total_copies" min="0" required class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400">
            </div>

            <div>
                <label class="block font-medium mb-1">Cover Image:</label>
                <input type="file" name="cover_img" accept="image/*" required class="w-full">
            </div>

            <div>
                <label class="block font-medium mb-1">Side Image:</label>
                <input type="file" name="side_img" accept="image/*" required class="w-full">
            </div>

            <div>
                <label class="block font-medium mb-1">Back Image:</label>
                <input type="file" name="back_img" accept="image/*" required class="w-full">
            </div>

            <div>
                <label class="block font-medium mb-1">Book PDF:</label>
                <input type="file" name="pdf_file" accept="application/pdf" required class="w-full">
            </div>

            <div class="flex justify-center space-x-4 pt-4">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-lg shadow hover:bg-indigo-700 transition-all duration-300">
                    ➕ Add Book
                </button>
                <a href="adminBookList.php" class="bg-red-600 text-white px-6 py-3 rounded-lg shadow hover:bg-red-700 transition-all duration-300">
                    ✖ Cancel
                </a>
            </div>
        </form>
    </div>
</body>
</html>
