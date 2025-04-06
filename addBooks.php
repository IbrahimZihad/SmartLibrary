<?php
// addBooks.php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_name = $_POST['book_name'];
    $total_copies = intval($_POST['total_copies']);
    $available_copies = $total_copies;

    // Insert into booklist
    $stmt = $conn->prepare("INSERT INTO booklist (book_name, total_copies, available_copies) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $book_name, $total_copies, $available_copies);
    $stmt->execute();
    $book_id = $stmt->insert_id;
    $stmt->close();

    // Create directory for this book's images
    $folder = "Book Images/book_$book_id/";
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    function uploadImage($fileInput, $folder, $name) {
        $target = $folder . $name . ".jpg";
        move_uploaded_file($_FILES[$fileInput]['tmp_name'], $target);
        return $target;
    }

    $cover_img = uploadImage('cover_img', $folder, 'cover');
    $side_img = uploadImage('side_img', $folder, 'side');
    $back_img = uploadImage('back_img', $folder, 'back');

    // Insert into bookimage
    $stmt = $conn->prepare("INSERT INTO bookimage (book_id, cover_img, side_img, back_img) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $book_id, $cover_img, $side_img, $back_img);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Book added successfully!'); window.location='bookList.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Book</title>
</head>
<body>
<h1>Add New Book</h1>
<form action="" method="POST" enctype="multipart/form-data">
    <label>Book Name:</label><br>
    <input type="text" name="book_name" required><br><br>

    <label>Total Copies:</label><br>
    <input type="number" name="total_copies" required><br><br>

    <label>Cover Image:</label><br>
    <input type="file" name="cover_img" accept="image/*" required><br><br>

    <label>Side Image:</label><br>
    <input type="file" name="side_img" accept="image/*" required><br><br>

    <label>Back Image:</label><br>
    <input type="file" name="back_img" accept="image/*" required><br><br>

    <button type="submit">Add Book</button>
</form>
</body>
</html>
