<?php
include '../connectdb.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

$student_id = '011221257';
$valid_books = ['B1', 'B2', 'B3', 'B4', 'B5'];
$today = date("Y-m-d");
$due_date = date("Y-m-d", strtotime("+7 days"));

// Load detections.json
$detections_path = 'Ai detection/detections.json';
if (!file_exists($detections_path)) {
    http_response_code(404);
    echo json_encode(['error' => 'detections.json not found']);
    exit;
}

$data = json_decode(file_get_contents($detections_path), true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

// Extract all class names
$classes = array_map(function($item) {
    return $item['class'] ?? '';
}, $data);

// Detection flags
$has_ibrahim = in_array('Ibrahim', $classes);
$has_pick = in_array('Pick', $classes);
$has_drop = in_array('Drop', $classes);
$book_name = null;

// Match the first detected valid book
foreach ($valid_books as $vb) {
    if (in_array($vb, $classes)) {
        $book_name = $vb;
        break;
    }
}

if ($has_ibrahim && $book_name) {
    // Get book_id from name
    $stmt = $conn->prepare("SELECT book_id FROM booklist WHERE book_name = ?");
    $stmt->bind_param("s", $book_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
    $stmt->close();

    if (!$book) {
        echo json_encode(['error' => "Book '$book_name' not found"]);
        exit;
    }

    $book_id = $book['book_id'];

    if ($has_pick) {
        // Check if not already borrowed
        $check = $conn->prepare("SELECT * FROM borrowhistory WHERE student_id = ? AND book_id = ? AND return_date IS NULL");
        $check->bind_param("ss", $student_id, $book_id);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows === 0) {
            // Insert borrow record with due date = today + 7 days
            $insert = $conn->prepare("INSERT INTO borrowhistory (student_id, book_id, borrow_date, due_date, borrowed_copies, penalty) VALUES (?, ?, ?, ?, 1, 0)");
            $insert->bind_param("ssss", $student_id, $book_id, $today, $due_date);
            $insert->execute();
            $insert->close();

            // Decrease available copies
            $conn->query("UPDATE booklist SET available_copies = available_copies - 1 WHERE book_id = '$book_id'");

            echo json_encode(['status' => "Book '$book_name' borrowed successfully"]);
        } else {
            echo json_encode(['message' => "Book '$book_name' already borrowed and not returned"]);
        }

        $check->close();
    }

    elseif ($has_drop) {
        // Return the book
        $update = $conn->prepare("UPDATE borrowhistory SET return_date = ? WHERE student_id = ? AND book_id = ? AND return_date IS NULL");
        $update->bind_param("sss", $today, $student_id, $book_id);
        $update->execute();
        $update->close();

        // Increase available copies
        $conn->query("UPDATE booklist SET available_copies = available_copies + 1 WHERE book_id = '$book_id'");

        echo json_encode(['status' => "Book '$book_name' returned successfully"]);
    }

} else {
    echo json_encode(['message' => 'No valid action/class detected']);
}
?>
