<?php
include 'connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
    $book_id = intval($_POST['book_id']);
    
    // Delete from bookimage first (due to foreign key constraint)
    $conn->query("DELETE FROM bookimage WHERE book_id = $book_id");

    // Then delete from booklist
    $conn->query("DELETE FROM booklist WHERE book_id = $book_id");

    // Redirect back
    header("Location: adminBookList.php");
    exit;
}
?>
