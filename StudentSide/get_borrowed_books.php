<?php
session_start();
header('Content-Type: application/json');
include '../connectdb.php'; // Adjust path if needed

// Ensure student is logged in
if (!isset($_SESSION['student_id'])) {
    echo json_encode(['error' => 'Student not logged in']);
    exit;
}

$student_id = $_SESSION['student_id'];

$sql = "SELECT 
            bh.book_id, 
            bl.book_name, 
            bi.cover_img,
            bh.due_date 
        FROM borrowhistory bh
        JOIN booklist bl ON bh.book_id = bl.book_id
        LEFT JOIN bookimage bi ON bh.book_id = bi.book_id
        WHERE bh.student_id = ? AND bh.return_date IS NULL
        ORDER BY bh.due_date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = [
        'book_id' => $row['book_id'],
        'book_name' => $row['book_name'],
        'cover_img' => $row['cover_img'] ?: 'default-cover.jpg',
        'due_date' => $row['due_date']
    ];
}

echo json_encode($books);
?>
